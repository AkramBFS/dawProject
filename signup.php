<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dawProject_db');

// Establish database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error. Please try again later.");
}

// Process signup form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: signup.php?error=All fields are required.");
        exit();
    }
    
    if ($password !== $confirm_password) {
        header("Location: signup.php?error=Passwords do not match.");
        exit();
    }
    
    if (strlen($password) < 8) {
        header("Location: signup.php?error=Password must be at least 8 characters long.");
        exit();
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: signup.php?error=Username already taken.");
        exit();
    }
    $stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user (default role is 'user')
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->bind_param("ss", $username, $hashed_password);
    
    if ($stmt->execute()) {
        header("Location: login.html?success=Registration successful. Please login.");
        exit();
    } else {
        header("Location: signup.php?error=Registration failed. Please try again.");
        exit();
    }
    
    $stmt->close();
}

$conn->close();
?>