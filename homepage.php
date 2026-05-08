<?php
/*
    MY Store - Homepage for Delivery 3
    This file uses PHP to read product data from MySQL.
    JavaScript is written inside this PHP file, so no separate .js file is needed.
*/

$homepage_db_host = "127.0.0.1";
$homepage_db_user = "root";
$homepage_db_password = "Wxc@20060405";
$homepage_db_name = "my_store";

function homepage_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function homepage_first_value($row, $names, $default = "") {
    foreach ($names as $name) {
        if (isset($row[$name]) && $row[$name] !== "") {
            return $row[$name];
        }
    }
    return $default;
}

function homepage_price_text($value) {
    if ($value === "" || $value === null) {
        return "Price not listed";
    }

    if (is_numeric($value)) {
        return "$" . number_format((float)$value, 2);
    }

    return (string)$value;
}

function homepage_image_text($value) {
    $value = trim((string)$value);

    if ($value === "") {
        return "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80";
    }

    return $value;
}

function homepage_make_product($row) {
    $id = homepage_first_value($row, array("productID", "product_id", "id", "ProductID"), "0");
    $name = homepage_first_value($row, array("productName", "product_name", "name", "title", "model", "ProductName"), "Unnamed Product");
    $price = homepage_first_value($row, array("price", "product_price", "sale_price", "ProductPrice"), "");
    $image = homepage_first_value($row, array("image", "image_url", "product_image", "photo", "picture", "ProductImage"), "");
    $seller = homepage_first_value($row, array("sellerName", "seller", "seller_name", "username", "seller_username", "SellerName"), "Demo Seller");
    $message = homepage_first_value($row, array("sellerMessage", "seller_message", "message", "description", "details", "comment", "SellerMessage"), "No seller message has been provided.");
    $created = homepage_first_value($row, array("uploadTime", "created_at", "upload_time", "listed_at", "date_added", "ProductDate"), "Not recorded");
    $brand = homepage_first_value($row, array("brand", "Brand"), "");
    $model = homepage_first_value($row, array("model", "Model"), "");
    $location = homepage_first_value($row, array("location", "Location"), "");

    return array(
        "id" => (string)$id,
        "name" => (string)$name,
        "price" => homepage_price_text($price),
        "image" => homepage_image_text($image),
        "seller" => (string)$seller,
        "message" => (string)$message,
        "created" => (string)$created,
        "brand" => (string)$brand,
        "model" => (string)$model,
        "location" => (string)$location,
        "detail_link" => "product.php?id=" . urlencode((string)$id)
    );
}

$homepage_products = array();
$homepage_db_status = "Products are loaded from sample data because the database is not connected yet.";

mysqli_report(MYSQLI_REPORT_OFF);
$homepage_connection = mysqli_connect($homepage_db_host, $homepage_db_user, $homepage_db_password, $homepage_db_name);

if ($homepage_connection) {
    mysqli_set_charset($homepage_connection, "utf8mb4");

    $homepage_table_name = "";
    $homepage_table_result = mysqli_query($homepage_connection, "SHOW TABLES");

    if ($homepage_table_result) {
        while ($homepage_table_row = mysqli_fetch_row($homepage_table_result)) {
            if ($homepage_table_row[0] === "products") {
                $homepage_table_name = "products";
            }
        }
    }

    if ($homepage_table_name === "") {
        $homepage_check_test = mysqli_query($homepage_connection, "SHOW TABLES LIKE 'test_product'");
        if ($homepage_check_test && mysqli_num_rows($homepage_check_test) > 0) {
            $homepage_table_name = "test_product";
        }
    }

    if ($homepage_table_name === "products") {
        $homepage_sql = "SELECT products.*, sellers.name AS sellerName
                         FROM products
                         LEFT JOIN sellers ON products.sellerID = sellers.sellerID
                         ORDER BY products.uploadTime DESC, products.productID DESC
                         LIMIT 20";
        $homepage_result = mysqli_query($homepage_connection, $homepage_sql);

        if ($homepage_result) {
            while ($homepage_row = mysqli_fetch_assoc($homepage_result)) {
                $homepage_products[] = homepage_make_product($homepage_row);
            }
        }
    } else if ($homepage_table_name === "test_product") {
        $homepage_sql = "SELECT * FROM test_product LIMIT 20";
        $homepage_result = mysqli_query($homepage_connection, $homepage_sql);

        if ($homepage_result) {
            while ($homepage_row = mysqli_fetch_assoc($homepage_result)) {
                $homepage_products[] = homepage_make_product($homepage_row);
            }
        }
    }

    if (count($homepage_products) > 0) {
        $homepage_db_status = "These products are loaded from the MySQL database.";
    } else {
        $homepage_db_status = "Database connected, but no product records were found.";
    }

    mysqli_close($homepage_connection);
}

