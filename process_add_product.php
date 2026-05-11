<?php
session_start();

// ID for local test
// $_SESSION['sellerID'] = 1; 

if (!isset($_SESSION['sellerID'])) {
    die("<h2>Error: You must be logged in as a seller to add a product.</h2><a href='seller_login.html'>Go to Login</a>");
}

$sellerID = $_SESSION['sellerID'];

include 'db_connect.php'; // uniform from the leader

$productName   = $_POST['productName'];
$price         = $_POST['price'];
$brand         = $_POST['brand'];
$model         = $_POST['model'];
$colour        = $_POST['colour'];
$year          = $_POST['year'];
$location      = $_POST['location'];
$sellerMessage = $_POST['sellerMessage'];

$imagePath = ""; 
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
    $targetDir = "uploads/";
    $fileName = time() . "_" . basename($_FILES["productImage"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
        $imagePath = $targetFilePath;
    } else {
        die("Error uploading the image.");
    }
} else {
    die("Please upload a product image.");
}

$sql = "INSERT INTO products 
        (sellerID, productName, brand, model, year, colour, location, price, image, sellerMessage, uploadTime) 
        VALUES 
        ('$sellerID', '$productName', '$brand', '$model', '$year', '$colour', '$location', '$price', '$imagePath', '$sellerMessage', NOW())";

if (mysqli_query($connection, $sql)) {
    echo "<h2>Product Launched Successfully!</h2>";
    echo "<p>Your product <strong>$productName</strong> has been added to the store.</p>";
    echo "<a href='add_product.php'>Add another product</a> | <a href='homepage.html'>Back to Home</a>";
} else {
    echo "Error inserting record: " . mysqli_error($connection);
}

mysqli_close($connection);
?>