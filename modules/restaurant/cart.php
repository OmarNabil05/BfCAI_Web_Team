<?php
// cart.php
require_once '../../config/db.php';
include("Navbar.php");

$user_id = $_SESSION['user_id'];

// PDO connection
$pdo = new PDO("mysql:host=localhost;dbname=my_store", "root", "");

// Fetch active order
$orderQuery = $pdo->prepare("
    SELECT o.id, o.status, o.created_at, o.city, o.address, 
           o.user_id, SUM(oi.quantity * i.price) as subtotal
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN items i ON oi.item_id = i.id
    WHERE o.user_id = ? AND o.status = 0
    GROUP BY o.id
    LIMIT 1
");

$orderQuery->execute([$user_id]);
$order = $orderQuery->fetch(PDO::FETCH_ASSOC);

// If no active order, create one
if (!$order) {
    $createOrderQuery = $pdo->prepare("
        INSERT INTO orders (user_id, status, created_at) 
        VALUES (?, 0, NOW())
    ");
    $createOrderQuery->execute([$user_id]);
    $order_id = $pdo->lastInsertId();
    $orderQuery->execute([$user_id]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);
}

$order_id = $order['id'];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_quantity'])) {
        $item_id = $_POST['item_id'];
        $action = $_POST['action'];

        $checkItemQuery = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? AND item_id = ?");
        $checkItemQuery->execute([$order_id, $item_id]);
        $existingItem = $checkItemQuery->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            $new_quantity = $existingItem['quantity'];
            if ($action === 'increment') $new_quantity++;
            elseif ($action === 'decrement' && $new_quantity > 1) $new_quantity--;

            $updateQuery = $pdo->prepare("UPDATE order_items SET quantity = ? WHERE order_id = ? AND item_id = ?");
            $updateQuery->execute([$new_quantity, $order_id, $item_id]);
        }
    }

    // Delete order item
    if (isset($_POST['delete_item'])) {
        $item_id = $_POST['item_id'];
        $deleteQuery = $pdo->prepare("DELETE FROM order_items WHERE order_id = ? AND item_id = ?");
        $deleteQuery->execute([$order_id, $item_id]);
    }

    // Checkout
    if (isset($_POST['checkout'])) {
        $city = $_POST['city'];
        $address = $_POST['address'];

        if (!empty($city) && !empty($address)) {
            $updateOrderQuery = $pdo->prepare("
                UPDATE orders 
                SET status = 1, city = ?, address = ? 
                WHERE id = ? AND user_id = ?
            ");
            $updateOrderQuery->execute([$city, $address, $order_id, $user_id]);

            $_SESSION['success_message'] = "Order placed successfully!";
        }
    }
}

// Fetch order items
$itemsQuery = $pdo->prepare("
    SELECT oi.*, i.name, i.price, i.photos as image_url
    FROM order_items oi
    JOIN items i ON oi.item_id = i.id
    WHERE oi.order_id = ?
");
$itemsQuery->execute([$order_id]);
$order_items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$delivery_fee = 5.00;
$discount = 0.00;
$total = $subtotal + $delivery_fee - $discount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Cart</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
.input {
    @apply w-full px-4 py-2 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 text-white;
}
</style>
</head>
<body class="bg-black text-white">
<section class="pt-10 pb-20 max-w-6xl mx-auto">

    <?php if (empty($order_items)): ?>
        <!-- Empty Cart UI -->
        <div class="flex flex-col items-center justify-center h-[60vh]">
            <h2 class="text-3xl font-bold text-[#fac564] mb-4">Your Cart is Empty</h2>
            <a href="menu.php" class="text-[#fac564] font-semibold hover:underline">Continue Shopping</a>
        </div>
    <?php else: ?>
        <!-- Cart with items -->
        <a href="menu.php" class="inline-flex items-center text-[#fac564] font-semibold hover:opacity-80 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
            Continue shopping
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
            <!-- Cart Items -->
            <div class="lg:col-span-3">
                <h2 class="border-b font-semibold text-xl py-3 text-[#fac564]">Cart</h2>
                <div>
                    <?php foreach ($order_items as $item): ?>
                        <div class="grid grid-cols-8 gap-4 border-b border-gray-700 pt-2 pb-4 items-center">
                            <div class="col-span-2">
                                <div class="bg-cover bg-center w-[100px] h-[100px] rounded-[10%]" 
                                    style="background-image: url('<?php echo htmlspecialchars($item['image_url']); ?>');"></div>
                            </div>
                            <div class="col-span-3 px-4">
                                <p class="font-semibold text-[12px] lg:text-[18px] "><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-gray-400">$<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            <div class="flex flex-col items-center">
                                <p class="font-semibold text-[12px] lg:text-[18px]">Quantity</p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <form method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <input type="hidden" name="action" value="decrement">
                                        <button type="submit" name="update_quantity" class="bg-white/10 w-5 h-5 lg:w-8 lg:h-8 flex items-center justify-center rounded-full hover:bg-white/5">-</button>
                                    </form>
                                    <span><?php echo $item['quantity']; ?></span>
                                    <form method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <input type="hidden" name="action" value="increment">
                                        <button type="submit" name="update_quantity" class="bg-white/10 w-5 h-5 lg:w-8 lg:h-8 flex items-center justify-center rounded-full hover:bg-white/5">+</button>
                                    </form>
                                </div>
                            </div>
                            <div class="text-right font-semibold text-[15px] lg:text-[18px] px-1">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            <!-- Trash Button -->
                            <div>
                                <form method="POST" idate>
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <button type="submit" name="delete_item" class="hover:text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13v5m6-5v5M4 7h16m-1 0-1 12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 7m4 0V4h6v3"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="grid grid-cols-8 pt-2">
                        <div class="col-span-7 flex justify-between font-semibold">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-8 pt-1">
                        <div class="col-span-7 flex justify-between text-gray-400">
                            <span>Delivery fee</span>
                            <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-8 pt-1 pb-2 border-b border-gray-700">
                        <div class="col-span-7 flex justify-between text-gray-400">
                            <span>Discount</span>
                            <span>-$<?php echo number_format($discount, 2); ?></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-8 pt-2">
                        <div class="col-span-7 flex justify-between font-semibold">
                            <span>Total</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout -->
            <div class="lg:col-span-2 mt-6 lg:mt-0">
                <h2 class="font-bold py-3 text-[#fac564] text-xl">Check Out</h2>
                <div class="rounded-xl p-6 bg-white/10">
                    <form method="POST" class="flex flex-col gap-3 mt-3">
                        <label class="block mb-1">City</label>
                        <input type="text" name="city" placeholder="City" class="input border border-[#fac564] py-2 rounded-lg px-2" value="<?php echo htmlspecialchars($order['city'] ?? ''); ?>" required>

                        <label class="block mb-1 mt-3">Address</label>
                        <input type="text" name="address" placeholder="Address" class="input border border-[#fac564] py-2 rounded-lg px-2" value="<?php echo htmlspecialchars($order['address'] ?? ''); ?>" required>

                        <button type="submit" name="checkout" class="bg-[#fac564] text-black w-full py-2 rounded-lg mt-4 hover:bg-[#f4b541]">
                            Pay $<?php echo number_format($total, 2); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

</section>

</body>

</html>

<?php include("footer.php"); ?>
