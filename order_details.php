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
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./assets/css/home.css">
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
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
        }
    </style>
</head>
<body>
<nav>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Shop</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        <div class="button">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button onclick="window.location.href='logout.php';">Logout</button>
            <?php else: ?>
                <button onclick="window.location.href='login.php';">Login</button>
                <button onclick="window.location.href='signup.php';">Sign Up</button>
            <?php endif; ?>
        </div>
    </nav>
    
    <div class="container my-5">

        <h1 class="text-center mb-4">Order Details</h1>
        <div id="cart-items">
            <!-- Cart items will be dynamically inserted here -->
        </div>
        <div class="total text-end">
            Total: $<span id="total-price">0.00</span>
        </div>

        <!-- User Information Form -->
        <form id="order-form" class="mt-4" onsubmit="placeOrder(event)">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Delivery Address</label>
                <textarea class="form-control" id="address" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Place Order
            <div style="width: 33%; height: 100%; background-color: rgb(30, 7, 208);"></div>
            </div>
            </button>
            
        </form>
        
    </div>

    <script>
        // Fetch cart data from session storage
        const cart = JSON.parse(sessionStorage.getItem('orderCart')) || [];
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
                cartItem.className = 'cart-item mb-3';
                cartItem.innerHTML = `
                    <div class="d-flex align-items-center">
                        <img src="${item.image}" alt="${item.name}" class="product-image">
                        <div>
                            <p class="mb-0"><strong>${item.name}</strong></p>
                            <p class="mb-0">Price: $${item.price.toFixed(2)}</p>
                            <p class="mb-0">Quantity: ${item.quantity}</p>
                            <p class="mb-0">Total: $${itemTotal.toFixed(2)}</p>
                        </div>
                    </div>
                `;
                cartItemsElement.appendChild(cartItem);
            });

            // Update total price
            totalPriceElement.textContent = totalPrice.toFixed(2);
        }

        // Function to handle form submission
        function placeOrder(event) {
            event.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;

            // Send data to the server
            fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart: cart,
                    name: name,
                    email: email,
                    phone: phone,
                    address: address
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order placed successfully!');
                    sessionStorage.removeItem('orderCart');
                    window.location.href = 'thankyou.php'; // Redirect to thank you page
                } else {
                    alert(data.message || 'Error placing order. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during checkout. Please try again later.');
            });
        }

        // Display cart items on page load
        displayCartItems();
    </script>
</body>
<footer style="background-color: #ea580c;">
        <p style="background-color:  #ea580c;"> 2025 E-commerce Website. All rights reserved.</p>
    </footer>
</html>