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
            <a class="nav-link active" href="products.php">
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
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    ☰
                </button>
                <h4 class="mb-0">Manage Products</h4>
            </div>
        </div>

        <div class="data-table">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Products</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add New Product
            </button>
        </div>

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
            <table class="table table-bordered table-striped">
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
                                        <img src="uploads/<?php echo htmlspecialchars($product['photos']); ?>" alt="Product" style="max-width: 50px; max-height: 50px;">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editProductModal"
                                            onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>', '<?php echo addslashes($product['description']); ?>', <?php echo $product['category_id']; ?>, '<?php echo addslashes($product['photos']); ?>')">
                                        Edit
                                    </button>
                                    <form method="POST" action="process_product.php" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(id, name, price, description, category_id, photo) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_category').value = category_id;
            document.getElementById('edit_current_photo').value = photo;
            
            if (photo) {
                document.getElementById('current_photo_preview').innerHTML = '<img src="uploads/' + photo + '" alt="Current" style="max-width: 100px; max-height: 100px;">';
            } else {
                document.getElementById('current_photo_preview').innerHTML = 'No image';
            }
        }
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('hidden');
            document.querySelector('.main-content').classList.toggle('expanded');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
