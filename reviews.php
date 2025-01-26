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

// Fetch reviews from the database
$query = "SELECT reviews.*, users.name AS user_name FROM reviews JOIN users ON reviews.user_id = users.user_id";
$stmt = $pdo->query($query);
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/home.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
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

    <!-- Reviews Section -->
    <div class="container my-5">
        <h1 class="text-center mb-4">Customer Reviews</h1>
        <div class="row">
            <?php foreach ($reviews as $review): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($review['user_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Rating: <?php echo htmlspecialchars($review['rating']); ?>/5
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #ea580c;">
        <p style="background-color:  #ea580c;">&copy; 2025 E-commerce Website. All rights reserved.</p>
    </footer>
</body>
</html>