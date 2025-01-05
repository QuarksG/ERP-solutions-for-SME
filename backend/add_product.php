<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php'; // Adjust this path as needed

header('Content-Type: application/json');

// Fetch the raw JSON data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
    http_response_code(400); // Bad Request
    exit;
}

// Sanitize and validate input data
$name = trim($data['name'] ?? '');
$price = trim($data['price'] ?? '');
$type = trim($data['type'] ?? '');
$quantity = trim($data['quantity'] ?? '');
$cenak = trim($data['cenak'] ?? '');

if (empty($name) || empty($price) || empty($type) || empty($quantity) || empty($cenak)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    http_response_code(400); // Bad Request
    exit;
}

try {
    // Prepare the SQL statement
    $sql = "INSERT INTO `inventory` (`Název`, `Cena`, `Typ`, `Množství`, `Cena K`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Execute the statement with the provided data
    if ($stmt->execute([$name, $price, $type, $quantity, $cenak])) {
        echo json_encode(['success' => true, 'message' => 'Record added successfully']);
    } else {
        throw new Exception('Failed to execute the statement.');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
    http_response_code(500); // Internal Server Error
}
?>
