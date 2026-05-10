<?php
include "db_connect.php";
session_start();

// 1. 检查是否登录
if (!isset($_SESSION['buyer_id'])) {
    echo "<script>alert('Please login first to add items to your bag!'); window.location.href='purchaser_login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $buyer_id = $_SESSION['buyer_id'];
    $product_id = mysqli_real_escape_string($connection, $_POST['product_id']);
    $quantity = 1; // 默认添加1辆
    $added_time = date('Y-m-d H:i:s');
    $status = 'In Cart';

    // 2. 检查购物车里是否已经有这辆车
    $check_sql = "SELECT * FROM cart WHERE buyerID = '$buyer_id' AND productID = '$product_id' AND cartStatus = 'In Cart'";
    $check_result = mysqli_query($connection, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('This car is already in your bag!'); window.location.href='search.php';</script>";
    } else {
        // 3. 插入新记录
        $insert_sql = "INSERT INTO cart (buyerID, productID, quantity, addedTime, cartStatus) 
                       VALUES ('$buyer_id', '$product_id', '$quantity', '$added_time', '$status')";
        
        if (mysqli_query($connection, $insert_sql)) {
            echo "<script>alert('Added to bag successfully!'); window.location.href='search.php';</script>";
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    }
}
?>