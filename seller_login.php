<?php

session_start();
require __DIR__ . '/db_connect.php';

$error = '';
$username_or_email = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_or_email) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        $sql = "SELECT sellerID, name, password FROM sellers WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username_or_email, $username_or_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {

            if ($password === $row['password']) {

                $_SESSION['seller_logged_in'] = true;
                $_SESSION['sellerID'] = $row['sellerID'];
                $_SESSION['seller_name'] = $row['name'];
                
                header("Location: add_product.php");
                exit();
                
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No account found with that username or email.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - MY Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="theme.css">
</head>
<body>

    <div class="homepage_top_header">
        <div class="homepage_title_area">
            <h1>MY Store</h1>
            <p class="homepage_title_sub">A simple and modern place to explore electronic products.</p>
        </div>
        <div class="homepage_header_icon">
            <a href="homepage.php">
                <img src="home.png" alt="Home"> 
            </a>
        </div>
    </div>

    <div class="homepage_nav_bar">
        <a href="purchaser_login.php">Buyer Login</a>
        <a href="search.php">Search</a>
        <a href="add_product.php">Product Registration</a>
        <a href="seller_login.php" class="is-active">Seller Login</a>
        <a href="bag.php">Bag</a>
    </div>

    <div class="auth-wrapper">
        <div class="auth-container" style="max-width: 400px;">
            <h2>Seller Login</h2>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="notice error">
                    <?php
                        echo htmlspecialchars($_SESSION['error_msg'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['error_msg']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="notice success">
                    <?php
                        echo htmlspecialchars($_SESSION['success_msg'], ENT_QUOTES, 'UTF-8');
                        unset($_SESSION['success_msg']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="seller_login.php" method="POST">
                <div class="form-group full-width">
                    <label>Username or Email</label>
                    <input type="text" name="username_or_email" value="<?php echo htmlspecialchars($username_or_email); ?>" required placeholder="Enter your username or email">
                </div>
                
                <div class="form-group full-width" style="margin-top: 15px;">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 25px;">Login</button>
            </form>
            
            <div class="auth-links" style="margin-top: 20px;">
                Don't have an account? <a href="seller_register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
