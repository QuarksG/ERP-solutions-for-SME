<?php
session_start();

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
            $sql = "DELETE FROM inventory WHERE ID = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$delete_id])) {
                $response['success'] = true;
                $response['message'] = 'Product deleted successfully';
            } else {
                $response['message'] = 'Failed to delete product.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }

    // Store response in session and redirect
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle addition of a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    // Get input data
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $cenak = trim($_POST['cenak'] ?? '');

    // Validate input data
    if (empty($name) || empty($price) || empty($type) || empty($quantity) || empty($cenak)) {
        $response['message'] = 'All fields are required.';
    } else {
        try {
            // Prepare and execute the SQL statement
            $sql = "INSERT INTO inventory (`Název`, `Cena`, `Typ`, `Množství`, `Cena K`) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$name, $price, $type, $quantity, $cenak])) {
                $response['success'] = true;
                $response['message'] = 'Product added successfully';
            } else {
                $response['message'] = 'Failed to add product.';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }

    // Store response in session and redirect
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Display response message from session if available
if (isset($_SESSION['response'])) {
    $response = $_SESSION['response'];
    unset($_SESSION['response']); // Clear message after use
}

// Fetch inventory data for display in the table
try {
    $result = $pdo->query("SELECT * FROM inventory");
    $products = $result->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
    $response['message'] = 'Error fetching data: ' . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém Řízení Zásob</title>
    <link rel="stylesheet" href="SideBar/CSS/inventory-view.css">
</head>
<body class="dark-mode">
    <div class="container">
        <header class="bg-dark-header p-4 rounded-lg shadow-md">
            <h1 class="text-2xl">Systém Řízení Zásob</h1>
        </header>

        <section class="add-product-form bg-dark p-6 rounded-lg shadow-md mt-4">
            <form id="productForm" method="POST">
                <input type="text" name="name" placeholder="Název" required>
                <input type="text" name="price" placeholder="Cena" required>
                <input type="text" name="type" placeholder="Typ" required>
                <input type="number" name="quantity" placeholder="Množství" required>
                <input type="text" name="cenak" placeholder="Cena K" required>
                <input type="submit" value="Přidat Produkt" class="bg-green-500 text-white rounded-lg shadow-md">
            </form>
        </section>

        <section class="filter-download-section bg-dark p-4 rounded-lg shadow-md mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="searchInput" placeholder="Vyhledat podle Názvu, Typu..." class="col-span-2">
            <select id="itemsPerPage" class="p-2 border rounded">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="40">40</option>
                <option value="80">80</option>
                <option value="160">160</option>
                <option value="300">300</option>
                <option value="1000">1000</option>
            </select>
            <button id="downloadExcel" class="bg-blue-500 text-white rounded-lg shadow-md">Excel.csv</button>
        </section>

        <table id="inventory-table" class="bg-white rounded-lg shadow-md mt-4 border">
            <thead class="table">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Název</th>
                    <th class="p-2">Cena</th>
                    <th class="p-2">Typ</th>
                    <th class="p-2">Množství</th>
                    <th class="p-2">Cena K</th>
                    <th class="p-2">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)) : ?>
                    <tr><td colspan="7">No products found.</td></tr>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <tr id="row-<?= htmlspecialchars($product['ID']) ?>">
                            <td><?= htmlspecialchars($product['ID']) ?></td>
                            <td><?= htmlspecialchars($product['Název']) ?></td>
                            <td><?= htmlspecialchars($product['Cena']) ?></td>
                            <td><?= htmlspecialchars($product['Typ']) ?></td>
                            <td><?= htmlspecialchars($product['Množství']) ?></td>
                            <td><?= htmlspecialchars($product['Cena K']) ?></td>
                            <td class='flex gap-2'>
                                <button type="button" class="edit-button" onclick="editProduct(<?= $product['ID'] ?>)">Upravit</button>
                                <button type="button" class="delete-button" onclick="confirmDelete(<?= $product['ID'] ?>)">Smazat</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span id="toastMessage"></span>
    </div>

    <!-- Confirm Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p>Are you sure you want to delete this product?</p>
            <form id="deleteForm" method="POST" style="display:inline;">
                <input type="hidden" name="delete_id" id="delete_id">
                <button type="submit" class="modal-button confirm">Yes, Delete</button>
                <button type="button" class="modal-button cancel" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
    <script src="SideBar/JS/inventory-view.js"></script>
</body>
</html>
