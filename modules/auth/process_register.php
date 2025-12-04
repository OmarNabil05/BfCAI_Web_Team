<?php
session_start();

// Include database connection
require_once '../../config/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=Please fill in all required fields");
        exit();
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit();
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: register.php?error=Email already registered");
        exit();
    }
    $stmt->close();
    
    // Insert new user (plain text password for now - matches current database)
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, password, role) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $name, $email, $phone_number, $password);
    
    if ($stmt->execute()) {
        // Registration successful
        header("Location: login.php?success=Registration successful! Please login.");
        exit();
    } else {
        header("Location: register.php?error=Registration failed. Please try again.");
        exit();
    }
    
    $stmt->close();
} else {
    // If not POST request, redirect to register page
    header("Location: register.php");
    exit();
}

$conn->close();
?>
