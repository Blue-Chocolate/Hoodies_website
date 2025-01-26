<?php
session_start(); // Start the session

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
    <title>Top Best</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="favicon" href="./assets/images/hoodies/3B2564AF-F214-41DC-AA37-C96B92833B81.jpeg">
    <link rel="stylesheet" href="./assets/css/home.css">
</head>
<body>
    <!-- Navigation and Header -->
    <nav>

        <ul>
        <div class="icon"></div>

            <li><a href="home.php">Home</a></li>
            <li><a href="#">Shop</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="reviews.php">Reviews</a></li>
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
    <header>
        <div class="content">
            <h1>Premium Hoodies for Every Style</h1>
            <p>Comfort meets fashion with our exclusive collection</p>
        </div>
        <button>Shop Now</button>
    </header>
    <svg onclick="openShoppingCart()" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" class="carticon">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
    </svg>
    <div class="start"><p id="Start">0</p></div>

    <!-- Products Section -->
    <div class="container">
        <section class="products" id="product">
            <h2 class="text-center my-4">Our Products</h2>
            <div class="products-container">
                <?php
                $query = "SELECT * FROM products";
                $stmt = $pdo->query($query);

                while ($row = $stmt->fetch()) {
                    echo '<div class="product card" style="width: 18rem;">';
                    echo '<img class="card-img-top" src="' . htmlspecialchars($row['image_path']) . '" alt="Product Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p class="card-text"><strong>Price: $' . number_format($row['price'], 2) . '</strong></p>';
                    echo '<p class="card-text"><strong>Stock: ' . htmlspecialchars($row['stock']) . '</strong></p>';
                    echo '<button class="btn btn-primary" onclick="addToCart(' . $row['product_id'] . ', \'' . htmlspecialchars($row['name']) . '\', ' . $row['price'] . ', \'' . htmlspecialchars($row['image_path']) . '\')">Add to Cart</button>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>
    </div>

    <!-- Shopping Cart Modal -->
    <div class="modal fade" id="shoppingCartModal" tabindex="-1" aria-labelledby="shoppingCartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shoppingCartModalLabel">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="cart-items" id="cart-items">
                        <!-- Cart items will be displayed here -->
                    </div>
                </div>
                <div class="bar1" style="border: 1px solid gray; width: 100%; height: 5px;">
    <div style="width: 33%; height: 100%; background-color: rgb(30, 7, 208);"></div>
</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="checkout()">Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #ea580c;">
        <p style="background-color:  #ea580c;"> 2025 E-commerce Website. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Cart Functionality -->
    <script>
      let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Function to add a product to the cart
function addToCart(productId, productName, productPrice, productImage) {
    const item = {
        id: productId,
        name: productName,
        price: productPrice,
        image: productImage,
        quantity: 1
    };

    const existingItem = cart.find(i => i.id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push(item);
    }

    // Update localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Update the cart display
    updateCartDisplay();
}

// Function to update the cart display
function updateCartDisplay() {
    const cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = '';

    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-center">Your cart is empty.</p>';
        return;
    }

    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item mb-3';
        cartItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                    <p class="mb-0">${item.name} - $${item.price.toFixed(2)} x ${item.quantity}</p>
                </div>
                <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">Remove</button>
            </div>
        `;
        cartItems.appendChild(cartItem);
    });
}

// Function to remove an item from the cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Function to open the shopping cart modal
function openShoppingCart() {
    updateCartDisplay(); // Update the cart display before showing the modal
    new bootstrap.Modal(document.getElementById('shoppingCartModal')).show();
}

// Function to handle checkout
function checkout() {
    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }

    // Prepare the cart data to send to the server
    const cartData = cart.map(item => ({
        productId: item.id,
        quantity: item.quantity
    }));

    // Send the cart data to the server to update stock
    fetch('update_stock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(cartData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the cart after successful checkout
            cart = [];
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();

            // Redirect to order_details.php
            window.location.href = 'order_details.php';
        } else {
            alert(data.message || 'Failed to update stock. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Initialize cart display on page load
updateCartDisplay();

        // Initialize cart count on page load
        updateCartCount();
    </script>
</body>
</html>