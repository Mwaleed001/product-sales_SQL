<?php
// Start session tracking architecture
session_start();
include 'db.php';

// Security Gate: Redirect users back to login if they are unauthorized or not customers
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php?role=customer");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$style_class = "";

// --- 1. ACTION: UPDATE CUSTOMER PROFILE ---
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_sql = "UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id=$user_id";
    if ($conn->query($update_sql)) {
        $_SESSION['user_name'] = $name; // Instantly update active workspace greeting name
        $message = "Your profile settings have been updated successfully!";
        $style_class = "success-msg";
    } else {
        $message = "Error updating profile details: " . $conn->error;
        $style_class = "error-msg";
    }
}

// --- 2. ACTION: DELETE CUSTOMER ACCOUNT ---
if (isset($_POST['delete_account'])) {
    // Cascades instantly to clean up transaction history links tied to this user ID
    $delete_sql = "DELETE FROM users WHERE id=$user_id";
    if ($conn->query($delete_sql)) {
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

// --- 3. ACTION: BUY PRODUCT & UPDATE STOCK ---
if (isset($_POST['purchase_product'])) {
    $product_id = intval($_POST['product_id']);
    $quantity_bought = intval($_POST['quantity_bought']);

    // Check available item inventory levels first
    $check_product = $conn->query("SELECT * FROM products WHERE id = $product_id");
    if ($check_product->num_rows > 0) {
        $product = $check_product->fetch_assoc();
        $current_stock = $product['quantity'];

        if ($quantity_bought > $current_stock) {
            $message = "Error: Not enough stock available to fulfill your request!";
            $style_class = "error-msg";
        } elseif ($quantity_bought <= 0) {
            $message = "Error: Please select a valid item purchase count.";
            $style_class = "error-msg";
        } else {
            // Deduct units from current warehouse stock availability balance
            $new_stock = $current_stock - $quantity_bought;
            $update_stock_sql = "UPDATE products SET quantity = $new_stock WHERE id = $product_id";
            
            // Log entry data mapping into central Sales Tracker table
            $record_sale_sql = "INSERT INTO sales (product_id, customer_id, quantity_bought) 
                                VALUES ($product_id, $user_id, $quantity_bought)";

            // Execute operations cleanly via atomic logical sequences
            if ($conn->query($update_stock_sql) && $conn->query($record_sale_sql)) {
                $message = "Thank you! Purchase transaction completed successfully.";
                $style_class = "success-msg";
            } else {
                $message = "Transaction processing failed: " . $conn->error;
                $style_class = "error-msg";
            }
        }
    }
}

// --- 4. DATA FETCH: FETCH CURRENT CUSTOMER INFO ---
$user_query = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user_data = $user_query->fetch_assoc();

// --- 5. DATA FETCH: FETCH AVAILABLE PRODUCTS IN MARKETPLACE ---
$products_query = $conn->query("SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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
        
        /* Interactive Shopping Card Styles */
        .product-showcase {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .item-card {
            border: 1px solid #e1e8ed;
            border-radius: 6px;
            padding: 15px;
            background: #fafbfc;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .price-text { font-size: 1.3rem; color: #2ecc71; font-weight: bold; margin: 5px 0; }
        .stock-count { font-size: 0.85rem; color: #7f8c8d; margin-bottom: 10px; }
        
        .btn-danger { background: #e74c3c; width: 100%; margin-top: 10px;}
        .btn-logout { background: #7f8c8d; margin-top: 0; padding: 5px 15px; font-size: 0.9rem;}
        .top-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="container">
        <header style="border-top: 5px solid #3498db;">
            <h1>Customer Shopping Dashboard</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($user_data['name']); ?></strong>!</p>
        </header>

        <div class="top-bar">
            <span>Marketplace Portal View</span>
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
                <form action="customer_dashboard.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn" style="width:100%;">Save Changes</button>
                </form>

                <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">

                <form action="customer_dashboard.php" method="POST" onsubmit="return confirm('Are you completely sure you want to delete your customer account? This decision cannot be undone.');">
                    <button type="submit" name="delete_account" class="btn btn-danger">Delete Account Permanently</button>
                </form>
            </div>

            <div class="panel" style="grid-column: span 2;">
                <h3>Available Products for Sale</h3>
                
                <?php if ($products_query->num_rows > 0): ?>
                    <div class="product-showcase">
                        <?php while($prod = $products_query->fetch_assoc()): ?>
                            <div class="item-card">
                                <div>
                                    <h4 style="color: #2c3e50; font-size: 1.1rem;"><?php echo htmlspecialchars($prod['product_name']); ?></h4>
                                    <p style="font-size: 0.85rem; color: #555; margin-top: 5px;"><?php echo htmlspecialchars($prod['description']); ?></p>
                                </div>
                                
                                <div style="margin-top: 15px;">
                                    <div class="price-text">$<?php echo number_format($prod['price'], 2); ?></div>
                                    <div class="stock-count">In Stock: <strong><?php echo $prod['quantity']; ?></strong> units left</div>
                                    
                                    <form action="customer_dashboard.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                        <div style="display: flex; gap: 5px;">
                                            <input type="number" name="quantity_bought" value="1" min="1" max="<?php echo $prod['quantity']; ?>" class="form-control" style="width: 75px; padding: 5px;" required>
                                            <button type="submit" name="purchase_product" class="btn" style="margin-top:0; padding: 5px 10px; font-size: 0.9rem; flex-grow: 1;">
                                                Buy Item
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #777; margin-top: 15px;">There are no items currently available for purchase in the marketplace database.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>