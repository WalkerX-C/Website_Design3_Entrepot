<?php
include "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    
    
    $sql = "UPDATE cart SET cartStatus = 'Purchased' WHERE buyerID = '$buyer_id' AND cartStatus = 'In Cart'";
    
    if (mysqli_query($connection, $sql)) {
        echo "<script>
                alert('Thank you for your purchase! Your order has been placed.');
                window.location.href='homepage.php';
              </script>";
    } else {
        echo "Checkout failed: " . mysqli_error($connection);
    }
} else {
    header("Location: bag.php");
}
?>