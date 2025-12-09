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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #1a1a1a;
            color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background-color: #0f0f0f;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding-top: 20px;
            transition: left 0.3s;
            border-right: 1px solid #333;
            z-index: 1000;
        }

        .sidebar.hidden {
            left: -250px;
        }

        .sidebar .brand {
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #f0c040;
            border-bottom: 1px solid #333;
        }

        .sidebar .nav-link {
            color: #b0b0b0;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }

        .sidebar .nav-link:hover {
            background-color: #242424;
            color: #f0c040;
        }

        .sidebar .nav-link.active {
            background-color: #f0c040;
            color: #1a1a1a;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .menu-toggle {
            background: transparent;
            border: 2px solid #f0c040;
            color: #f0c040;
            font-size: 20px;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            background-color: #f0c040;
            color: #1a1a1a;
        }

        .topbar {
            background-color: #242424;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 16px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar h4 {
            color: #f0f0f0;
            margin: 0;
        }

        .data-table {
            background-color: #242424;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #333;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #1f2d1f;
            color: #51cf66;
            border-color: #51cf66;
        }

        .alert-danger {
            background-color: #2d1f1f;
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .btn-close {
            background: transparent;
            border: none;
            color: inherit;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.7;
        }

        .btn-close:hover {
            opacity: 1;
        }

        h5 {
            color: #f0c040;
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .table-responsive {
            overflow-x: auto;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #1a1a1a;
        }

        th {
            padding: 12px;
            text-align: left;
            color: #f0c040;
            font-weight: 600;
            border-bottom: 2px solid #333;
        }

        td {
            padding: 12px;
            color: #f0f0f0;
            border-bottom: 1px solid #333;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #1a1a1a;
        }

        .form-select {
            padding: 6px 10px;
            border: 2px solid #f0c040;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: #f0f0f0;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .form-select:focus {
            outline: none;
            border-color: #ffffff;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid;
        }

        .btn-info {
            background-color: transparent;
            color: #4dabf7;
            border-color: #4dabf7;
        }

        .btn-info:hover {
            background-color: #4dabf7;
            color: #1a1a1a;
        }

        .btn-secondary {
            background-color: transparent;
            color: #b0b0b0;
            border-color: #b0b0b0;
        }

        .btn-secondary:hover {
            background-color: #b0b0b0;
            color: #1a1a1a;
        }

        .text-center {
            text-align: center;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-dialog {
            max-width: 800px;
            width: 90%;
        }

        .modal-content {
            background-color: #242424;
            border-radius: 10px;
            border: 1px solid #333;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            color: #f0c040;
            font-size: 1.5em;
            margin: 0;
        }

        .modal-body {
            padding: 24px;
            color: #f0f0f0;
            max-height: 500px;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid #333;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            foodie
        </div>
        <nav>
            <a class="nav-link" href="index.php">
             Dashboard
            </a>
            <a class="nav-link" href="users.php">
             Users
            </a>
            <a class="nav-link" href="products.php">
             Food Items
            </a>
            <a class="nav-link" href="categories.php">
             Categories
            </a>
            <a class="nav-link active" href="orders.php">
             Orders
            </a>
            <a class="nav-link" href="../../index.php">
             Home
            </a>
            <a class="nav-link" href="../auth/logout.php">
             Logout
            </a>
        </nav>
    </div>

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
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="0" selected>Pending</option>
                                        <option value="1">Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info" 
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
        <h5>Completed Orders</h5>
        <div class="table-responsive">
            <table>
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
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="0">Pending</option>
                                        <option value="1" selected>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info" 
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

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('expanded');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailsModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>