<?php
// Include the database connection
$pdo = include __DIR__ . '/BackEnd/db_connect.php';

if ($pdo === false) {
    die('Database connection failed. Please check your database connection settings.');
}

// Fetch total sales count from the delivery_notes table
$stmt = $pdo->prepare("
    SELECT SUM(quantity) AS total_sales_count
    FROM (
        SELECT DISTINCT deliveryNoteNumber, quantity
        FROM delivery_notes
    ) AS unique_notes
");
$stmt->execute();
$totalSalesCountRow = $stmt->fetch(PDO::FETCH_ASSOC);
$totalSalesCount = $totalSalesCountRow['total_sales_count'] ?? 0;

// Fetch total assets (total sales value)
$stmt = $pdo->prepare("
    SELECT SUM(totalServiceAmount) AS total_assets
    FROM (
        SELECT DISTINCT deliveryNoteNumber, totalServiceAmount
        FROM delivery_notes
    ) AS unique_notes
");
$stmt->execute();
$totalSalesValueRow = $stmt->fetch(PDO::FETCH_ASSOC);
$totalSalesValue = $totalSalesValueRow['total_assets'] ?? 0;

// Fetch total liabilities (payments received)
$stmt = $pdo->prepare("
    SELECT SUM(wallet_czk + bank_czk) AS total_liabilities
    FROM kasa
");
$stmt->execute();
$paymentsReceivedRow = $stmt->fetch(PDO::FETCH_ASSOC);
$paymentsReceived = $paymentsReceivedRow['total_liabilities'] ?? 0;

// Calculate the outstanding balance (equity)
$outstandingBalance = $totalSalesValue - $paymentsReceived;

// Fetch driver performance data excluding BAKIYE_DUZELTME
$stmt = $pdo->prepare("
    SELECT driverName, SUM(quantity) AS total_quantity, SUM(totalServiceAmount) AS total_amount
    FROM delivery_notes
    WHERE driverName != 'BAKIYE_DUZELTME'
    GROUP BY driverName
");
$stmt->execute();
$driverPerformanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch trader data (orderNumber and totalServiceAmount)
$stmt = $pdo->prepare("
    SELECT orderNumber, SUM(totalServiceAmount) AS balance
    FROM delivery_notes
    GROUP BY orderNumber
    ORDER BY balance DESC
    LIMIT 10
");
$stmt->execute();
$traderData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch vendor data (correcting the column name to 'company_name')
$stmt = $pdo->prepare("
    SELECT company_name, equity AS debt
    FROM vendors
    ORDER BY debt DESC
    LIMIT 10
");
$stmt->execute();
$vendorData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gösterge Paneli</title>
    <link rel="stylesheet" href="SideBar/CSS/dashboard.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Gösterge Paneli</h1>
        <div class="grid">
            <div class="card">
                <h2 class="card-title">Toplam Satış Adet</h2>
                <div class="card-value"><?php echo $totalSalesCount; ?></div>
            </div>
            <div class="card">
                <h2 class="card-title">Toplam Satış Kč</h2>
                <div class="card-value"><?php echo number_format($totalSalesValue, 2, ',', ' ') . ' Kč'; ?></div>
            </div>
            <div class="card">
                <h2 class="card-title">Kasaya Giren Ödeme Kč</h2>
                <div class="card-value"><?php echo number_format($paymentsReceived, 2, ',', ' ') . ' Kč'; ?></div>
            </div>
            <div class="card">
                <h2 class="card-title">Açıkta Kalan Bakiye</h2>
                <div class="card-value"><?php echo number_format($outstandingBalance, 2, ',', ' ') . ' Kč'; ?></div>
            </div>
            <div id="bar-chart" class="card">
                <canvas id="bar-chart-canvas"></canvas>
            </div>
            <div class="card">
            <h2 class="card-title">Ust Seviye Tedarikci</h2>
            <div class="trader-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Balance (Kč)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($traderData as $trader) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trader['orderNumber']); ?></td>
                                <td><?php echo number_format($trader['balance'], 2, ',', ' ') . ' Kč'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <h2 class="card-title">Top 10 Borclu listesi</h2>
            <div class="trader-table">
                <table>
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Debt (Kč)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendorData as $vendor) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vendor['company_name']); ?></td>
                                <td><?php echo number_format($vendor['debt'], 2, ',', ' ') . ' Kč'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    <!-- Include Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Inline JavaScript to render the chart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bar-chart-canvas').getContext('2d');
            const driverNames = <?php echo json_encode(array_column($driverPerformanceData, 'driverName')); ?>;
            const quantities = <?php echo json_encode(array_column($driverPerformanceData, 'total_quantity')); ?>;
            const totalAmounts = <?php echo json_encode(array_column($driverPerformanceData, 'total_amount')); ?>;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: driverNames,
                    datasets: [
                        {
                            label: 'Total Quantity',
                            data: quantities,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Total Service Amount (Kč)',
                            data: totalAmounts,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
