<?php
require_once '../../config/db.php';
include("Navbar.php");

$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);


$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

if ($selected_category > 0) {
    $items_query = "SELECT i.*, c.name as category_name 
                    FROM items i 
                    JOIN categories c ON i.category_id = c.id 
                    WHERE i.category_id = ? 
                    ORDER BY i.name ASC";
    $stmt = $conn->prepare($items_query);
    $stmt->bind_param("i", $selected_category);
    $stmt->execute();
    $items_result = $stmt->get_result();
} else {
    $items_query = "SELECT i.*, c.name as category_name 
                    FROM items i 
                    JOIN categories c ON i.category_id = c.id 
                    ORDER BY i.name ASC";
    $items_result = $conn->query($items_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Menu - Pizza Fiesta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/menu.css">
</head>
<body>

  <div class="menu-container">
    <h1 class="mb-3">OUR MENU</h1>

    <div class="menu-divider">
      <div class="line"></div>
      <div class="diamond"></div>
      <div class="diamond large"></div>
      <div class="diamond"></div>
      <div class="line"></div>
    </div>

    <p class="menu-description">
      From classic favorites to innovative creations, our hot pizza meals promise a delightful symphony of flavors that will leave you craving for more.
    </p>
  </div>

  <div class="container text-center">
    <div class="menu-buttons">
      <button class="btn-custom <?php echo $selected_category == 0 ? 'active' : ''; ?>" 
              onclick="window.location.href='menu.php'">All</button>
      <?php 
      if ($categories_result && $categories_result->num_rows > 0) {
          $categories_result->data_seek(0); 
          while ($category = $categories_result->fetch_assoc()) {
              $active_class = ($selected_category == $category['id']) ? 'active' : '';
              echo '<button class="btn-custom ' . $active_class . '" 
                      onclick="window.location.href=\'menu.php?category=' . $category['id'] . '\'">' 
                      . htmlspecialchars($category['name']) . '</button>';
          }
      }
      ?>
    </div>
  </div>

  <div class="products-container">
    <div class="row">
      <?php 
      if ($items_result && $items_result->num_rows > 0) {
          while ($item = $items_result->fetch_assoc()) {
              $image_id = $item['image_id'];
              
              $description = $item['description'];
              if (strlen($description) > 80) {
                  $description = substr($description, 0, 77) . '...';
              }
      ?>
      <!-- Product: <?php echo htmlspecialchars($item['name']); ?> -->
      <div class="col-lg-4 col-md-6 col-sm-12">
        <div class="product-card">
          <?php if ($image_id): ?>
          <img src="../../image.php?id=<?php echo intval($image_id); ?>" 
               alt="<?php echo htmlspecialchars($item['name']); ?>" 
               class="product-image">
          <?php else: ?>
          <img src="../../image.php?id=0" 
               alt="No image" 
               class="product-image">
          <?php endif; ?>

          <div class="product-info">
            <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
            <p class="product-description"><?php echo htmlspecialchars($description); ?></p>
          </div>
          <div class="product-footer">
            <div class="product-price">$<?php echo number_format($item['price'], 2); ?></div>
            <form action="make_order.php" method="POST">
              <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
              <button type="submit" class="btn-order">Order</button>
</form>

          </div>
        </div>
      </div>
      <?php 
          }
      } else {
          echo '<div class="col-12 text-center"><p class="text-muted">No items available in this category.</p></div>';
      }
      ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
<?php include("footer.php"); ?>
