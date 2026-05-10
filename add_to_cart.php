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
    
    $quantity = 1;
    $added_time = date('Y-m-d H:i:s');
    $status = 'In Cart';

    
    $check_sql = "SELECT * FROM cart WHERE buyerID = '$buyer_id' AND productID = '$product_id' AND cartStatus = 'In Cart'";
    $check_result = mysqli_query($connection, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        
        $_SESSION['error_msg'] = "This item is already in your bag!";
        header("Location: search.php");
        exit();
    } else {
        
        $insert_sql = "INSERT INTO cart (buyerID, productID, quantity, addedTime, cartStatus) 
                       VALUES ('$buyer_id', '$product_id', '$quantity', '$added_time', '$status')";
        
        if (mysqli_query($connection, $insert_sql)) {
            
            $_SESSION['success_msg'] = "Added to bag successfully!";
            header("Location: search.php"); 
            exit();
        } else {
            
            $_SESSION['error_msg'] = "Database error: " . mysqli_error($connection);
            header("Location: search.php");
            exit();
        }
    }
}
?>
