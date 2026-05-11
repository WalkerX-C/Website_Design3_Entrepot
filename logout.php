<?php
session_start();

unset($_SESSION["buyer_id"]);
unset($_SESSION["buyer_name"]);
unset($_SESSION["seller_logged_in"]);
unset($_SESSION["sellerID"]);
unset($_SESSION["seller_name"]);

$_SESSION["success_msg"] = "You have signed out.";

header("Location: homepage.php");
exit();
?>
