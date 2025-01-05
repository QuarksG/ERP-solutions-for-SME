<?php
try {
    // Include the database connection
    $pdo = include __DIR__ . '/../db_connect.php';

    // Get the JSON data sent from the client
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract the orderNumber
    $orderNumber = $data['orderNumber'] ?? null;

    if ($orderNumber) {
        // Fetch total assets from delivery_notes for the given orderNumber
        $stmt = $pdo->prepare("SELECT SUM(totalServiceAmount) AS assets FROM delivery_notes WHERE orderNumber = :orderNumber");
        $stmt->execute([':orderNumber' => $orderNumber]);
        $totalAssetsRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $assets = $totalAssetsRow['assets'] ?? 0;

        // Calculate total liabilities from kasa for the customer (wallet + bank)
        $stmt = $pdo->prepare("SELECT SUM(wallet_czk + bank_czk) AS liability FROM kasa WHERE costumer = :customerName");
        $stmt->execute([':customerName' => $orderNumber]); // Assuming orderNumber matches customerName
        $totalLiabilitiesRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $liability = $totalLiabilitiesRow['liability'] ?? 0;

        // Calculate the equity
        $equity = $assets - $liability;

        // Update the vendor's assets, liabilities, and equity in the vendors table
        $stmt = $pdo->prepare("
            UPDATE vendors 
            SET assets = :assets, 
                liability = :liability, 
                equity = :equity, 
                lastupdate = NOW() 
            WHERE customer_name = :customerName
        ");
        $stmt->execute([
            ':assets' => $assets,
            ':liability' => $liability,
            ':equity' => $equity,
            ':customerName' => $orderNumber // Assuming orderNumber matches customer_name
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No vendor found with the provided order number']);
        }
    } else {
        throw new Exception('No order number provided');
    }
} catch (Exception $e) {
    error_log('Error updating vendor balance: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
