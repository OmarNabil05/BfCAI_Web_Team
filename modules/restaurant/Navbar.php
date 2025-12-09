<!-- Navbar Component -->
<style>
  /* Font Import */
  @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&display=swap');

  body {
    font-family: 'Josefin Sans', sans-serif;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<nav class="bg-[#121618] md:px-32 px-4 py-6 flex items-center justify-between relative">

  <!-- Logo -->
  <a href=""  class="text-[#fac564] text-[18px] md:text-2xl font-bold transition-all duration-300">
    Foodie
  </a>

  <!-- Desktop Links -->
  <div class="hidden md:flex gap-7 text-white font-bold transition-all duration-300 text-[12px] md:text-[16px]">
    <a href="" class="hover:text-[#fac564] transition-all duration-300">Home</a>
    <a href="menu.php" class="hover:text-[#fac564] transition-all duration-300">Menu</a>
    <a href="#" class="hover:text-[#fac564] transition-all duration-300">Services</a>
    <a href="#" class="hover:text-[#fac564] transition-all duration-300">About</a>
    <a href="#" class="hover:text-[#fac564] transition-all duration-300">Contact</a>
  </div>

  <!-- Desktop Buttons -->
  <div class="hidden md:flex gap-3 text-[12px] md:text-[16px] items-center">
    <a href="../auth/login.php" class="text-white font-bold hover:text-[#fac564] transition-all duration-300">Login</a>
    <a href="../auth/register.php"
      class="py-1 md:py-2 px-2 md:px-6 rounded-full font-bold bg-[#fac564] text-black text-nowrap transition-all duration-300 cursor-pointer">
      Sign Up
    </a>
  </div>

  <!-- Mobile Button -->
  <button id="menuBtn" class="md:hidden text-white text-3xl transition-all duration-300">☰</button>

</nav>

<!-- Mobile Menu -->
<div id="mobileMenu"
  class="fixed top-0 right-[-100%] w-[50%] h-full bg-[#0b0d10] flex flex-col p-6 gap-6 transition-all duration-300 z-50">

  <button id="closeBtn" class="text-white text-2xl self-end">✕</button>

  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Home</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Menu</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Services</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">About</a>
  <a href="#" class="text-white font-bold text-lg hover:text-[#fac564]">Contact</a>

  <div class="mt-6 flex flex-col gap-4">
    <button class="text-white font-bold hover:text-[#fac564]">Login</button>
    <button class="py-2 rounded-full font-bold bg-[#fac564] text-black">Sign Up</button>
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
