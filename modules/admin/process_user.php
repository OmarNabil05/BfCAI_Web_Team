<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Add new user
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $password = $_POST['password'];
        $role = intval($_POST['role']);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            header("Location: users.php?error=Email already exists");
            exit();
        }
        $stmt->close();
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $phone_number, $password, $role);
        
        if ($stmt->execute()) {
            header("Location: users.php?success=User added successfully");
        } else {
            header("Location: users.php?error=Failed to add user");
        }
        $stmt->close();
        
    } elseif ($action == 'edit') {
        // Edit existing user
        $user_id = intval($_POST['user_id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $password = $_POST['password'];
        $role = intval($_POST['role']);
        
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            header("Location: users.php?error=Email already exists for another user");
            exit();
        }
        $stmt->close();
        
        // Update user
        if (!empty($password)) {
            // Update with new password
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, password = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssssii", $name, $email, $phone_number, $password, $role, $user_id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssii", $name, $email, $phone_number, $role, $user_id);
        }
        
        if ($stmt->execute()) {
            header("Location: users.php?success=User updated successfully");
        } else {
            header("Location: users.php?error=Failed to update user");
        }
        $stmt->close();
        
    } elseif ($action == 'delete') {
        // Delete user
        $user_id = intval($_POST['user_id']);
        
        // Prevent deleting current admin
        if ($user_id == $_SESSION['user_id']) {
            header("Location: users.php?error=Cannot delete your own account");
            exit();
        }
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            header("Location: users.php?success=User deleted successfully");
        } else {
            header("Location: users.php?error=Failed to delete user");
        }
        $stmt->close();
    }
} else {
    header("Location: users.php");
}

$conn->close();
?>
