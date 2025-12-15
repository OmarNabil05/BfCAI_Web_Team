<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// Fetch all orders grouped by order_status
$all_orders_sql = "SELECT orders.*, users.name as user_name, users.email as user_email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        WHERE orders.payment_status = 1
        ORDER BY orders.created_at DESC";
$all_orders_result = $conn->query($all_orders_sql);

// Group orders by order_status
// 0: Received, 1: Preparing, 2: In delivery, 3: Delivered, 4: Cancelled
$orders_by_status = [
    0 => [], // Received
    1 => [], // Preparing
    2 => [], // In delivery
    3 => [], // Delivered
    4 => []  // Cancelled
];

if ($all_orders_result && $all_orders_result->num_rows > 0) {
    while ($order = $all_orders_result->fetch_assoc()) {
        $status = intval($order['order_status']);
        if (isset($orders_by_status[$status])) {
            $orders_by_status[$status][] = $order;
        }
    }
}

// Status labels
$status_labels = [
    0 => 'Received',
    1 => 'Preparing',
    2 => 'In Delivery',
    3 => 'Delivered',
    4 => 'Cancelled'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="components/styles.css">
    
    <style>
        /* Page specific styles only */
        .modal-dialog {
            max-width: 800px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border: 4px solid #333;
            border-top-color: #f0c040;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .main-content {
                margin-left: 0;
            }

            .topbar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    ☰
                </button>
                <h4>Manage Orders</h4>
            </div>
        </div>

        <div class="data-table">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        <?php endif; ?>

        <?php
        // Loop through each order status and display tables
        foreach ([0, 1, 2] as $status): // Received, Preparing, In delivery
            $orders = $orders_by_status[$status];
        ?>
        <!-- <?php echo $status_labels[$status]; ?> Orders -->
        <h5 class="mt-4"><?php echo $status_labels[$status]; ?> Orders</h5>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Order Date</th>
                        <th>Order Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($order['city']); ?></td>
                            <td><?php echo htmlspecialchars($order['address']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="process_order_status.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="0" <?php echo $order['order_status'] == 0 ? 'selected' : ''; ?>>Received</option>
                                        <option value="1" <?php echo $order['order_status'] == 1 ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="2" <?php echo $order['order_status'] == 2 ? 'selected' : ''; ?>>In Delivery</option>
                                        <option value="3" <?php echo $order['order_status'] == 3 ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="4" <?php echo $order['order_status'] == 4 ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No <?php echo strtolower($status_labels[$status]); ?> orders</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>

        <!-- Delivered Orders (Collapsible) -->
        <div class="mt-4">
            <h5>
                <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#deliveredOrders" aria-expanded="false" aria-controls="deliveredOrders">
                    <i class="bi bi-chevron-down"></i> Delivered Orders (<?php echo count($orders_by_status[3]); ?>)
                </button>
            </h5>
            <div class="collapse" id="deliveredOrders">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Order Date</th>
                                <th>Order Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders_by_status[3]) > 0): ?>
                                <?php foreach($orders_by_status[3] as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($order['city']); ?></td>
                                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" action="process_order_status.php" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="0" <?php echo $order['order_status'] == 0 ? 'selected' : ''; ?>>Received</option>
                                                <option value="1" <?php echo $order['order_status'] == 1 ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="2" <?php echo $order['order_status'] == 2 ? 'selected' : ''; ?>>In Delivery</option>
                                                <option value="3" <?php echo $order['order_status'] == 3 ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="4" <?php echo $order['order_status'] == 4 ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No delivered orders</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cancelled Orders (Collapsible) -->
        <div class="mt-4">
            <h5>
                <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#cancelledOrders" aria-expanded="false" aria-controls="cancelledOrders">
                    <i class="bi bi-chevron-down"></i> Cancelled Orders (<?php echo count($orders_by_status[4]); ?>)
                </button>
            </h5>
            <div class="collapse" id="cancelledOrders">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Address</th>
                                <th>Order Date</th>
                                <th>Order Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders_by_status[4]) > 0): ?>
                                <?php foreach($orders_by_status[4] as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($order['city']); ?></td>
                                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" action="process_order_status.php" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="0" <?php echo $order['order_status'] == 0 ? 'selected' : ''; ?>>Received</option>
                                                <option value="1" <?php echo $order['order_status'] == 1 ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="2" <?php echo $order['order_status'] == 2 ? 'selected' : ''; ?>>In Delivery</option>
                                                <option value="3" <?php echo $order['order_status'] == 3 ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="4" <?php echo $order['order_status'] == 4 ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No cancelled orders</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" onclick="closeModal()">×</button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            document.getElementById('orderDetailsModal').classList.add('show');
            
            // Fetch order details via AJAX
            fetch('get_order_details.php?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderDetailsContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('orderDetailsContent').innerHTML = '<div class="alert alert-danger">Error loading order details</div>';
                });
        }

        function closeModal() {
            document.getElementById('orderDetailsModal').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailsModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin Scripts -->
    <script src="components/scripts.js"></script>
</body>
</html>
<?php $conn->close(); ?>