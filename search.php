<?php
include "db_connect.php";
session_start();

// 获取搜索关键词和排序
$raw_query = isset($_GET['q']) ? $_GET['q'] : "";

// 问题 1 解决：处理 SQL 通配符问题，将 % 和 _ 转义为普通字符
$safe_query = mysqli_real_escape_string($connection, $raw_query);
$like_query = str_replace(['%', '_'], ['\%', '\_'], $safe_query);

$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";

// 构建 SQL 语句
$sql = "SELECT * FROM products WHERE (productName LIKE '%$like_query%' OR brand LIKE '%$like_query%')";

// 排序逻辑
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

    <main class="search-container">
        <div class="search-header">
            <form action="search.php" method="GET" style="width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                <input type="text" name="q" class="search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($raw_query); ?>">
                <select name="sort" class="sort-dropdown" onchange="this.form.submit()">
                    <option value="newest" <?php if($sort=='newest') echo 'selected'; ?>>Sort by: Newest</option>
                    <option value="priceLH" <?php if($sort=='priceLH') echo 'selected'; ?>>Sort by: Price (Low to High)</option>
                    <option value="priceHL" <?php if($sort=='priceHL') echo 'selected'; ?>>Sort by: Price (High to Low)</option>
                </select>
            </form>
        </div>

        <div class="layout-wrapper">
            <aside class="filter-sidebar">
                <div class="filter-group">
                    <h3>Product Categories</h3>
                    <label class="filter-label"><input type="checkbox"> Phones</label>
                    <label class="filter-label"><input type="checkbox"> Laptops</label>
                    <label class="filter-label"><input type="checkbox"> Accessories</label>
                </div>
            </aside>

            <section class="product-grid" id="resultsGrid">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <!-- 问题 2 解决：为整个卡片添加跳转逻辑，并设置手型光标 -->
                        <div class="product-card" 
                             style="cursor: pointer;" 
                             onclick="window.location.href='product.php?id=<?php echo $row['productID']; ?>'">
                            
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product">
                            <div class="product-title"><?php echo htmlspecialchars($row['productName']); ?></div>
                            <div class="product-price">$<?php echo number_format($row['price'], 2); ?></div>
                            
                            <!-- 阻止冒泡：点击按钮时只触发添加购物车，不会触发卡片跳转 -->
                            <form action="add_to_cart.php" method="POST" onclick="event.stopPropagation();">
                                <input type="hidden" name="product_id" value="<?php echo $row['productID']; ?>">
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