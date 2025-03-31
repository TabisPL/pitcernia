<?php
session_start();
// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik = 'root';
$haslo = '';

// Połączenie z bazą danych
$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);

// Sprawdzenie połączenia
if ($baza->connect_error) {
  die("Błąd połączenia: " . $baza->connect_error);
}
// Sprawdzanie użytkownika
$czyzalogowany = isset($_SESSION['UzytkownikID']);
if ($czyzalogowany) {
  $logged_user = $_SESSION['UzytkownikID'];
}
if (isset($_SESSION['UzytkownikID'])) {
  $sql = "SELECT czyAdmin FROM `uzytkownicy` WHERE UzytkownikID ='$logged_user';";
  $admin = mysqli_query($baza, $sql);
  foreach ($admin as $a) {
    if ($a['czyAdmin']) {
      header("Location: ../UserPanel/adminPanel.php");
    }
  }
}
else header("Location: ../login/login.php");
?>




<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>test</title>
  
  <link rel="stylesheet" href="../navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark text-white">
<?php include '../navbar.php'; ?>
<main>
  <div class="container">
    <section class="main">
      <div class="main-top">
        <h1>Admin Panel</h1>
        <?php

       ?>
      </div>
      <div class="">
        
        <div class="">
          
          <h3>szczegóły zamówień</h3>
          <?php
          // Pobranie ID zamówienia z parametru GET
          $zamowienieID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

          if ($zamowienieID > 0) {
          // Pobranie szczegółów zamówienia
          $query = "SELECT Z.ZamowienieID, Z.UzytkownikID, Z.KwotaCalkowita, Z.Status, Z.DataUtworzenia, U.NazwaUzytkownika 
          FROM Zamowienia Z
           JOIN Uzytkownicy U ON Z.UzytkownikID = U.UzytkownikID WHERE Z.ZamowienieID = ?";
    
          $stmt = $conn->prepare($query);
          $stmt->bind_param("i", $zamowienieID);
          $stmt->execute();
          $result = $stmt->get_result();
    
          if ($result->num_rows > 0) {
          $order = $result->fetch_assoc();
          echo "<h2>Szczegóły zamówienia #" . $order['ZamowienieID'] . "</h2>";
          echo "<p>Użytkownik: " . $order['NazwaUzytkownika'] . " (ID: " . $order['UzytkownikID'] . ")</p>";
          echo "<p>Kwota całkowita: " . number_format($order['KwotaCalkowita'], 2) . " PLN</p>";
          echo "<p>Status: " . $order['Status'] . "</p>";
          echo "<p>Data utworzenia: " . $order['DataUtworzenia'] . "</p>";
          } else {
          echo "<p>Nie znaleziono zamówienia.</p>";
          }
    
          $stmt->close();
          } else {
          echo "<p>Nie podano prawidłowego ID zamówienia.</p>";
          }
          ?>
        </div>
        <div class="">
          
          <h3>historia zamówień</h3>
          
        </div>
        <div class="">
          
          <h3>lista zamówień z dzisiaj, kwota zamówień</h3>
            <?php
              // dzisiejsze zamówienia
              $zamowienie = "SELECT ZamowienieID, UzytkownikID, KwotaCalkowita, Status, DataUtworzenia FROM Zamowienia WHERE DATE(DataUtworzenia) = CURDATE()";
              $lista_zamowien = $baza->query($zamowienie);
              if ($lista_zamowien->num_rows > 0) {
              echo "<table border='1'>";
              echo "<tr><th>ID Zamówienia</th><th>ID Użytkownika</th><th>Kwota</th><th>Status</th><th>Data Utworzenia</th></tr>";
              while ($row = $lista_zamowien->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['ZamowienieID'] . "</td>";
              echo "<td>" . $row['UzytkownikID'] . "</td>";
              echo "<td>" . number_format($row['KwotaCalkowita']) . " PLN</td>";
              echo "<td>" . $row['Status'] . "</td>";
              echo "<td>" . $row['DataUtworzenia'] . "</td>";
              echo "</tr>";
              }
              echo "</table>";
              } else {
              echo "<p>Brak zamówień na dziś ;></p>";
              }



              // Dzisiaj kwota
              $dzisiaj = "SELECT SUM(KwotaCalkowita) AS total FROM Zamowienia WHERE DATE(DataUtworzenia) = CURDATE()";
              $result = $baza->query($dzisiaj);
              $sumadzis = ($result->fetch_assoc()['total']);
              echo "<p>Kwota zamówień z dzisiaj: " . number_format($sumadzis) . " zł</p>";
            ?>
        </div>
        <div class="">
          
          <h3>podsumówanie kwoty zamówień dla dnia dzisiejszego, miesiąca wstecz i roku wstecz</h3>
          <?php
        
          //30 dni
          $miesiac = "SELECT SUM(KwotaCalkowita) AS total FROM Zamowienia WHERE DataUtworzenia >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
          $result = $baza->query($miesiac);
          $sumamiesianca = ($result->fetch_assoc()['total']);
          // rok
          $rok = "SELECT SUM(KwotaCalkowita) AS total FROM Zamowienia WHERE DataUtworzenia >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
          $result = $baza->query($rok);
          $sumarok = ($result->fetch_assoc()['total']);
          echo "<p>Dzisiaj: " . number_format($sumadzis) . " zł</p>";
          echo "<p>Ostatnie 30 dni: " . number_format($sumamiesianca) . " zł</p>";
          echo "<p>Ostatnie 365 dni: " . number_format($sumarok) . " zł</p>";

          ?>
        </div>
      </div>
      
      </section>
    </section>
  </div>
</main>
</body>
</html></span>