<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /dawProject/admin/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - VERA</title>
    <link rel="stylesheet" href="/dawProject/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    .admin-main,
    .admin-main .card,
    .admin-main .card h1,
    .admin-main .card h2,
    .admin-main .card h3,
    .admin-main .card p,
    .admin-main table th,
    .admin-main table,
    .admin-main form,
    .admin-main form label {
        color: #000000 !important;
    }

    /* Reuse admin-container and sidebar styles from dashboard */
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
    .action-buttons {
        margin-bottom: 20px;
    }
    .action-buttons button {
        padding: 10px 15px;
        background: #333;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th {
        padding: 12px;
        text-align: left;
        background-color: #f4f4f4;
        color: #000000; /* Keep thead text black */
    }
    td {
        padding: 12px;
        text-align: left;
        color: #ffffff; /* Make table body text white */
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .edit-btn, .delete-btn {
        padding: 5px 10px;
        margin: 0 5px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .edit-btn {
        background: #4CAF50;
        color: white;
    }
    .delete-btn {
        background: #f44336;
        color: white;
    }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <h2 style="padding: 0 20px;">VERA Admin</h2>
            <a href="/dawProject/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="/dawProject/admin/products.php"><i class="fas fa-tshirt"></i> Products</a>
            <a href="/dawProject/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="/dawProject/admin/users.php"><i class="fas fa-users"></i> Users</a>
            <a href="/dawProject/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <h1>Product Management</h1>
            <!-- Page-specific content -->
            <div class="action-buttons">
                <button onclick="window.location.href='/dawProject/admin/add_product.php'">Add New Product</button>
            </div>
            <table>
                <thead>
                    <tr style="background-color: #333; color: white;">
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Size</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once '../php/db.php';
                    $conn = connect_to_db();
                    $query = "SELECT p.*, pv.stock, pv.size 
                             FROM products p 
                             LEFT JOIN product_variants pv ON p.id = pv.product_id 
                             ORDER BY p.id DESC";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr style="background-color: #000000; color:rgb(255, 255, 255);">
                        <td><?php echo $row['id']; ?></td>
                        <td><img src="/dawProject/<?php echo str_replace('../', '', $row['image_path']); ?>" width="50"></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>$<?php echo $row['price']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td><?php echo $row['size']; ?></td>
                        <td>
                            <button class="edit-btn" onclick="window.location.href='/dawProject/admin/edit_product.php?id=<?php echo $row['id']; ?>'">Edit</button>
                            <button class="delete-btn" onclick="if(confirm('Are you sure?')) window.location.href='/dawProject/admin/delete_product.php?id=<?php echo $row['id']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>