<?php
// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php';

header('Content-Type: application/json'); // Ensure the response is JSON

try {
    // Prepare the SQL query to fetch relevant vendor data, including the 'equity' column
    $stmt = $pdo->prepare("SELECT `ID`, `customer_name`, `ico`, `dic`, `address`, `company_name`, `equity` FROM `vendors` WHERE 1");
    $stmt->execute();

    // Fetch all results as an associative array
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if data was fetched and ensure it's not empty
    if ($vendors === false || empty($vendors)) {
        throw new Exception("No vendors found or query failed.");
    }

    // Output the data as JSON
    echo json_encode($vendors);

} catch (Exception $e) {
    // Handle any errors
    http_response_code(500); // Set HTTP status code to 500
    echo json_encode(['error' => $e->getMessage()]); // Output error as JSON
}
