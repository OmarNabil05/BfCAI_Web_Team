<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

$admin_name = $_SESSION['user_name'];

// Fetch statistics
$users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$products_count = $conn->query("SELECT COUNT(*) as count FROM items")->fetch_assoc()['count'];
$categories_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$orders_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];

// Fetch recent users
$recent_users_sql = "SELECT id, name, email, role FROM users ORDER BY id DESC LIMIT 8";
$recent_users = $conn->query($recent_users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - My Store</title>
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
        }
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .icon-blue { background: #dbeafe; color: #3b82f6; }
        .icon-green { background: #dcfce7; color: #22c55e; }
        .icon-purple { background: #f3e8ff; color: #a855f7; }
        .icon-pink { background: #fce7f3; color: #ec4899; }
        .data-table {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .table th {
            border-bottom: 2px solid #e5e7eb;
            color: #6b7280;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            padding: 12px;
        }
        .table td {
            padding: 16px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
        }
        .badge-active {
            background: #dcfce7;
            color: #16a34a;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6b7280;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 24px;
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
            <a class="nav-link active" href="index.php">
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
            <a class="nav-link" href="orders.php">
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
        <!-- Top Bar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    ☰
                </button>
                <h4 class="mb-0">Dashboard</h4>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mt-3">
            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size: 14px;">Total Users</div>
                            <h2 class="mb-0"><?php echo number_format($users_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-blue">
                            👥
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size: 14px;">Total Orders</div>
                            <h2 class="mb-0"><?php echo number_format($orders_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-green">
                            🛒
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size: 14px;">Categories</div>
                            <h2 class="mb-0"><?php echo number_format($categories_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-purple">
                            📁
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size: 14px;">Available Products</div>
                            <h2 class="mb-0"><?php echo number_format($products_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-pink">
                            🔒
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="data-table mt-4">
            <h5 class="mb-4">Recent Users</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>STATUS</th>
                        <th>ROLE</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $recent_users->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 500;"><?php echo htmlspecialchars($user['name']); ?></div>
                                    <div style="font-size: 12px; color: #9ca3af;">ID: <?php echo $user['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                        </td>
                        <td>
                            <span class="badge-active">Active</span>
                        </td>
                        <td style="color: #6b7280;">
                            <?php echo $user['role'] == 1 ? 'Admin' : 'Customer'; ?>
                        </td>
                        <td>
                            <a href="users.php" style="color: #3b82f6; text-decoration: none; font-weight: 500;">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mt-3">
            <div class="col-md-3">
                <a href="users.php" style="text-decoration: none;">
                    <div class="stats-card text-center">
                        <div style="font-size: 32px; margin-bottom: 10px;">👥</div>
                        <div style="font-weight: 600;">Manage Users</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="products.php" style="text-decoration: none;">
                    <div class="stats-card text-center">
                        <div style="font-size: 32px; margin-bottom: 10px;">📦</div>
                        <div style="font-weight: 600;">Manage Products</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="categories.php" style="text-decoration: none;">
                    <div class="stats-card text-center">
                        <div style="font-size: 32px; margin-bottom: 10px;">📁</div>
                        <div style="font-weight: 600;">Manage Categories</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="orders.php" style="text-decoration: none;">
                    <div class="stats-card text-center">
                        <div style="font-size: 32px; margin-bottom: 10px;">📋</div>
                        <div style="font-weight: 600;">Manage Orders</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('expanded');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
