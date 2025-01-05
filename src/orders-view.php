<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
try {
    $pdo = include __DIR__ . '/BackEnd/db_connect.php';
    if (!$pdo) {
        throw new Exception('Database connection failed.');
    }
} catch (Exception $e) {
    die('Critical Error: ' . $e->getMessage());
}

// Initialize variables
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10; // Default to 10 items per page
$pagenum = isset($_GET['pagenum']) ? max(1, intval($_GET['pagenum'])) : 1;
$offset = ($pagenum - 1) * $itemsPerPage;

// Prepare SQL query to fetch unique orders based on delivery notes
$sql = "SELECT deliveryNoteNumber, 
               MIN(orderNumber) AS orderNumber, 
               MIN(deliveryAddress) AS deliveryAddress, 
               MIN(icoNumber) AS icoNumber, 
               MIN(dicNumber) AS dicNumber, 
               MIN(companyName) AS companyName, 
               MIN(orderDate) AS orderDate, 
               MIN(driverName) AS driverName, 
               MIN(totalServiceAmount) AS totalServiceAmount, 
               MIN(amountDue) AS amountDue
        FROM delivery_notes
        WHERE (orderNumber LIKE :searchTerm1 OR deliveryNoteNumber LIKE :searchTerm2)
        GROUP BY deliveryNoteNumber
        ORDER BY deliveryNoteNumber DESC
        LIMIT :offset, :itemsPerPage";

try {
    // Prepare and execute the SQL query
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':searchTerm1', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':searchTerm2', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the orders
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total order count for pagination
    $countSql = "SELECT COUNT(DISTINCT deliveryNoteNumber) FROM delivery_notes WHERE (orderNumber LIKE :searchTerm1 OR deliveryNoteNumber LIKE :searchTerm2)";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->bindValue(':searchTerm1', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $countStmt->bindValue(':searchTerm2', '%' . $searchTerm . '%', PDO::PARAM_STR);
    $countStmt->execute();
    $totalOrders = $countStmt->fetchColumn();

    if ($totalOrders === false) {
        throw new Exception("Failed to fetch the total number of orders.");
    }

} catch (Exception $e) {
    echo 'Error fetching orders: ' . htmlspecialchars($e->getMessage()) . "<br>";
    error_log($e->getMessage(), 3, __DIR__ . '/error_log.txt');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="SideBar/CSS/orders-view.css">
    <title>Order Management</title>
</head>
<body>
    <div id="content" class="container mx-auto p-4">
        <div id="mainHeader" class="flex justify-between items-center mb-4">
        <form method="get" style="display: inline;">
            <input
                type="text"
                name="search"
                id="searchBox"
                placeholder="Search data"
                value="<?= htmlspecialchars($searchTerm) ?>"
                class="border p-2 rounded"
            />
            <select
                id="itemsPerPage"
                name="itemsPerPage"
                class="border p-2 rounded"
                onchange="window.location.href='?page=orders-view&itemsPerPage=' + this.value + '&pagenum=1&search=<?= urlencode($searchTerm) ?>'"
            >
                <?php
                $itemsPerPageOptions = [10, 20, 30, 50, 100,1000];
                foreach ($itemsPerPageOptions as $num) {
                    $selected = $itemsPerPage == $num ? 'selected' : '';
                    echo "<option value='$num' $selected>$num</option>";
                }
                ?>
            </select>
            <input type="hidden" name="pagenum" value="1" /> <!-- Changed to 'pagenum' -->
        </form>
            <a href="?download=true&page=orders-view&search=<?= urlencode($searchTerm) ?>&itemsPerPage=<?= $itemsPerPage ?>&pagenum=<?= $pagenum ?>" id="downloadExcel" class="bg-blue-500 text-white rounded p-2">Download as Excel</a>
        </div>
        <?php if (!empty($orders)): ?>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500 text-white">
                        <th>Dodacího Cislo</th>
                        <th>Odberatel</th>
                        <th>ICO</th>
                        <th>DIC</th>
                        <th>Název</th>
                        <th>Adresa</th>
                        <th>Datum</th>
                        <th>Ridiče</th>
                        <th>Celkem</th>
                        <th>Dlužná Cástka</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="javascript:void(0);" onclick="fetchAndGeneratePDF('<?= htmlspecialchars($order['deliveryNoteNumber']) ?>')">
                                    <?= htmlspecialchars($order['deliveryNoteNumber']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($order['orderNumber']) ?></td>
                            <td><?= htmlspecialchars($order['icoNumber']) ?></td>
                            <td><?= htmlspecialchars($order['dicNumber']) ?></td>
                            <td><?= htmlspecialchars($order['companyName']) ?></td>
                            <td><?= htmlspecialchars($order['deliveryAddress']) ?></td>
                            <td><?= htmlspecialchars($order['orderDate']) ?></td>
                            <td><?= htmlspecialchars($order['driverName']) ?></td>
                            <td><?= number_format((float)$order['totalServiceAmount'], 2) ?></td>
                            <td><?= number_format((float)$order['amountDue'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found for the current criteria.</p>
        <?php endif; ?>
    <div id="paginationControls" class="mt-4">
        <span id="recordsDisplayCount">Showing <?= max(1, ($pagenum - 1) * $itemsPerPage + 1) ?> to <?= min($pagenum * $itemsPerPage, $totalOrders) ?> of <?= $totalOrders ?></span>
        <button id="prevPage" <?= $pagenum <= 1 ? 'disabled' : '' ?> 
            onclick="window.location.href='?page=orders-view&pagenum=<?= $pagenum - 1 ?>&itemsPerPage=<?= $itemsPerPage ?>&search=<?= urlencode($searchTerm) ?>'">Previous</button>
        <span>Page <?= $pagenum ?></span>
        <button id="nextPage" <?= ($pagenum * $itemsPerPage) >= $totalOrders ? 'disabled' : '' ?> 
            onclick="window.location.href='?page=orders-view&pagenum=<?= $pagenum + 1 ?>&itemsPerPage=<?= $itemsPerPage ?>&search=<?= urlencode($searchTerm) ?>'">Next</button>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>
    <script src="SideBar/JS/orders-view.js"></script>
</body>
</html>
