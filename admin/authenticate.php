<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /dawProject/admin/login.php');
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    header('Location: /dawProject/admin/login.php?error=Username and password are required.');
    exit();
}

$conn = connect_to_db();
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: /dawProject/admin/dashboard.php');
        exit();
    }
}

header('Location: /dawProject/admin/login.php?error=Invalid credentials. Please check your username and password.');
exit();
?>