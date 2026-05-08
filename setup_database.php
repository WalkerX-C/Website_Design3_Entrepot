<?php
$host = "127.0.0.1";
$user = "root";
$password = "Wxc@20060405";

$connection = mysqli_connect($host, $user, $password);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS my_store");

mysqli_select_db($connection, "my_store");

mysqli_query($connection, "DROP TABLE IF EXISTS cart");
mysqli_query($connection, "DROP TABLE IF EXISTS products");
mysqli_query($connection, "DROP TABLE IF EXISTS buyers");
mysqli_query($connection, "DROP TABLE IF EXISTS sellers");

$sql = "CREATE TABLE sellers (
    sellerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    address VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(50),
    username VARCHAR(30),
    password VARCHAR(30)
)";

mysqli_query($connection, $sql);

$sql = "CREATE TABLE buyers (
    buyerID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    address VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(50),
    username VARCHAR(30),
    password VARCHAR(30)
)";

mysqli_query($connection, $sql);

$sql = "CREATE TABLE products (
    productID INT AUTO_INCREMENT PRIMARY KEY,
    sellerID INT,
    productName VARCHAR(80),
    brand VARCHAR(50),
    model VARCHAR(50),
    year INT,
    colour VARCHAR(30),
    location VARCHAR(50),
    price INT,
    image VARCHAR(255),
    sellerMessage VARCHAR(255),
    uploadTime DATETIME
)";

mysqli_query($connection, $sql);

$sql = "CREATE TABLE cart (
    cartID INT AUTO_INCREMENT PRIMARY KEY,
    buyerID INT,
    productID INT,
    quantity INT
)";

mysqli_query($connection, $sql);

mysqli_query($connection, "INSERT INTO sellers
(name, address, phone, email, username, password)
VALUES
('Demo Seller', 'Beijing', '13800138000', 'seller01@example.com', 'seller01', 'seller123')");

mysqli_query($connection, "INSERT INTO buyers
(name, address, phone, email, username, password)
VALUES
('Demo Buyer', 'Beijing', '13900139000', 'buyer01@example.com', 'buyer01', 'buyer123')");

mysqli_query($connection, "INSERT INTO products
(sellerID, productName, brand, model, year, colour, location, price, image, sellerMessage, uploadTime)
VALUES
(1, 'iPhone 17 Pro Max', 'Apple', 'iPhone 17 Pro Max', 2026, 'Silver', 'Beijing', 9999,
'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80',
'Almost new condition. Suitable for daily use, study, work, and mobile photography.',
'2026-05-06 14:30:00'),

(1, 'iPad Pro', 'Apple', 'iPad Pro', 2025, 'Black', 'Shanghai', 7999,
'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=900&q=80',
'A clean tablet for note taking, online classes, design work, and entertainment.',
'2026-05-06 14:35:00'),

(1, 'AirPods Pro', 'Apple', 'AirPods Pro', 2025, 'White', 'Guangzhou', 1899,
'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=900&q=80',
'Light and convenient wireless earphones for music, calls, and online meetings.',
'2026-05-06 14:40:00'),

(1, 'Nintendo Switch', 'Nintendo', 'Switch', 2024, 'Red Blue', 'Chengdu', 2299,
'https://images.unsplash.com/photo-1578303512597-81e6cc155b3e?auto=format&fit=crop&w=900&q=80',
'A portable game console in good condition, suitable for home and travel use.',
'2026-05-06 14:45:00'),

(1, 'Sony PlayStation 5', 'Sony', 'PS5', 2025, 'White', 'Beijing', 3899,
'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?auto=format&fit=crop&w=900&q=80',
'A modern console for high quality games and home entertainment.',
'2026-05-06 14:50:00')");

echo "<h1>Database setup finished.</h1>";
echo "<p>Database name: my_store</p>";
echo "<p>Tables created: sellers, buyers, products, cart</p>";
echo "<p>Products table includes sellerMessage and uploadTime.</p>";
echo "<p><a href='homepage.php'>Open homepage</a></p>";
echo "<p><a href='test_products.php'>View products table</a></p>";

mysqli_close($connection);
?>