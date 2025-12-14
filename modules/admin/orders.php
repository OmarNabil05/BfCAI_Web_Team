<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// Fetch pending orders
$pending_sql = "SELECT orders.*, users.name as user_name, users.email as user_email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        WHERE orders.status = 0
        ORDER BY orders.created_at DESC";
$pending_result = $conn->query($pending_sql);

// Fetch completed orders
$completed_sql = "SELECT orders.*, users.name as user_name, users.email as user_email 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        WHERE orders.status = 1
        ORDER BY orders.created_at DESC";
$completed_result = $conn->query($completed_sql);
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
                <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
            </div>
        <?php endif; ?>

        <!-- Pending Orders -->
        <h5>Pending Orders</h5>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php if ($pending_result->num_rows > 0): ?>
                        <?php while($order = $pending_result->fetch_assoc()): ?>
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
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="0" selected>Pending</option>
                                        <option value="1">Completed</option>
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
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No pending orders</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Completed Orders -->
        <h5>Completed Orders</h5>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php if ($completed_result->num_rows > 0): ?>
                        <?php while($order = $completed_result->fetch_assoc()): ?>
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
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="0">Pending</option>
                                        <option value="1" selected>Completed</option>
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
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No completed orders</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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