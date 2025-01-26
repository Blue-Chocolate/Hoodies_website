<?php
session_start();

// Database connection
$localhost = 'localhost';
$username = 'root';
$password = '';
$db = 'hoodiestore';

try {
    $pdo = new PDO("mysql:host=$localhost;dbname=$db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get cart and user data from the request
$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$address = $data['address'];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Insert order into the database
try {
    $pdo->beginTransaction();

    // Insert into orders table
    $userId = $_SESSION['user_id'] ?? 1; // Default to admin ID 1 if not logged in
    $totalPrice = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$userId, $totalPrice]);
    $orderId = $pdo->lastInsertId();

    // Insert into order_details table
    $stmt = $pdo->prepare("INSERT INTO order_details (order_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$orderId, $name, $email, $phone, $address]);

    // Insert into order_items table
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);

        // Update product stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
        $stmt->execute([$item['quantity'], $item['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>