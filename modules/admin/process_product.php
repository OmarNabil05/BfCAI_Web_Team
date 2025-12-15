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

// Mime type mapping
$mime_types = array(
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Add new product
        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $description = trim($_POST['description']);
        
        // Handle file upload to database
        $image_id = null;
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
            
            // Read image data
            $image_data = file_get_contents($file_tmp);
            $mime_type = $mime_types[$file_ext];
            
            // Insert image into database
            $img_stmt = $conn->prepare("INSERT INTO images (mime_type, data, original_name) VALUES (?, ?, ?)");
            $img_stmt->bind_param("sss", $mime_type, $image_data, $file_name);
            
            if (!$img_stmt->execute()) {
                header("Location: products.php?error=Failed to save image to database.");
                exit();
            }
            
            $image_id = $img_stmt->insert_id;
            $img_stmt->close();
        } else {
            header("Location: products.php?error=Please select a photo.");
            exit();
        }
        
        // Insert new product with image_id
        $stmt = $conn->prepare("INSERT INTO items (name, price, description, category_id, image_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsii", $name, $price, $description, $category_id, $image_id);
        
        if ($stmt->execute()) {
            header("Location: products.php?success=Product added successfully");
        } else {
            // Delete image from DB if product insert fails
            if ($image_id) {
                $del_stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                $del_stmt->bind_param("i", $image_id);
                $del_stmt->execute();
                $del_stmt->close();
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
        $current_image_id = isset($_POST['current_image_id']) ? intval($_POST['current_image_id']) : null;
        
        $image_id = $current_image_id;
        
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
            
            // Read image data
            $image_data = file_get_contents($file_tmp);
            $mime_type = $mime_types[$file_ext];
            
            // Insert new image into database
            $img_stmt = $conn->prepare("INSERT INTO images (mime_type, data, original_name) VALUES (?, ?, ?)");
            $img_stmt->bind_param("sss", $mime_type, $image_data, $file_name);
            
            if ($img_stmt->execute()) {
                $image_id = $img_stmt->insert_id;
                
                // Delete old image if exists
                if ($current_image_id) {
                    $del_stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                    $del_stmt->bind_param("i", $current_image_id);
                    $del_stmt->execute();
                    $del_stmt->close();
                }
            } else {
                header("Location: products.php?error=Failed to upload new image.");
                exit();
            }
            $img_stmt->close();
        }
        
        // Update product
        $stmt = $conn->prepare("UPDATE items SET name = ?, price = ?, description = ?, category_id = ?, image_id = ? WHERE id = ?");
        $stmt->bind_param("sdsiii", $name, $price, $description, $category_id, $image_id, $product_id);
        
        if ($stmt->execute()) {
            header("Location: products.php?success=Product updated successfully");
        } else {
            header("Location: products.php?error=Failed to update product");
        }
        $stmt->close();
        
    } elseif ($action == 'delete') {
        // Delete product
        $product_id = intval($_POST['product_id']);
        
        // Get image_id before deleting product
        $stmt = $conn->prepare("SELECT image_id FROM items WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            // Delete image from images table if exists
            if ($product && $product['image_id']) {
                $del_stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
                $del_stmt->bind_param("i", $product['image_id']);
                $del_stmt->execute();
                $del_stmt->close();
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
