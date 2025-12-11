<?php
session_start();
require_once '../../config/db.php';

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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Josefin Sans', sans-serif;
        }

        body {
            background-color: #0b0d10;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        /* Prevent custom styles from affecting Tailwind components */
        nav,
        footer {
            font-family: 'Josefin Sans', sans-serif !important;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1a1d23 0%, #0b0d10 100%);
            padding: 80px 20px;
            text-align: center;
            margin-top: 0;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5em;
            font-weight: 700;
            color: #fac564;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero p {
            font-size: 1.3em;
            color: #b0b0b0;
            margin-bottom: 40px;
            animation: fadeInUp 1s ease-out;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease-out;
        }

        .btn-primary {
            padding: 15px 40px;
            background-color: #fac564;
            color: #0b0d10;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #ffdb7a;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(250, 197, 100, 0.3);
        }

        .btn-secondary {
            padding: 15px 40px;
            background-color: transparent;
            color: #fac564;
            border: 2px solid #fac564;
            font-weight: 700;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background-color: #fac564;
            color: #0b0d10;
            transform: translateY(-3px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background-color: #121618;
        }

        .section-title {
            text-align: center;
            font-size: 2.5em;
            color: #fac564;
            margin-bottom: 50px;
            font-weight: 700;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: linear-gradient(135deg, #1a1d23 0%, #151820 100%);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #2a2d35;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #fac564;
            box-shadow: 0 15px 35px rgba(250, 197, 100, 0.2);
        }

        .feature-icon {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.5em;
            color: #fac564;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-card p {
            color: #b0b0b0;
            line-height: 1.6;
        }

        /* Categories Section */
        .categories {
            padding: 80px 20px;
            background-color: #0b0d10;
        }

        .categories-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .category-card {
            background: linear-gradient(135deg, #1a1d23 0%, #151820 100%);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid #2a2d35;
        }

        .category-card:hover {
            transform: translateY(-10px);
            border-color: #fac564;
            box-shadow: 0 15px 35px rgba(250, 197, 100, 0.2);
        }

        .category-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .category-content {
            padding: 20px;
            text-align: center;
        }

        .category-content h3 {
            font-size: 1.5em;
            color: #fac564;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .category-content p {
            color: #b0b0b0;
            font-size: 0.95em;
        }

        /* Popular Items Section */
        .popular-items {
            padding: 80px 20px;
            background-color: #121618;
        }

        .items-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .item-card {
            background: linear-gradient(135deg, #1a1d23 0%, #151820 100%);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid #2a2d35;
        }

        .item-card:hover {
            transform: translateY(-10px);
            border-color: #fac564;
            box-shadow: 0 15px 35px rgba(250, 197, 100, 0.2);
        }

        .item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .item-content {
            padding: 20px;
        }

        .item-content h4 {
            font-size: 1.3em;
            color: #f0f0f0;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .item-content p {
            color: #b0b0b0;
            font-size: 0.9em;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .item-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-price {
            font-size: 1.3em;
            color: #fac564;
            font-weight: 700;
        }

        .btn-order {
            padding: 8px 20px;
            background-color: #fac564;
            color: #0b0d10;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-order:hover {
            background-color: #ffdb7a;
            transform: scale(1.05);
        }

        /* About Section */
        .about {
            padding: 80px 20px;
            background-color: #0b0d10;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 60px;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2.5em;
            color: #fac564;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .about-text p {
            color: #b0b0b0;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #1a1d23 0%, #151820 100%);
            border-radius: 12px;
            border: 1px solid #2a2d35;
        }

        .stat-number {
            font-size: 2.5em;
            color: #fac564;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #b0b0b0;
            font-size: 0.9em;
        }

        .about-image {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
        }

        .about-image img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid #fac564;
        }

        /* CTA Section */
        .cta {
            padding: 80px 20px;
            background: linear-gradient(135deg, #fac564 0%, #ff9800 100%);
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5em;
            color: #0b0d10;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .cta p {
            font-size: 1.2em;
            color: #1a1d23;
            margin-bottom: 30px;
        }

        .cta .btn-primary {
            background-color: #0b0d10;
            color: #fac564;
        }

        .cta .btn-primary:hover {
            background-color: #1a1d23;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5em;
            }

            .hero p {
                font-size: 1.1em;
            }

            .section-title {
                font-size: 2em;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

<?php include("Navbar.php"); ?>

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
            <div class="feature-icon">🚀</div>
            <h3>Fast Delivery</h3>
            <p>Get your favorite meals delivered in 30 minutes or less. We value your time!</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🍔</div>
            <h3>Fresh Ingredients</h3>
            <p>We use only the freshest, highest quality ingredients in all our dishes.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⭐</div>
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
            <img src="../admin/uploads/<?= htmlspecialchars($category['photo']) ?>" alt="<?= htmlspecialchars($category['name']) ?>" class="category-image">
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
            <?php 
            $photos = explode(',', $item['photos']);
            $main_photo = trim($photos[0]);
            ?>
            <img src="../admin/uploads/<?= htmlspecialchars($main_photo) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
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
<section class="cta">
    <h2>Ready to Order?</h2>
    <p>Join thousands of happy customers enjoying delicious meals every day!</p>
    <a href="menu.php" class="btn-primary">Explore Our Menu</a>
</section>

<!-- Include Footer -->
<?php include 'footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>