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

// Parse the JSON input
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID provided.']);
    exit;
}

try {
    // Delete the product from the database
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE `ID` = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
