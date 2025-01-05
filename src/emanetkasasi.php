<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
$pdo = include __DIR__ . '/BackEnd/db_connect.php';

if (!$pdo) {
    die('Database connection failed. Please check the connection settings.');
}

$response = ['success' => false, 'message' => ''];

// Handle deletion when delete button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    if ($delete_id > 0) {
        try {
            $sql = "DELETE FROM kasa WHERE ID = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$delete_id])) {
                $response['success'] = true;
                $response['message'] = 'Record deleted successfully';
            } else {
                $response['message'] = 'Failed to delete record.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }

    // Redirect to the same page to prevent resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle addition of a new record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    // Get input data
    $date = trim($_POST['date'] ?? '');
    $costumer = trim($_POST['costumer'] ?? '');
    $wallet_czk = trim($_POST['wallet_czk'] ?? '');
    $wallet_eur = trim($_POST['wallet_eur'] ?? '');
    $bank_czk = trim($_POST['bank_czk'] ?? '');
    $bank_eur = trim($_POST['bank_eur'] ?? '');
    $stravenky = trim($_POST['stravenky'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Validate input data
    if (empty($date) || empty($costumer)) {
        $response['message'] = 'Date and costumer fields are required.';
    } else {
        try {
            // Prepare and execute the SQL statement
            $sql = "INSERT INTO kasa (`date`, `costumer`, `wallet_czk`, `wallet_eur`, `bank_czk`, `bank_eur`, `stravenky`, `comment`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$date, $costumer, $wallet_czk, $wallet_eur, $bank_czk, $bank_eur, $stravenky, $comment])) {
                $response['success'] = true;
                $response['message'] = 'Record added successfully';
            } else {
                $response['message'] = 'Failed to add record.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }

    // Redirect to the same page to prevent resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch customer_name and vendorcode, and combine them into a single string
try {
    $stmt = $pdo->query("SELECT CONCAT(customer_name, '_', vendorcode) AS customer_vendor FROM vendors");
    $customers = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $customers = [];
    $response['message'] = 'Error fetching vendor data: ' . htmlspecialchars($e->getMessage());
}

// Fetch records from the `kasa` table for display in the table
try {
    $result = $pdo->query("SELECT * FROM kasa ORDER BY date DESC");
    $invoices = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $invoices = [];
    $response['message'] = 'Error fetching data: ' . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sledování Výdajů Společnosti</title>
    <link rel="stylesheet" href="SideBar/CSS/emanetkasasi.css">
    <script>
        // Pass PHP array to JavaScript
        window.customerVendors = <?php echo json_encode($customers); ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="SideBar/JS/kasa.js"></script>
</head>
<body>
<div class="container">
    <h1 class="title">Sledování Výdajů Společnosti</h1>
    <div class="controls">
        <form id="expenseForm" method="POST">
            <input type="date" name="date" required />
            <div style="position:relative;">
                <input type="text" name="costumer" placeholder="Položka" required oninput="showSuggestions(this)" onfocus="showSuggestions(this)" onclick="showSuggestions(this)" />
                <div class="suggestions-list"></div>
            </div>
            <input type="number" name="wallet_czk" placeholder="KASA KC" step="0.01" />
            <input type="number" name="wallet_eur" placeholder="KASA EUR" step="0.01" />
            <input type="number" name="bank_czk" placeholder="BANKA KC" step="0.01" />
            <input type="number" name="bank_eur" placeholder="BANKA EUR" step="0.01" />
            <input type="text" name="stravenky" placeholder="Stravenky" />
            <textarea name="comment" placeholder="Comment"></textarea>
            <button type="submit">Přidat Výdaj</button>
        </form>
    </div>

    <!-- Items Per Page Control -->
    <div class="pagination-controls" style="margin-bottom: 20px;">
        <label for="itemsPerPage">Items per page:</label>
        <select id="itemsPerPage" onchange="paginateTable()">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="5000">5000</option>
        </select>
        <button onclick="downloadAsExcel()" style="margin-left: 20px;">Stáhnout jako Excel</button>
    </div>
    
    <!-- Table Display -->
    <table>
        <caption>Track of company expenses</caption>
        <thead>
            <tr>
                <th>DATÜM</th>
                <th>POLOŽKY</th>
                <th>KASA KC</th>
                <th>KASA EUR</th>
                <th>BANKA KC</th>
                <th>BANKA EUR</th>
                <th>Stravenky</th>
                <th>Comment</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($invoices)) : ?>
            <?php foreach ($invoices as $invoice) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['costumer']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['wallet_czk']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['wallet_eur']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['bank_czk']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['bank_eur']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['stravenky']); ?></td>
                    <td><?php echo htmlspecialchars($invoice['comment']); ?></td>
                    <td class="flex">
                        <button type="button" class="delete-button" onclick="confirmDelete(<?= $invoice['ID'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="9" class="text-center">No data available</td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">Total</td>
            </tr>
        </tfoot>
    </table>

    <!-- Pagination Controls -->
    <div class="pagination-controls" style="margin-top: 20px;">
        <button onclick="prevPage()">Previous</button>
        <span id="pageInfo"></span>
        <button onclick="nextPage()">Next</button>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Are you sure you want to delete this record?</p>
        <form id="deleteForm" method="POST" style="display:inline;">
            <input type="hidden" name="delete_id" id="delete_id">
            <button type="submit" class="modal-button confirm">Yes, Delete</button>
            <button type="button" class="modal-button cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
// Confirm delete functionality
function confirmDelete(id) {
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Show suggestions based on user input
document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.querySelector('input[name="costumer"]');
    inputElement.addEventListener('input', () => showSuggestions(inputElement));
    inputElement.addEventListener('focus', () => showSuggestions(inputElement));
    inputElement.addEventListener('click', () => showSuggestions(inputElement)); // Show suggestions when the input is clicked
});

function showSuggestions(inputElement) {
    if (!window.customerVendors) {
        console.error("Customer vendor data not loaded.");
        return;
    }

    const userInput = inputElement.value.toLowerCase();
    const suggestions = window.customerVendors.filter(customerVendor => customerVendor.toLowerCase().includes(userInput));
    let dataList = inputElement.nextElementSibling;

    if (!dataList || !dataList.classList.contains('suggestions-list')) {
        dataList = document.createElement('div');
        dataList.className = 'suggestions-list';
        inputElement.parentNode.appendChild(dataList);
    }

    dataList.innerHTML = '';
    if (suggestions.length > 0) {
        suggestions.forEach(suggestion => {
            const option = document.createElement('div');
            option.className = 'suggestion-item';
            option.textContent = suggestion;

            option.addEventListener('click', () => {
                inputElement.value = suggestion;
                dataList.innerHTML = '';  // Clear the suggestions list after selection
            });

            dataList.appendChild(option);
        });
    } else {
        const noResults = document.createElement('div');
        noResults.className = 'suggestion-item';
        noResults.textContent = 'No results found';
        dataList.appendChild(noResults);
    }

    applySuggestionListStyles();
}

function applySuggestionListStyles() {
    if (document.querySelector('style#suggestionStyles')) return;

    const style = document.createElement('style');
    style.id = 'suggestionStyles';
    style.innerHTML = `
        .suggestions-list {
            position: absolute;
            background-color: #333;
            color: white;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            z-index: 1000;
        }
        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #555;
        }
    `;
    document.head.appendChild(style);
}
</script>

</body>
</html>
