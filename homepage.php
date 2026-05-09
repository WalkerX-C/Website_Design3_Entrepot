<?php
/*
    MY Store - Homepage for Delivery 3

    Main logic:
    1. Read latest products from MySQL database.
    2. Read random products from MySQL database.
    3. Use JavaScript to randomly choose a hero video.
    4. Use JavaScript to open product modal.
*/

include "db_connect.php";

function homepage_h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function homepage_value($row, $key, $default = "") {
    if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== "") {
        return $row[$key];
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
        return "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=900";
    }

    return $value;
}

function homepage_make_product($row) {
    $id = homepage_value($row, "productID", "0");
    $name = homepage_value($row, "productName", "Unnamed Product");
    $price = homepage_value($row, "price", "");
    $image = homepage_value($row, "image", "");
    $seller = homepage_value($row, "sellerName", "Unknown Seller");
    $message = homepage_value($row, "sellerMessage", "No seller message has been provided.");
    $created = homepage_value($row, "uploadTime", "Not recorded");
    $brand = homepage_value($row, "brand", "");
    $model = homepage_value($row, "model", "");
    $location = homepage_value($row, "location", "");
    $colour = homepage_value($row, "colour", "");
    $year = homepage_value($row, "year", "");

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
        "colour" => (string)$colour,
        "year" => (string)$year,
        "detail_link" => "product.php?id=" . urlencode((string)$id)
    );
}

function homepage_fetch_products($connection, $sql) {
    $products = array();

    $result = mysqli_query($connection, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = homepage_make_product($row);
        }
    }

    return $products;
}

mysqli_set_charset($connection, "utf8mb4");

$homepage_latest_products = array();
$homepage_explore_products = array();
$homepage_db_status = "Database connected, but no product records were found.";
$homepage_total_product_count = 0;

$count_sql = "SELECT COUNT(*) AS totalProducts FROM products";
$count_result = mysqli_query($connection, $count_sql);

if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $homepage_total_product_count = (int)$count_row["totalProducts"];
}

$homepage_latest_sql = "
    SELECT products.*, sellers.name AS sellerName
    FROM products
    LEFT JOIN sellers ON products.sellerID = sellers.sellerID
    ORDER BY products.uploadTime DESC, products.productID DESC
    LIMIT 4
";

$homepage_latest_products = homepage_fetch_products($connection, $homepage_latest_sql);

$homepage_latest_ids = array();

foreach ($homepage_latest_products as $product) {
    $homepage_latest_ids[] = (int)$product["id"];
}

$homepage_exclude_sql = "";

if (count($homepage_latest_ids) > 0) {
    $homepage_exclude_sql = "WHERE products.productID NOT IN (" . implode(",", $homepage_latest_ids) . ")";
}

$homepage_explore_sql = "
    SELECT products.*, sellers.name AS sellerName
    FROM products
    LEFT JOIN sellers ON products.sellerID = sellers.sellerID
    $homepage_exclude_sql
    ORDER BY RAND()
    LIMIT 3
";

$homepage_explore_products = homepage_fetch_products($connection, $homepage_explore_sql);

if (count($homepage_explore_products) < 3) {
    $homepage_explore_sql_backup = "
        SELECT products.*, sellers.name AS sellerName
        FROM products
        LEFT JOIN sellers ON products.sellerID = sellers.sellerID
        ORDER BY RAND()
        LIMIT 3
    ";

    $homepage_explore_products = homepage_fetch_products($connection, $homepage_explore_sql_backup);
}

$homepage_modal_products_map = array();

foreach ($homepage_latest_products as $product) {
    $homepage_modal_products_map[$product["id"]] = $product;
}

foreach ($homepage_explore_products as $product) {
    $homepage_modal_products_map[$product["id"]] = $product;
}

$homepage_modal_products = array_values($homepage_modal_products_map);

if ($homepage_total_product_count > 0) {
    $homepage_db_status = "These products are loaded from the MySQL database. Total products: " . $homepage_total_product_count . ".";
}

