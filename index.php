<?php
session_start();

// If user is logged in, redirect based on role
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
        // Admin user - redirect to dashboard
        header('Location: modules/admin/index.php');
        exit;
    } else {
        // Regular user - redirect to restaurant
        header('Location: modules/restaurant/home.php');
        exit;
    }
}

// If not logged in, redirect to login
header('Location: modules/auth/login.php');
exit;
?>
      
