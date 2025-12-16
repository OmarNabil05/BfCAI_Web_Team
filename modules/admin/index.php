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
    <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="components/styles.css">
    
    <style>


    </style>
</head>
<body>
    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle btn" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0">Dashboard</h4>
            </div>
            <div>
                <span style="color: var(--gold);">Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-2">Total Users</div>
                            <h2><?php echo number_format($users_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-blue">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-2">Total Orders</div>
                            <h2><?php echo number_format($orders_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-green">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-2">Categories</div>
                            <h2><?php echo number_format($categories_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-purple">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted mb-2">Food Items</div>
                            <h2><?php echo number_format($products_count); ?></h2>
                        </div>
                        <div class="stats-icon icon-pink">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="users.php" class="quick-action-card">
                <div class="icon" style="color: #60a5fa;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="title">Manage Users</div>
            </a>
            <a href="orders.php" class="quick-action-card">
                <div class="icon" style="color: #4ade80;">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div class="title">Manage Orders</div>
            </a>
            <a href="categories.php" class="quick-action-card">
                <div class="icon" style="color: #c084fc;">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <div class="title">Manage Categories</div>
            </a>
            <a href="products.php" class="quick-action-card">
                <div class="icon" style="color: var(--gold);">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="title">Manage Food Items</div>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin Scripts -->
    <script src="components/scripts.js"></script>
</body>
</html>
<?php $conn->close(); ?>