$homepage_sample_products = array(
    array(
        "id" => "1",
        "name" => "iPhone 17 Pro Max",
        "price" => "$9,999.00",
        "image" => "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80",
        "seller" => "Demo Seller",
        "message" => "Almost new condition. Suitable for daily use, study, work, and mobile photography.",
        "created" => "2026-05-06 14:30:00",
        "brand" => "Apple",
        "model" => "iPhone 17 Pro Max",
        "location" => "Beijing",
        "detail_link" => "product.php?id=1"
    ),
    array(
        "id" => "2",
        "name" => "iPad Pro",
        "price" => "$7,999.00",
        "image" => "https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=900&q=80",
        "seller" => "Demo Seller",
        "message" => "A clean tablet for note taking, online classes, design work, and entertainment.",
        "created" => "2026-05-06 14:35:00",
        "brand" => "Apple",
        "model" => "iPad Pro",
        "location" => "Shanghai",
        "detail_link" => "product.php?id=2"
    ),
    array(
        "id" => "3",
        "name" => "AirPods Pro",
        "price" => "$1,899.00",
        "image" => "https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=900&q=80",
        "seller" => "Demo Seller",
        "message" => "Light and convenient wireless earphones for music, calls, and online meetings.",
        "created" => "2026-05-06 14:40:00",
        "brand" => "Apple",
        "model" => "AirPods Pro",
        "location" => "Guangzhou",
        "detail_link" => "product.php?id=3"
    ),
    array(
        "id" => "4",
        "name" => "Nintendo Switch",
        "price" => "$2,299.00",
        "image" => "https://images.unsplash.com/photo-1578303512597-81e6cc155b3e?auto=format&fit=crop&w=900&q=80",
        "seller" => "Demo Seller",
        "message" => "A portable game console in good condition, suitable for home and travel use.",
        "created" => "2026-05-06 14:45:00",
        "brand" => "Nintendo",
        "model" => "Switch",
        "location" => "Chengdu",
        "detail_link" => "product.php?id=4"
    ),
    array(
        "id" => "5",
        "name" => "Sony PlayStation 5",
        "price" => "$3,899.00",
        "image" => "https://images.unsplash.com/photo-1607853202273-797f1c22a38e?auto=format&fit=crop&w=900&q=80",
        "seller" => "Demo Seller",
        "message" => "A modern console for high quality games and home entertainment.",
        "created" => "2026-05-06 14:50:00",
        "brand" => "Sony",
        "model" => "PS5",
        "location" => "Beijing",
        "detail_link" => "product.php?id=5"
    )
);

if (count($homepage_products) === 0) {
    $homepage_products = $homepage_sample_products;
}

while (count($homepage_products) < 7) {
    foreach ($homepage_sample_products as $homepage_sample_product) {
        $homepage_products[] = $homepage_sample_product;
        if (count($homepage_products) >= 7) {
            break;
        }
    }
}

$homepage_latest_products = array_slice($homepage_products, 0, 4);
$homepage_explore_products = $homepage_products;
shuffle($homepage_explore_products);
$homepage_explore_products = array_slice($homepage_explore_products, 0, 3);

