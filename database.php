<?php
$host = "127.0.0.1";
$user = "root";
$database = "my_store";
$primary_password = "Wxc@20060405";
$fallback_password = "";

function database_connect_without_db($host, $user, $primary_password, $fallback_password) {
    try {
        return mysqli_connect($host, $user, $primary_password);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1045) {
            try {
                return mysqli_connect($host, $user, $fallback_password);
            } catch (mysqli_sql_exception $e2) {
                die("Connection failed on both environments: " . $e2->getMessage());
            }
        }

        die("Connection error: " . $e->getMessage());
    }
}

function database_run($connection, $sql, $message) {
    if (!mysqli_query($connection, $sql)) {
        die($message . ": " . mysqli_error($connection));
    }
}

$connection = database_connect_without_db($host, $user, $primary_password, $fallback_password);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

database_run($connection, "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci", "Create database failed");

if (!mysqli_select_db($connection, $database)) {
    die("Select database failed: " . mysqli_error($connection));
}

mysqli_set_charset($connection, "utf8mb4");

database_run($connection, "SET FOREIGN_KEY_CHECKS = 0", "Disable foreign key checks failed");
database_run($connection, "DROP TABLE IF EXISTS cart", "Drop cart table failed");
database_run($connection, "DROP TABLE IF EXISTS products", "Drop products table failed");
database_run($connection, "DROP TABLE IF EXISTS buyers", "Drop buyers table failed");
database_run($connection, "DROP TABLE IF EXISTS sellers", "Drop sellers table failed");
database_run($connection, "SET FOREIGN_KEY_CHECKS = 1", "Enable foreign key checks failed");

