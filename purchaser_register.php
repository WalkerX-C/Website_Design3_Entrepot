<?php
include "db_connect.php";
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    
    if (!empty($name) && !empty($email) && !empty($password)) {
        
        $check_sql = "SELECT * FROM buyers WHERE email = '$email'";
        $check_result = mysqli_query($connection, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $message = "This email is already registered.";
        } else {
            $register_time = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO buyers (name, address, phone, email, username, password, registerTime) 
                    VALUES ('$name', 'Not Provided', '$phone', '$email', '$email', '$password', '$register_time')";
            
            if (mysqli_query($connection, $sql)) {
                $_SESSION['success_msg'] = "Registration successful. Please login.";
                header("Location: purchaser_login.php");
                exit();
            } else {
                $message = "Error: " . mysqli_error($connection);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - MY Store</title>
    <link rel="stylesheet" href="login&search.css">
    <link rel="stylesheet" href="theme.css">
</head>
<body class="login-body">
    <header class="top-header">
        <div class="title-area">
            <h1>MY Store</h1>
            <p class="title-sub">Join us to start your journey.</p>
        </div>
        <div class="header-icon"><a href="homepage.php"><img src="home.png" alt="Home"></a></div>
    </header>

    <nav class="nav-bar">
        <a href="purchaser_login.php">Buyer Login</a>
        <a href="search.php">Search</a>
        <a href="add_product.php">Product Registration</a>
        <a href="seller_login.php">Seller Login</a>
        <a href="bag.php">Bag</a>
    </nav>

    <main class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Create Account</h1>
                <p>Fill in the details below to register.</p>
                <?php if($message): ?>
                    <p style="color: #e30000;"><?php echo $message; ?></p>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <div class="input-group">
                    <div class="field-wrapper">
                        <input type="text" name="name" placeholder="Full Name" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="field-wrapper">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="field-wrapper">
                        <input type="text" name="phone" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="field-wrapper">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="login-actions">
                    <button type="submit" class="confirm-btn login-btn">Register</button>
                </div>
            </form>
            <div class="login-footer">
                <p>Already have an account? <a href="purchaser_login.php">Login here</a></p>
            </div>
        </div>
    </main>
</body>
</html>
