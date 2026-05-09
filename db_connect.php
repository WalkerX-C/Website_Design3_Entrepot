<?php
$host = "127.0.0.1";
$user = "root";
$database = "my_store";
$primary_password = "Wxc@20060405";
$fallback_password = "";

try {
    $connection = mysqli_connect($host, $user, $primary_password, $database);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1045) {
        try {
            $connection = mysqli_connect($host, $user, $fallback_password, $database);
        } catch (mysqli_sql_exception $e2) {
            die("Database connection failed on both environments: " . $e2->getMessage());
        }
    } else {
        die("Database error: " . $e->getMessage());
    }
}
?>
