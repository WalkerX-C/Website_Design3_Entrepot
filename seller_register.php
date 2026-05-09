<?php
session_start();
require __DIR__ . '/db_connect.php';

$error = '';
$success = '';

// 预定义变量，用于表单回显（用户填错时不需要重新输入全部内容）
$name = $address = $phone = $email = $username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name can only contain letters and spaces.";
    } elseif (empty($address)) {
        $error = "Business address cannot be empty.";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        // 手机号正则：10到15位纯数字
        $error = "Phone number must be between 10 to 15 digits.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // 邮箱验证：使用PHP内置且最安全的过滤器
        $error = "Invalid email format.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{4,20}$/", $username)) {
        // 用户名正则：4-20位，只能包含大小写字母、数字和下划线
        $error = "Username must be 4-20 characters (letters, numbers, underscores).";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/", $password)) {
        // 密码正则：至少8位，必须包含至少一个字母和一个数字
        $error = "Password must be at least 8 characters, including 1 letter and 1 number.";
    } else {
        $check_sql = "SELECT sellerID FROM sellers WHERE email = ? OR username = ?";
        $check_stmt = mysqli_prepare($connection, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $email, $username);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Registration failed: Email or Username is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_sql = "INSERT INTO sellers (name, address, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $insert_sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $address, $phone, $email, $username, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Account created successfully! You can now login.";
                $name = $address = $phone = $email = $username = '';
            } else {
                $error = "Database Error: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Registration - MY Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="auth-container">
        <h2>Seller Registration</h2>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div style="color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center;">
                <?php echo $success; ?> <a href="seller_login.php">Go to Login</a>
            </div>
        <?php endif; ?>

        <form action="seller_register.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Business Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Min 8 chars, 1 letter, 1 number">
                </div>
            </div>
            <button type="submit" class="submit-btn">Register Now</button>
        </form>
        <div class="auth-links">
            Already have an account? <a href="seller_login.php">Login here</a>
        </div>
    </div>
</body>
</html>
