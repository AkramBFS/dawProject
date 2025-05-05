<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../php/db.php';
$conn = connect_to_db();

// Fetch product data if ID exists
$product = null;
if (isset($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("
        SELECT p.*, pv.size, pv.stock, pv.id as variant_id 
        FROM products p 
        LEFT JOIN product_variants pv ON p.id = pv.product_id 
        WHERE p.id = '$product_id'
    ");
    $product = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $variant_id = $conn->real_escape_string($_POST['variant_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock = $conn->real_escape_string($_POST['stock']);
    $size = $conn->real_escape_string($_POST['size']);

    // Handle image update if new image is uploaded
    $image_path = $product['image_path']; // Keep old image by default
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../image/prods/";  // Keep relative path for file saving
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = "image/prods/" . basename($_FILES["image"]["name"]); // Store without ../
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update product
        $product_sql = "UPDATE products SET 
                name = '$name', 
                price = '$price', 
                category = '$category', 
                description = '$description', 
                image_path = '$image_path' 
                WHERE id = '$id'";
        
        if ($conn->query($product_sql)) {
            // Update product variant
            $variant_sql = "UPDATE product_variants SET 
                          size = '$size',
                          stock = '$stock'
                          WHERE id = '$variant_id'";
            
            if ($conn->query($variant_sql)) {
                $conn->commit();
                header('Location: products.php');
                exit();
            } else {
                throw new Exception("Error updating variant");
            }
        } else {
            throw new Exception("Error updating product");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error updating product: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - VERA</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            color: #000000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
        }
        .submit-btn {
            background: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .current-image {
            max-width: 200px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2 style="padding: 0 20px;">VERA Admin</h2>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
            <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="users.php"><i class="fas fa-users"></i> Users</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <h1>Edit Product</h1>
            
            <?php if ($product): ?>
            <div class="form-container">
                <form action="edit_product.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="variant_id" value="<?php echo $product['variant_id']; ?>">
                    
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" id="price" name="price" value="<?php echo $product['price']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="T-shirts" <?php echo $product['category'] == 'T-shirts' ? 'selected' : ''; ?>>T-shirts</option>
                            <option value="Shoes" <?php echo $product['category'] == 'Shoes' ? 'selected' : ''; ?>>Shoes</option>
                            <option value="Hoodies" <?php echo $product['category'] == 'Hoodies' ? 'selected' : ''; ?>>Hoodies</option>
                            <option value="Jeans" <?php echo $product['category'] == 'Jeans' ? 'selected' : ''; ?>>Jeans</option>
                            <option value="Socks" <?php echo $product['category'] == 'Socks' ? 'selected' : ''; ?>>Socks</option>
                            <option value="Sweatpants" <?php echo $product['category'] == 'Sweatpants' ? 'selected' : ''; ?>>Sweatpants</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select id="size" name="size" required>
                            <option value="S" <?php echo $product['size'] == 'S' ? 'selected' : ''; ?>>Small (S)</option>
                            <option value="L" <?php echo $product['size'] == 'L' ? 'selected' : ''; ?>>Large (L)</option>
                            <option value="39" <?php echo $product['size'] == '39' ? 'selected' : ''; ?>>Size 39 (Shoes)</option>
                            <option value="40" <?php echo $product['size'] == '40' ? 'selected' : ''; ?>>Size 40 (Shoes)</option>
                            <option value="41" <?php echo $product['size'] == '41' ? 'selected' : ''; ?>>Size 41 (Shoes)</option>
                            <option value="42" <?php echo $product['size'] == '42' ? 'selected' : ''; ?>>Size 42 (Shoes)</option>
                            <option value="43" <?php echo $product['size'] == '43' ? 'selected' : ''; ?>>Size 43 (Shoes)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Image:</label>
                        <img src="/dawProject/<?php echo str_replace('../', '', $product['image_path']); ?>" class="current-image">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">New Image (Leave blank to keep current)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    
                    <button type="submit" class="submit-btn">Update Product</button>
                </form>
            </div>
            <?php else: ?>
                <p>Product not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>