<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// File upload configuration
$upload_dir = 'uploads/';
$allowed_types = array('jpg', 'jpeg', 'png', 'gif');
$max_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Add new category
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        // Handle file upload
        $photo_name = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_size = $_FILES['photo']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($file_ext, $allowed_types)) {
                header("Location: categories.php?error=Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.");
                exit();
            }
            
            // Validate file size
            if ($file_size > $max_size) {
                header("Location: categories.php?error=File size exceeds 5MB limit.");
                exit();
            }
            
            // Generate unique filename
            $photo_name = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $photo_name;
            
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                header("Location: categories.php?error=Failed to upload photo.");
                exit();
            }
        } else {
            header("Location: categories.php?error=Please select a photo.");
            exit();
        }
        
        // Insert new category
        $stmt = $conn->prepare("INSERT INTO categories (name, photo, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $photo_name, $description);
        
        if ($stmt->execute()) {
            header("Location: categories.php?success=Category added successfully");
        } else {
            // Delete uploaded file if database insert fails
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            header("Location: categories.php?error=Failed to add category");
        }
        $stmt->close();
        
    } elseif ($action == 'edit') {
        // Edit existing category
        $category_id = intval($_POST['category_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $current_photo = $_POST['current_photo'];
        
        $photo_name = $current_photo;
        
        // Handle new file upload if provided
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_size = $_FILES['photo']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($file_ext, $allowed_types)) {
                header("Location: categories.php?error=Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.");
                exit();
            }
            
            // Validate file size
            if ($file_size > $max_size) {
                header("Location: categories.php?error=File size exceeds 5MB limit.");
                exit();
            }
            
            // Generate unique filename
            $photo_name = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $photo_name;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old photo if exists
                if ($current_photo && file_exists($upload_dir . $current_photo)) {
                    unlink($upload_dir . $current_photo);
                }
            } else {
                header("Location: categories.php?error=Failed to upload new photo.");
                exit();
            }
        }
        
        // Update category
        $stmt = $conn->prepare("UPDATE categories SET name = ?, photo = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $photo_name, $description, $category_id);
        
        if ($stmt->execute()) {
            header("Location: categories.php?success=Category updated successfully");
        } else {
            header("Location: categories.php?error=Failed to update category");
        }
        $stmt->close();
        
    } elseif ($action == 'delete') {
        // Delete category
        $category_id = intval($_POST['category_id']);
        
        // Check if category has products
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM items WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row['count'] > 0) {
            header("Location: categories.php?error=Cannot delete category. It has " . $row['count'] . " product(s) associated with it.");
            exit();
        }
        
        // Get photo filename before deleting
        $stmt = $conn->prepare("SELECT photo FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            // Delete photo file if exists
            if ($category && $category['photo'] && file_exists($upload_dir . $category['photo'])) {
                unlink($upload_dir . $category['photo']);
            }
            header("Location: categories.php?success=Category deleted successfully");
        } else {
            header("Location: categories.php?error=Failed to delete category");
        }
        $stmt->close();
    }
} else {
    header("Location: categories.php");
}

$conn->close();
?>
