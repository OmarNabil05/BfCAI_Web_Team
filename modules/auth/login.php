<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #1a1a1a;
            color: #f0f0f0;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 1rem;
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

        .card {
            background-color: #242424;
            border-radius: 10px;
            padding: 40px;
            border: 1px solid #333;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card-title {
            font-size: 1.8em;
            text-align: center;
            margin-bottom: 30px;
            color: #f0f0f0;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-danger {
            background-color: #2d1f1f;
            color: #ff6b6b;
            border-color: #ff6b6b;
        }

        .alert-success {
            background-color: #1f2d1f;
            color: #51cf66;
            border-color: #51cf66;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #f0c040;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #f0c040;
            border-radius: 5px;
            font-size: 16px;
            background-color: #1a1a1a;
            color: #f0f0f0;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #ffffff;
            background-color: #242424;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: transparent;
            color: #f0f0f0;
            border: 2px solid #f0c040;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: #f0c040;
            border-color: #f0c040;
            color: #1a1a1a;
            transform: scale(1.02);
        }

        .btn:active {
            transform: scale(0.98);
        }

        hr {
            border: none;
            border-top: 1px solid #333;
            margin: 25px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-center a {
            color: #f0c040;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .text-center a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .text-center p {
            color: #b0b0b0;
            margin-bottom: 10px;
        }

        @media (max-width: 600px) {
            .header h1 {
                font-size: 2em;
            }

            .card {
                padding: 30px 20px;
            }
        }
    </style>
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