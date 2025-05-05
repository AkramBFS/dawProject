<?php
session_start();
require_once '../php/db.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /dawProject/admin/login.php');
    exit();
}

$conn = connect_to_db();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VERA</title>
    <link rel="stylesheet" href="/dawProject/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .admin-main .card,
        .admin-main .card h3,
        .admin-main .card p,
        .admin-main table,
        .admin-main table th,
        .admin-main table td {
            color: #000000;
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
            <h1>Dashboard Overview</h1>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Products</h3>
                    <p><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM products");
                        echo $result->fetch_row()[0];
                    ?></p>
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM orders");
                        echo $result->fetch_row()[0];
                    ?></p>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php 
                        $result = $conn->query("SELECT COUNT(*) FROM users");
                        echo $result->fetch_row()[0];
                    ?></p>
                </div>
            </div>

            <div class="card">
                <h3>Recent Orders</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("
                            SELECT o.id, u.username, o.total_amount, o.status, o.created_at 
                            FROM orders o
                            JOIN users u ON o.user_id = u.id
                            ORDER BY o.created_at DESC LIMIT 5
                        ");
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td>$<?php echo $row['total_amount']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>