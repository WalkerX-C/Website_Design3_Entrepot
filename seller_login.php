<?php
session_start();
require __DIR__ . '/db_connect.php';

$error = '';
$email_input = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $email_input = $email;

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email format.";
    } else {
        $sql = "SELECT sellerID, username, password FROM sellers WHERE email = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $seller = mysqli_fetch_assoc($result);
            if (password_verify($password, $seller['password'])) {
                $_SESSION['sellerID'] = $seller['sellerID'];
                $_SESSION['username'] = $seller['username'];
                $_SESSION['role'] = 'seller';
                header("Location: homepage.php");
                exit;
            } else {
                $error = "Invalid email or password."; 
            }
        } else {
            $error = "Invalid email or password."; 
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
</head>
<body>
    <header class="homepage_top_header">
        <div class="homepage_title_area">
            <h1>MY Store</h1>
            <p class="homepage_title_sub">A simple and modern place to explore electronic products.</p>
        </div>
        <div class="homepage_header_icon">
            <a href="homepage.php">
                <img src="home-icon.png" alt="Home">
            </a>
        </div>
    </header>

    <nav class="homepage_nav_bar">
        <a href="buyer_login.php">Buyer Login</a>
        <a href="search.php">Search</a>
        <a href="product_register.php">Product Registration</a>
        <a href="seller_login.php" style="color: #0071e3;">Seller Login</a>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-container">
            <h2>Seller Login</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="seller_login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email_input); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Login</button>
            </form>
            
            <div class="auth-links">
                Don't have an account? <a href="seller_register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
