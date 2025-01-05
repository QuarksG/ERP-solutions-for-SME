<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
try {
    $pdo = include __DIR__ . '/BackEnd/db_connect.php';
    if (!$pdo) {
        throw new Exception('Database connection failed. Please check the connection settings.');
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ensure PDO exceptions are thrown
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

$errors = [];
$response = ['success' => false, 'message' => ''];

// Initialize customers array
$customers = [];

// Function to generate a random unique vendor code
function generateVendorCode($pdo) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $vendorCode = '';
    do {
        $vendorCode = '';
        for ($i = 0; $i < 4; $i++) {
            $vendorCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        // Check if the generated code is unique
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vendors WHERE vendorcode = ?");
        $stmt->execute([$vendorCode]);
    } while ($stmt->fetchColumn() > 0);
    
    return $vendorCode;
}

// Handle deletion of a customer/vendor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    if ($delete_id > 0) {
        try {
            $sql = "DELETE FROM vendors WHERE ID = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$delete_id])) {
                $response['success'] = true;
                $response['message'] = 'Customer/vendor deleted successfully';
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to delete customer/vendor: ' . $errorInfo[2]);
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log($e->getMessage()); // Log the error for debugging
        }
    }
}

// Handle addition/editing of a customer/vendor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    if (isset($_POST['inline_edit'])) {
        // Inline edit logic
        $rowId = intval($_POST['inline_edit']);
        $account_type = $_POST["edit-{$rowId}-1"] ?? '';
        $customer_name = $_POST["edit-{$rowId}-2"] ?? '';
        $ico = $_POST["edit-{$rowId}-3"] ?? '';
        $dic = $_POST["edit-{$rowId}-4"] ?? '';
        $address = $_POST["edit-{$rowId}-5"] ?? '';
        $company_name = $_POST["edit-{$rowId}-6"] ?? '';
        $email = $_POST["edit-{$rowId}-7"] ?? '';
        $phone = $_POST["edit-{$rowId}-8"] ?? '';
        $note = $_POST["edit-{$rowId}-9"] ?? '';

        try {
            $sql = "UPDATE vendors SET account_type = ?, customer_name = ?, ico = ?, dic = ?, address = ?, company_name = ?, email = ?, phone = ?, note = ? WHERE ID = ?";
            $stmt = $pdo->prepare($sql);
            $params = [$account_type, $customer_name, $ico, $dic, $address, $company_name, $email, $phone, $note, $rowId];

            if ($stmt->execute($params)) {
                $response['success'] = true;
                $response['message'] = 'Customer/vendor updated successfully';
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to update customer/vendor: ' . $errorInfo[2]);
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log($e->getMessage()); // Log the error for debugging
        }

    } else {
        // Form submission logic (adding new or updating existing)
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $account_type = trim($_POST['account_type'] ?? '');
        $customer_name = trim($_POST['customer_name'] ?? '');
        $ico = trim($_POST['ico'] ?? '');
        $dic = trim($_POST['dic'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $company_name = trim($_POST['company_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $note = trim($_POST['note'] ?? '');

        // Validate input data
        if (empty($customer_name)) {
            $errors[] = 'The "Odběratel" (customer_name) field is required.';
        } else {
            try {
                if ($id) {
                    // Update existing record
                    $sql = "UPDATE vendors SET account_type = ?, customer_name = ?, ico = ?, dic = ?, address = ?, company_name = ?, email = ?, phone = ?, note = ? WHERE ID = ?";
                    $stmt = $pdo->prepare($sql);
                    $params = [$account_type, $customer_name, $ico, $dic, $address, $company_name, $email, $phone, $note, $id];
                } else {
                    // Insert new record
                    $vendorCode = generateVendorCode($pdo);
                    $sql = "INSERT INTO vendors (account_type, customer_name, ico, dic, address, company_name, email, phone, note, vendorcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $params = [$account_type, $customer_name, $ico, $dic, $address, $company_name, $email, $phone, $note, $vendorCode];
                }

                if ($stmt->execute($params)) {
                    $response['success'] = true;
                    $response['message'] = $id ? 'Customer/vendor updated successfully' : 'Customer/vendor added successfully with code: ' . $vendorCode;
                    // Reset form values
                    $_POST = [];
                } else {
                    $errorInfo = $stmt->errorInfo();
                    throw new Exception('Failed to save customer/vendor: ' . $errorInfo[2]);
                }

            } catch (Exception $e) {
                $response['message'] = 'Error: ' . $e->getMessage();
                error_log($e->getMessage()); // Log the error for debugging
            }
        }
    }
}

// Handle messages based on the URL query parameter
if ($response['success']) {
    echo "<script>
        alert('" . htmlspecialchars($response['message']) . "');
        window.location.href = window.location.href; // Refresh the page to clear form inputs
    </script>";
}

// Fetch customer/vendor data for display in the table
try {
    $result = $pdo->query("SELECT * FROM vendors");
    $customers = $result->fetchAll(PDO::FETCH_ASSOC);
    if (!$customers) {
        $customers = []; // Ensure $customers is an array if no results
    }
} catch (Exception $e) {
    $response['message'] = 'Error fetching data: ' . htmlspecialchars($e->getMessage());
    error_log($e->getMessage()); // Log the error for debugging
    $customers = []; // Ensure $customers is an array in case of an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Kartı</title>
    <link rel="stylesheet" href="SideBar/CSS/musteri-karti.css">
    <script>
        function confirmSave() {
            return confirm('The record will be saved. Do you really want to save it?');
        }

        function confirmDeletion() {
            return confirm('Are you sure you want to delete this customer/vendor?');
        }

        // Function to filter the table based on search input
        function filterTable() {
            let input = document.getElementById('searchBox');
            let filter = input.value.toUpperCase();
            let table = document.querySelector('.styled-table tbody');
            let rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    let cell = cells[j];
                    if (cell) {
                        if (cell.textContent.toUpperCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                }

                rows[i].style.display = match ? '' : 'none';
            }
        }

        // Pagination logic
        let currentPage = 1;
        let itemsPerPage = 5000;

        function changePage(delta) {
            let table = document.querySelector('.styled-table tbody');
            let rows = table.getElementsByTagName('tr');
            let totalPages = Math.ceil(rows.length / itemsPerPage);

            currentPage += delta;

            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            document.getElementById('pageNumber').textContent = `Page ${currentPage}`;
            updateTable();
        }

        function updateTable() {
            let table = document.querySelector('.styled-table tbody');
            let rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = (i >= (currentPage - 1) * itemsPerPage && i < currentPage * itemsPerPage) ? '' : 'none';
            }
        }

        window.onload = function() {
            updateTable();
        }

        // Enable inline editing of table rows
        function enableInlineEdit(button, rowId) {
            const row = document.getElementById(`row-${rowId}`);
            const cells = row.getElementsByTagName('td');

            for (let i = 1; i < cells.length - 2; i++) { // Skip the first and last two columns (ID, Vendor Code, Actions)
                let cell = cells[i];
                let inputValue = cell.textContent.trim();
                if (i === cells.length - 3) { // This is the note field, handle textarea
                    cell.innerHTML = `<textarea name="edit-${rowId}-${i}" rows="2">${inputValue}</textarea>`;
                } else {
                    cell.innerHTML = `<input type="text" name="edit-${rowId}-${i}" value="${inputValue}" />`;
                }
            }

            button.textContent = 'Uložit';
            button.setAttribute('onclick', `saveInlineEdit(${rowId})`);
        }

        function saveInlineEdit(rowId) {
            const form = document.createElement('form');
            form.method = 'POST';

            // Add hidden field to indicate this is an inline edit submission
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'inline_edit';
            hiddenField.value = rowId;
            form.appendChild(hiddenField);

            // Gather all the input fields created for editing
            const row = document.getElementById(`row-${rowId}`);
            const cells = row.getElementsByTagName('td');

            for (let i = 1; i < cells.length - 2; i++) {
                let inputElement = cells[i].querySelector('input, textarea');
                if (inputElement) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `edit-${rowId}-${i}`;
                    hiddenInput.value = inputElement.value;
                    form.appendChild(hiddenInput);
                }
            }

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="title">Nový Zákazník</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" onsubmit="return confirmSave();">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_POST['id'] ?? ''); ?>">
            <div class="form-group">
                <label for="account_type">Účet Typ:</label>
                <input type="text" id="account_type" name="account_type" value="<?php echo htmlspecialchars($_POST['account_type'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="customer_name">Odběratel: <span style="color:red;">*</span></label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="ico">IČO:</label>
                <input type="text" id="ico" name="ico" value="<?php echo htmlspecialchars($_POST['ico'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="dic">DIČ:</label>
                <input type="text" id="dic" name="dic" value="<?php echo htmlspecialchars($_POST['dic'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Adresa:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="company_name">Název:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Telefon:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="note">Poznámka:</label>
                <textarea id="note" name="note"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-save">+ Uložit</button>
            </div>
        </form>

        <h2 class="subtitle">Zákazníci</h2>

        <!-- Search box -->
        <input type="text" id="searchBox" placeholder="Search for customers..." onkeyup="filterTable()">

        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Účet Typ</th>
                    <th>Odběratel</th>
                    <th>IČO</th>
                    <th>DIČ</th>
                    <th>Adresa</th>
                    <th>Název</th>
                    <th>E-mail</th>
                    <th>Telefon</th>
                    <th>Poznámka</th>
                    <th>Vendor Code</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr id="row-<?php echo htmlspecialchars($customer['ID']); ?>">
                        <td><?php echo htmlspecialchars($customer['ID']); ?></td>
                        <td><?php echo htmlspecialchars($customer['account_type']); ?></td>
                        <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['ico']); ?></td>
                        <td><?php echo htmlspecialchars($customer['dic']); ?></td>
                        <td><?php echo htmlspecialchars($customer['address']); ?></td>
                        <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                        <td><?php echo htmlspecialchars($customer['note']); ?></td>
                        <td><?php echo htmlspecialchars($customer['vendorcode']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirmDeletion();">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($customer['ID']); ?>">
                                <button type="submit" class="btn-delete">Smazat</button>
                            </form>
                            <button type="button" class="btn-edit" onclick="enableInlineEdit(this, <?php echo htmlspecialchars($customer['ID']); ?>)">Upravit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <div class="pagination-controls">
            <button onclick="changePage(-1)">Previous</button>
            <span id="pageNumber">Page 1</span>
            <button onclick="changePage(1)">Next</button>
        </div>
    </div>
</body>
</html>
