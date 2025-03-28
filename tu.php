<?php
// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'c3test';
$uzytkownik = 'root';
$haslo = '';

// Połączenie z bazą danych
$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);
?>






<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>test</title>
  
  <link rel="stylesheet" href="navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark text-white">
<?php include 'navbar.php'; ?>
<main>
  <div class="container">
    <section class="main">
      <div class="main-top">
        <h1>Admin Panel</h1>
        
      </div>
      <div class="main-skills">
        <div class="card">
          
          <h3>zamówienia</h3>
          <p>pizza</p>
          <button>sprawdz</button>
        </div>
        <div class="card">
          
          <h3>szczegóły</h3>
          <p>pizza</p>
          <button>sprawdz</button>
        </div>
        <div class="card">
          
          <h3>historia</h3>
          <p>pizza</p>
          <button>sprawdz</button>
        </div>
        <div class="card">
          
          <h3>zamówienia dzisiejsze</h3>
          <h3>Łączna kwota zamówień: </h3>
      <?php
      $sql = "SELECT zamowienia.KwotaCalkowita, zamowienia.Status, zamowienia.DataUtworzenia, zamowienia.DataAktualizacji, szczegolyzamowienia.Ilosc, pizze.Nazwa, pizze.Rozmiar, zamowienia.ZamowienieID FROM `zamowienia`
      JOIN szczegolyzamowienia ON szczegolyzamowienia.ZamowienieID = zamowienia.ZamowienieID JOIN pizze ON pizze.PizzaID = szczegolyzamowienia.PizzaID
      WHERE zamowienia.UzytkownikID = '' ORDER BY zamowienia.Status;";
      
        echo "<h2>$total_amount</h2>"
      
      ?>
        </div>
        <div class="card">
          
          <h3>podsumówanie kwoty zamówień</h3>
          <p>pizza</p>
          <button>pizza</button>
        </div>
      </div>
      
      </section>
    </section>
  </div>
</main>
</body>
</html></span>