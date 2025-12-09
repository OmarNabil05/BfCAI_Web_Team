<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['item_id'])) {
    die("No item selected.");
}

$item_id = intval($_POST['item_id']);
$quantity = 1;

$insert_order = "INSERT INTO orders (user_id, status, city, address, created_at) 
                 VALUES (?, 0, '', '', NOW())";

$stmt = $conn->prepare($insert_order);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$order_id = $stmt->insert_id;

$insert_item = "INSERT INTO order_items (order_id, item_id, quantity) VALUES (?, ?, ?)";

$stmt2 = $conn->prepare($insert_item);
if (!$stmt2) {
    die("Prepare failed (order_items): " . $conn->error);
}
$stmt2->bind_param("iii", $order_id, $item_id, $quantity);
if (!$stmt2->execute()) {
    die("Execute failed (order_items): " . $stmt2->error);
}

header("Location: menu.php?order=success");
exit();
?>
