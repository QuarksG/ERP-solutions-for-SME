<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log the current URL and query parameters to help trace issues
$currentUrl = strtok($_SERVER["REQUEST_URI"], '?');
$logMessage = "Accessing URL: $currentUrl with params: " . json_encode($_GET) . "\n";
error_log($logMessage, 3, __DIR__ . '/error_log.txt');

// Check if the 'page' parameter is set and equals 'dashboard'
if (isset($_GET['page']) && $_GET['page'] === 'dashboard') {
    error_log('Page parameter set to "dashboard", loading dashboard...', 3, __DIR__ . '/error_log.txt');
    // You can add specific handling for this case if needed
    // exit; // Uncomment if you want to stop further execution
}

// Include the database connection file and ensure it's successfully included
try {
    $pdo = include __DIR__ . '/BackEnd/db_connect.php';
    if (!$pdo) {
        throw new Exception('Database connection failed. $pdo is not set.');
    }
} catch (Exception $e) {
    error_log('Database connection error: ' . $e->getMessage(), 3, __DIR__ . '/error_log.txt');
    die('Critical Error: ' . $e->getMessage());
}

// Update the Typ field in delivery_notes based on a match in inventory
try {
    $updateSql = "
        UPDATE delivery_notes dn
        JOIN inventory i ON dn.itemName = i.Název
        SET dn.Typ = i.Typ
        WHERE dn.Typ IS NULL OR dn.Typ <> i.Typ
    ";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute();
    error_log('Typ field in delivery_notes updated successfully.', 3, __DIR__ . '/error_log.txt');
} catch (Exception $e) {
    error_log('Error updating Typ field: ' . $e->getMessage(), 3, __DIR__ . '/error_log.txt');
}

// Set default values for date parameters if they are missing
$dateFilter = $_GET['dateFilter'] ?? 'today';
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$lastWeek = date('Y-m-d', strtotime('-1 week'));

// Initialize startDate and endDate with defaults based on dateFilter
switch ($dateFilter) {
    case 'today':
        $startDate = $today;
        $endDate = $today;
        break;
    case 'yesterday':
        $startDate = $yesterday;
        $endDate = $yesterday;
        break;
    case 'lastWeek':
        $startDate = $lastWeek;
        $endDate = $today;
        break;
    case 'custom':
        $startDate = $_GET['startDate'] ?? $lastWeek;
        $endDate = $_GET['endDate'] ?? $today;
        break;
    default:
        $startDate = $today;
        $endDate = $today;
        error_log('Invalid dateFilter value: ' . $dateFilter, 3, __DIR__ . '/error_log.txt');
}

// Pagination logic with validation
$itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
$pagenum = isset($_GET['pagenum']) ? intval($_GET['pagenum']) : 1;

// Set a maximum limit for itemsPerPage to avoid potential issues with large queries
$maxItemsPerPage = 1000;
if ($itemsPerPage > $maxItemsPerPage) {
    $itemsPerPage = $maxItemsPerPage;
} elseif ($itemsPerPage < 10) {
    $itemsPerPage = 10;
}

// Recalculate the offset after validating itemsPerPage
$offset = ($pagenum - 1) * $itemsPerPage;

// Log the itemsPerPage value to verify it
error_log("itemsPerPage validated to: $itemsPerPage", 3, __DIR__ . '/error_log.txt');

