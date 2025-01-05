<?php
// Include database connection
$pdo = include __DIR__ . '/../db_connect.php';

// Get the `cenak` parameter from the query string
$cenak = isset($_GET['cenak']) ? $_GET['cenak'] : null;

if ($cenak === null) {
    echo json_encode(['error' => 'Missing cenak parameter']);
    exit;
}

try {
    // Prepare and execute the SQL query
    $stmt = $pdo->prepare('SELECT `Název`, `Cena`, `Typ`, `Množství`, `Cena K`, `ID` FROM `inventory` WHERE `Cena K` = :cenak');
    $stmt->execute(['cenak' => $cenak]);

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode($results);

} catch (PDOException $e) {
    // Handle any errors that occur
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
