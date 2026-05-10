<?php
include "db_connect.php";
session_start();

if (!isset($_SESSION['buyer_id'])) {
    
    $_SESSION['error_msg'] = "Please login first to add items!";
    header("Location: purchaser_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $buyer_id = $_SESSION['buyer_id'];
    $product_id = mysqli_real_escape_string($connection, $_POST['product_id']);
    
    
    
    
    $_SESSION['success_msg'] = "Added to bag successfully!";
    header("Location: search.php"); 
    exit();

    
    $_SESSION['error_msg'] = "This item is already in your bag!";
    header("Location: search.php");
    exit();
}
?>
