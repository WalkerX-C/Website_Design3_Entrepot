<?php
session_start();

if (!isset($_SESSION['sellerID'])) {
    die("<h2>Error: You must be logged in as a seller to add a product.</h2><a href='seller_login.php'>Go to Login</a>");
}

$sellerID = $_SESSION['sellerID'];

include 'db_connect.php';

$productName   = mysqli_real_escape_string($connection, $_POST['productName']);
$price         = mysqli_real_escape_string($connection, $_POST['price']);
$brand         = mysqli_real_escape_string($connection, $_POST['brand']);
$model         = mysqli_real_escape_string($connection, $_POST['model']);
$colour        = mysqli_real_escape_string($connection, $_POST['colour']);
$year          = mysqli_real_escape_string($connection, $_POST['year']);
$location      = mysqli_real_escape_string($connection, $_POST['location']);
$sellerMessage = mysqli_real_escape_string($connection, $_POST['sellerMessage']);

// create the upload folder automatically
$imagePath = ""; 
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
    $targetDir = "uploads/";

    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
        die("Error creating the upload folder.");
    }

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
    mysqli_close($connection);
    header("Location: add_product.php?success=1");
    exit();
} else {
    echo "Error inserting record: " . mysqli_error($connection);
    mysqli_close($connection);
}
?>
