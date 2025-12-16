<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Store</title>
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>LOGIN</h1>
            <div class="menu-divider">
                <div class="line"></div>
                <div class="diamond"></div>
                <div class="diamond large"></div>
                <div class="diamond"></div>
                <div class="line"></div>
            </div>
        </div>

        <div class="card">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label"> Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label"> Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            
            <hr>
            
            <p class="text-center">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>