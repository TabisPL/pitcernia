<?php
session_start();
// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'pizza4test';
$uzytkownik = 'root';
$haslo = '';

// Połączenie z bazą danych
$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);

// Sprawdzenie połączenia
if ($baza->connect_error) {
  die("Błąd połączenia: " . $baza->connect_error);
}
/*
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

*/


// Obsługa akcji – oznaczanie zamówienia jako wykonane
if (isset($_GET['action']) && $_GET['action'] === 'markCompleted' && isset($_GET['id'])) {
  $orderId = intval($_GET['id']);
  // Zmieniono: kolumna zamieniona z 'id' na 'ZamowienieID', a także poprawiono nazwę daty aktualizacji na DataAktualizacji
  $sql = "UPDATE Zamowienia SET Status = 'wykonane', DataAktualizacji = NOW() WHERE ZamowienieID = $orderId";
  if ($baza->query($sql) === TRUE) {
      header("Location: admin.php?view=active");
      exit;
  } else {
      echo "Błąd aktualizacji: " . $baza->error;
  }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel</title>
  
  <link rel="stylesheet" href="../navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
</head>
<body class="bg-dark text-white">
<?php include '../navbar.php'; ?>
<main>
  <div class="container">
    <section class="main">
      <div class="main-top">
        <h1>Admin Panel</h1>
      </div>
      <div class="">
        <div class="">
          <?php
          $view = isset($_GET['view']) ? $_GET['view'] : 'active';

          if ($view === 'active') {
              echo "<h2>Aktywne zamówienia</h2>";
              // Pobieramy aktywne zamówienia – uwzględniamy aktualne nazwy kolumn
              $sql = "SELECT `z`.`ZamowienieID` AS orderId, `u`.`UzytkownikID` AS username, `z`.`KwotaCalkowita`, `z`.Status, `z`.`DataUtworzenia`
                      FROM Zamowienia z
                      JOIN uzytkownicy u ON z.UzytkownikID = u.UzytkownikID
                      WHERE z.Status != 'wykonane'
                      ORDER BY z.DataUtworzenia";
              $result = $baza->query($sql);

              if ($result && $result->num_rows > 0) {
                  echo "<table class='table table-dark table-bordered'>";
                  echo "<tr>
                          <th>ID</th>
                          <th>Użytkownik</th>
                          <th>Kwota</th>
                          <th>Data utworzenia</th>
                          <th>Akcje</th>
                        </tr>";
                  while ($order = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($order['orderId']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['username']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['KwotaCalkowita']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['DataUtworzenia']) . "</td>";
                      echo "<td>
                              <a href=\"admin.php?view=details&id=" . urlencode($order['orderId']) . "\">Szczegóły</a> | 
                              <a href=\"admin.php?action=markCompleted&id=" . urlencode($order['orderId']) . "\">Oznacz jako wykonane</a>
                            </td>";
                      echo "</tr>";
                  }
                  echo "</table>";
              } else {
                  echo "<p>Brak aktywnych zamówień.</p>";
              }
          } elseif ($view === 'details' && isset($_GET['id'])) {
              // Widok szczegółów zamówienia
              $orderId = intval($_GET['id']);
              $sqlOrder = "SELECT z.ZamowienieID AS orderId, z.UzytkownikID, u.NazwaUzytkow AS username, 
                                  z.KwotaCalkowita, z.Status, z.DataUtworzenia, z.DataAktualizacji
                           FROM Zamowienia z
                           JOIN uzytkownicy u ON z.UzytkownikID = u.UzytkownikID
                           WHERE z.ZamowienieID = $orderId";
              $resultOrder = $baza->query($sqlOrder);
              if ($resultOrder && $resultOrder->num_rows > 0) {
                  $order = $resultOrder->fetch_assoc();
                  echo "<h2>Szczegóły zamówienia #" . htmlspecialchars($order['orderId']) . "</h2>";
                  echo "<p><strong>Użytkownik:</strong> " . htmlspecialchars($order['username']) . "</p>";
                  echo "<p><strong>Kwota:</strong> " . htmlspecialchars($order['KwotaCalkowita']) . "</p>";
                  echo "<p><strong>Status:</strong> " . htmlspecialchars($order['Status']) . "</p>";
                  echo "<p><strong>Data utworzenia:</strong> " . htmlspecialchars($order['DataUtworzenia']) . "</p>";

                  // Szczegóły zamówienia – zamówione pizze i ilości
                  $sqlDetails = "SELECT sz.Ilosc, p.Nazwa 
                                 FROM szczegolyzamowienia sz
                                 JOIN pizze p ON sz.PizzaID = p.PizzaID
                                 WHERE sz.ZamowienieID = $orderId";
                  $resultDetails = $baza->query($sqlDetails);
                  if ($resultDetails && $resultDetails->num_rows > 0) {
                      echo "<h3>Pozycje zamówienia:</h3>";
                      echo "<table class='table table-dark table-bordered'>";
                      echo "<tr><th>Pizza</th><th>Ilość</th></tr>";
                      while ($detail = $resultDetails->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . htmlspecialchars($detail['Nazwa']) . "</td>";
                          echo "<td>" . htmlspecialchars($detail['Ilosc']) . "</td>";
                          echo "</tr>";
                      }
                      echo "</table>";
                  }

                  // Pobieramy adres dostawy na podstawie UzytkownikID
                  $sqlAddress = "SELECT Ulica, NumerDomu, NumerMieszkania, Miasto, KodPocztowy
                                 FROM adresyuzytkownikow
                                 WHERE UzytkownikID = " . intval($order['UzytkownikID']);
                  $resultAddress = $baza->query($sqlAddress);
                  if ($resultAddress && $resultAddress->num_rows > 0) {
                      $address = $resultAddress->fetch_assoc();
                      echo "<h3>Adres dostawy:</h3>";
                      echo "<p>" . htmlspecialchars($address['Ulica']) . " " . htmlspecialchars($address['NumerDomu']);
                      if (!empty($address['NumerMieszkania'])) {
                          echo "/" . htmlspecialchars($address['NumerMieszkania']);
                      }
                      echo ", " . htmlspecialchars($address['Miasto']) . ", " . htmlspecialchars($address['KodPocztowy']) . "</p>";
                  }
              } else {
                  echo "<p>Zamówienie nie zostało odnalezione.</p>";
              }
          } elseif ($view === 'history') {
              echo "<h2>Historia zamówień</h2>";
              $sql = "SELECT z.ZamowienieID AS orderId, u.NazwaUzytkow AS username, z.KwotaCalkowita, 
                             z.Status, z.DataUtworzenia, z.DataAktualizacji
                      FROM Zamowienia z
                      JOIN uzytkownicy u ON z.UzytkownikID = u.UzytkownikID
                      WHERE z.Status = 'wykonane'
                      ORDER BY z.DataUtworzenia DESC";
              $result = $baza->query($sql);
              if ($result && $result->num_rows > 0) {
                  echo "<table class='table table-dark table-bordered'>";
                  echo "<tr>
                          <th>ID</th>
                          <th>Użytkownik</th>
                          <th>Kwota</th>
                          <th>Data utworzenia</th>
                          <th>Data aktualizacji</th>
                        </tr>";
                  while ($order = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($order['orderId']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['username']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['KwotaCalkowita']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['DataUtworzenia']) . "</td>";
                      echo "<td>" . htmlspecialchars($order['DataAktualizacji']) . "</td>";
                      echo "</tr>";
                  }
                  echo "</table>";
              } else {
                  echo "<p>Brak zamówień w historii.</p>";
              }
          } else {
              echo "<p>Wybrany widok nie istnieje.</p>";
          }
          ?>
        </div>
        
        <div class="">
          <h3>Lista zamówień z dzisiaj, kwota zamówień</h3>
          <?php
            // Zamówienia z dnia dzisiejszego
            $zamowienie = "SELECT ZamowienieID, UzytkownikID, KwotaCalkowita, Status, DataUtworzenia 
                           FROM Zamowienia 
                           WHERE DATE(DataUtworzenia) = CURDATE()";
            $lista_zamowien = $baza->query($zamowienie);
            if ($lista_zamowien->num_rows > 0) {
              echo "<table class='table table-dark table-bordered'>";
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

            // Podsumowanie kwoty zamówień z dzisiaj
            $dzisiaj = "SELECT SUM(KwotaCalkowita) AS total FROM Zamowienia WHERE DATE(DataUtworzenia) = CURDATE()";
            $result = $baza->query($dzisiaj);
            $sumadzis = ($result->fetch_assoc()['total']);
            echo "<p>Kwota zamówień z dzisiaj: " . number_format($sumadzis) . " zł</p>";
          ?>
        </div>
        
        <div class="">
          <h3>Podsumowanie kwoty zamówień - dzisiaj, ostatnie 30 dni, ostatnie 365 dni</h3>
          <?php
            // Ostatnie 30 dni
            $miesiac = "SELECT SUM(KwotaCalkowita) AS total FROM Zamowienia WHERE DataUtworzenia >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $result = $baza->query($miesiac);
            $sumamiesianca = ($result->fetch_assoc()['total']);
            // Ostatnie 365 dni
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
  </div>
</main>
</body>
</html>
