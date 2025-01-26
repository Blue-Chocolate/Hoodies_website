<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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

    <!-- Contact Section -->
    <div class="container my-5">
        <h1 class="text-center mb-4">Contact Us</h1>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form action="submit_contact.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Your Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #ea580c;">
        <p style="background-color:  #ea580c;">&copy; 2025 E-commerce Website. All rights reserved.</p>
    </footer>
</body>
</html> 