<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Store</title>
    <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
    <div class="register-container">
        <div class="header">
            <h1>CREATE ACCOUNT</h1>
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
            
            <form action="process_register.php" method="POST">
                <div class="form-group">
                    <label for="name" class="form-label"> Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label"> Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="phone_number" class="form-label"> Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="01000000000">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label"> Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="form-label"> Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            
            <hr>
            
            <p class="text-center">
                Already have an account? <a href="login.php">Login here</a>
            </p>

        </div>
    </div>
</body>
</html>