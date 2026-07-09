<?php

session_start();
include 'db.php';

$message = "";
$style_class = "";

$get_role = isset($_GET['role']) ? $_GET['role'] : 'customer';

if (isset($_POST['submit_login'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'supplier') {
                header("Location: supplier_dashboard.php");
            } else {
                header("Location: customer_dashboard.php");
            }
            exit(); 
            
        } else {
            $message = "Incorrect password! Please try again.";
            $style_class = "error-msg";
        }
    } else {
        $message = "No account found with that email for this portal.";
        $style_class = "error-msg";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Access</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-card {
            background: #fff;
            max-width: 420px;
            margin: 60px auto;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-top: 5px solid <?php echo ($get_role == 'supplier') ? '#2ecc71' : '#3498db'; ?>;
        }
        .form-group { margin-bottom: 20px; text-align: left; }


        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }

        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem;
     }
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 0.95rem; }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>
                <?php echo ucfirst($get_role); ?> 
                Login Portal</h1>
            <p>Please log in with your account credentials to continue.</p>
            
        </header>

        <div class="login-card">
            <?php if(!empty($message)): ?>
                <div class="<?php echo $style_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="login.php?role=<?php echo $get_role; ?>" method="POST" onsubmit="return trimLoginFields()">
                
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($get_role); ?>">

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="enter your email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="login-password" name="password" class="form-control" placeholder="enter your password" required>
                    <button type="button" class="btn" style="width:100%; margin-top:10px; background:#555;" onclick="togglePasswordVisibility('login-password')">
                        Show Password
                    </button>
                </div>

                <button type="submit" name="submit_login" class="btn <?php echo ($get_role == 'supplier') ? 'btn-supplier' : ''; ?>" style="width:100%;">
                    Log In
                </button>
            </form>

            <p style="margin-top: 25px; font-size: 0.9rem; text-align: center;">
                Don't have an account? <a href="registration.php?role=<?php echo $get_role; ?>" style="color: #3498db; text-decoration: none; font-weight: bold;">Register here</a>
            </p>
            <p style="margin-top: 10px; font-size: 0.85rem; text-align: center;">
                <a href="index.php" style="color: #777; text-decoration: none;"> Change Portal Type</a>
            </p>
        </div>
    </div>

    <script src="login.js"></script>

</body>
</html>