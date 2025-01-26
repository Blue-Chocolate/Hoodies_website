<?php
session_start(); // Start the session

// Redirect to login if not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

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

// Fetch categories for the dropdown
try {
    $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add product
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id']; // Dynamically selected category
        $created_by = $_SESSION['user_id']; // Admin ID

        // Default image
        $defaultImage = "/uploads/default_image.png"; // Web-accessible path to the default image
        $imagePath = $defaultImage; // Initialize imagePath with the default image

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/'; // Absolute path to the uploads directory

            // Check if the upload directory exists, and if not, create it
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Validate file type and size
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $maxFileSize = 5 * 1024 * 1024; // 5 MB
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];

            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                $fileName = uniqid() . "_" . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;

                // Attempt to move the uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $imagePath = "/uploads/" . $fileName; // Web-accessible path
                } else {
                    die("Error uploading the file. Please try again.");
                }
            } else {
                die("Invalid file type or size. Only JPEG, PNG, and GIF up to 5 MB are allowed.");
            }
        }

        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, image_path, price, stock, category_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $imagePath, $price, $stock, $category_id, $created_by]);
            echo "<p>Product added successfully!</p>";
        } catch (PDOException $e) {
            die("Error adding product: " . $e->getMessage());
        }
    } elseif (isset($_POST['delete_product'])) {
        // Delete product
        $product_id = $_POST['product_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            echo "<p>Product deleted successfully!</p>";
        } catch (PDOException $e) {
            die("Error deleting product: " . $e->getMessage());
        }
    } elseif (isset($_POST['add_user'])) {
        // Add user
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
        $user_type = $_POST['user_type']; // 'customer' or 'admin'

        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $user_type]);
            echo "<p>User added successfully!</p>";
        } catch (PDOException $e) {
            die("Error adding user: " . $e->getMessage());
        }
    } elseif (isset($_POST['remove_user'])) {
        // Remove user
        $user_id = $_POST['user_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            echo "<p>User removed successfully!</p>";
        } catch (PDOException $e) {
            die("Error removing user: " . $e->getMessage());
        }
    } elseif (isset($_POST['cancel_order'])) {
        // Cancel order
        $order_id = $_POST['order_id'];
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
            $stmt->execute([$order_id]);
            echo "<p>Order cancelled successfully!</p>";
        } catch (PDOException $e) {
            die("Error cancelling order: " . $e->getMessage());
        }
    } elseif (isset($_POST['update_product'])) {
        // Update product
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];

        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE product_id = ?");
            $stmt->execute([$name, $description, $price, $stock, $category_id, $product_id]);
            echo "<p>Product updated successfully!</p>";
        } catch (PDOException $e) {
            die("Error updating product: " . $e->getMessage());
        }
    }
}

// Fetch products
try {
    $query = "SELECT * FROM products";
    $stmt = $pdo->query($query);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}

// Fetch users
try {
    $query = "SELECT * FROM users";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Fetch orders
try {
    $query = "SELECT orders.order_id, users.name AS user_name, orders.order_date, orders.status, orders.total_price 
              FROM orders 
              JOIN users ON orders.user_id = users.user_id";
    $stmt = $pdo->query($query);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/home.css">
</head>
<body>
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
    <div class="container">
        <h1>Admin Dashboard</h1>

        <!-- Add User Form -->
        <h2>Add User</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type</label>
                <select name="user_type" class="form-control" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </form>

        <!-- Remove User Form -->
        <h2 class="mt-5">Remove User</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="user_id" class="form-label">Select User to Remove</label>
                <select name="user_id" class="form-control" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="remove_user" class="btn btn-danger">Remove User</button>
        </form>

        <!-- Add Product Form -->
        <h2 class="mt-5">Add Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>

        <!-- Update Product Form -->
        <h2 class="mt-5">Update Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="product_id" class="form-label">Select Product to Update</label>
                <select name="product_id" class="form-control" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?> ($<?php echo number_format($product['price'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
        </form>

        <!-- Delete Product Form -->
        <h2 class="mt-5">Delete Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="product_id" class="form-label">Select Product to Delete</label>
                <select name="product_id" class="form-control" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['product_id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?> ($<?php echo number_format($product['price'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="delete_product" class="btn btn-danger">Delete Product</button>
        </form>

        <!-- Cancel Order Form -->
       <h2 class="mt-5">Cancel Order</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="order_id" class="form-label">Select Order to Cancel</label>
                <select name="order_id" class="form-control" required>
                    <?php foreach ($orders as $order): ?>
                        <option value="<?php echo $order['order_id']; ?>">
                            Order #<?php echo htmlspecialchars($order['order_id']); ?> by <?php echo htmlspecialchars($order['user_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="cancel_order" class="btn btn-danger">Cancel Order</button>
        </form>

        <!-- View Users -->
        <h2 class="mt-5">Users</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- View Orders -->
        <h2 class="mt-5">Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- View Products -->
        <h2 class="mt-5">Existing Products</h2>
        <ul class="list-group">
            <?php foreach ($products as $product): ?>
                <li class="list-group-item">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="width: 50px; height: auto;">
                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                    <p>Stock: <?php echo htmlspecialchars($product['stock']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>