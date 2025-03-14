<?php
session_start(); // Start sesji
// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';
// Połączenie z bazą danych
$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);

$czyzalogowany = isset($_SESSION['UzytkownikID']);
if ($czyzalogowany) {
  $logged_user = $_SESSION['UzytkownikID'];
}
?>
<!-- NAGŁÓWEK -->
<header class="p-3 bg-orange text-white">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
      <a class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
      <img src="https://i.imgur.com/hUa9V6E.png" alt="Logo" class="Logo" style="width: 100px; height: auto;"></a>
      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <li><a href="../menu.php" class="nav-link px-2 text-white">MENU</a></li>
        <li><a href="#" class="nav-link px-2 text-white">KOSZYK</a></li>
        <li><a href="../UserPanel/userPanel.php" class="nav-link px-2 text-white">MOJE KONTO</a></li>
      </ul>
      <?php if (!$czyzalogowany): ?>
      <div class="text-end">
      <a href="../login/login.php" class="btn btn-outline-light me-2">Login</a>
      <a href="../login/rejestracja.php" class="btn btn-warning">Sign-up</a>
      <?php endif; ?>
      </div>
    </div>
  </div>
</header>