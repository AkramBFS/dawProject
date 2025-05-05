<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../php/db.php';
$conn = connect_to_db();

// Update user role if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $role = $conn->real_escape_string($_POST['role']);
    $sql = "UPDATE users SET role = '$role' WHERE id = '$user_id'";
    $conn->query($sql);
}

// Delete user if requested
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM users WHERE id = '$delete_id'";
    $conn->query($sql);
    header('Location: users.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - VERA</title>
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
    .admin-main form,
    .admin-main form label {
        color: #000000 ;
    }

    /* Reuse admin styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        background-color: #f4f4f4;
        color: #000000; /* Keep thead text black */
    }
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        color: #ffffff; /* Make table body text white */
    }
    .role-admin {
        color: #e74c3c; /* Keep role-specific colors */
        font-weight: bold;
    }
    .role-client {
        color: #3498db; /* Keep role-specific colors */
    }
    .role-select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .delete-btn {
        color: #e74c3c;
        text-decoration: none;
        margin-left: 10px;
    }
    .delete-btn:hover {
        text-decoration: underline;
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
            <h1>User Management</h1>
            <!-- Page-specific content -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                    while ($row = $result->fetch_assoc()):
                        $role_class = 'role-' . strtolower($row['role']);
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td class="<?php echo $role_class; ?>">
                            <form action="users.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <select name="role" class="role-select" onchange="this.form.submit()">
                                    <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="client" <?php echo $row['role'] == 'client' ? 'selected' : ''; ?>>Client</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="users.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>