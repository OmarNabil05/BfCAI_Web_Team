<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$item_id = intval($_POST['item_id']);
$quantity = 1;

$check_order = "SELECT id FROM orders WHERE user_id = ? AND status = 0 LIMIT 1";
$stmt = $conn->prepare($check_order);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $order_id = $order['id'];
} else {
    $insert_order = "INSERT INTO orders (user_id, status, city, address, created_at) VALUES (?, 0, '', '', NOW())";
    $stmt2 = $conn->prepare($insert_order);
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $order_id = $stmt2->insert_id;
}

$insert_item = "INSERT INTO order_items (order_id, item_id, quantity) VALUES (?, ?, ?)";
$stmt3 = $conn->prepare($insert_item);
$stmt3->bind_param("iii", $order_id, $item_id, $quantity);

if (!$stmt3->execute()) {
    if ($conn->errno == 1062) {
        $update_qty = "UPDATE order_items SET quantity = quantity + ? WHERE order_id = ? AND item_id = ?";
        $stmt4 = $conn->prepare($update_qty);
        $stmt4->bind_param("iii", $quantity, $order_id, $item_id);
        $stmt4->execute();
    } else {
        die("Error: " . $stmt3->error);
    }
}

header("Location: menu.php?order=success");
exit();
?>
