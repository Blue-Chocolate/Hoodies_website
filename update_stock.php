<?php
session_start();

// Debugging: Log the received data
$cartData = json_decode(file_get_contents('php://input'), true);
error_log('Received Cart Data: ' . print_r($cartData, true));

// Database connection
$localhost = 'localhost';
$username = 'root';
$password = '';
$db = 'hoodiestore';

try {
    $pdo = new PDO("mysql:host=$localhost;dbname=$db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

try {
    $pdo->beginTransaction();

    foreach ($cartData as $item) {
        $productId = $item['productId'];
        $quantity = $item['quantity'];

        // Debugging: Log the product ID and quantity
        error_log('Processing Product ID: ' . $productId . ', Quantity: ' . $quantity);

        // Fetch the current stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['stock'] >= $quantity) {
            // Reduce the stock
            $newStock = $product['stock'] - $quantity;
            $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
            $stmt->execute([$newStock, $productId]);

            // Debugging: Log the updated stock
            error_log('Updated Stock for Product ID ' . $productId . ': ' . $newStock);
        } else {
            // If stock is insufficient, rollback and return an error
            $pdo->rollBack();
            error_log('Insufficient stock for Product ID: ' . $productId);
            die(json_encode(['success' => false, 'message' => 'Insufficient stock for product ID ' . $productId]));
        }
    }

    // Commit the transaction
    $pdo->commit();
    error_log('Stock update successful');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Rollback the transaction in case of an error
    $pdo->rollBack();
    error_log('Error updating stock: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating stock: ' . $e->getMessage()]);
}
?>