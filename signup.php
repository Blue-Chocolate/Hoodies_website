<?php
// Database connection settings
$host = 'localhost';  // Your database host
$username = 'root';   // Your database username
$password = '';       // Your database password
$dbname = 'HoodieStore'; // Your database name

// Establish a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize a message variable
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['Phone_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate input
    if (empty($name) || empty($phone) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $sql = "INSERT INTO Users (name, phone, email, password, user_type) VALUES ('$name', '$phone', '$email', '$hashedPassword', 'customer')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to home.php
            header("Location: home.php");
            exit(); // Ensure the script stops executing after redirection
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="assets/signup.css">
</head>
<style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #FF4500, #1E90FF);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .signup-container {
            background-color: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            color: #FF4500;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            padding: 10px;
            border: 1px solid #1E90FF;
            border-radius: 8px;
            outline: none;
        }
        .form-control:focus {
            border-color: #FF4500;
            box-shadow: 0 0 5px #FF4500;
        }
        .btn-primary {
            background-color: #1E90FF;
            border: none;
            border-radius: 8px;
            padding: 10px;
            width: 100%;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #FF4500;
        }
        p {
            text-align: center;
            margin-top: 15px;
        }
        a {
            color: #1E90FF;
            text-decoration: none;
        }
        a:hover {
            color: #FF4500;
        }
        @media (max-width: 576px) {
            .signup-container {
                padding: 20px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"> <?php echo $message; ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="Phone_number">Phone Number</label>
                <input type="text" name="Phone_number" class="form-control" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>