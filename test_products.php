<?php
include "db_connect.php";

$result = mysqli_query($connection, "SELECT * FROM products");

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Products Test</title>
</head>
<body>

<h1>Products in Database</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Seller ID</th>
        <th>Name</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Year</th>
        <th>Colour</th>
        <th>Location</th>
        <th>Price</th>
        <th>Image</th>
    </tr>

    <?php
    while ($row = mysqli_fetch_row($result)) {
        echo "<tr>";
        echo "<td>" . $row[0] . "</td>";
        echo "<td>" . $row[1] . "</td>";
        echo "<td>" . $row[2] . "</td>";
        echo "<td>" . $row[3] . "</td>";
        echo "<td>" . $row[4] . "</td>";
        echo "<td>" . $row[5] . "</td>";
        echo "<td>" . $row[6] . "</td>";
        echo "<td>" . $row[7] . "</td>";
        echo "<td>" . $row[8] . "</td>";
        echo "<td>" . $row[9] . "</td>";
        echo "</tr>";
    }
    ?>

</table>

</body>
</html>

<?php
mysqli_close($connection);
?>