


<?php

session_start();
include 'db.php';

// Security Gate: If the user is not logged in OR is not a supplier, boot them to login page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    header("Location: login.php?role=supplier");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$style_class = "";

// --- 1. ACTION: UPDATE SUPPLIER PROFILE ---
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_sql = "UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id=$user_id";
    if ($conn->query($update_sql)) {
        $_SESSION['user_name'] = $name; // Instantly update greeting name
        $message = "Your profile details have been updated!";
        $style_class = "success-msg";
    } else {
        $message = "Failed to update profile: " . $conn->error;
        $style_class = "error-msg";
    }
}

// --- 2. ACTION: DELETE SUPPLIER ACCOUNT ---
if (isset($_POST['delete_account'])) {
    // Because your database schema uses 'ON DELETE CASCADE', deleting this user 
    // will automatically delete all products uploaded by this supplier too!
    $delete_sql = "DELETE FROM users WHERE id=$user_id";
    if ($conn->query($delete_sql)) {
        session_destroy(); // Destroy login session tokens
        header("Location: index.php"); // Send them back to landing page
        exit();
    }
}

// --- 3. ACTION: ADD NEW PRODUCT TO MARKET ---
if (isset($_POST['add_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $product_sql = "INSERT INTO products (supplier_id, product_name, price, quantity, description) 
                    VALUES ($user_id, '$p_name', $price, $quantity, '$desc')";
    if ($conn->query($product_sql)) {
        $message = "Product successfully added to the catalog!";
        $style_class = "success-msg";
    } else {
        $message = "Failed to add product: " . $conn->error;
        $style_class = "error-msg";
    }
}

// --- 4. DATA FETCH: GET CURRENT SUPPLIER INFO ---
$user_query = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user_data = $user_query->fetch_assoc();

// --- 5. DATA FETCH: GET PRODUCTS ADDED BY THIS SUPPLIER ---
$my_products_query = $conn->query("SELECT * FROM products WHERE supplier_id=$user_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Specific layouts using your existing responsive theme elements */
        .dashboard-layout {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .panel {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .panel h3 { margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #f4f7f6; padding-bottom: 5px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2c3e50; color: #fff; }
        .btn-danger { background: #e74c3c; width: 100%; margin-top: 10px;}
        .btn-logout { background: #7f8c8d; margin-top: 0; padding: 5px 15px; font-size: 0.9rem;}
        .top-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="container">
        <header style="border-top: 5px solid #2ecc71;">
            <h1>Supplier Workspace Dashboard</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($user_data['name']); ?></strong>!</p>
        </header>

        <div class="top-bar">
            <span>Logged in as Management Supplier</span>
            <div>
                <a href="stock.php" class="btn" style="margin-top:0; padding: 5px 15px; font-size: 0.9rem; background: #7f8c8d;"> Global Stock Page</a>
                <a href="logout.php" class="btn btn-logout">Log Out</a>
            </div>
        </div>

        <?php if(!empty($message)): ?>
            <div class="<?php echo $style_class; ?>" style="margin-top: 20px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="dashboard-layout">
            
            <div class="panel">
                <h3>My Profile Details</h3>
                <form action="supplier_dashboard.php" method="POST">
                    <div class="form-group">
                        <label>Business Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Operational Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-supplier" style="width:100%;">Update Details</button>
                </form>

                <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">

                <form action="supplier_dashboard.php" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to completely delete your account? All your uploaded products will be removed permanently.');">
                    <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account Permanently</button>
                </form>
            </div>

            <div class="panel">
                <h3>Add New Product Listing</h3>
                <form action="supplier_dashboard.php" method="POST">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control" placeholder="e.g. Wireless Mouse" required>
                    </div>
                    <div class="form-group">
                        <label>Price (Per Unit)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Initial Stock Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="e.g. 50" required>
                    </div>
                    <div class="form-group">
                        <label>Item Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe key item specs..."></textarea>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-supplier" style="width:100%;">Upload & Live Track</button>
                </form>
            </div>

        </div>

        <div class="panel" style="margin-top: 20px;">
            <h3>My Currently Listed Products</h3>
            <?php if ($my_products_query->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Stock Left</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $my_products_query->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                                    <td><?php echo $row['quantity']; ?> pcs</td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #777; margin-top: 10px;">You haven't added any products to the market system yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>