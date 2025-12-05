<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #1a1a1a; 
      color: #f0f0f0; 
      font-family: Arial, sans-serif;
    }

    
    .menu-container {
      max-width: 64rem;
      margin: 0 auto;
      text-align: center;
      padding: 4rem 1rem;
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
    }

    .menu-buttons .btn-custom:first-child {
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
      <button class="btn-custom">Pizza</button>
      <button class="btn-custom">Drinks</button>
      <button class="btn-custom">Burgers</button>
      <button class="btn-custom">Ice-Creams</button>
      <button class="btn-custom">Pasta</button>
      <button class="btn-custom">Juices</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
