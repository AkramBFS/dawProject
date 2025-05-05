<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = true;
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            header("Location: /dawProject/admin/dashboard.php");
            exit();
        }
    }
    
    header("Location: /dawProject/admin/login.php?error=Invalid credentials");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - VERA</title>
    <link rel="stylesheet" href="/dawProject/css/style.css">
    <style>
        .admin-login {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .admin-login h2 {
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }
        .admin-login input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .admin-login button {
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .normal-login-link {
            color: #000000;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <h2>Admin Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <form action="/dawProject/admin/authenticate.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <div class="normal-login-link">
            Normal login here <a href="../html/login.html">normal login</a>
        </div>
        </form>
    </div>
</body>
</html>