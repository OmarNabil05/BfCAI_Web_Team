<?php
require_once '../../config/db.php';


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
  <style>
    body {
      background-color: #1a1a1a; 
      color: #f0f0f0; 
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .menu-container {
      max-width: 64rem;
      margin: 0 auto;
      text-align: center;
      padding: 4rem 1rem 2rem 1rem;
    }

    .menu-divider {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      margin: 1rem 0 2rem 0;
    }

    .menu-divider .line {
      flex: 1;
      height: 1px;
      background-color: #f0c040; 
      max-width: 3rem;
    }

    .menu-divider .diamond {
      width: 0.6rem;
      height: 0.6rem;
      background-color: #f0c040;
      transform: rotate(45deg);
    }

    .menu-divider .diamond.large {
      width: 1rem;
      height: 1rem;
    }

    .menu-description {
      color: #b0b0b0; 
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .menu-buttons {
      display: flex;
      justify-content: center;
      gap: 0.75rem;
      flex-wrap: wrap;
      margin: 3rem 0;
    }

    .menu-buttons .btn-custom {
      min-width: 6rem;
      height: 3rem;
      border-radius: 50px;
      font-weight: 500;
      border: 2px solid #f0c040;
      background-color: #1a1a1a;
      color: #f0c040;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .menu-buttons .btn-custom.active {
      background-color: #f0c040;
      color: #1a1a1a;
    }

    .menu-buttons .btn-custom:hover {
      background-color: #f0c040;
      color: #1a1a1a;
      border-color: #ffffff;
      transform: scale(1.05);
    }

    .menu-buttons .btn-custom:active {
      transform: scale(0.97);
    }

    .products-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1rem 4rem 1rem;
    }

    .product-card {
      background-color: #1a1a1a;
      border-radius: 10px;
      overflow: hidden;
      transition: transform 0.3s ease;
      margin-bottom: 2rem;
      position: relative;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-image {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 50%;
      padding: 1rem;
    }

    .product-info {
      padding: 1.5rem;
      text-align: center;
    }

    .product-name {
      font-size: 1.5rem;
      font-weight: bold;
      color: #f0f0f0;
      margin-bottom: 1rem;
    }

    .product-description {
      color: #b0b0b0;
      font-size: 0.9rem;
      line-height: 1.5;
      margin-bottom: 1rem;
      min-height: 60px;
    }

    .product-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1rem 1rem 1rem;
    }

    .product-price {
      font-size: 1.1rem;
      color: #f0c040;
      font-weight: bold;
    }

    .product-price span {
      color: #b0b0b0;
      font-size: 0.9rem;
      margin-right: 0.3rem;
    }

    .btn-order {
      background-color: transparent;
      color: #f0f0f0;
      border: 2px solid #f0f0f0;
      padding: 0.5rem 1.5rem;
      border-radius: 5px;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-order:hover {
      background-color: #f0c040;
      border-color: #f0c040;
      color: #1a1a1a;
    }

    .zoom-icon {
      position: absolute;
      bottom: 280px;
      right: 20px;
      background-color: rgba(240, 192, 64, 0.9);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .zoom-icon:hover {
      background-color: #f0c040;
      transform: scale(1.1);
    }

    .zoom-icon svg {
      width: 20px;
      height: 20px;
      fill: #1a1a1a;
    }

  </style>
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
          $categories_result->data_seek(0); // Reset pointer
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
              $photos = $item['photos'] ? explode(',', $item['photos']) : ['placeholder.jpg'];
              $main_photo = trim($photos[0]);
              
              $description = $item['description'];
              if (strlen($description) > 80) {
                  $description = substr($description, 0, 77) . '...';
              }
      ?>
       <?php echo htmlspecialchars($item['name']); ?> 
      <div class="col-lg-3 col-md-6">
        <div class="product-card">
          <img src="image/<?php echo htmlspecialchars($main_photo); ?>" 
               alt="<?php echo htmlspecialchars($item['name']); ?>" 
               class="product-image">
          <div class="zoom-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
          </div>
          <div class="product-info">
            <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
            <p class="product-description"><?php echo htmlspecialchars($description); ?></p>
          </div>
          <div class="product-footer">
            <div class="product-price">$<?php echo number_format($item['price'], 2); ?></div>
            <button class="btn-order" onclick="alert('Order feature coming soon!')">Order</button>
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
