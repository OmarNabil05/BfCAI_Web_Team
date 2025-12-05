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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
        }
        .sidebar {
            background: #1e293b;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar .brand {
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #334155;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        .sidebar.hidden {
            left: -250px;
        }
        .menu-toggle {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
            margin-bottom: 24px;
        }
        .data-table {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            🛒 My Store
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php">
                <span>📊</span> Dashboard
            </a>
            <a class="nav-link" href="users.php">
                <span>👥</span> Users
            </a>
            <a class="nav-link" href="products.php">
                <span>📦</span> Products
            </a>
            <a class="nav-link" href="categories.php">
                <span>📁</span> Categories
            </a>
            <a class="nav-link active" href="orders.php">
                <span>🛍️</span> Orders
            </a>
            <a class="nav-link" href="../../index.php">
                <span>🏠</span> Home
            </a>
            <a class="nav-link" href="../auth/logout.php">
                <span>🚪</span> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    ☰
                </button>
                <h4 class="mb-0">Manage Orders</h4>
            </div>
        </div>

        <div class="data-table">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Pending Orders -->
        <h5 class="mb-3">Pending Orders</h5>
        <div class="table-responsive mb-5">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
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
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="process_order_status.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="0" selected>Pending</option>
                                        <option value="1">Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#orderDetailsModal"
                                        onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                    View Details
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
        <h5 class="mb-3">Completed Orders</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
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
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="process_order_status.php" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="0">Pending</option>
                                        <option value="1" selected>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#orderDetailsModal"
                                        onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                    View Details
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
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrderDetails(orderId) {
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
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('expanded');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
