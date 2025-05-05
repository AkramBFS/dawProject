<?php
session_start();

// Database configuration
require_once 'db.php';

// Process signup form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']); // Add email field
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: ../html/signup.html?error=All fields are required.");
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../html/signup.html?error=Invalid email format.");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../html/signup.html?error=Passwords do not match.");
        exit();
    }
    
    if (strlen($password) < 8) {
        header("Location: ../html/signup.html?error=Password must be at least 8 characters long.");
        exit();
    }
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: ../html/signup.html?error=Username or email already taken.");
        exit();
    }
    $stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user (default role is 'client')
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'client', NOW())");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        header("Location: ../html/login.html?success=Registration successful. Please login.");
        exit();
    } else {
        header("Location: ../html/signup.html?error=Registration failed. Please try again.");
        exit();
    }
    
    $stmt->close();
}

$conn->close();
?>