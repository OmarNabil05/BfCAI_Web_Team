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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --dark-bg: #1a1a1a;
            --card-bg: #242424;
            --sidebar-bg: #0f0f0f;
            --gold: #f0c040;
            --border-color: #333;
            --text-light: #f0f0f0;
            --text-muted: #b0b0b0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding-top: 20px;
            transition: left 0.3s;
            border-right: 1px solid var(--border-color);
            z-index: 1000;
        }

        .sidebar.hidden {
            left: -250px;
        }

        .sidebar .brand {
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            color: var(--gold);
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: var(--card-bg);
            color: var(--gold);
        }

        .sidebar .nav-link.active {
            background-color: var(--gold);
            color: var(--dark-bg);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Top Navigation Bar */
        .top-navbar {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 16px 24px;
            margin-bottom: 24px;
        }

        .menu-toggle {
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            font-size: 20px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            background-color: var(--gold);
            color: var(--dark-bg);
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

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold) 0%, #ff9800 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--dark-bg);
        }

        .stats-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
            color: var(--text-light);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .stats-card h2 {
            color: #fdfdfd;
            font-size: 2.25rem;
        }

        .stats-card .text-muted {
            color: rgba(240, 240, 240, 0.65) !important;
            letter-spacing: 0.05em;
        }

        .data-table {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 24px;
        }

        .data-table h5 {
            color: var(--gold);
            margin-bottom: 20px;
            font-size: 1.3em;
        }

        .table {
            color: var(--text-light);
            width: 100%;
            border-collapse: collapse;
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--sidebar-bg);
            color: var(--gold);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border-color);
        }

        .table tbody td {
            padding: 16px 12px;
            color: var(--text-light);
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: var(--sidebar-bg);
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
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-shop"></i> foodie
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="index.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link" href="users.php">
                <i class="bi bi-people"></i> Users
            </a>
            <a class="nav-link" href="products.php">
                <i class="bi bi-box-seam"></i> Food Items
            </a>
            <a class="nav-link" href="categories.php">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a class="nav-link" href="orders.php">
                <i class="bi bi-cart-check"></i> Orders
            </a>
            <a class="nav-link" href="../../index.php">
                <i class="bi bi-house-door"></i> Home
            </a>
            <a class="nav-link" href="../auth/logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle btn" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0">Dashboard</h4>
            </div>
            <div>
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
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
                            <i class="bi bi-people"></i>
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
                            <i class="bi bi-cart-check"></i>
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
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-1" style="font-size: 14px;">Food Items</div>
                            <h2 class="mb-0"><?php echo number_format($products_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-pink">
                            <i class="bi bi-box-seam"></i>
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
                                    <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <?php echo $user['role'] == 1 ? '<span class="badge bg-warning text-dark">Admin</span>' : '<span class="badge bg-secondary">Customer</span>'; ?>
                        </td>
                        <td>
                            <a href="users.php" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-pencil"></i>
                            </a>
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('mainContent').classList.toggle('expanded');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>