<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['item_id']) || empty($_POST['item_id'])) {
    header("Location: menu.php?error=no_item");
    exit();
}

$item_id = intval($_POST['item_id']);
$quantity = 1;

// Check if there's an active order (payment_status = 0) for this user
$check_order = "SELECT id FROM orders WHERE user_id = ? AND payment_status = 0 LIMIT 1";
$stmt = $conn->prepare($check_order);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $order_id = $order['id'];
} else {
    // Create a new order
    $insert_order = "INSERT INTO orders (user_id, payment_status, city, address, created_at) VALUES (?, 0, '', '', NOW())";
    $stmt2 = $conn->prepare($insert_order);
    $stmt2->bind_param("i", $user_id);
    if (!$stmt2->execute()) {
        header("Location: menu.php?error=order_creation_failed");
        exit();
    }
    $order_id = $stmt2->insert_id;
}

// Check if this item already exists in the order
$check_item = "SELECT id, quantity FROM order_items WHERE order_id = ? AND item_id = ? LIMIT 1";
$stmt_check = $conn->prepare($check_item);
$stmt_check->bind_param("ii", $order_id, $item_id);
$stmt_check->execute();
$item_result = $stmt_check->get_result();

if ($item_result->num_rows > 0) {
    // Item exists, update quantity
    $update_qty = "UPDATE order_items SET quantity = quantity + ? WHERE order_id = ? AND item_id = ?";
    $stmt4 = $conn->prepare($update_qty);
    $stmt4->bind_param("iii", $quantity, $order_id, $item_id);
    if (!$stmt4->execute()) {
        header("Location: menu.php?error=update_failed");
        exit();
    }
} else {
    // Item doesn't exist, insert new
    $insert_item = "INSERT INTO order_items (order_id, item_id, quantity) VALUES (?, ?, ?)";
    $stmt3 = $conn->prepare($insert_item);
    $stmt3->bind_param("iii", $order_id, $item_id, $quantity);
    if (!$stmt3->execute()) {
        header("Location: menu.php?error=add_failed");
        exit();
    }
}

header("Location: menu.php?order=success");
exit();
?>
?>
