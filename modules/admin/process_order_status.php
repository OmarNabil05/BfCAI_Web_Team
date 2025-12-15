<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    
    // Check if updating order_status
    if (isset($_POST['order_status'])) {
        $order_status = intval($_POST['order_status']);
        
        // Validate order_status (0-4: Received, Preparing, In Delivery, Delivered, Cancelled)
        if ($order_status < 0 || $order_status > 4) {
            header("Location: orders.php?error=Invalid order status value");
            exit();
        }
        
        // Update order status
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("ii", $order_status, $order_id);
        
        if ($stmt->execute()) {
            $status_labels = ['Received', 'Preparing', 'In Delivery', 'Delivered', 'Cancelled'];
            $status_text = $status_labels[$order_status];
            header("Location: orders.php?success=Order status updated to " . $status_text);
        } else {
            header("Location: orders.php?error=Failed to update order status");
        }
        
        $stmt->close();
    } else {
        header("Location: orders.php?error=Missing order status parameter");
    }
} else {
    header("Location: orders.php");
}

$conn->close();
?>
