<?php
include "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["buyer_id"])) {
    header("Location: bag.php");
    exit();
}

$buyer_id = (int)$_SESSION["buyer_id"];

$stmt = mysqli_prepare($connection, "UPDATE cart SET cartStatus = 'Purchased' WHERE buyerID = ? AND cartStatus = 'In Cart'");
mysqli_stmt_bind_param($stmt, "i", $buyer_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION["success_msg"] = "Thank you for your purchase. Your order has been placed.";
    mysqli_stmt_close($stmt);
    header("Location: homepage.php");
    exit();
}

$error = mysqli_error($connection);
mysqli_stmt_close($stmt);
$_SESSION["error_msg"] = "Checkout failed: " . $error;
header("Location: bag.php");
exit();
?>
