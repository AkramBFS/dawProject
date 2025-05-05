<?php
session_start();

// Include database configuration
require_once 'db.php';

// Establish database connection
$conn = connect_to_db();

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $username = trim($_POST['username']);
    $password = $_POST['password']; 
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header("Location: /dawProject/html/login.html?error=Username and password are required.");
        exit();
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Check if there's a redirect parameter
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/dawProject/html/index.html';
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = true;
                header("Location: /dawProject/admin/dashboard.php");
            } else {
                header("Location: " . $redirect);
            }
            exit();
        } else {
            // Invalid password
            header("Location: /dawProject/html/login.html?error=Invalid username or password." . (isset($_GET['redirect']) ? "&redirect=" . $_GET['redirect'] : ""));
            exit();
        }
    } else {
        // User not found
        header("Location: /dawProject/html/login.html?error=Invalid username or password." . (isset($_GET['redirect']) ? "&redirect=" . $_GET['redirect'] : ""));
        exit();
    }
    
    $stmt->close();
}

$conn->close();
?>