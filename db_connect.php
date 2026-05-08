<?php
$host = "127.0.0.1";
$user = "root";
$password = "Wxc@20060405";
$database = "my_store";

$connection = mysqli_connect($host, $user, $password, $database);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>