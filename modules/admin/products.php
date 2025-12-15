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
    <!-- Admin Sidebar Styles -->
    <link rel="stylesheet" href="components/styles.css">
    
    <style>
        /* Page specific styles only */
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
    <?php include 'components/sidebar.php'; ?>

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
                                        <?php if ($product['image_id']): ?>
                                            <img src="../../image.php?id=<?php echo $product['image_id']; ?>" alt="Product" style="max-width: 50px; max-height: 50px; border-radius: 5px;">
                                        <?php else: ?>
                                            No image
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>', '<?php echo addslashes($product['description']); ?>', <?php echo $product['category_id']; ?>, <?php echo $product['image_id'] ? $product['image_id'] : 0; ?>)">
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
                        <input type="hidden" name="current_image_id" id="edit_current_image_id">
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

        function editProduct(id, name, price, description, category_id, image_id) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_category').value = category_id;
            document.getElementById('edit_current_image_id').value = image_id;
            
            if (image_id > 0) {
                document.getElementById('current_photo_preview').innerHTML = '<img src="../../image.php?id=' + image_id + '" alt="Current" style="max-width: 100px; max-height: 100px; border-radius: 5px;">';
            } else {
                document.getElementById('current_photo_preview').innerHTML = 'No image';
            }

            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }

    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin Scripts -->
    <script src="components/scripts.js"></script>
</body>
</html>

    </script>
</body>
</html>
<?php $conn->close(); ?>