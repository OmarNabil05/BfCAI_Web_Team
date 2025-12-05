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
        // Add new product
        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
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
                header("Location: products.php?error=Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.");
                exit();
            }
            
            // Validate file size
            if ($file_size > $max_size) {
                header("Location: products.php?error=File size exceeds 5MB limit.");
                exit();
            }
            
            // Generate unique filename
            $photo_name = uniqid() . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $photo_name;
            
            if (!move_uploaded_file($file_tmp, $upload_path)) {
                header("Location: products.php?error=Failed to upload photo.");
                exit();
            }
        } else {
            header("Location: products.php?error=Please select a photo.");
            exit();
        }
        
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO items (name, photos, price, description, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $photo_name, $price, $description, $category_id);
        
        if ($stmt->execute()) {
            header("Location: products.php?success=Product added successfully");
        } else {
            // Delete uploaded file if database insert fails
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            header("Location: products.php?error=Failed to add product");
        }
        $stmt->close();
        
    } elseif ($action == 'edit') {
        // Edit existing product
        $product_id = intval($_POST['product_id']);
        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
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
                header("Location: products.php?error=Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.");
                exit();
            }
            
            // Validate file size
            if ($file_size > $max_size) {
                header("Location: products.php?error=File size exceeds 5MB limit.");
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
                header("Location: products.php?error=Failed to upload new photo.");
                exit();
            }
        }
        
        // Update product
        $stmt = $conn->prepare("UPDATE items SET name = ?, photos = ?, price = ?, description = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("ssdsii", $name, $photo_name, $price, $description, $category_id, $product_id);
        
        if ($stmt->execute()) {
            header("Location: products.php?success=Product updated successfully");
        } else {
            header("Location: products.php?error=Failed to update product");
        }
        $stmt->close();
        
    } elseif ($action == 'delete') {
        // Delete product
        $product_id = intval($_POST['product_id']);
        
        // Get photo filename before deleting
        $stmt = $conn->prepare("SELECT photos FROM items WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            // Delete photo file if exists
            if ($product && $product['photos'] && file_exists($upload_dir . $product['photos'])) {
                unlink($upload_dir . $product['photos']);
            }
            header("Location: products.php?success=Product deleted successfully");
        } else {
            header("Location: products.php?error=Failed to delete product");
        }
        $stmt->close();
    }
} else {
    header("Location: products.php");
}

$conn->close();
?>
