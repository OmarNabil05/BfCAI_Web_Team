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
    }
  </style>
</head>
<body>

<div class="container text-center py-5">
  <h1 class="mb-3">OUR MENU</h1>

  <div class="menu-divider">
    <div class="line"></div>
    <div class="diamond"></div>
    <div class="diamond large"></div>
    <div class="diamond"></div>
    <div class="line"></div>
  </div>

  <p class="menu-description">
    From classic favorites to innovative creations, our hot meals promise a delightful symphony of flavors that will leave you craving for more.
  </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
