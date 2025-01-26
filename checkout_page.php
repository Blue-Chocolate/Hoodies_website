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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .total {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Checkout</h1>
        <div id="cart-items">
            <!-- Cart items will be dynamically inserted here -->
        </div>
        <div class="total text-end">
            Total: $<span id="total-price">0.00</span>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary btn-lg" onclick="confirmOrder()">Confirm Order</button>
        </div>
    </div>

    <script>
        // Fetch cart data from localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartItemsElement = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');

        // Function to display cart items
        function displayCartItems() {
            cartItemsElement.innerHTML = '';
            let totalPrice = 0;

            if (cart.length === 0) {
                cartItemsElement.innerHTML = '<p class="text-center">Your cart is empty.</p>';
                totalPriceElement.textContent = '0.00';
                return;
            }

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                totalPrice += itemTotal;

                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                cartItem.innerHTML = `
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-0"><strong>${item.name}</strong></p>
                        </div>
                        <div class="col-2 text-center">
                            <p class="mb-0">$${item.price.toFixed(2)}</p>
                        </div>
                        <div class="col-2 text-center">
                            <p class="mb-0">${item.quantity}</p>
                        </div>
                        <div class="col-2 text-end">
                            <p class="mb-0">$${itemTotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;
                cartItemsElement.appendChild(cartItem);
            });

            // Update total price
            totalPriceElement.textContent = totalPrice.toFixed(2);
        }

        // Function to confirm the order
        function confirmOrder() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }

            // Save cart data to session storage
            sessionStorage.setItem('orderCart', JSON.stringify(cart));

            // Redirect to order details page
            window.location.href = 'order_details.php';
        }

        // Display cart items on page load
        displayCartItems();
    </script>
</body>
</html>