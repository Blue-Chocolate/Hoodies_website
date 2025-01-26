<?php
try {
    $dsn = 'mysql:host=localhost;dbname=hoodiestore;charset=utf8mb4';
    $username = 'root'; // Replace with your database username
    $password = ''; // Replace with your database password
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>