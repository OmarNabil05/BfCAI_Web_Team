<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../auth/login.php?error=Access denied. Admin only.");
    exit();
}

require_once '../../config/db.php';

// Fetch all products with category names
$sql = "SELECT items.*, categories.name as category_name 
        FROM items 
        LEFT JOIN categories ON items.category_id = categories.id 
        ORDER BY items.id ASC";
$result = $conn->query($sql);

// Fetch all categories for the dropdown
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    
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
        }

        .table thead th {
            background-color: var(--sidebar-bg);
            color: var(--gold);
            border-color: var(--border-color);
        }

        .table tbody td {
            border-color: var(--border-color);
        }

        .table tbody tr:hover {
            background-color: var(--sidebar-bg);
        }

        /* Custom Buttons */
        .btn-gold {
            background-color: transparent;
            color: var(--gold);
            border: 2px solid var(--gold);
            font-weight: 600;
        }

        .btn-gold:hover {
            background-color: var(--gold);
            color: var(--dark-bg);
            border-color: var(--gold);
        }

        /* Modal Customization */
        .modal-content {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .modal-title {
            color: var(--gold);
        }

        .btn-close {
            filter: invert(1);
        }

        /* Form Controls */
        .form-control, .form-select {
            background-color: var(--dark-bg);
            color: var(--text-light);
            border: 2px solid var(--gold);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-light);
            border-color: white;
            box-shadow: 0 0 0 0.25rem rgba(240, 192, 64, 0.25);
        }

        .form-label {
            color: var(--gold);
            font-weight: 600;
        }
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
            <a class="nav-link active" href="products.php">
                <i class="bi bi-box-seam"></i> Food Items
            </a>
            <a class="nav-link" href="categories.php">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a class="nav-link" href="orders.php">
                <i class="bi bi-cart-check"></i> Orders
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
                <h4 class="mb-0">Manage Products</h4>
            </div>
            <div>
                <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Product Management</h5>
                <button type="button" class="btn btn-primary" onclick="openModal('addProductModal')">
                    Add New Product
                </button>
            </div>
           
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

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Photo</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($product = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>
                                        <?php if ($product['photos']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($product['photos']); ?>" alt="Product" style="max-width: 50px; max-height: 50px; border-radius: 5px;">
                                        <?php else: ?>
                                            No image
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>', '<?php echo addslashes($product['description']); ?>', <?php echo $product['category_id']; ?>, '<?php echo addslashes($product['photos']); ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="process_product.php" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No products found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" onclick="closeModal('addProductModal')">×</button>
                </div>
                <form method="POST" action="process_product.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="add_price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_category" class="form-label">Category</label>
                            <select class="form-select" id="add_category" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_photo" class="form-label">Product Photo</label>
                            <input type="file" class="form-control" id="add_photo" name="photo" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addProductModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal" id="editProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" onclick="closeModal('editProductModal')">×</button>
                </div>
                <form method="POST" action="process_product.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <input type="hidden" name="current_photo" id="edit_current_photo">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Category</label>
                            <select class="form-select" id="edit_category" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
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
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editProductModal')">Cancel</button>
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

        function editProduct(id, name, price, description, category_id, photo) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_category').value = category_id;
            document.getElementById('edit_current_photo').value = photo;
            
            if (photo) {
                document.getElementById('current_photo_preview').innerHTML = '<img src="uploads/' + photo + '" alt="Current" style="max-width: 100px; max-height: 100px; border-radius: 5px;">';
            } else {
                document.getElementById('current_photo_preview').innerHTML = 'No image';
            }

            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('hidden');
            document.getElementById('mainContent').classList.toggle('expanded');
        }
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>