<?php
// Start the session to track logged-in users
session_start();
include 'db.php';

// Security Gate: Redirect users back to login if they aren't logged in at all
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. DATA FETCH: FETCH ALL PRODUCTS AND DETAILED SUPPLIER INFORMATION
$stock_sql = "SELECT p.*, u.name AS supplier_name 
              FROM products p 
              JOIN users u ON p.supplier_id = u.id 
              ORDER BY p.id DESC";
$stock_results = $conn->query($stock_sql);

// 2. DATA FETCH: FETCH COMPREHENSIVE SALES LOG HISTORY DATA
$sales_sql = "SELECT s.id, s.quantity_bought, s.sale_date, p.product_name, p.price, u.name AS customer_name 
              FROM sales s
              JOIN products p ON s.product_id = p.id
              JOIN users u ON s.customer_id = u.id
              ORDER BY s.id DESC";
$sales_results = $conn->query($sales_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stock & Sales Tracking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .report-panel {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .report-panel h3 { 
            margin-bottom: 15px; 
            color: #2c3e50; 
            border-bottom: 2px solid #34495e; 
            padding-bottom: 5px; 
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #34495e; color: #fff; }
        tr:hover { background-color: #f8f9fa; }
        
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; }
        .in-stock { background: #d4edda; color: #155724; }
        .out-of-stock { background: #f8d7da; color: #721c24; }
        
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #fff; 
            padding: 15px 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <header style="background: #34495e;">
            
            <p>Real-time analytics and transaction verification panels.</p>
        </header>

        <div class="top-bar">
            <span>Viewing Live Server Registry Metrics</span>
            <a href="<?php echo ($_SESSION['user_role'] === 'supplier') ? 'supplier_dashboard.php' : 'customer_dashboard.php'; ?>" class="btn" style="margin-top: 0;">
                ← Back to Dashboard
            </a>
        </div>

        <div class="report-panel">
            <h3>Warehouse Stock Availability</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Supplier Partner</th>
                            <th>Unit Price</th>
                            <th>Quantity Left</th>
                            <th>Inventory Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stock_results->num_rows > 0): ?>
                            <?php while($item = $stock_results->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $item['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['supplier_name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?> units</td>
                                    <td>
                                        <?php if ($item['quantity'] > 0): ?>
                                            <span class="status-badge in-stock">In Stock</span>
                                        <?php else: ?>
                                            <span class="status-badge out-of-stock">Sold Out</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No product assets exist in the system.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-panel">
            <h3>Completed Market Transactions Log</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Product Name</th>
                            <th>Customer Name</th>
                            <th>Quantity Sold</th>
                            <th>Total Revenue</th>
                            <th>Transaction Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($sales_results->num_rows > 0): ?>
                            <?php while($sale = $sales_results->fetch_assoc()): ?>
                                <?php 
                                    // Math formula computation block for total order revenue pricing
                                    $total_revenue = $sale['price'] * $sale['quantity_bought']; 
                                ?>
                                <tr>
                                    <td>#<?php echo $sale['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($sale['product_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                    <td><?php echo $sale['quantity_bought']; ?> pcs</td>
                                    <td style="color: #2ecc71; font-weight: bold;">$<?php echo number_format($total_revenue, 2); ?></td>
                                    <td><small><?php echo $sale['sale_date']; ?></small></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No transaction records recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>