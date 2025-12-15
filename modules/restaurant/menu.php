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
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&display=swap');

body {
      background: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)), 
                  url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920') center/cover fixed;
      color: #f0f0f0; 
      font-family: 'Josefin Sans', sans-serif;
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }
a{
    color: white; 
    text-decoration: none; 
    }
a:hover
{
    color:#fac564 ;
}

.logo 
{
  color: #fac564; 
}
.navbar
{
  background-color:#121618 ; 
}
.text{
  color:#9ca3af;
}
h5{
  font-weight: 600;
}
  
    .menu-container {
      max-width: 64rem;
      margin: 0 auto;
      text-align: center;
      padding: 4rem 1rem 2rem 1rem;
      background: rgba(10, 10, 10, 0.6);
      backdrop-filter: blur(5px);
      border-radius: 15px;
      margin-top: 2rem;
    }
    
    .menu-container h1 {
      color: #fac564;
      font-size: 3.5rem;
      font-weight: 700;
      text-shadow: 0 0 20px rgba(250, 197, 100, 0.5);
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

    .products-container .row {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -0.75rem;
    }

    .products-container .col-lg-4,
    .products-container .col-md-6 {
      display: flex;
      padding: 0 0.75rem;
      margin-bottom: 1.5rem;
    }

    .product-card {
      background: rgba(26, 26, 26, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      overflow: hidden;
      transition: all 0.3s ease;
      position: relative;
      width: 100%;
      display: flex;
      flex-direction: column;
      height: 100%;
      border: 1px solid rgba(250, 197, 100, 0.2);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    }

    .product-card:hover {
      transform: translateY(-10px);
      border-color: #fac564;
      box-shadow: 0 8px 25px rgba(250, 197, 100, 0.3);
    }

    .product-image {
      width: 100%;
      height: 250px;
      object-fit: cover;
      padding: 1rem;
      flex-shrink: 0;
      aspect-ratio: 1/1;
    }

    .product-info {
      padding: 0.5rem 1.5rem 1rem 1.5rem;
      text-align: center;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .product-name {
      font-size: 1.5rem;
      font-weight: bold;
      color: #f0f0f0;
      margin-bottom: 1rem;
      height: 3.5em;
      line-height: 1.4;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .product-description {
      color: #b0b0b0;
      font-size: 0.9rem;
      line-height: 1.5;
      margin-bottom: 1rem;
      height: 4.5em;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
    }

    .product-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1.5rem 1.5rem 1.5rem;
      margin-top: auto;
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
      top: 200px;
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
      z-index: 10;
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
