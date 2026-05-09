<?php
include "db_connect.php";

$sql = "SELECT products.productID, products.productName, products.price,
               products.image, products.model, products.year,
               products.uploadTime, products.sellerMessage,
               sellers.name
        FROM products, sellers
        WHERE products.sellerID = sellers.sellerID
        ORDER BY products.productID DESC";

$result = mysqli_query($connection, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MY Store Homepage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #222;
        }

        .header {
            background: #111;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 42px;
        }

        .header p {
            margin-top: 12px;
            color: #cccccc;
        }

        .nav {
            background: #222;
            padding: 14px;
            text-align: center;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 14px;
            font-size: 16px;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 12px rgba(0,0,0,0.10);
        }

        .product-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            display: block;
        }

        .product-info {
            padding: 16px;
        }

        .product-info h2 {
            font-size: 20px;
            margin: 0 0 10px 0;
        }

        .product-info p {
            margin: 7px 0;
            font-size: 14px;
            line-height: 1.4;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #222;
        }

        .message {
            color: #555;
            min-height: 55px;
        }

        .detail-link {
            display: inline-block;
            margin-top: 12px;
            padding: 9px 14px;
            background: #111;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .detail-link:hover {
            background: #444;
        }

        .footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            background: #111;
            color: white;
        }

        @media only screen and (max-width: 900px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .header h1 {
                font-size: 34px;
            }

            .nav a {
                display: inline-block;
                margin: 8px;
            }
        }

        @media only screen and (max-width: 600px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 24px 16px;
            }

            .header h1 {
                font-size: 28px;
            }

            .section-title {
                font-size: 24px;
            }

            .nav a {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>MY Store</h1>
        <p>A simple online marketplace for electronic products.</p>
    </div>

    <div class="nav">
        <a href="homepage.php">Home</a>
        <a href="search.php">Search</a>
        <a href="seller_register.php">Seller Register</a>
        <a href="seller_login.php">Seller Login</a>
        <a href="buyer_register.php">Buyer Register</a>
        <a href="buyer_login.php">Buyer Login</a>
        <a href="add_product.php">Add Product</a>
        <a href="cart.php">Cart</a>
    </div>

    <div class="main">
        <h2 class="section-title">Featured Products</h2>

        <div class="product-grid">
            <?php
            while ($row = mysqli_fetch_row($result)) {
                echo "<div class='product-card'>";

                echo "<img src='" . $row[3] . "' alt='Product image'>";

                echo "<div class='product-info'>";
                echo "<h2>" . $row[1] . "</h2>";

                echo "<p><strong>Product ID:</strong> " . $row[0] . "</p>";
                echo "<p class='price'>Price: ¥" . $row[2] . "</p>";
                echo "<p><strong>Model:</strong> " . $row[4] . "</p>";
                echo "<p><strong>Year:</strong> " . $row[5] . "</p>";
                echo "<p><strong>Seller:</strong> " . $row[8] . "</p>";
                echo "<p><strong>Upload Time:</strong> " . $row[6] . "</p>";
                echo "<p class='message'><strong>Seller Message:</strong> " . $row[7] . "</p>";

                echo "<a class='detail-link' href='product_detail.php?productID=" . $row[0] . "'>View Details</a>";

                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <div class="footer">
        <p>MY Store Phase B Demo Homepage</p>
    </div>

</body>
</html>

<?php
mysqli_close($connection);
?>