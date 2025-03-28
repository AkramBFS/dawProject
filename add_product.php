<?php
include('db.php'); // Include the DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    // Image Upload Handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    // SQL Query with Prepared Statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (name, price, category, description, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssis", $name, $price, $category, $description, $stock, $target_file);
    
    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <h2>Add New Product</h2>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label for="name">Product Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label for="price">Price:</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label for="category">Category:</label><br>
        <input type="text" name="category" required><br><br>

        <label for="description">Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label for="stock">Stock Quantity:</label><br>
        <input type="number" name="stock" required><br><br>

        <label for="image">Product Image:</label><br>
        <input type="file" name="image" required><br><br>

        <input type="submit" value="Add Product">
    </form>
</body>
</html>
