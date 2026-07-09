<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Stock Management Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Sales & Stock Management Portal</span>
                <h1>Manage customers, suppliers, and inventory in one place</h1>
                <p> dashboard experience designed for faster updates, better control, and smoother workflows.</p>
                <div class="hero-actions">
                    <a href="login.php?role=customer" class="btn">Customer Login</a>
                    <a href="login.php?role=supplier" class="btn btn-supplier">Supplier Login</a>
                </div>
            </div>
            <div class="hero-panel">
                <div class="hero-stat">
                    <strong>24/7</strong>
                    <span>Access</span>
                </div>
                <div class="hero-stat">
                    <strong>Live</strong>
                    <span>Inventory View</span>
                </div>
                <div class="hero-stat">
                    <strong>Fast</strong>
                    <span>Order Tracking</span>
                </div>
            </div>
        </header>

        <section class="highlights">
            <div class="highlight-card">
              
        </section>

        <main class="portal-selection">
            <div class="card customer">
                <h2>Customer Portal</h2>
                <p>Browse available products, manage your personal profile, and view your purchase history seamlessly.</p>
                <a href="login.php?role=customer" class="btn">Enter Customer Portal</a>
            </div>

            <div class="card supplier">
                <h2>Supplier Portal</h2>
                <p>Add new products to the market, monitor stock levels, manage inventory values, and update company details.</p>
                <a href="login.php?role=supplier" class="btn btn-supplier">Enter Supplier Portal</a>
            </div>
        </main>
    </div>

</body>
</html>