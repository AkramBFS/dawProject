<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../php/db.php';
$conn = connect_to_db();

// Update order status if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $conn->real_escape_string($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $sql = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $conn->query($sql);
}

// Get all orders with basic info
$orders_query = "SELECT o.id, u.username, o.created_at, o.total_amount, o.status, o.shipping_address 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_query);

// Prepare to get items for each order
$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $order_id = $order['id'];
    
    // Get items for this order
    $items_query = "SELECT p.name, oi.quantity, oi.price 
                   FROM order_items oi
                   JOIN product_variants pv ON oi.product_variant_id = pv.id
                   JOIN products p ON pv.product_id = p.id
                   WHERE oi.order_id = $order_id";
    $items_result = $conn->query($items_query);
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item['name'] . ' (' . $item['quantity'] . ' × $' . $item['price'] . ')';
    }
    
    $order['items'] = implode('<br>', $items);
    $orders[] = $order;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - VERA</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-main,
        .admin-main .card,
        .admin-main .card h1,
        .admin-main .card h2,
        .admin-main .card h3,
        .admin-main .card p,
        .admin-main table,
        .admin-main table th,
        .admin-main form,
        .admin-main form label {
            color: #000000 !important;
        }

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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        td {
            color: white !important;
        }
        .status-pending {
            color: #e67e22;
        }
        .status-shipped {
            color: #3498db;
        }
        .status-delivered {
            color: #2ecc71;
        }
        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
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
            <h1>Order Management</h1>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Shipping Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= $order['username'] ?></td>
                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['items'] ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td><?= $order['shipping_address'] ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>