<?php
include "db_connect.php";
session_start();

function redirect_back($fallback = "search.php") {
    $redirect = isset($_POST["redirect"]) ? trim($_POST["redirect"]) : $fallback;

    if ($redirect === "" || preg_match("/^https?:\/\//i", $redirect)) {
        $redirect = $fallback;
    }

    header("Location: " . $redirect);
    exit();
}

if (!isset($_SESSION["buyer_id"])) {
    $_SESSION["error_msg"] = "Please login first to add items.";
    header("Location: purchaser_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["product_id"])) {
    $_SESSION["error_msg"] = "No product was selected.";
    redirect_back();
}

$buyer_id = (int)$_SESSION["buyer_id"];
$product_id = (int)$_POST["product_id"];

if ($buyer_id <= 0) {
    session_unset();
    session_destroy();

    session_start();
    $_SESSION["error_msg"] = "Please login again.";
    header("Location: purchaser_login.php");
    exit();
}

if ($product_id <= 0) {
    $_SESSION["error_msg"] = "Invalid product.";
    redirect_back();
}

/* Check whether the buyer still exists */
$buyer_check = mysqli_prepare($connection, "SELECT buyerID FROM buyers WHERE buyerID = ? LIMIT 1");
mysqli_stmt_bind_param($buyer_check, "i", $buyer_id);
mysqli_stmt_execute($buyer_check);
mysqli_stmt_store_result($buyer_check);

if (mysqli_stmt_num_rows($buyer_check) === 0) {
    mysqli_stmt_close($buyer_check);

    session_unset();
    session_destroy();

    session_start();
    $_SESSION["error_msg"] = "Your login session is invalid. Please login again.";
    header("Location: purchaser_login.php");
    exit();
}

mysqli_stmt_close($buyer_check);

/* Check whether the product exists */
$product_check = mysqli_prepare($connection, "SELECT productID FROM products WHERE productID = ? LIMIT 1");
mysqli_stmt_bind_param($product_check, "i", $product_id);
mysqli_stmt_execute($product_check);
mysqli_stmt_store_result($product_check);

if (mysqli_stmt_num_rows($product_check) === 0) {
    mysqli_stmt_close($product_check);
    $_SESSION["error_msg"] = "This product no longer exists.";
    redirect_back();
}

mysqli_stmt_close($product_check);

/* Check whether the item is already in cart */
$cart_check = mysqli_prepare(
    $connection,
    "SELECT cartID, quantity FROM cart WHERE buyerID = ? AND productID = ? AND cartStatus = 'In Cart' LIMIT 1"
);

mysqli_stmt_bind_param($cart_check, "ii", $buyer_id, $product_id);
mysqli_stmt_execute($cart_check);
$cart_result = mysqli_stmt_get_result($cart_check);
$cart_row = mysqli_fetch_assoc($cart_result);
mysqli_stmt_close($cart_check);

if ($cart_row) {
    $new_quantity = (int)$cart_row["quantity"] + 1;
    $cart_id = (int)$cart_row["cartID"];

    $update = mysqli_prepare(
        $connection,
        "UPDATE cart SET quantity = ?, addedTime = NOW() WHERE cartID = ? AND buyerID = ?"
    );

    mysqli_stmt_bind_param($update, "iii", $new_quantity, $cart_id, $buyer_id);
    $ok = mysqli_stmt_execute($update);
    mysqli_stmt_close($update);
} else {
    $quantity = 1;
    $status = "In Cart";

    $insert = mysqli_prepare(
        $connection,
        "INSERT INTO cart (buyerID, productID, quantity, addedTime, cartStatus) VALUES (?, ?, ?, NOW(), ?)"
    );

    mysqli_stmt_bind_param($insert, "iiis", $buyer_id, $product_id, $quantity, $status);
    $ok = mysqli_stmt_execute($insert);
    mysqli_stmt_close($insert);
}

if ($ok) {
    $_SESSION["success_msg"] = "Added to bag successfully.";
} else {
    $_SESSION["error_msg"] = "Could not add this item: " . mysqli_error($connection);
}

redirect_back("bag.php");
?>
