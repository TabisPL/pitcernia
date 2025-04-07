<?php
$czyzalogowany = isset($_SESSION['UzytkownikID']);
if ($czyzalogowany) {
  $logged_user = $_SESSION['UzytkownikID'];
}
?>
<!-- NAGŁÓWEK -->
<header class="p-3 shadow text-white" style="background-color: #ff6600; background: radial-gradient(circle, rgba(255,102,0,1) 0%, rgba(255,140,0,1) 92%, rgba(250,160,60,1) 100%);">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      <a class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
      <img src="https://i.imgur.com/hUa9V6E.png" alt="Logo" class="Logo" style="width: 64px; height: auto;"></a>
      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <li><a href="../menu.php" class="nav-link px-2 text-white">MENU</a></li>
        <li><a href="#" class="nav-link px-2 text-white">KOSZYK</a></li>
        <li><a href="../UserPanel/userPanel.php" class="nav-link px-2 text-white">MOJE KONTO</a></li>
      </ul>
      <?php if (!$czyzalogowany): ?>
      <div class="text-end">
      <a href="../login/login.php" class="btn btn-outline-light me-2">Login</a>
      <a href="../login/rejestracja.php" class="btn shadow btn-warning">Sign-up</a>
      <?php endif; ?>
      </div>
    </div>
  </div>
</header>