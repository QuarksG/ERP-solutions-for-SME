<?php
session_start();
header('Content-Type: application/json');

// Include the database connection file
$pdo = include __DIR__ . '/../db_connect.php';

try {
    // Get the JSON data sent from the client
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract the necessary data
    $deliveryNoteNumber = $data['deliveryNoteNumber'] ?? null;
    $orderNumber = $data['orderNumber'] ?? null;
    $deliveryAddress = $data['deliveryAddress'] ?? null;
    $icoNumber = $data['icoNumber'] ?? null;
    $dicNumber = $data['dicNumber'] ?? null;
    $companyName = $data['companyName'] ?? null;
    $shippingStart = $data['shippingStart'] ?? null;
    $orderDate = $data['orderDate'] ?? null;
    $driverName = $data['driverName'] ?? null;
    $shippingEnd = $data['shippingEnd'] ?? null;
    $totalServiceAmount = $data['totalServiceAmount'] ?? 0.0;
    $oldBalance = $data['oldBalance'] ?? 0.0;
    $amountDue = $data['amountDue'] ?? 0.0;

    // Gather arrays for items
    $itemNames = $data['itemNames'] ?? [];
    $quantities = $data['quantities'] ?? [];
    $unitPrices = $data['unitPrices'] ?? [];
    $totalItemAmounts = $data['totalItemAmounts'] ?? [];

    // Prepare the SQL statement for inserting each item into the delivery_notes table
    $sql = "INSERT INTO delivery_notes 
            (deliveryNoteNumber, orderNumber, deliveryAddress, icoNumber, dicNumber, companyName, shippingStart, orderDate, driverName, shippingEnd, itemName, quantity, unitPrice, totalItemAmount, totalServiceAmount, oldBalance, amountDue) 
            VALUES (:deliveryNoteNumber, :orderNumber, :deliveryAddress, :icoNumber, :dicNumber, :companyName, :shippingStart, :orderDate, :driverName, :shippingEnd, :itemName, :quantity, :unitPrice, :totalItemAmount, :totalServiceAmount, :oldBalance, :amountDue)";

    $stmt = $pdo->prepare($sql);

    // Loop through each item and insert it into the delivery_notes table
    for ($i = 0; $i < count($itemNames); $i++) {
        $stmt->execute([
            ':deliveryNoteNumber' => $deliveryNoteNumber,
            ':orderNumber' => $orderNumber,
            ':deliveryAddress' => $deliveryAddress,
            ':icoNumber' => $icoNumber,
            ':dicNumber' => $dicNumber,
            ':companyName' => $companyName,
            ':shippingStart' => $shippingStart,
            ':orderDate' => $orderDate,
            ':driverName' => $driverName,
            ':shippingEnd' => $shippingEnd,
            ':itemName' => $itemNames[$i] ?? null,
            ':quantity' => $quantities[$i] ?? null,
            ':unitPrice' => $unitPrices[$i] ?? null,
            ':totalItemAmount' => $totalItemAmounts[$i] ?? null,
            ':totalServiceAmount' => $totalServiceAmount,
            ':oldBalance' => $oldBalance,
            ':amountDue' => $amountDue,
        ]);
    }

    // Return success response
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    // Return error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
