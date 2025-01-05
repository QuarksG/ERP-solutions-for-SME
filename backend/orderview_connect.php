<?php
$host = 'localhost';
$dbname = 'u888950461_Product_list';
$username = 'u888950461_huseyin_1';
$password = 'Huseyin123*!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
    $offset = ($page - 1) * $itemsPerPage;

    
    $stmt = $pdo->prepare("SELECT * FROM delivery_notes ORDER BY download_timestamp DESC LIMIT :offset, :itemsPerPage");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $totalQuery = $pdo->query("SELECT COUNT(*) as total FROM delivery_notes");
    $total = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

    
    header('Content-Type: application/json');
    echo json_encode(['data' => $records, 'total' => $total]);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
