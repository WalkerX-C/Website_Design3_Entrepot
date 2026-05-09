<?php
session_start();
require __DIR__ . '/db_connect.php';

$error = '';
$email_input = ''; // 用于表单回显

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $email_input = $email;

    // 1. 登录前置验证：避免无效的数据库查询
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email format.";
    } else {
        // 2. 查询对应邮箱的卖家记录
        $sql = "SELECT sellerID, username, password FROM sellers WHERE email = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            $seller = mysqli_fetch_assoc($result);

            if (password_verify($password, $seller['password'])) {
                // 登录成功
                $_SESSION['sellerID'] = $seller['sellerID'];
                $_SESSION['username'] = $seller['username'];
                $_SESSION['role'] = 'seller';
                
                header("Location: homepage.php");
                exit;
            } else {
                $error = "Invalid email or password."; // 密码错误
            }
        } else {
            $error = "Invalid email or password."; // 邮箱不存在
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
    <?php include 'header.php'; ?>

    <div class="auth-container" style="max-width: 400px;"> <!-- 登录框窄一点更好看 -->
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
</body>
</html>
