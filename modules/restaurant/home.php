<?php
require_once '../../config/db.php';
include("Navbar.php"); 
// Detect logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch featured categories
$featured_categories_sql = "SELECT * FROM categories LIMIT 3";
$featured_categories = $conn->query($featured_categories_sql);

// Fetch popular items
$popular_items_sql = "SELECT * FROM items ORDER BY id DESC LIMIT 6";
$popular_items = $conn->query($popular_items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodie - Your Food Delivery Service</title>
    <link rel="stylesheet" href="styles/home.css">
</head>
<body>


<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Foodie</h1>
        <p>Delicious meals delivered right to your doorstep, anytime, anywhere</p>
        <div class="hero-buttons">
            <a href="menu.php" class="btn-primary">Order Now</a>
            <a href="#features" class="btn-secondary">Learn More</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <h2 class="section-title">Why Choose Foodie?</h2>
    <div class="features-grid">
        <div class="feature-card">
           
            <h3>Fast Delivery</h3>
            <p>Get your favorite meals delivered in 30 minutes or less. We value your time!</p>
        </div>
        <div class="feature-card">
            
            <h3>Fresh Ingredients</h3>
            <p>We use only the freshest, highest quality ingredients in all our dishes.</p>
        </div>
        <div class="feature-card">
           
            <h3>Top Rated</h3>
            <p>Loved by thousands of customers with 5-star reviews and ratings.</p>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories" id="categories">
    <h2 class="section-title">Browse Categories</h2>
    <div class="categories-grid">
        <?php while($category = $featured_categories->fetch_assoc()): ?>
        <div class="category-card">
            <?php $image_id = $category['image_id']; ?>
            <?php if ($image_id): ?>
            <img src="../../image.php?id=<?= intval($image_id) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-image">
            <?php else: ?>
            <img src="../../image.php?id=0" alt="No image" class="category-image">
            <?php endif; ?>
            <div class="category-content">
                <h3><?= htmlspecialchars($category['name']) ?></h3>
                <p><?= htmlspecialchars($category['description']) ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Popular Items Section -->
<section class="popular-items">
    <h2 class="section-title">Popular Dishes</h2>
    <div class="items-grid">
        <?php while($item = $popular_items->fetch_assoc()): ?>
        <div class="item-card">
            <?php $image_id = $item['image_id']; ?>
            <?php if ($image_id): ?>
            <img src="../../image.php?id=<?= intval($image_id) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
            <?php else: ?>
            <img src="../../image.php?id=0" alt="No image" class="item-image">
            <?php endif; ?>
            <div class="item-content">
                <h4><?= htmlspecialchars($item['name']) ?></h4>
                <p><?= htmlspecialchars(substr($item['description'], 0, 80)) ?>...</p>
                <div class="item-footer">
                    <span class="item-price">$<?= number_format($item['price'], 2) ?></span>
                    <button class="btn-order" onclick="window.location.href='menu.php'">Order Now</button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- About Section -->
<section class="about" id="about">
    <div class="about-content">
        <div class="about-text">
            <h2>About Foodie</h2>
            <p>
                Welcome to Foodie, where passion for great food meets exceptional service. Since our founding, 
                we've been dedicated to bringing you the finest culinary experiences right to your doorstep.
            </p>
            <p>
                Our team of talented chefs uses only the freshest, locally-sourced ingredients to create dishes 
                that delight your taste buds. From classic comfort food to innovative fusion cuisine, every meal 
                is prepared with love and attention to detail.
            </p>
            <p>
                We believe that good food brings people together, and we're honored to be part of your special 
                moments – whether it's a family dinner, a celebration, or just a well-deserved treat.
            </p>
            
            <div class="about-stats">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Menu Items</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5★</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
        
        <div class="about-image">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800" alt="Delicious Food">
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta" id="cta">
    <h2>Ready to Order?</h2>
    <p>Join thousands of happy customers enjoying delicious meals every day!</p>
    <a href="menu.php" class="btn-primary">Explore Our Menu</a>
</section>

<!-- Include Footer -->
<?php include 'footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>