$homepage_products_json = json_encode($homepage_modal_products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Store - Homepage</title>
    <link rel="stylesheet" href="homepage_style.css">

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
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
        <video id="homepage_hero_video" class="homepage_hero_video" autoplay muted loop playsinline>
            <source id="homepage_hero_video_source"
                    src="https://www.apple.com/105/media/us/apple-vision-pro/2026/9251fc5e-bf57-4fae-8994-b06bbd3bb104/anim/drawer-visionos-voice/medium.mp4"
                    type="video/mp4">
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
            <?php if (count($homepage_latest_products) > 0) { ?>
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
            <?php } else { ?>
                <p>No product records were found in the database.</p>
            <?php } ?>
        </div>
    </section>

    <section class="homepage_product_group">
        <h3 class="homepage_group_title homepage_floating" data-delay="0.06">More to Explore</h3>

        <div class="homepage_product_grid homepage_explore_grid">
            <?php if (count($homepage_explore_products) > 0) { ?>
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

        var homepage_video_list = [
            "https://www.apple.com/105/media/us/apple-vision-pro/2026/9251fc5e-bf57-4fae-8994-b06bbd3bb104/anim/drawer-visionos-voice/medium.mp4",
            "https://www.apple.com/105/media/us/macbook-neo/2026/eee281c9-06d4-45d9-9a37-ef16ad413279/anim/hero/medium_2x.mp4",
            "https://www.apple.com/105/media/us/airpods-max/2024/e8f376d6-82b2-40ca-8a22-5f87de755d6b/anim/max-loop/medium_2x.mp4"
        ];

        var homepage_hls_player = null;

        function homepage_goToSearch() {
            window.location.href = "search.php";
        }

        function homepage_scrollToLatest() {
            document.getElementById("homepage_latest").scrollIntoView();
        }

        function homepage_tryPlayVideo(video) {
            var playResult = video.play();

            if (playResult !== undefined) {
                playResult.catch(function () {
                    console.log("The browser blocked autoplay or the selected video could not be played.");
                });
            }
        }

        function homepage_playMp4Video(videoUrl) {
            var video = document.getElementById("homepage_hero_video");
            var source = document.getElementById("homepage_hero_video_source");

            if (homepage_hls_player !== null) {
                homepage_hls_player.destroy();
                homepage_hls_player = null;
            }

            source.src = videoUrl;
            source.type = "video/mp4";

            video.load();
            homepage_tryPlayVideo(video);
        }

        function homepage_playHlsVideo(videoUrl) {
            var video = document.getElementById("homepage_hero_video");
            var source = document.getElementById("homepage_hero_video_source");

            if (homepage_hls_player !== null) {
                homepage_hls_player.destroy();
                homepage_hls_player = null;
            }

            source.removeAttribute("src");
            video.removeAttribute("src");
            video.load();

            if (video.canPlayType("application/vnd.apple.mpegurl")) {
                source.src = videoUrl;
                source.type = "application/vnd.apple.mpegurl";

                video.load();
                homepage_tryPlayVideo(video);
            } else if (typeof Hls !== "undefined" && Hls.isSupported()) {
                homepage_hls_player = new Hls();
                homepage_hls_player.loadSource(videoUrl);
                homepage_hls_player.attachMedia(video);

                homepage_hls_player.on(Hls.Events.MANIFEST_PARSED, function () {
                    homepage_tryPlayVideo(video);
                });
            } else {
                homepage_playMp4Video(homepage_video_list[0]);
            }
        }

        function homepage_setRandomHeroVideo() {
            var randomIndex = Math.floor(Math.random() * homepage_video_list.length);
            var videoUrl = homepage_video_list[randomIndex];

            console.log("Current hero video:", videoUrl);

            if (videoUrl.indexOf(".m3u8") !== -1) {
                homepage_playHlsVideo(videoUrl);
            } else {
                homepage_playMp4Video(videoUrl);
            }
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

            document.getElementById("homepage_modal_desc").textContent =
                "Brand: " + product.brand +
                " | Model: " + product.model +
                " | Year: " + product.year +
                " | Colour: " + product.colour +
                " | Location: " + product.location;

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

        window.onload = function () {
            homepage_setRandomHeroVideo();
            homepage_showFloatingItems();
        };

        window.onscroll = homepage_showFloatingItems;
    </script>

</body>
</html>
