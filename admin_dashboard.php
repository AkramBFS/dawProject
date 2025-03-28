<?php
include('db.php');
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'dawProject_db');
$query = "SELECT * FROM products";
$result = $conn->query($query);

echo "<table border='1'>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Category</th>
    <th>Stock</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['price']}</td>
        <td>{$row['category']}</td>
        <td>{$row['stock']}</td>
    </tr>";
}

echo "</table>";
?>
