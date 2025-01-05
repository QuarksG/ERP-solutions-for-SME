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

$response = ['success' => false, 'message' => ''];

// Handle deletion of an employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    if ($delete_id > 0) {
        try {
            $sql = "DELETE FROM calisanlar WHERE ID = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$delete_id])) {
                $response['success'] = true;
                $response['message'] = 'Employee deleted successfully';
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to delete employee: ' . $errorInfo[2]);
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log($e->getMessage()); // Log the error for debugging
        }
    }

    // Redirect to the same page to prevent resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle addition of a new employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    // Get input data
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $employee_ID = trim($_POST['employee_ID'] ?? '');
    $position = trim($_POST['position'] ?? '');

    // Validate input data
    if (empty($name) || empty($surname) || empty($employee_ID) || empty($position)) {
        $response['message'] = 'All fields are required.';
    } else {
        try {
            // Prepare and execute the SQL statement
            $sql = "INSERT INTO calisanlar (`name`, `surname`, `employee_ID`, `position`) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$name, $surname, $employee_ID, $position])) {
                $response['success'] = true;
                $response['message'] = 'Employee added successfully';
            } else {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to add employee: ' . $errorInfo[2]);
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log($e->getMessage()); // Log the error for debugging
        }
    }

    // Redirect to the same page to prevent resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch employee data for display in the table
try {
    $result = $pdo->query("SELECT * FROM calisanlar");
    $employees = $result->fetchAll(PDO::FETCH_ASSOC);
    if (!$employees) {
        $employees = [];
    }
} catch (Exception $e) {
    $response['message'] = 'Error fetching data: ' . htmlspecialchars($e->getMessage());
    error_log($e->getMessage()); // Log the error for debugging
    $employees = [];
}

$totalEmployees = count($employees); // Calculate the total number of employees
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaměstnanci</title>
    <link rel="stylesheet" href="SideBar/CSS/calisanlar.css">
</head>
<body>
<div class="container">
    <h1 class="title">Zaměstnanci</h1>

    <?php if ($response['message']): ?>
        <div class="alert <?php echo $response['success'] ? 'alert-success' : 'alert-error'; ?>">
            <?php echo htmlspecialchars($response['message']); ?>
        </div>
    <?php endif; ?>
    
    <section class="add-employee-form bg-dark p-6 rounded-lg shadow-md mt-4">
        <form id="employeeForm" method="POST">
            <input type="text" name="name" placeholder="Jméno" autocomplete="given-name" required>
            <input type="text" name="surname" placeholder="Příjmení" autocomplete="family-name" required>
            <input type="text" name="employee_ID" placeholder="ID zaměstnance" autocomplete="off" required>
            <input type="text" name="position" placeholder="Pozice" autocomplete="organization-title" required>
            <input type="submit" value="Přidat zaměstnance" class="bg-green-500 text-white rounded-lg shadow-md">
        </form>
    </section>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jméno</th>
                <th>Příjmení</th>
                <th>ID zaměstnance</th>
                <th>Pozice</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($employees)) : ?>
                <tr><td colspan="6">Žádní zaměstnanci nebyli nalezeni.</td></tr>
            <?php else : ?>
                <?php foreach ($employees as $employee) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($employee['ID']); ?></td>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['surname']); ?></td>
                        <td><?php echo htmlspecialchars($employee['employee_ID']); ?></td>
                        <td><?php echo htmlspecialchars($employee['position']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Opravdu chcete tohoto zaměstnance smazat?');" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $employee['ID']; ?>">
                                <input type="submit" value="Smazat" class="bg-red-500 text-white rounded-lg shadow-md">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Celkový počet zaměstnanců: <?php echo $totalEmployees; ?></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
