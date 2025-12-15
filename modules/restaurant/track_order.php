<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once '../../config/db.php';
$user_id = (int) ($_SESSION['user_id'] ?? 0);
if (!$user_id) {
    header("Location:../auth/login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {

    $order_id = (int) $_POST['order_id'];

    $sql = "UPDATE orders 
    SET status = 4
    WHERE id = ? 
      AND user_id = ?
      AND status IN (1,2)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: track_order.php");
    exit;
}


$sql = "
SELECT o.id as order_id, o.status, o.city, o.address, o.created_at,
       oi.quantity, i.name as item_name
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
LEFT JOIN items i ON oi.item_id = i.id
WHERE o.user_id = ?
AND o.status > 0
ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();


$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[$row['order_id']]['info'] = [
            'status' => $row['status'],
            'city' => $row['city'],
            'address' => $row['address'],
            'created_at' => $row['created_at']
        ];
        if ($row['item_name']) {
            $orders[$row['order_id']]['items'][] = [
                'name' => $row['item_name'],
                'quantity' => $row['quantity']
            ];
        }
    }
}

$status_text = [
    1 => "Pending",
    2 => "Preparing",
    3 => "Delivered",
    4 => "Cancelled"
];

$status_colors = [
    "Pending" => "text-yellow-400",
    "Preparing" => "text-blue-400",
    "Delivered" => "text-green-400",
    "Cancelled" => "text-red-500"
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#0b0d10] text-white min-h-screen">

    <?php
    ob_start();
    include("navbar.php"); ?>

    <div class="max-w-5xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-center mb-10 text-[#fac564]">
            Your Orders
        </h2>

        <?php if (!empty($orders)): ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order_id => $data):
                    $status_name = $status_text[$data['info']['status']];
                ?>
                    <div class="bg-[#121618] rounded-2xl p-6 shadow-lg hover:shadow-xl transition">


                        <div class="flex flex-wrap justify-between gap-4 border-b border-gray-700 pb-4">
                            <div>
                                <p class="text-sm text-gray-400">Order ID</p>
                                <p class="font-bold">#<?= $order_id ?></p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-400">Status</p>
                                <p class="font-bold <?= $status_colors[$status_name] ?>">
                                    <?= $status_name ?>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-400">City</p>
                                <p><?= $data['info']['city'] ?></p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-400">Created At</p>
                                <p><?= date("d M Y, h:i A", strtotime($data['info']['created_at'])) ?></p>
                            </div>
                        </div>


                        <p class="mt-4 text-gray-300">
                            <span class="text-gray-400">Address:</span>
                            <?= $data['info']['address'] ?>
                        </p>


                        <?php if (!empty($data['items'])): ?>
                            <div class="mt-6">
                                <p class="font-semibold text-[#fac564] mb-2">Items</p>
                                <ul class="space-y-2">
                                    <?php foreach ($data['items'] as $item): ?>
                                        <li class="flex justify-between bg-[#0b0d10] p-3 rounded-lg">
                                            <span><?= $item['name'] ?></span>
                                            <span class="text-gray-400">x<?= $item['quantity'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if (in_array($data['info']['status'], [1, 2])): ?>

                            <form method="POST" class="mt-4">
                                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                                <button
                                    type="submit"
                                    name="cancel_order"
                                    onclick="return confirm('Are you sure you want to cancel this order?')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                    Cancel Order
                                </button>
                            </form>

                        <?php elseif ($data['info']['status'] == 4): ?>

                            <span class="text-sm text-red-400 mt-4 inline-block">
                                This order has been cancelled
                            </span>

                        <?php endif; ?>


                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-400">
                You don’t have any orders yet
            </p>
        <?php endif; ?>
    </div>

    <?php include("footer.php"); ?>

</body>

</html>