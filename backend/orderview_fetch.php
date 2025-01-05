<?php
include 'db_connect.php'; 

header('Content-Type: application/json');
$sql = "SELECT `ID`, `name`, `nazev`, `ico`, `dic`, `balance`, `payment`, `debt`, `address`, `phone`, `note` FROM `New_Product_list`";
$stmt = $pdo->prepare($sql);



if (!$stmt->execute()) {
    echo json_encode(['error' => 'Error fetching records: ' . $stmt->errorInfo()[2]]);
    exit;
}

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($records);
?>
