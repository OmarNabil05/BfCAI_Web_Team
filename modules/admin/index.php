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
    <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="components/styles.css">
    
    <style>

        /* Stats Cards */
        .stats-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(240, 192, 64, 0.15);
            border-color: var(--gold);
        }

        .stats-card h2 {
            color: var(--text-light);
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stats-card .text-muted {
            color: var(--text-muted) !important;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .icon-blue { 
            background: rgba(59, 130, 246, 0.15); 
            color: #60a5fa;
        }
        .icon-green { 
            background: rgba(34, 197, 94, 0.15); 
            color: #4ade80;
        }
        .icon-purple { 
            background: rgba(168, 85, 247, 0.15); 
            color: #c084fc;
        }
        .icon-pink { 
            background: rgba(240, 192, 64, 0.15); 
            color: var(--gold);
        }

        /* Table Styles */
        .data-table {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-top: 24px;
            overflow: hidden;
        }

        .data-table h5 {
            color: var(--gold);
            margin-bottom: 20px;
            font-size: 1.3em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table {
            color: var(--text-light);
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--sidebar-bg);
            color: var(--gold);
            padding: 16px 12px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            border-color: var(--border-color);
            vertical-align: middle;
        }

        .table tbody td {
            padding: 16px 12px;
            color: var(--text-light);
            border-color: var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold) 0%, #ff9800 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--dark-bg);
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Badges */
        .badge {
            font-weight: 600;
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge.bg-success {
            background-color: rgba(34, 197, 94, 0.2) !important;
            color: #4ade80 !important;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .badge.bg-warning {
            background-color: rgba(240, 192, 64, 0.2) !important;
            color: var(--gold) !important;
            border: 1px solid rgba(240, 192, 64, 0.3);
        }

        .badge.bg-secondary {
            background-color: rgba(108, 117, 125, 0.2) !important;
            color: #adb5bd !important;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }

        .quick-action-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 10px 25px rgba(240, 192, 64, 0.15);
            color: var(--text-light);
            text-decoration: none;
        }

        .quick-action-card .icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            width: 60px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
        }

        .quick-action-card .title {
            font-weight: 600;
            color: var(--text-light);
            font-size: 16px;
        }

        /* Action Button */
        .btn-outline-info {
            border-color: #4dabf7;
            color: #4dabf7;
            padding: 6px 15px;
            font-size: 14px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-outline-info:hover {
            background-color: #4dabf7;
            color: var(--dark-bg);
            border-color: #4dabf7;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .top-navbar {
                padding: 15px;
            }
            
            .stats-card h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .data-table {
                padding: 20px;
            }
             .table {
            color: var(--text-light);
            background-color: var(--card-bg);
        }

        .table thead th {
            background-color: var(--sidebar-bg);
            color: var(--gold);
            border-color: var(--border-color);
        }

        .table tbody td {
            border-color: var(--border-color);
            background-color: var(--card-bg);
        }

        .table tbody tr:hover {
            background-color: var(--sidebar-bg);
        }

         .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02);
        }
   
            .table-responsive {
                margin: 0 -20px;
                padding: 0 20px;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .top-navbar h4 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .stats-card {
                text-align: center;
            }
            
            .stats-card .d-flex {
                flex-direction: column;
                gap: 15px;
            }
            
            .stats-icon {
                margin: 0 auto;
            }
            
            .menu-toggle {
                width: 40px;
                height: 40px;
                padding: 0;
            }
        }

        /* Table responsive fix */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
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
                <span class="text-muted">Welcome, <strong style="color: var(--gold);"><?php echo htmlspecialchars($admin_name); ?></strong></span>
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
            <a href="products.php" class="quick-action-card">
                <div class="icon" style="color: var(--gold);">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="title">Manage Food Items</div>
            </a>
            <a href="categories.php" class="quick-action-card">
                <div class="icon" style="color: #c084fc;">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <div class="title">Manage Categories</div>
            </a>
            <a href="orders.php" class="quick-action-card">
                <div class="icon" style="color: #4ade80;">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div class="title">Manage Orders</div>
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