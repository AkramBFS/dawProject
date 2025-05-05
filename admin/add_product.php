<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../php/db.php';
    
    $conn = connect_to_db();
    
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock = $conn->real_escape_string($_POST['stock']);
    $size = $conn->real_escape_string($_POST['size']);

    // Handle file upload
    $target_dir = "../image/prods/";  // Keep this for actual file upload
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    $image_path = "../image/prods/" . basename($_FILES["image"]["name"]); // Keep ../ in database

    // Start transaction
    $conn->begin_transaction();

    try {
        // First insert the product
        $product_sql = "INSERT INTO products (name, price, category, description, image_path) 
                VALUES ('$name', '$price', '$category', '$description', '$image_path')";
        
        if ($conn->query($product_sql)) {
            $product_id = $conn->insert_id;
            
            // Then insert the product variant
            $variant_sql = "INSERT INTO product_variants (product_id, size, stock) 
                          VALUES ('$product_id', '$size', '$stock')";
            
            if ($conn->query($variant_sql)) {
                $conn->commit();
                header('Location: products.php');
                exit();
            } else {
                throw new Exception("Error inserting variant");
            }
        } else {
            throw new Exception("Error inserting product");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - VERA</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background: #333;
            color: white;
            padding: 20px 0;
        }
        .admin-sidebar a {
            display: block;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
        }
        .admin-sidebar a:hover {
            background: #555;
        }
        .admin-main {
            flex: 1;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            color: #000000 !important;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
            color: #000000 !important;s
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #000000 !important;
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #000000 !important;
        }
        .form-group textarea {
            height: 100px;
            color: #000000 !important;
        }
        .submit-btn {
            background: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .admin-main h1 {
            margin-left: 220px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (same as before) -->
        <div class="admin-sidebar">
            <h2 style="padding: 0 20px;">VERA Admin</h2>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="admin-main">
            <h1>Add New Product</h1>
            
            <div class="form-container">
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" id="price" name="price" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="T-shirts">T-shirts</option>
                            <option value="Shoes">Shoes</option>
                            <option value="Hoodies">Hoodies</option>
                            <option value="Jeans">Jeans</option>
                            <option value="Socks">Socks</option>
                            <option value="Sweatpants">Sweatpants</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select id="size" name="size" required>
                            <option value="S">Small (S)</option>
                            <option value="L">Large (L)</option>
                            <option value="39">Size 39 (Shoes)</option>
                            <option value="40">Size 40 (Shoes)</option>
                            <option value="41">Size 41 (Shoes)</option>
                            <option value="42">Size 42 (Shoes)</option>
                            <option value="43">Size 43 (Shoes)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>