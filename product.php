<?php
include "db_connect.php";

function product_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function product_price_text($value)
{
    if ($value === "" || $value === null) {
        return "Price not listed";
    }

    if (is_numeric($value)) {
        return "$" . number_format((float)$value, 2);
    }

    return (string)$value;
}

function product_image_text($value)
{
    $value = trim((string)$value);

    if ($value === "") {
        return "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=900";
    }

    return $value;
}

$product_id = 0;

if (isset($_GET["id"])) {
    $product_id = (int)$_GET["id"];
}

$product_found = false;

$product = array(
    "productID" => "0",
    "sellerID" => "",
    "productName" => "Product Not Found",
    "brand" => "",
    "model" => "",
    "year" => "",
    "colour" => "",
    "location" => "",
    "price" => "",
    "image" => "",
    "sellerMessage" => "This product does not exist in the database.",
    "uploadTime" => "",
    "sellerName" => "Unknown Seller",
    "sellerPhone" => "",
    "sellerEmail" => ""
);

if ($product_id > 0) {
    mysqli_set_charset($connection, "utf8mb4");

    $sql = "
        SELECT
            products.productID,
            products.sellerID,
            products.productName,
            products.brand,
            products.model,
            products.year,
            products.colour,
            products.location,
            products.price,
            products.image,
            products.sellerMessage,
            products.uploadTime,
            sellers.name AS sellerName,
            sellers.phone AS sellerPhone,
            sellers.email AS sellerEmail
        FROM products
        LEFT JOIN sellers ON products.sellerID = sellers.sellerID
        WHERE products.productID = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($connection, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result(
            $stmt,
            $db_productID,
            $db_sellerID,
            $db_productName,
            $db_brand,
            $db_model,
            $db_year,
            $db_colour,
            $db_location,
            $db_price,
            $db_image,
            $db_sellerMessage,
            $db_uploadTime,
            $db_sellerName,
            $db_sellerPhone,
            $db_sellerEmail
        );

        if (mysqli_stmt_fetch($stmt)) {
            $product_found = true;

            $product = array(
                "productID" => $db_productID,
                "sellerID" => $db_sellerID,
                "productName" => $db_productName,
                "brand" => $db_brand,
                "model" => $db_model,
                "year" => $db_year,
                "colour" => $db_colour,
                "location" => $db_location,
                "price" => $db_price,
                "image" => $db_image,
                "sellerMessage" => $db_sellerMessage,
                "uploadTime" => $db_uploadTime,
                "sellerName" => $db_sellerName,
                "sellerPhone" => $db_sellerPhone,
                "sellerEmail" => $db_sellerEmail
            );
        }

        mysqli_stmt_close($stmt);
    }
}

$product_name = $product["productName"];
$product_price = product_price_text($product["price"]);
$product_image = product_image_text($product["image"]);

$product_description = $product["sellerMessage"];

if ($product_description === "" || $product_description === null) {
    $product_description = "No seller message has been provided for this product.";
}

$product_options = "Brand: " . $product["brand"] .
                   " | Model: " . $product["model"] .
                   " | Year: " . $product["year"] .
                   " | Colour: " . $product["colour"] .
                   " | Location: " . $product["location"];

$product_tag = "Database Product";

if (!$product_found) {
    $product_tag = "Not Found";
    $product_options = "Please return to homepage or search page and choose an existing product.";
}

$product_js_data = array(
    "id" => (string)$product["productID"],
    "name" => (string)$product_name,
    "priceText" => (string)$product_price,
    "priceNumber" => is_numeric($product["price"]) ? (float)$product["price"] : 0,
    "image" => (string)$product_image,
    "brand" => (string)$product["brand"],
    "model" => (string)$product["model"],
    "year" => (string)$product["year"],
    "colour" => (string)$product["colour"],
    "location" => (string)$product["location"],
    "sellerName" => (string)$product["sellerName"],
    "sellerPhone" => (string)$product["sellerPhone"],
    "sellerEmail" => (string)$product["sellerEmail"],
    "sellerMessage" => (string)$product_description,
    "uploadTime" => (string)$product["uploadTime"]
);

