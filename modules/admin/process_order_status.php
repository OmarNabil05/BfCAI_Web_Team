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
    $status = intval($_POST['status']);
    
    // Validate status
    if ($status !== 0 && $status !== 1) {
        header("Location: orders.php?error=Invalid status value");
        exit();
    }
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $order_id);
    
    if ($stmt->execute()) {
        $status_text = $status == 1 ? 'Completed' : 'Pending';
        header("Location: orders.php?success=Order status updated to " . $status_text);
    } else {
        header("Location: orders.php?error=Failed to update order status");
    }
    
    $stmt->close();
} else {
    header("Location: orders.php");
}

$conn->close();
?>
