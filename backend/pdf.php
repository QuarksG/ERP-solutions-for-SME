<?php
session_start();

// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Ensure the response is JSON

// Error handler function to catch warnings and notices
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    http_response_code(500); // Set HTTP status code to 500
    $error = [
        'error' => 'Internal Server Error',
        'details' => [
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    echo json_encode($error);
    exit(); // Stop the script
}

// Set the custom error handler
set_error_handler("customErrorHandler");

// Shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null) {
        http_response_code(500); // Set HTTP status code to 500
        $errorDetails = [
            'error' => 'Internal Server Error',
            'details' => $error
        ];
        echo json_encode($errorDetails);
    }
});

$deliveryNoteNumber = $_GET['deliveryNoteNumber'] ?? '';

if ($deliveryNoteNumber) {
    try {
        // Prepare the SQL query to fetch the order data
        $stmt = $pdo->prepare("SELECT * FROM delivery_notes WHERE deliveryNoteNumber = :deliveryNoteNumber");
        $stmt->bindParam(':deliveryNoteNumber', $deliveryNoteNumber, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all results as an associative array
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($orders) {
            echo json_encode($orders);
        } else {
            http_response_code(404); // Set HTTP status code to 404
            echo json_encode(['error' => 'No data found for the specified delivery note number.']);
        }
    } catch (Exception $e) {
        // Handle any errors
        http_response_code(500); // Set HTTP status code to 500
        echo json_encode(['error' => 'Error fetching data: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400); // Set HTTP status code to 400 for bad request
    echo json_encode(['error' => 'Invalid delivery note number.']);
}