database_run($connection, "
CREATE TABLE sellers (
    sellerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    registerTime DATETIME NOT NULL,
    UNIQUE KEY unique_seller_email (email),
    UNIQUE KEY unique_seller_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Create sellers table failed");

database_run($connection, "
CREATE TABLE buyers (
    buyerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    registerTime DATETIME NOT NULL,
    UNIQUE KEY unique_buyer_email (email),
    UNIQUE KEY unique_buyer_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Create buyers table failed");

database_run($connection, "
CREATE TABLE products (
    productID INT AUTO_INCREMENT PRIMARY KEY,
    sellerID INT NOT NULL,
    productName VARCHAR(150) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    colour VARCHAR(50) NOT NULL,
    location VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(500) NOT NULL,
    sellerMessage TEXT,
    uploadTime DATETIME NOT NULL,
    INDEX idx_products_seller (sellerID),
    INDEX idx_products_upload_time (uploadTime),
    CONSTRAINT fk_products_seller FOREIGN KEY (sellerID) REFERENCES sellers(sellerID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Create products table failed");

database_run($connection, "
CREATE TABLE cart (
    cartID INT AUTO_INCREMENT PRIMARY KEY,
    buyerID INT NOT NULL,
    productID INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    addedTime DATETIME NOT NULL,
    cartStatus VARCHAR(50) NOT NULL DEFAULT 'In Cart',
    INDEX idx_cart_buyer_status (buyerID, cartStatus),
    INDEX idx_cart_product (productID),
    CONSTRAINT fk_cart_buyer FOREIGN KEY (buyerID) REFERENCES buyers(buyerID),
    CONSTRAINT fk_cart_product FOREIGN KEY (productID) REFERENCES products(productID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "Create cart table failed");

database_run($connection, "
INSERT INTO sellers
(name, address, phone, email, username, password, registerTime)
VALUES
('Demo Seller', 'Beijing Chaoyang District', '13800000001', 'seller01@test.com', 'seller01', 'Wxc@20060405', '2026-05-06 10:00:00'),
('Digital World Store', 'Shanghai Pudong District', '13800000002', 'seller02@test.com', 'seller02', 'Wxc@20060405', '2026-05-06 10:10:00'),
('Smart Life Shop', 'Guangzhou Tianhe District', '13800000003', 'seller03@test.com', 'seller03', 'Wxc@20060405', '2026-05-06 10:20:00'),
('Game Plus Market', 'Chengdu High-tech Zone', '13800000004', 'seller04@test.com', 'seller04', 'Wxc@20060405', '2026-05-06 10:30:00'),
('Future Tech Seller', 'Shenzhen Nanshan District', '13800000005', 'seller05@test.com', 'seller05', 'Wxc@20060405', '2026-05-06 10:40:00')
", "Insert sellers failed");

database_run($connection, "
INSERT INTO buyers
(name, address, phone, email, username, password, registerTime)
VALUES
('Demo Buyer', 'Beijing Haidian District', '13900000001', 'buyer01@test.com', 'buyer01', 'Wxc@20060405', '2026-05-06 11:00:00'),
('Alex Wang', 'Shanghai Minhang District', '13900000002', 'buyer02@test.com', 'buyer02', 'Wxc@20060405', '2026-05-06 11:10:00'),
('Ming Li', 'Guangzhou Yuexiu District', '13900000003', 'buyer03@test.com', 'buyer03', 'Wxc@20060405', '2026-05-06 11:20:00'),
('Chen Zhang', 'Chengdu Wuhou District', '13900000004', 'buyer04@test.com', 'buyer04', 'Wxc@20060405', '2026-05-06 11:30:00'),
('Sophia Liu', 'Shenzhen Futian District', '13900000005', 'buyer05@test.com', 'buyer05', 'Wxc@20060405', '2026-05-06 11:40:00')
", "Insert buyers failed");

database_run($connection, "
INSERT INTO products
(sellerID, productName, brand, model, year, colour, location, price, image, sellerMessage, uploadTime)
VALUES
(1, 'iPhone 17 Pro Max', 'Apple', 'iPhone 17 Pro Max', 2026, 'Silver', 'Beijing', 9999.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=900', 'Almost new condition. Suitable for daily use, study, photography and mobile gaming.', '2026-05-06 14:30:00'),
(2, 'iPad Pro', 'Apple', 'iPad Pro', 2025, 'Black', 'Shanghai', 7999.00, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=900', 'A clean tablet for note taking, online classes, design work and video watching.', '2026-05-06 14:35:00'),
(3, 'AirPods Pro', 'Apple', 'AirPods Pro', 2025, 'White', 'Guangzhou', 1899.00, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=900', 'Light and convenient wireless earphones for music, online meetings and daily commuting.', '2026-05-06 14:40:00'),
(4, 'Nintendo Switch', 'Nintendo', 'Switch', 2024, 'Red Blue', 'Chengdu', 2299.00, 'https://images.unsplash.com/photo-1578303512597-81e6cc155b3e?w=900', 'A portable game console in good condition, suitable for family entertainment and travel.', '2026-05-06 14:45:00'),
(4, 'Sony PlayStation 5', 'Sony', 'PS5', 2025, 'White', 'Beijing', 3899.00, 'https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=900', 'A modern console for high quality games and home entertainment. Controller included.', '2026-05-06 14:50:00'),
(5, 'Apple Vision Pro', 'Apple', 'Vision Pro', 2026, 'White', 'Shenzhen', 11999.00, 'https://images.unsplash.com/photo-1622979135225-d2ba269cf1ac?w=900', 'A special mixed reality product for immersive media, study and creative work.', '2026-05-06 15:00:00'),
(2, 'MacBook Air', 'Apple', 'MacBook Air M3', 2025, 'Midnight', 'Shanghai', 8999.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=900', 'Lightweight laptop for programming, office work, online learning and daily tasks.', '2026-05-06 15:05:00'),
(3, 'Samsung Galaxy Phone', 'Samsung', 'Galaxy S Series', 2025, 'Black', 'Guangzhou', 5999.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=900', 'A powerful Android smartphone with a clear display and good battery life.', '2026-05-06 15:10:00'),
(5, 'Sony Camera', 'Sony', 'Alpha Camera', 2024, 'Black', 'Shenzhen', 6999.00, 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=900', 'A camera suitable for photography, travel recording and video creation.', '2026-05-06 15:15:00'),
(1, 'Bluetooth Speaker', 'JBL', 'Portable Speaker', 2024, 'Blue', 'Beijing', 699.00, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=900', 'A portable speaker with clear sound, suitable for home use and outdoor activities.', '2026-05-06 15:20:00')
", "Insert products failed");

database_run($connection, "
INSERT INTO cart
(buyerID, productID, quantity, addedTime, cartStatus)
VALUES
(1, 1, 1, '2026-05-06 16:00:00', 'In Cart'),
(1, 3, 2, '2026-05-06 16:05:00', 'In Cart'),
(2, 2, 1, '2026-05-06 16:10:00', 'In Cart'),
(2, 5, 1, '2026-05-06 16:15:00', 'Purchased'),
(3, 4, 1, '2026-05-06 16:20:00', 'In Cart'),
(3, 7, 1, '2026-05-06 16:25:00', 'In Cart'),
(4, 8, 1, '2026-05-06 16:30:00', 'Purchased'),
(4, 10, 2, '2026-05-06 16:35:00', 'In Cart'),
(5, 6, 1, '2026-05-06 16:40:00', 'In Cart'),
(5, 9, 1, '2026-05-06 16:45:00', 'Purchased')
", "Insert cart failed");

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - MY Store</title>
    <link rel="stylesheet" href="theme.css">
</head>
<body>
    <main class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Database setup finished.</h1>
                <p>Database name: my_store</p>
                <p>Tables created: sellers, buyers, products, cart.</p>
                <p>Sample data inserted successfully.</p>
            </div>
            <div class="login-actions" style="display: flex; flex-direction: column; gap: 12px;">
                <a href="homepage.php" class="confirm-btn login-btn" style="text-decoration: none; text-align: center;">Open Homepage</a>
                <a href="seller_login.php" class="confirm-btn login-btn" style="text-decoration: none; text-align: center;">Seller Login</a>
                <a href="purchaser_login.php" class="confirm-btn login-btn" style="text-decoration: none; text-align: center;">Buyer Login</a>
            </div>
        </div>
    </main>
</body>
</html>