$product_json = json_encode($product_js_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Store - <?php echo product_h($product_name); ?></title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="theme.css">
</head>

<body>

    <header class="top-header">
        <div class="title-area">
            <h1>MY Store</h1>
            <p class="title-sub">Product details and seller information.</p>
        </div>

        <div class="header-icon">
            <a href="homepage.php">
                <img src="home.png" alt="Home Icon">
            </a>
        </div>
    </header>

    <nav class="nav-bar">
        <a href="purchaser_login.php">Buyer Login</a>
        <a href="search.php">Search</a>
        <a href="add_product.php">Product Registration</a>
        <a href="seller_login.php">Seller Login</a>
        <a href="bag.php">Bag</a>
    </nav>

    <main class="product-page">

        <?php if (!$product_found) { ?>
            <section class="product-error-card">
                <h1>Product Not Found</h1>
                <p>This product ID does not exist in the database. Please return to homepage or search page.</p>
                <a href="homepage.php">Back to Homepage</a>
            </section>
        <?php } else { ?>

            <section class="product-main-card">
                <div class="product-left">
                    <div class="product-image-box">
                        <img id="productImage" src="<?php echo product_h($product_image); ?>" alt="<?php echo product_h($product_name); ?>">
                    </div>
                </div>

                <div class="product-right">
                    <p class="product-tag" id="productTag"><?php echo product_h($product_tag); ?></p>

                    <div class="variant-bar-wrap">
                        <div class="variant-bar" id="variantBar">
                            <button class="variant-btn active" type="button">
                                <?php echo product_h($product["brand"]); ?> Product
                            </button>
                        </div>
                    </div>

                    <div class="info-block main-title-block">
                        <h1 id="productName"><?php echo product_h($product_name); ?></h1>
                        <p class="product-price-main" id="productPrice"><?php echo product_h($product_price); ?></p>
                    </div>

                    <div class="info-block">
                        <h3>Model</h3>
                        <p id="productModel"><?php echo product_h($product["model"]); ?></p>
                    </div>

                    <div class="info-block">
                        <h3>Description</h3>
                        <p id="productDescription"><?php echo product_h($product_description); ?></p>
                    </div>

                    <div class="info-block">
                        <h3>Highlights</h3>
                        <ul id="productHighlights" class="highlight-list">
                            <li>Brand: <?php echo product_h($product["brand"]); ?></li>
                            <li>Model: <?php echo product_h($product["model"]); ?></li>
                            <li>Year: <?php echo product_h($product["year"]); ?></li>
                            <li>Colour: <?php echo product_h($product["colour"]); ?></li>
                            <li>Location: <?php echo product_h($product["location"]); ?></li>
                            <li>Seller: <?php echo product_h($product["sellerName"]); ?></li>
                        </ul>
                    </div>

                    <div class="info-block">
                        <h3>Options</h3>
                        <p id="productOptions"><?php echo product_h($product_options); ?></p>
                    </div>

                    <div class="info-block">
                        <h3>Seller Information</h3>
                        <p>
                            Seller: <?php echo product_h($product["sellerName"]); ?><br>
                            Phone: <?php echo product_h($product["sellerPhone"]); ?><br>
                            Email: <?php echo product_h($product["sellerEmail"]); ?><br>
                            Listed: <?php echo product_h($product["uploadTime"]); ?>
                        </p>
                    </div>

                    <div class="info-block buy-block">
                        <button class="cart-btn" type="button" onclick="addCurrentToBag()">Add to Bag</button>
                    </div>
                </div>
            </section>

        <?php } ?>

    </main>

    <script>
        var productPageData = <?php echo $product_json; ?>;
    </script>
    <script src="product.js"></script>
</body>
</html>
