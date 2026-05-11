<?php
include "db_connect.php";
session_start();

if (!isset($_SESSION["buyer_id"])) {
    header("Location: purchaser_login.php");
    exit();
}

if (!isset($_GET["id"])) {
    $_SESSION["error_msg"] = "No bag item was selected.";
    header("Location: bag.php");
    exit();
}

$cart_id = (int)$_GET["id"];
$buyer_id = (int)$_SESSION["buyer_id"];

$stmt = mysqli_prepare($connection, "DELETE FROM cart WHERE cartID = ? AND buyerID = ?");
mysqli_stmt_bind_param($stmt, "ii", $cart_id, $buyer_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION["success_msg"] = "Item removed from bag.";
} else {
    $_SESSION["error_msg"] = "Delete failed: " . mysqli_error($connection);
}

mysqli_stmt_close($stmt);
header("Location: bag.php");
exit();
?>
 
