<?php
include "db_connect.php";
session_start();


if (!isset($_SESSION['buyer_id'])) {
    die("<script>alert('Please login first!'); window.location.href='purchaser_login.php';</script>");
}

$buyer_id = $_SESSION['buyer_id'];


$sql = "SELECT cart.*, products.productName, products.price, products.image 
        FROM cart 
        JOIN products ON cart.productID = products.productID 
        WHERE cart.buyerID = '$buyer_id' AND cart.cartStatus = 'In Cart'";
$result = mysqli_query($connection, $sql);

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login&search.css">
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

    <main class="bag-container">
        <h1>Review your bag.</h1>
        
        <div id="bag-items-container">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($item = mysqli_fetch_assoc($result)): 
                    $total_price += $item['price'] * $item['quantity'];
                ?>
                    <div class="bag-item">
                        <img src="<?php echo $item['image']; ?>" alt="Car">
                        <div class="bag-item-info">
                            <div class="bag-item-title"><?php echo $item['productName']; ?></div>
                            <div class="bag-item-price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></div>
                            <a href="remove_item.php?id=<?php echo $item['cartID']; ?>" class="remove-btn">Remove</a>
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
                <input type="text" name="name" value="<?php echo $_SESSION['buyer_name']; ?>" required>

                <label>Shipping Address</label>
                <textarea name="address" required></textarea>

                <button type="submit" class="confirm-btn">Confirm Purchase</button>
            </form>
        </div>
    </div>
</body>
</html>