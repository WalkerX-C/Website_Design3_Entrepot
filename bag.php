<?php
include "db_connect.php";
session_start();

function bag_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

if (!isset($_SESSION['buyer_id'])) {
    $_SESSION['error_msg'] = "Please login first.";
    header("Location: purchaser_login.php");
    exit();
}

$buyer_id = (int)$_SESSION['buyer_id'];


$sql = "SELECT cart.*, products.productName, products.price, products.image 
        FROM cart 
        JOIN products ON cart.productID = products.productID 
        WHERE cart.buyerID = ? AND cart.cartStatus = 'In Cart'
        ORDER BY cart.addedTime DESC, cart.cartID DESC";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $buyer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login&search.css">
    <link rel="stylesheet" href="theme.css">
    <title>Your Bag</title>
</head>
<body>
    <header class="top-header">
        <div class="title-area">
            <h1>MY Store</h1>
            <p class="title-sub">Review your car selection.</p>
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
                echo bag_h($_SESSION['success_msg']);
                unset($_SESSION['success_msg']);
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_msg'])): ?>
        <div class="notice error">
            <?php
                echo bag_h($_SESSION['error_msg']);
                unset($_SESSION['error_msg']);
            ?>
        </div>
    <?php endif; ?>

    <main class="bag-container">
        <h1>Review your bag.</h1>
        
        <div id="bag-items-container">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($item = mysqli_fetch_assoc($result)): 
                    $total_price += $item['price'] * $item['quantity'];
                ?>
                    <div class="bag-item">
                        <img src="<?php echo bag_h($item['image']); ?>" alt="<?php echo bag_h($item['productName']); ?>">
                        <div class="bag-item-info">
                            <div class="bag-item-title"><?php echo bag_h($item['productName']); ?></div>
                            <div class="bag-item-price">$<?php echo number_format((float)$item['price'], 2); ?> x <?php echo (int)$item['quantity']; ?></div>
                            <a href="remove_item.php?id=<?php echo (int)$item['cartID']; ?>" class="remove-btn">Remove</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your bag is empty.</p>
            <?php endif; ?>
        </div>

        <div class="bag-total">
            Total: $<span id="bag-total-price"><?php echo number_format($total_price, 2); ?></span>
        </div>

        <?php if($total_price > 0): ?>
            <button class="btn-add" style="margin-top: 16px;" onclick="document.getElementById('checkoutModal').style.display='block'">Checkout</button>
        <?php endif; ?>
    </main>

    
    <div id="checkoutModal" class="modal-overlay">
        <div class="modal-box">
            <button class="close-btn" onclick="document.getElementById('checkoutModal').style.display='none'">&times;</button>
            <h2>Payment Information</h2>
            <form action="process_checkout.php" method="POST" class="modal-form">
                <label>Total Price</label>
                <input type="text" name="total" value="$<?php echo number_format($total_price, 2); ?>" readonly>

                <label>Bank Card Number</label>
                <input type="text" name="card" placeholder="Enter your card number" required>

                <label>Receiver Name</label>
                <input type="text" name="name" value="<?php echo bag_h($_SESSION['buyer_name'] ?? ''); ?>" required>

                <label>Shipping Address</label>
                <textarea name="address" required></textarea>

                <button type="submit" class="confirm-btn">Confirm Purchase</button>
            </form>
        </div>
    </div>
</body>
</html>
 
