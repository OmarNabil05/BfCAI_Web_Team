<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// File upload configuration
$allowed_types = array('jpg', 'jpeg', 'png', 'gif');
$max_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Add new category
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        // Handle file upload
        $image_id = null;
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
            
            // Get MIME type
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            ];
            $mime_type = $mime_types[$file_ext];
            
            // Read image data
            $image_data = file_get_contents($file_tmp);
            
            // Insert image into database
            $stmt = $conn->prepare("INSERT INTO images (mime_type, data, original_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $mime_type, $image_data, $file_name);
            
            if ($stmt->execute()) {
                $image_id = $conn->insert_id;
            } else {
                header("Location: categories.php?error=Failed to upload photo.");
                exit();
            }
            $stmt->close();
        } else {
            header("Location: categories.php?error=Please select a photo.");
            exit();
        }
        
        // Insert new category
        $stmt = $conn->prepare("INSERT INTO categories (name, image_id, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $image_id, $description);
        
        if ($stmt->execute()) {
            header("Location: categories.php?success=Category added successfully");
        } else {
            // Delete uploaded image if database insert fails
            if ($image_id) {
                $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                $stmt->bind_param("i", $image_id);
                $stmt->execute();
                $stmt->close();
            }
            header("Location: categories.php?error=Failed to add category");
        }
        $stmt->close();
        
    } elseif ($action == 'edit') {
        // Edit existing category
        $category_id = intval($_POST['category_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $current_image_id = !empty($_POST['current_image_id']) ? intval($_POST['current_image_id']) : null;
        
        $image_id = $current_image_id;
        
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
            
            // Get MIME type
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            ];
            $mime_type = $mime_types[$file_ext];
            
            // Read image data
            $image_data = file_get_contents($file_tmp);
            
            // Insert new image into database
            $stmt = $conn->prepare("INSERT INTO images (mime_type, data, original_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $mime_type, $image_data, $file_name);
            
            if ($stmt->execute()) {
                $new_image_id = $conn->insert_id;
                
                // Delete old image if exists
                if ($current_image_id) {
                    $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                    $stmt->bind_param("i", $current_image_id);
                    $stmt->execute();
                }
                
                $image_id = $new_image_id;
            } else {
                header("Location: categories.php?error=Failed to upload new photo.");
                exit();
            }
            $stmt->close();
        }
        
        // Update category
        $stmt = $conn->prepare("UPDATE categories SET name = ?, image_id = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sisi", $name, $image_id, $description, $category_id);
        
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
        
        // Get image_id before deleting
        $stmt = $conn->prepare("SELECT image_id FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            // Delete image from database if exists
            if ($category && $category['image_id']) {
                $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                $stmt->bind_param("i", $category['image_id']);
                $stmt->execute();
                $stmt->close();
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
