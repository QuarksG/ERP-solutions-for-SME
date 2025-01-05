<?php
// Start the session
session_start();

// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php';

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    // Generate the next delivery note number
    $stmt = $pdo->query("SELECT deliveryNoteNumber FROM delivery_notes ORDER BY ID DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nextNumber = '000001'; // Default starting number
    if ($row) {
        $lastNumber = $row['deliveryNoteNumber'];
        $nextNumber = str_pad((int)$lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    // Return the next delivery note number as a JSON response
    echo json_encode(['status' => 'success', 'deliveryNoteNumber' => $nextNumber]);

} catch (Exception $e) {
    // Return an error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
