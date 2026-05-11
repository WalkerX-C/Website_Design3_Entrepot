<?php
include "db_connect.php";
session_start();


$raw_query = isset($_GET['q']) ? $_GET['q'] : "";

$safe_query = mysqli_real_escape_string($connection, $raw_query);
$like_query = str_replace(['%', '_'], ['\%', '\_'], $safe_query);

$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";
$category = isset($_GET['category']) ? $_GET['category'] : "all";

$sql = "SELECT * FROM products WHERE (productName LIKE '%$like_query%' OR brand LIKE '%$like_query%')";

$category_sql_map = array(
    "phones" => "(productName LIKE '%phone%' OR productName LIKE '%Galaxy%' OR model LIKE '%phone%')",
    "computers" => "(productName LIKE '%MacBook%' OR productName LIKE '%iPad%' OR productName LIKE '%Vision%' OR model LIKE '%Pro%')",
    "audio" => "(productName LIKE '%AirPods%' OR productName LIKE '%Speaker%' OR brand LIKE '%JBL%')",
    "gaming" => "(productName LIKE '%Nintendo%' OR productName LIKE '%Switch%' OR productName LIKE '%PlayStation%' OR model LIKE '%PS5%')"
);

if (isset($category_sql_map[$category])) {
    $sql .= " AND " . $category_sql_map[$category];
}


if ($sort == "priceLH") {
    $sql .= " ORDER BY price ASC";
} elseif ($sort == "priceHL") {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY uploadTime DESC";
}

$result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login&search.css">
    <link rel="stylesheet" href="theme.css">
    <title>Search & Filter Products</title>
</head>
<body>
    <header class="top-header">
        <div class="title-area">
            <h1>MY Store</h1>
            <p class="title-sub">A simple and modern place to explore products.</p>
        </div>

        <div class="header-icon">
            <a href="homepage.php">
                <img src="home.png" alt="Home">
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
    
    <?php if(isset($_SESSION['success_msg'])): ?>
        <div class="notice success">
            <?php 
                echo htmlspecialchars($_SESSION['success_msg']); 
                unset($_SESSION['success_msg']); 
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error_msg'])): ?>
        <div class="notice error">
            <?php 
                echo htmlspecialchars($_SESSION['error_msg']); 
                unset($_SESSION['error_msg']); 
            ?>
        </div>
    <?php endif; ?>

    <main class="search-container">
        <div class="search-header">
            <form action="search.php" method="GET" class="search-form">
                <div class="search-field-row">
                    <input type="text" name="q" class="search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($raw_query); ?>">
                </div>

                <div class="search-tools-row">
                    <label class="sort-label" for="sortSelect">Sort by</label>
                    <select id="sortSelect" name="sort" class="sort-dropdown" onchange="this.form.submit()">
                        <option value="newest" <?php if($sort=='newest') echo 'selected'; ?>>Newest</option>
                        <option value="priceLH" <?php if($sort=='priceLH') echo 'selected'; ?>>Price: Low to High</option>
                        <option value="priceHL" <?php if($sort=='priceHL') echo 'selected'; ?>>Price: High to Low</option>
                    </select>
                </div>

                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            </form>
        </div>

        <div class="layout-wrapper">
            <aside class="filter-sidebar">
                <form class="filter-group" action="search.php" method="GET">
                    <input type="hidden" name="q" value="<?php echo htmlspecialchars($raw_query, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
                    <h3>Product Categories</h3>
                    <label class="filter-label"><input type="radio" name="category" value="all" onchange="this.form.submit()" <?php if($category=='all') echo 'checked'; ?>> All Products</label>
                    <label class="filter-label"><input type="radio" name="category" value="phones" onchange="this.form.submit()" <?php if($category=='phones') echo 'checked'; ?>> Phones</label>
                    <label class="filter-label"><input type="radio" name="category" value="computers" onchange="this.form.submit()" <?php if($category=='computers') echo 'checked'; ?>> Computers & Tablets</label>
                    <label class="filter-label"><input type="radio" name="category" value="audio" onchange="this.form.submit()" <?php if($category=='audio') echo 'checked'; ?>> Audio</label>
                    <label class="filter-label"><input type="radio" name="category" value="gaming" onchange="this.form.submit()" <?php if($category=='gaming') echo 'checked'; ?>> Gaming</label>
                </form>
            </aside>

            <section class="product-grid" id="resultsGrid">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        
                        <div class="product-card" 
                             style="cursor: pointer;" 
                             onclick="window.location.href='product.php?id=<?php echo (int)$row['productID']; ?>'">
                            
                            <img src="<?php echo htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="product-title"><?php echo htmlspecialchars($row['productName']); ?></div>
                            <div class="product-price">$<?php echo number_format((float)$row['price'], 2); ?></div>
                            
                            
                            <form action="add_to_cart.php" method="POST" onclick="event.stopPropagation();">
                                <input type="hidden" name="product_id" value="<?php echo (int)$row['productID']; ?>">
                                <input type="hidden" name="redirect" value="search.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                                <button type="submit" class="btn-add">Add to Bag</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <p>No products found matching your search.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

</body>
</html>
