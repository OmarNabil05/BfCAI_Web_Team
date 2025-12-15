<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    exit('Access denied');
}

require_once '../../config/db.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // Fetch order details
    $order_sql = "SELECT orders.*, users.name as user_name, users.email as user_email, users.phone_number 
                  FROM orders 
                  LEFT JOIN users ON orders.user_id = users.id 
                  WHERE orders.id = ?";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order = $order_result->fetch_assoc();
    
    if ($order) {
        // Fetch order items
        $items_sql = "SELECT order_items.*, items.name as item_name, items.price 
                      FROM order_items 
                      LEFT JOIN items ON order_items.item_id = items.id 
                      WHERE order_items.order_id = ?";
        $stmt = $conn->prepare($items_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
        
        echo '<div class="mb-3">';
        echo '<h6>Customer Information</h6>';
        echo '<table class="table table-sm">';
        echo '<tr><td><strong>Name:</strong></td><td>' . htmlspecialchars($order['user_name']) . '</td></tr>';
        echo '<tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($order['user_email']) . '</td></tr>';
        echo '<tr><td><strong>Phone:</strong></td><td>' . htmlspecialchars($order['phone_number']) . '</td></tr>';
        echo '<tr><td><strong>Order Date:</strong></td><td>' . date('M d, Y H:i', strtotime($order['created_at'])) . '</td></tr>';
        
        // Display Order Status
        $status_labels = ['Received', 'Preparing', 'In Delivery', 'Delivered', 'Cancelled'];
        $order_status_text = $status_labels[$order['order_status']] ?? 'Unknown';
        echo '<tr><td><strong>Order Status:</strong></td><td>' . $order_status_text . '</td></tr>';
        
        echo '<tr><td><strong>Payment Status:</strong></td><td>' . ($order['payment_status'] == 1 ? 'Completed' : 'Pending') . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        echo '<div>';
        echo '<h6>Order Items</h6>';
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Item</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>';
        echo '<tbody>';
        
        $total = 0;
        while ($item = $items_result->fetch_assoc()) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            echo '<tr>';
            echo '<td>' . htmlspecialchars($item['item_name']) . '</td>';
            echo '<td>$' . number_format($item['price'], 2) . '</td>';
            echo '<td>' . $item['quantity'] . '</td>';
            echo '<td>$' . number_format($subtotal, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '<tfoot><tr><th colspan="3" class="text-end">Total:</th><th>$' . number_format($total, 2) . '</th></tr></tfoot>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">Order not found</div>';
    }
    
    $stmt->close();
}

$conn->close();
?>