$homepage_products_json = json_encode($homepage_products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Store - Homepage</title>
    <link rel="stylesheet" href="homepage_style.css">
</head>
<body>

    <header class="homepage_top_header">
        <div class="homepage_title_area homepage_floating" data-delay="0.05">
            <h1>MY Store</h1>
            <p class="homepage_title_sub">A simple and modern place to explore electronic products.</p>
        </div>

        <div class="homepage_header_icon homepage_floating" data-delay="0.10">
            <a href="homepage.php">
                <img src="home.png" alt="Home Icon">
            </a>
        </div>
    </header>

    <nav class="homepage_nav_bar homepage_floating" data-delay="0.10">
        <a href="purchaser_login.php">Buyer Login</a>
        <a href="search.php">Search</a>
        <a href="add_product.php">Product Registration</a>
        <a href="seller_login.php">Seller Login</a>
    </nav>

    <section class="homepage_hero homepage_floating" data-delay="0.16">
        <video class="homepage_hero_video" autoplay muted loop playsinline>
            <source src="https://www.apple.com/105/media/us/apple-vision-pro/2026/9251fc5e-bf57-4fae-8994-b06bbd3bb104/anim/drawer-visionos-voice/medium.mp4" type="video/mp4">
        </video>

        <div class="homepage_hero_overlay"></div>

        <div class="homepage_hero_content">
            <p class="homepage_hero_tag">New Season</p>
            <h2>Best Seller of the Season</h2>
            <p class="homepage_hero_desc">Discover premium electronic products with a clean and modern shopping experience.</p>

            <div class="homepage_hero_buttons">
                <button type="button" onclick="homepage_goToSearch()">Shop Now</button>
                <button type="button" onclick="homepage_scrollToLatest()">Latest Products</button>
            </div>
        </div>
    </section>

    <section id="homepage_latest" class="homepage_section_title homepage_floating" data-delay="0.08">
        <h2>Latest Products</h2>
        <p>These products are loaded from the MySQL database.</p>
        <p class="homepage_db_status"><?php echo homepage_h($homepage_db_status); ?></p>
    </section>

    <section class="homepage_product_group">
        <h3 class="homepage_group_title homepage_floating" data-delay="0.06">Recently Listed</h3>

        <div class="homepage_product_grid">
            <?php foreach ($homepage_latest_products as $index => $product) { ?>
                <article class="homepage_product_card homepage_floating" data-delay="<?php echo homepage_h(0.08 + ($index % 4) * 0.06); ?>">
                    <div class="homepage_product_image_wrap">
                        <img src="<?php echo homepage_h($product["image"]); ?>" alt="<?php echo homepage_h($product["name"]); ?>">
                    </div>

                    <div class="homepage_product_text">
                        <p class="homepage_product_id">ID: <?php echo homepage_h($product["id"]); ?></p>
                        <h3><?php echo homepage_h($product["name"]); ?></h3>
                        <p class="homepage_product_price"><?php echo homepage_h($product["price"]); ?></p>
                        <p class="homepage_product_seller">Seller: <?php echo homepage_h($product["seller"]); ?></p>
                        <p class="homepage_product_message"><?php echo homepage_h($product["message"]); ?></p>
                        <p class="homepage_product_time">Listed: <?php echo homepage_h($product["created"]); ?></p>
                    </div>

                    <button type="button" class="homepage_card_btn" onclick="homepage_openProductModal('<?php echo homepage_h($product["id"]); ?>')">Learn More</button>
                </article>
            <?php } ?>
        </div>
    </section>

    <section class="homepage_product_group">
        <h3 class="homepage_group_title homepage_floating" data-delay="0.06">More to Explore</h3>

        <div class="homepage_product_grid homepage_explore_grid">
            <?php foreach ($homepage_explore_products as $index => $product) { ?>
                <article class="homepage_product_card homepage_floating" data-delay="<?php echo homepage_h(0.08 + ($index % 4) * 0.06); ?>">
                    <div class="homepage_product_image_wrap">
                        <img src="<?php echo homepage_h($product["image"]); ?>" alt="<?php echo homepage_h($product["name"]); ?>">
                    </div>

                    <div class="homepage_product_text">
                        <p class="homepage_product_id">ID: <?php echo homepage_h($product["id"]); ?></p>
                        <h3><?php echo homepage_h($product["name"]); ?></h3>
                        <p class="homepage_product_price"><?php echo homepage_h($product["price"]); ?></p>
                        <p class="homepage_product_seller">Seller: <?php echo homepage_h($product["seller"]); ?></p>
                        <p class="homepage_product_message"><?php echo homepage_h($product["message"]); ?></p>
                    </div>

                    <button type="button" class="homepage_card_btn" onclick="homepage_openProductModal('<?php echo homepage_h($product["id"]); ?>')">Learn More</button>
                </article>
            <?php } ?>

            <a class="homepage_more_link_card homepage_floating" data-delay="0.24" href="search.php">
                <span>...</span>
                <p>Search More Products</p>
            </a>
        </div>
    </section>

    <section class="homepage_section_title homepage_floating" data-delay="0.08">
        <h2>Why Choose MY Store</h2>
        <p>Simple, clear, and efficient for buyers and sellers.</p>
    </section>

    <section class="homepage_info_row">
        <div class="homepage_info_box homepage_floating" data-delay="0.10">
            <h3>Database Driven</h3>
            <p>The homepage can show product records stored by the backend instead of only fixed HTML content.</p>
        </div>

        <div class="homepage_info_box homepage_floating" data-delay="0.16">
            <h3>Clear Navigation</h3>
            <p>Users can move between homepage, search, buyer login, seller login, and product registration pages.</p>
        </div>

        <div class="homepage_info_box homepage_floating" data-delay="0.22">
            <h3>Responsive Layout</h3>
            <p>The product layout is adjusted for desktop, tablet, and mobile screens.</p>
        </div>
    </section>

    <footer class="homepage_footer homepage_floating" data-delay="0.08">
        <p>© 2026 MY Store. All rights reserved.</p>
    </footer>

    <div id="homepage_product_modal" class="homepage_product_modal_overlay" onclick="homepage_closeProductModal(event)">
        <button type="button" class="homepage_modal_close" onclick="homepage_closeProductModalButton()">&times;</button>

        <div class="homepage_product_modal_box">
            <div class="homepage_product_modal_main">
                <div class="homepage_product_modal_left">
                    <img id="homepage_modal_image" src="" alt="Product">
                </div>

                <div class="homepage_product_modal_right">
                    <p id="homepage_modal_label" class="homepage_modal_label"></p>
                    <h2 id="homepage_modal_title"></h2>
                    <p id="homepage_modal_price" class="homepage_modal_price"></p>
                    <p id="homepage_modal_desc" class="homepage_modal_desc"></p>

                    <div class="homepage_modal_block">
                        <h3>Seller Information</h3>
                        <p id="homepage_modal_seller"></p>
                    </div>

                    <div class="homepage_modal_block">
                        <h3>Seller Message</h3>
                        <p id="homepage_modal_message"></p>
                    </div>

                    <div class="homepage_modal_block">
                        <h3>Listing Time</h3>
                        <p id="homepage_modal_time"></p>
                    </div>

                    <button type="button" id="homepage_modal_learn_more_btn">Learn More</button>
                </div>
            </div>

            <div class="homepage_product_modal_bottom">
                <div class="homepage_advantage_item">
                    <p class="homepage_advantage_title">Flexible payment</p>
                    <p class="homepage_advantage_text">Support for a clearer purchase process in the next stage.</p>
                </div>
                <div class="homepage_advantage_item">
                    <p class="homepage_advantage_title">Seller record</p>
                    <p class="homepage_advantage_text">Each product can show its seller and seller message.</p>
                </div>
                <div class="homepage_advantage_item">
                    <p class="homepage_advantage_title">Backend ready</p>
                    <p class="homepage_advantage_text">The page can read from MySQL through PHP.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        var homepage_products = <?php echo $homepage_products_json; ?>;

        function homepage_goToSearch() {
            window.location.href = "search.php";
        }

        function homepage_scrollToLatest() {
            document.getElementById("homepage_latest").scrollIntoView();
        }

        function homepage_findProduct(productId) {
            for (var i = 0; i < homepage_products.length; i++) {
                if (homepage_products[i].id == productId) {
                    return homepage_products[i];
                }
            }
            return null;
        }

        function homepage_openProductModal(productId) {
            var product = homepage_findProduct(productId);

            if (product == null) {
                return;
            }

            document.getElementById("homepage_modal_label").textContent = "Product ID: " + product.id;
            document.getElementById("homepage_modal_title").textContent = product.name;
            document.getElementById("homepage_modal_price").textContent = product.price;
            document.getElementById("homepage_modal_desc").textContent = "Brand: " + product.brand + " | Model: " + product.model + " | Location: " + product.location;
            document.getElementById("homepage_modal_seller").textContent = product.seller;
            document.getElementById("homepage_modal_message").textContent = product.message;
            document.getElementById("homepage_modal_time").textContent = product.created;
            document.getElementById("homepage_modal_image").src = product.image;
            document.getElementById("homepage_modal_image").alt = product.name;

            document.getElementById("homepage_modal_learn_more_btn").onclick = function () {
                window.location.href = product.detail_link;
            };

            document.getElementById("homepage_product_modal").classList.add("homepage_show_modal");
            document.body.style.overflow = "hidden";
        }

        function homepage_closeProductModal(event) {
            if (event.target.id === "homepage_product_modal") {
                homepage_closeProductModalButton();
            }
        }

        function homepage_closeProductModalButton() {
            document.getElementById("homepage_product_modal").classList.remove("homepage_show_modal");
            document.body.style.overflow = "auto";
        }

        function homepage_showFloatingItems() {
            var items = document.getElementsByClassName("homepage_floating");

            for (var i = 0; i < items.length; i++) {
                var itemTop = items[i].getBoundingClientRect().top;
                var windowHeight = window.innerHeight;

                if (itemTop < windowHeight - 60) {
                    var delay = items[i].getAttribute("data-delay");
                    items[i].style.transitionDelay = delay + "s";
                    items[i].classList.add("homepage_show");
                }
            }
        }

        window.onload = homepage_showFloatingItems;
        window.onscroll = homepage_showFloatingItems;
    </script>
</body>
</html>