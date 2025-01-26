<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/home.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
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

    <!-- About Section -->
    <div class="container my-5">
        <h1 class="text-center mb-4">About Us</h1>
        <div class="row">
            <div class="col-md-6">
                <img src="./assets/images/hoodies/3B2564AF-F214-41DC-AA37-C96B92833B81.jpeg" alt="About Us" class="img-fluid rounded">
            </div>
            <div class="col-md-6">
                <h2>Our Story</h2>
                <p>
                    Welcome to <strong>Premium Hoodies</strong>, where comfort meets style. We started our journey in 2025 with a simple mission: to provide high-quality hoodies that are both fashionable and comfortable. Our team is passionate about creating products that you'll love to wear every day.
                </p>
                <h2>Our Mission</h2>
                <p>
                    Our mission is to deliver premium-quality hoodies that combine style, comfort, and durability. We believe that everyone deserves to feel confident and cozy in their clothing, and we're here to make that happen.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #ea580c;">
        <p style="background-color:  #ea580c;">&copy; 2025 E-commerce Website. All rights reserved.</p>
    </footer>
</body>
</html>