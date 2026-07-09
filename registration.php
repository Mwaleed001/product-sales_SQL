<?php
include 'db.php';

$message = "";
$style_class = "";

$get_role = isset($_GET['role']) ? $_GET['role'] : 'customer';

if (isset($_POST['submit_register'])) {

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);


    // Securely hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email is already registered
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);



    if ($result->num_rows > 0) {
        $message = "This email is already registered!";
        $style_class = "error-msg";
    } 

    else {

        
        $sql = "INSERT INTO users (name, email, password, role, phone, address) 
                VALUES ('$name', '$email', '$hashed_password', '$role', '$phone', '$address')";

        if ($conn->query($sql) === TRUE) {
            $message = "Account created successfully! <a href='login.php?role=$role'>Login here</a>";
            $style_class = "success-msg";
        } 
        else {
            $message = "Error: " . $conn->error;
            $style_class = "error-msg";
        }
    }
}

// Automatically detect desired role from URL if present
$get_role = isset($_GET['role']) ? $_GET['role'] : 'customer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Form specific styling mapped cleanly into your layout theme */
        .form-card {
            background: #fff;
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-top: 5px solid <?php echo ($get_role == 'supplier') ? '#2ecc71' : '#3498db'; ?>;
        }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;}
        .error-msg { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;}
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Create Your Account</h1>
            <p>Fill out the fields below for purchasing products.</p>
        </header>

        <div class="form-card">
            <?php if(!empty($message)): ?>
                <div class="<?php echo $style_class; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="registration.php" method="POST">
                
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($get_role); ?>">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="register-password" name="password" class="form-control" placeholder="Create safe password" required>
                    <button type="button" class="btn" style="width:100%; margin-top:10px; background:#555;" onclick="togglePasswordVisibility('register-password')">
                        Show Password
                    </button>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. +923001234567">
                </div>

                <div class="form-group">
                    <label>Complete Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter city, street address"></textarea>
                </div>

                <button type="submit" name="submit_register" class="btn <?php echo ($get_role == 'supplier') ? 'btn-supplier' : ''; ?>" style="width:100%;">
                    Register as <?php echo ucfirst($get_role); ?>
                </button>
            </form>

            <p style="margin-top: 20px; font-size: 0.9rem; text-align: center;">
                Already have an account? <a href="login.php?role=<?php echo $get_role; ?>" style="color: #3498db; text-decoration: none; font-weight: bold;">Login Here</a>
            </p>
        </div>
    </div>

    <script src="login.js"></script>

</body>
</html>