try {
    // Prepare SQL query with placeholders for the parameters
    $sql = "SELECT `ID`, `orderNumber`, `deliveryAddress`, `companyName`, 
            `shippingStart`, `orderDate`, `deliveryNoteNumber`, `driverName`, 
            `itemName`, `quantity`, `Typ`
            FROM `delivery_notes` 
            WHERE orderDate BETWEEN :startDate AND :endDate 
            LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $stmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->bindValue(':itemsPerPage', (int) $itemsPerPage, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total items for pagination
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `delivery_notes` WHERE orderDate BETWEEN :startDate AND :endDate");
    $countStmt->bindValue(':startDate', $startDate, PDO::PARAM_STR);
    $countStmt->bindValue(':endDate', $endDate, PDO::PARAM_STR);
    $countStmt->execute();
    $totalItems = $countStmt->fetchColumn();

    if ($totalItems === false) {
        throw new Exception("Failed to fetch the total number of items.");
    }

} catch (Exception $e) {
    error_log('SQL query error: ' . $e->getMessage(), 3, __DIR__ . '/error_log.txt');
    die('Error fetching data: ' . htmlspecialchars($e->getMessage()));
}

// Headers matching the data structure in Czech
$headers = [
    'ID' => 'ID',
    'orderNumber' => 'Objednávky',
    'deliveryAddress' => 'Adresa',
    'companyName' => 'Název',
    'shippingStart' => 'Začátek',
    'orderDate' => 'Datum',
    'deliveryNoteNumber' => 'Dodacího listu',
    'driverName' => 'Ridiče',
    'itemName' => 'Položky',
    'quantity' => 'Množství',
    'Typ' => 'Typ'
];
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kafa Kağıdı</title>
    <link rel="stylesheet" href="SideBar/CSS/kafa-kagidi.css">
</head>
<body>

<div class="container">
    <h1 class="title">Kafa Kağıdı</h1>
    <div class="controls">
        <input type="text" id="searchBox" placeholder="Vyhledávání" class="search-input" />
        <div class="buttons">
            <button class="btn btn-export-excel">Exportovat do Excelu</button>
            <button class="btn btn-export-pdf">Exportovat do PDF</button>
            <button id="generatePdfButton">Kafa Kagdi PDF</button>
        </div>
    </div>

    <div class="date-filter">
        <label for="dateFilter">Filtrovat podle data:</label>
        <select name="dateFilter" id="dateFilter">
            <option value="today" <?php if ($dateFilter == 'today') echo 'selected'; ?>>Dnes</option>
            <option value="yesterday" <?php if ($dateFilter == 'yesterday') echo 'selected'; ?>>Včera</option>
            <option value="lastWeek" <?php if ($dateFilter == 'lastWeek') echo 'selected'; ?>>Poslední týden</option>
            <option value="custom" <?php if ($dateFilter == 'custom') echo 'selected'; ?>>Vlastní</option>
        </select>
        <div id="customDateRange" style="display: <?php echo $dateFilter == 'custom' ? 'block' : 'none'; ?>;">
            <label for="startDate">Od:</label>
            <input type="date" id="startDate" name="startDate" value="<?php echo htmlspecialchars($startDate); ?>">
            <label for="endDate">Do:</label>
            <input type="date" id="endDate" name="endDate" value="<?php echo htmlspecialchars($endDate); ?>">
        </div>
        <button onclick="applyFilters()">Apply Filters</button>
    </div>

    <div class="pagination">
        <label for="itemsPerPage">Počet položek na stránku:</label>
        <select id="itemsPerPage" name="itemsPerPage" class="items-select" onchange="changeItemsPerPage()">
            <option value="10" <?php if ($itemsPerPage == 10) echo 'selected'; ?>>10</option>
            <option value="20" <?php if ($itemsPerPage == 20) echo 'selected'; ?>>20</option>
            <option value="30" <?php if ($itemsPerPage == 30) echo 'selected'; ?>>30</option>
            <option value="100" <?php if ($itemsPerPage == 100) echo 'selected'; ?>>100</option>
            <option value="300" <?php if ($itemsPerPage == 300) echo 'selected'; ?>>300</option>
            <option value="1000" <?php if ($itemsPerPage == 1000) echo 'selected'; ?>>1000</option>
        </select>
    </div>

    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <?php foreach ($headers as $key => $header): ?>
                        <th>
                            <div class="header-filter">
                                <?php echo $header; ?>
                                <input type="text" placeholder="Filtrovat podle <?php echo strtolower($header); ?>" class="filter-input" />
                            </div>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($headers as $key => $header): ?>
                                <td><?php echo htmlspecialchars($row[$key]); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?php echo count($headers); ?>">No data found for the current criteria.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($data)): ?>
        <div id="paginationControls" class="mt-4">
            <span id="recordsDisplayCount">Showing <?= max(1, ($pagenum - 1) * $itemsPerPage + 1) ?> to <?= min($pagenum * $itemsPerPage, $totalItems) ?> of <?= $totalItems ?></span>
            
            <!-- Previous Page Button -->
            <button id="prevPage" <?= $pagenum <= 1 ? 'disabled' : '' ?>
                onclick="changePage(<?= $pagenum - 1 ?>)">Previous</button>
            
            <span>Page <?= $pagenum ?></span>
            
            <!-- Next Page Button -->
            <button id="nextPage" <?= ($pagenum * $itemsPerPage) >= $totalItems ? 'disabled' : '' ?>
                onclick="changePage(<?= $pagenum + 1 ?>)">Next</button>
        </div>
    <?php endif; ?>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>
<script src="SideBar/JS/KafaKagdi.js"></script>

<script>
    function changePage(newPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('pagenum', newPage);
        window.location.href = url.toString();
    }

    function changeItemsPerPage() {
        const itemsPerPage = document.getElementById('itemsPerPage').value;
        const url = new URL(window.location.href);
        url.searchParams.set('itemsPerPage', itemsPerPage);
        url.searchParams.set('pagenum', 1); // Reset to first page
        window.location.href = url.toString();
    }

    function applyFilters() {
        const dateFilter = document.getElementById('dateFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        const url = new URL(window.location.href);
        url.searchParams.set('dateFilter', dateFilter);
        url.searchParams.set('startDate', startDate);
        url.searchParams.set('endDate', endDate);
        url.searchParams.set('pagenum', 1); // Reset to first page
        window.location.href = url.toString();
    }

    document.getElementById('dateFilter').addEventListener('change', function () {
        if (this.value === 'custom') {
            document.getElementById('customDateRange').style.display = 'block';
        } else {
            document.getElementById('customDateRange').style.display = 'none';
        }
    });
</script>

</body>
</html>
