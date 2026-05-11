<?php
include "db_connect.php";
session_start();

$error_message = "";


$is_logged_in = isset($_SESSION['buyer_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$is_logged_in) {
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM buyers WHERE email = '$email'";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if ($password === $user['password']) {
                $_SESSION['buyer_id'] = $user['buyerID'];
                $_SESSION['buyer_name'] = $user['name'];
                header("Location: purchaser_login.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Account does not exist. Please register first.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - MY Store</title>
    <link rel="stylesheet" href="login&search.css">
    <link rel="stylesheet" href="theme.css">
</head>
<body class="login-body">
    <header class="top-header">
        <div class="title-area">
            <h1>MY Store</h1>
            <p class="title-sub">A simple and modern place to explore products.</p>
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

    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="notice success">
            <?php
                echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['success_msg']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_msg'])): ?>
        <div class="notice error">
            <?php
                echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['error_msg']);
            ?>
        </div>
    <?php endif; ?>

    <main class="login-container">
        <div class="login-card">
            <?php if ($is_logged_in): ?>
                <div class="login-header">
                    <h1>Welcome back!</h1>
                    <p>You are signed in as <strong><?php echo htmlspecialchars($_SESSION['buyer_name']); ?></strong></p>
                </div>
                <div class="login-actions" style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
                    <a href="search.php" class="confirm-btn login-btn" style="text-decoration: none; text-align: center;">Go Shopping</a>
                    <a href="logout.php" class="confirm-btn login-btn" style="text-decoration: none; text-align: center; background-color: #86868b;">Sign Out</a>
                </div>
            <?php else: ?>
                <div class="login-header">
                    <h1>Sign in</h1>
                    <p>Enter your details to access your account.</p>
                    <?php if($error_message): ?>
                        <p style="color: #e30000;"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                </div>

                <form method="POST" action="">
                    <div class="input-group">
                        <div class="field-wrapper">
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="field-wrapper">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="login-actions">
                        <button type="submit" class="confirm-btn login-btn">Sign in</button>
                    </div>
                </form>
                <div class="login-footer">
                    <p>Don't have an account? <a href="purchaser_register.php">Register here</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
 
