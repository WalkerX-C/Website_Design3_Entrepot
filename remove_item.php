<?php
include "db_connect.php";
session_start();

if (isset($_GET['id']) && isset($_SESSION['buyer_id'])) {
    $cart_id = mysqli_real_escape_string($connection, $_GET['id']);
    $buyer_id = $_SESSION['buyer_id'];

    // 只允许删除属于当前登录用户的购物车物品
    $sql = "DELETE FROM cart WHERE cartID = '$cart_id' AND buyerID = '$buyer_id'";
    
    if (mysqli_query($connection, $sql)) {
        header("Location: bag.php");
    } else {
        echo "Delete failed: " . mysqli_error($connection);
    }
} else {
    header("Location: purchaser_login.php");
}
?>