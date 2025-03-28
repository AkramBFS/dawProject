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
    header("Location: login.html?error=Database connection error. Please try again later.");
    exit();
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header("Location: login.html?error=Username and password are required.");
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
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                $_SESSION['admin'] = true;
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.html");
            }
            exit();
        } else {
            // Invalid password
            header("Location: login.html?error=Invalid username or password.");
            exit();
        }
    } else {
        // User not found
        header("Location: login.html?error=Invalid username or password.");
        exit();
    }
    
    $stmt->close();
}

$conn->close();
?>