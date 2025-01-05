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

try {
    // Prepare and execute the SQL statement to fetch all records from the inventory table
    $stmt = $pdo->prepare("SELECT * FROM inventory");
    $stmt->execute();

    // Fetch all records as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($products);
} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
