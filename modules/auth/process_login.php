<?php
session_start();

// Include database connection
require_once '../../config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields");
        exit();
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (plain text comparison for now - matches current database)
        if ($password === $user['password']) {
            // Password is correct, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] == 1) {
                // Admin user
                header("Location: ../../index.php");
            } else {
                // Regular user
                header("Location: ../restaurant/home.php");
            }
            exit();
        } else {
            header("Location: login.php?error=Invalid email or password");
            exit();
        }
    } else {
        header("Location: login.php?error=Invalid email or password");
        exit();
    }
    
    $stmt->close();
} else {
    // If not POST request, redirect to login page
    header("Location: login.php");
    exit();
}

$conn->close();
?>
