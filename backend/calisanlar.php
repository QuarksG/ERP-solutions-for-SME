<?php
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

try {
    // Prepare the SQL query to fetch employee data from `calisanlar` table
    $stmt = $pdo->prepare("SELECT `ID`, `name`, `surname`, `employee_ID`, `position` FROM `calisanlar` WHERE 1");
    $stmt->execute();

    // Fetch all results as an associative array
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output the data as JSON
    echo json_encode($employees);

} catch (Exception $e) {
    // Handle any errors
    http_response_code(500); // Set HTTP status code to 500
    echo json_encode(['error' => $e->getMessage()]); // Output error as JSON
}
