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

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f0c040 0%, #ff9800 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #1a1a1a;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stats-card {
            background-color: #242424;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            border-color: #f0c040;
            box-shadow: 0 8px 20px rgba(240, 192, 64, 0.2);
        }

        .stats-card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-info h2 {
            color: #f0f0f0;
            font-size: 2em;
            margin-top: 5px;
        }

        .stats-info .label {
            color: #b0b0b0;
            font-size: 14px;
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

        .icon-blue { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .icon-green { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .icon-purple { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
        .icon-pink { background: rgba(236, 72, 153, 0.2); color: #ec4899; }

        .data-table {
            background-color: #242424;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 24px;
            margin-top: 24px;
        }

        .data-table h5 {
            color: #f0c040;
            margin-bottom: 20px;
            font-size: 1.3em;
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
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid #333;
        }

        td {
            padding: 16px 12px;
            color: #f0f0f0;
            border-bottom: 1px solid #333;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #1a1a1a;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-cell .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .user-info .name {
            font-weight: 500;
            color: #f0f0f0;
        }

        .user-info .id {
            font-size: 12px;
            color: #b0b0b0;
        }

        .badge-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .role-text {
            color: #b0b0b0;
        }

        .edit-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .edit-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .quick-action-card {
            background-color: #242424;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 30px 24px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: #f0f0f0;
        }

        .quick-action-card:hover {
            transform: translateY(-4px);
            border-color: #f0c040;
            box-shadow: 0 8px 20px rgba(240, 192, 64, 0.2);
        }

        .quick-action-card .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .quick-action-card .title {
            font-weight: 600;
            color: #f0f0f0;
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

            .row {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
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
            🛒 My Store
        </div>
        <nav>
            <a class="nav-link active" href="index.php">
                <span>📊</span> Dashboard
            </a>
            <a class="nav-link" href="users.php">
                <span>👥</span> Users
            </a>
            <a class="nav-link" href="products.php">
                <span>🍔</span> Food Items
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
        <div class="topbar">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    ☰
                </button>
                <h4>Dashboard</h4>
            </div>
            <div class="user-avatar">
                <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-info">
                        <div class="label">Total Users</div>
                        <h2><?php echo number_format($users_count); ?></h2>
                    </div>
                    <div class="stats-icon icon-blue">
                        👥
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-info">
                        <div class="label">Total Orders</div>
                        <h2><?php echo number_format($orders_count); ?></h2>
                    </div>
                    <div class="stats-icon icon-green">
                        🛒
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-info">
                        <div class="label">Categories</div>
                        <h2><?php echo number_format($categories_count); ?></h2>
                    </div>
                    <div class="stats-icon icon-purple">
                        📁
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card-content">
                    <div class="stats-info">
                        <div class="label">Available Products</div>
                        <h2><?php echo number_format($products_count); ?></h2>
                    </div>
                    <div class="stats-icon icon-pink">
                        🍔
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="data-table">
            <h5>Recent Users</h5>
            <table>
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
                            <div class="user-cell">
                                <div class="avatar">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                                <div class="user-info">
                                    <div class="name"><?php echo htmlspecialchars($user['name']); ?></div>
                                    <div class="id">ID: <?php echo $user['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge-active">Active</span>
                        </td>
                        <td class="role-text">
                            <?php echo $user['role'] == 1 ? 'Admin' : 'Customer'; ?>
                        </td>
                        <td>
                            <a href="users.php" class="edit-link">Edit</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="users.php" class="quick-action-card">
                <div class="icon">👥</div>
                <div class="title">Manage Users</div>
            </a>
            <a href="products.php" class="quick-action-card">
                <div class="icon">🍔</div>
                <div class="title">Manage Food Items</div>
            </a>
            <a href="categories.php" class="quick-action-card">
                <div class="icon">📁</div>
                <div class="title">Manage Categories</div>
            </a>
            <a href="orders.php" class="quick-action-card">
                <div class="icon">📋</div>
                <div class="title">Manage Orders</div>
            </a>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('expanded');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>