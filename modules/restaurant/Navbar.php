<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=my_store", "root", "");

// Detect logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;

// Default cart count
$cart_count = 0;

if ($isLoggedIn) {
  // SQL to get cart item count
  $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(oi.quantity), 0) AS cart_count
        FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        WHERE o.user_id = :uid
        AND o.status = 0
    ");
  $stmt->execute(['uid' => $user_id]);
  $cart_count = (int)$stmt->fetchColumn();
}
?>

<!-- Navbar Component -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&display=swap');

  body {
    font-family: 'Josefin Sans', sans-serif;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<nav class="bg-[#121618] md:px-32 px-4 py-6 flex items-center justify-between relative">

  <!-- Logo -->
  <a href="" class="text-[#fac564] text-[18px] md:text-2xl font-bold transition-all duration-300">
    Foodie
  </a>

  <!-- Desktop Links -->
  <div class="hidden md:flex gap-7 text-white font-bold text-[12px] md:text-[16px]">
    <a href="" class="hover:text-[#fac564]">Home</a>
    <a href="menu.php" class="hover:text-[#fac564]">Menu</a>
    <a href="#" class="hover:text-[#fac564]">Services</a>
    <a href="#" class="hover:text-[#fac564]">About</a>
    <a href="#" class="hover:text-[#fac564]">Contact</a>
  </div>

  <!-- Desktop Right Section -->
  <div class="hidden md:flex gap-6 items-center text-white font-bold text-[16px]">

    <?php if ($isLoggedIn): ?>

      <!-- CART ICON -->
      <a href="cart.php" class="relative hover:text-[#fac564] transition">
        <svg xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor"
          class="w-7 h-7">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 
          14.25a3 3 0 100 6 3 3 0 000-6zm9.75 0a3 3 0 110 6 
          3 3 0 010-6zM7.5 14.25h9.75m-12.75-9l1.5 
          6h12.708c.967 0 1.72-.88 1.568-1.836l-.75-4.5A1.688 
          1.688 0 0017.864 4.5H4.5z" />
        </svg>

        <?php if ($cart_count > 0): ?>
          <span class="absolute -top-2 -right-3 bg-red-500 text-white 
                     text-xs px-2 rounded-full">
            <?= $cart_count ?>
          </span>
        <?php endif; ?>
      </a>

      <!-- PROFILE ICON -->
      <a href="profile.php" class="hover:text-[#fac564] transition">
        <svg xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="1"
          class="w-6 h-6">
          <circle cx="12" cy="7" r="4" />
          <path d="M4 20c1-4 4-6 8-6s7 2 8 6" />
        </svg>

      </a>

    <?php else: ?>

      <!-- Login / Signup -->
      <a href="../auth/login.php" class="hover:text-[#fac564]">Login</a>
      <a href="../auth/register.php"
        class="py-2 px-6 rounded-full bg-[#fac564] text-black font-bold">
        Sign Up
      </a>

    <?php endif; ?>

  </div>

  <!-- Mobile Menu Button -->
  <button id="menuBtn" class="md:hidden text-white text-3xl">☰</button>

</nav>

<!-- Mobile Menu -->
<div id="mobileMenu"
  class="fixed top-0 right-[-100%] w-[50%] h-full bg-[#0b0d10] flex flex-col p-6 gap-6 transition-all duration-300 z-50">

  <button id="closeBtn" class="text-white text-2xl self-end">✕</button>

  <!-- Links -->
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Home</a>
  <a href="menu.php" class="text-white font-bold text-lg hover:text-[#fac564]">Menu</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Services</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">About</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Contact</a>

  <div class="mt-6 flex flex-col gap-4">
    <?php if ($isLoggedIn): ?>

      <!-- CART ICON (mobile) -->
      <a href="cart.php" class="flex items-center gap-2 text-white hover:text-[#fac564]">
        <svg xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor"
          class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 
          1.437M7.5 14.25a3 3 0 100 6 3 3 0 000-6zm9.75 
          0a3 3 0 110 6 3 3 0 010-6zM7.5 14.25h9.75m-12.75-9l1.5 
          6h12.708c.967 0 1.72-.88 1.568-1.836l-.75-4.5A1.688 
          1.688 0 0017.864 4.5H4.5z" />
        </svg>

        <?php if ($cart_count > 0): ?>
          <span class="bg-red-500 px-2 text-white text-sm rounded-full"><?= $cart_count ?></span>
        <?php endif; ?>
      </a>

      <!-- PROFILE ICON (mobile) -->
      <a href="profile.php" class="flex items-center gap-2 text-white hover:text-[#fac564]">
        <svg xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="1"
          class="w-6 h-6">
          <circle cx="12" cy="7" r="4" />
          <path d="M4 20c1-4 4-6 8-6s7 2 8 6" />
        </svg>

      </a>

    <?php else: ?>

      <a href="../auth/login.php" class="text-white font-bold hover:text-[#fac564]">Login</a>
      <a href="../auth/register.php"
        class="py-2 rounded-full bg-[#fac564] text-black font-bold text-center">Sign Up</a>

    <?php endif; ?>
  </div>

</div>

<script>
  const menuBtn = document.getElementById("menuBtn");
  const closeBtn = document.getElementById("closeBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  menuBtn.addEventListener("click", () => {
    mobileMenu.classList.remove("right-[-100%]");
    mobileMenu.classList.add("right-0");
  });

  closeBtn.addEventListener("click", () => {
    mobileMenu.classList.remove("right-0");
    mobileMenu.classList.add("right-[-100%]");
  });
</script>