<?php
// Include the database connection
$pdo = include __DIR__ . '/BackEnd/db_connect.php';

if ($pdo === false) {
    die('Database connection failed. Please check your database connection settings.');
}

$stmt = $pdo->prepare("
    SELECT 
        SUM(assets) AS total_assets, 
        SUM(liability) AS total_liabilities, 
        SUM(assets - liability) AS total_equity 
    FROM vendors
");

if ($stmt->execute()) {
    $totalsRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAssets = $totalsRow['total_assets'] ?? 0;
    $totalLiabilities = $totalsRow['total_liabilities'] ?? 0;
    $zustatek = $totalsRow['total_equity'] ?? 0;
} else {
    $totalAssets = 0;
    $totalLiabilities = 0;
    $zustatek = 0;
    error_log("Failed to execute totals query: " . print_r($stmt->errorInfo(), true));
}

$stmt = $pdo->prepare("
    SELECT 
        v.ID, 
        v.customer_name AS Odberatel, 
        v.ico, 
        v.dic, 
        v.address, 
        v.company_name AS Název, 
        v.vendorcode, 
        IFNULL(unique_notes.totalServiceAmount, 0) AS assets, 
        IFNULL(SUM(k.wallet_czk + k.bank_czk), 0) AS liability, 
        v.lastupdate, 
        v.comment
    FROM 
        vendors v
    LEFT JOIN (
        SELECT 
            orderNumber, 
            SUM(totalServiceAmount) AS totalServiceAmount 
        FROM (
            SELECT DISTINCT deliveryNoteNumber, orderNumber, totalServiceAmount 
            FROM delivery_notes
        ) AS unique_notes
        GROUP BY orderNumber
    ) AS unique_notes
    ON v.customer_name = unique_notes.orderNumber
    LEFT JOIN kasa k
    ON CONCAT(v.customer_name, '_', v.vendorcode) = k.costumer
    GROUP BY 
        v.ID, v.customer_name, v.ico, v.dic, v.address, v.company_name, v.vendorcode, v.lastupdate, v.comment
");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updateStmt = $pdo->prepare("
    UPDATE vendors
    SET 
        assets = :assets,
        liability = :liability,
        equity = :equity
    WHERE vendorcode = :vendorcode
");

foreach ($rows as $row) {
    $equity = $row['assets'] - $row['liability'];

    if (!$updateStmt->execute([
        ':assets' => $row['assets'],
        ':liability' => $row['liability'],
        ':equity' => $equity,
        ':vendorcode' => $row['vendorcode']
    ])) {
        error_log("Failed to update vendor data: " . print_r($updateStmt->errorInfo(), true));
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muhasebe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../SideBar/CSS/muhasebe.css">
    <script src="../SideBar/JS/muhasebe.js" defer></script>
</head>
<body>
<div class="container mt-5">
    <div class="top-container">
        <div class="box box-revenue">
            <h3>Toplam Satis Kč</h3>
            <p><?php echo number_format($totalAssets, 2, '.', ','); ?> Kč</p>
        </div>
        <div class="box box-receivables">
            <h3>Kasaya Giren Odeme Kč</h3>
            <p><?php echo number_format($totalLiabilities, 2, '.', ','); ?> Kč</p>
        </div>
        <div class="box box-liabilities">
            <h3>Acikda Kalan Bakiye</h3>
            <p><?php echo number_format($zustatek, 2, '.', ','); ?> Kč</p>
        </div>
    </div>

    <div class="top-buttons mb-4">
        <button class="btn btn-primary" id="alacakli-listesi">Alacakli Listesi</button>
        <button class="btn btn-warning" id="askiya-alinanlar">Askiya alinanlar</button>
        <button class="btn btn-success" id="ust-seviye-ticaret">Üst Seviye ticaret</button>
    </div>

    <h1 class="title">Muhasebe</h1>
    <div class="controls">
        <input type="text" id="search-box" placeholder="Vyhledat podle Názvu, Typu..." class="search-box"/>
        <button id="download-csv" class="download-button">Stáhnout jako CSV</button>
    </div>
    <table class="styled-table" id="vendor-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Odberatel</th>
                <th>Název</th>
                <th>Address</th>
                <th>IČO</th>
                <th>DIČ</th>
                <th>VendorCode</th>
                <th>Objem</th>
                <th>Platba</th>
                <th>Zustatek</th>
                <th>Aktualizace</th>
                <th>Komentář</th>
                <th>Zmenit</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Odberatel']); ?></td>
                <td><?php echo htmlspecialchars($row['Název']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['ico']); ?></td>
                <td><?php echo htmlspecialchars($row['dic']); ?></td>
                <td><?php echo htmlspecialchars($row['vendorcode']); ?></td>
                <td><?php echo number_format($row['assets'], 2, '.', ','); ?></td>
                <td><?php echo number_format($row['liability'], 2, '.', ','); ?></td>
                <td><?php echo number_format($row['assets'] - $row['liability'], 2, '.', ','); ?></td>
                <td><?php echo htmlspecialchars($row['lastupdate']); ?></td>
                <td contenteditable="true"><?php echo htmlspecialchars($row['comment']); ?></td>
                <td><button class="edit-button">Změnit</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
