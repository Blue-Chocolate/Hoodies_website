<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=hoodiestore;charset=utf8mb4', 'root', '');

// Decode incoming JSON
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];

// Fetch product from database
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if ($product) {
    if ($product['stock'] > 0) {
        $_SESSION['cart'][$productId]['name'] = $product['name'];
        $_SESSION['cart'][$productId]['price'] = $product['price'];
        $_SESSION['cart'][$productId]['quantity'] = $_SESSION['cart'][$productId]['quantity'] ?? 0;
        $_SESSION['cart'][$productId]['quantity']++;

        // Decrease stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE product_id = ?");
        $stmt->execute([$productId]);

        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Out of stock.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
}
?>