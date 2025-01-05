<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php';

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Initialize the response array 
$response = ['success' => false, 'message' => ''];

// Handle update when Save button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['edit_id'])) {
        $edit_id = intval($input['edit_id']);
        $name = trim($input['name'] ?? '');
        $price = trim($input['price'] ?? '');
        $type = trim($input['type'] ?? '');
        $quantity = trim($input['quantity'] ?? '');
        $cenak = trim($input['cenak'] ?? '');

        // Validate the input data
        if ($edit_id > 0 && !empty($name) && !empty($price) && !empty($type) && !empty($quantity) && !empty($cenak)) {
            try {
                // Prepare and execute the SQL statement for update
                $sql = "UPDATE inventory SET `Název` = ?, `Cena` = ?, `Typ` = ?, `Množství` = ?, `Cena K` = ? WHERE ID = ?";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute([$name, $price, $type, $quantity, $cenak, $edit_id])) {
                    $response['success'] = true;
                    $response['message'] = 'Product updated successfully';
                } else {
                    $response['message'] = 'Failed to update product.';
                }
            } catch (Exception $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid data provided.';
        }
    } else {
        $response['message'] = 'No product ID provided for update.';
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
