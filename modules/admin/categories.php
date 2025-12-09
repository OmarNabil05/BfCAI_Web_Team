<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// Fetch all categories
$sql = "SELECT * FROM categories ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    
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
            text-decoration: none;
            display: block;
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

        /* Custom Bootstrap Overrides */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-header {
            background-color: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            color: var(--gold);
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

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-row h2 {
            color: #f0c040;
            margin: 0;
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
            float: right;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid;
        }

        .btn-primary {
            background-color: transparent;
            color: #f0c040;
            border-color: #f0c040;
        }

        .btn-primary:hover {
            background-color: #f0c040;
            color: #1a1a1a;
        }

        .btn-warning {
            background-color: transparent;
            color: #ffd43b;
            border-color: #ffd43b;
        }

        .btn-warning:hover {
            background-color: #ffd43b;
            color: #1a1a1a;
        }

        .btn-danger {
            background-color: transparent;
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .btn-danger:hover {
            background-color: #ff6b6b;
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

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #1a1a1a;
        }

        th {
            padding: 12px;
            text-align: left;
            color: #f0c040;
            font-weight: 600;
            border: 1px solid #333;
        }

        td {
            padding: 12px;
            color: #f0f0f0;
            border: 1px solid #333;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #1a1a1a;
        }

        tbody tr:nth-child(even) {
            background-color: #1f1f1f;
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
            max-width: 600px;
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

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #f0c040;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px;
            border: 2px solid #f0c040;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: #f0f0f0;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #ffffff;
            background-color: #242424;
        }

        textarea.form-control {
            resize: vertical;
        }

        .mb-3 {
            margin-bottom: 20px;
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
            <a class="nav-link" href="index.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link" href="users.php">
                <i class="bi bi-people"></i> Users
            </a>
            <a class="nav-link" href="products.php">
                <i class="bi bi-box-seam"></i> Food Items
            </a>
            <a class="nav-link active" href="categories.php">
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
                <h4 class="mb-0">Manage Categories</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manage Categories</h5>
                <button type="button" class="btn btn-outline-light" onclick="openModal('addCategoryModal')">
                    <i class="bi bi-plus-circle"></i> Add New Category
                </button>
            </div>

            <div class="card-body">
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

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Description</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($category = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $category['id']; ?></td>
                                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                                        <td>
                                            <?php if ($category['photo']): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($category['photo']); ?>" alt="Category" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($category['description']); ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo addslashes($category['name']); ?>', '<?php echo addslashes($category['description']); ?>', '<?php echo addslashes($category['photo']); ?>')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="process_category.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal" id="addCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" onclick="closeModal('addCategoryModal')">×</button>
                </div>
                <form method="POST" action="process_category.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_photo" class="form-label">Category Photo</label>
                            <input type="file" class="form-control" id="add_photo" name="photo" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addCategoryModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal" id="editCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" onclick="closeModal('editCategoryModal')">×</button>
                </div>
                <form method="POST" action="process_category.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="category_id" id="edit_category_id">
                        <input type="hidden" name="current_photo" id="edit_current_photo">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Photo</label>
                            <div id="current_photo_preview"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_photo" class="form-label">New Photo (leave blank to keep current)</label>
                            <input type="file" class="form-control" id="edit_photo" name="photo" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editCategoryModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        function editCategory(id, name, description, photo) {
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_current_photo').value = photo;
            
            if (photo) {
                document.getElementById('current_photo_preview').innerHTML = '<img src="uploads/' + photo + '" alt="Current" style="max-width: 100px; max-height: 100px; border-radius: 5px;">';
            } else {
                document.getElementById('current_photo_preview').innerHTML = 'No image';
            }

            openModal('editCategoryModal');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('mainContent').classList.toggle('expanded');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        }
    </script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>