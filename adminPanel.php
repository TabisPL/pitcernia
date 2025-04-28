<?php
session_start();
// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'pizza3test';
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
          
          
          <?php 
          // Obsługa zmiany statusu zamówienia
            if (isset($_GET['wykonane'])) {
               $id = (int)$_GET['wykonane'];
                $baza->query("UPDATE Zamowienia SET Status='Zakonczone' WHERE ZamowienieID=$id");
               header("Location: adminPanel.php");
              exit;
}

              // Funkcja do pobierania zamówień
              function pobierzZamowienia($conn, $status) {
                $zamowienia = [];
                $query = "SELECT Z.ZamowienieID, U.NazwaUzytkownika, 
                   A.Ulica, A.NumerDomu, A.NumerMieszkania, A.Miasto, A.KodPocztowy, 
                   Z.KwotaCalkowita, Z.Status, 
                   P.PizzaID, SZ.Ilosc
                    FROM Zamowienia Z
                    JOIN Uzytkownicy U ON Z.UzytkownikID = U.UzytkownikID
                    JOIN adresyuzytkownikow A ON U.UzytkownikID = A.UzytkownikID
                    JOIN SzczegolyZamowienia SZ ON Z.ZamowienieID = SZ.ZamowienieID
                    JOIN pizze P ON SZ.PizzaID = P.PizzaID
                    WHERE Z.Status = '$status'
                    ORDER BY Z.ZamowienieID DESC";

                $result = $conn->query($query);

           if ($result) {
             while ($row = $result->fetch_assoc()) {
              $id = $row['ZamowienieID'];

          // Składanie adresu:
          $adres = $row['Ulica'] . ' ' . $row['NumerDomu'];
          if (!empty($row['NumerMieszkania'])) {
              $adres .= '/' . $row['NumerMieszkania'];
          }
          $adres .= ', ' . $row['KodPocztowy'] . ' ' . $row['Miasto'];

          if (!isset($zamowienia[$id])) {
              $zamowienia[$id] = [
                  'uzytkownik' => $row['NazwaUzytkownika'],
                  'adres' => $adres,
                  'kwota' => $row['KwotaCalkowita'],
                  'status' => $row['Status'],
                  'pizze' => []
              ];
          }
          $zamowienia[$id]['pizze'][] = [
              'nazwa' => $row['NazwaPizzy'],
              'ilosc' => $row['Ilosc']
          ];
      }
  }

  return $zamowienia;
}



// Pobranie zamówień aktywnych i zakończonych
$aktywneZamowienia = pobierzZamowienia($baza, 'Aktywne');
$zakonczoneZamowienia = pobierzZamowienia($baza, 'Zakonczone');
// Wyświetlanie aktywnych zamówień
echo "<h2>Aktywne zamówienia</h2>";
if (!empty($aktywneZamowienia)) {
    foreach ($aktywneZamowienia as $id => $zamowienie) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
        echo "<strong>Zamówienie #$id</strong><br>";
        echo "Użytkownik: " . htmlspecialchars($zamowienie['uzytkownik']) . "<br>";
        echo "Adres: " . htmlspecialchars($zamowienie['adres']) . "<br>";
        echo "Kwota: " . number_format($zamowienie['kwota'], 2) . " PLN<br>";
        echo "<ul>";
        foreach ($zamowienie['pizze'] as $pizza) {
            echo "<li>" . (int)$pizza['ilosc'] . "x " . htmlspecialchars($pizza['nazwa']) . "</li>";
        }
        echo "</ul>";
        echo "<a href='adminPanel.php?wykonane=$id' style='color: green;'>Oznacz jako wykonane</a>";
        echo "</div>";
    }
} else {
    echo "<p>Brak aktywnych zamówień.</p>";
}

// Wyświetlanie historii zamówień
echo "<h2>Historia zamówień</h2>";
if (!empty($zakonczoneZamowienia)) {
    foreach ($zakonczoneZamowienia as $id => $zamowienie) {
        echo "<div style='border:1px solid #eee; margin:10px; padding:10px; background-color:#f9f9f9;'>";
        echo "<strong>Zamówienie #$id</strong><br>";
        echo "Użytkownik: " . htmlspecialchars($zamowienie['uzytkownik']) . "<br>";
        echo "Adres: " . htmlspecialchars($zamowienie['adres']) . "<br>";
        echo "Kwota: " . number_format($zamowienie['kwota'], 2) . " PLN<br>";
        echo "<ul>";
        foreach ($zamowienie['pizze'] as $pizza) {
            echo "<li>" . (int)$pizza['ilosc'] . "x " . htmlspecialchars($pizza['nazwa']) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
} else {
    echo "<p>Brak zakończonych zamówień.</p>";
}


          ?>
        </div>
        <div class="">
          
          <h3>szczegóły zamówień</h3>
          <?php

          // Sprawdzenie czy podano ID zamówienia
if (!isset($_GET['zamowienie_id'])) {
  die("Nie podano ID zamówienia.");
}

$zamowienie_id = (int)$_GET['zamowienie_id'];

// Pobranie danych zamówienia
$query = "SELECT Z.ZamowienieID, U.NazwaUzytkownika, 
               A.Ulica, A.NumerDomu, A.NumerMieszkania, A.Miasto, A.KodPocztowy,
               Z.KwotaCalkowita, Z.Status, 
               P.NazwaPizzy, SZ.Ilosc
        FROM Zamowienia Z
        JOIN Uzytkownicy U ON Z.UzytkownikID = U.UzytkownikID
        JOIN adresyuzytkownikow A ON U.UzytkownikID = A.UzytkownikID
        JOIN SzczegolyZamowienia SZ ON Z.ZamowienieID = SZ.ZamowienieID
        JOIN pizze P ON SZ.PizzaID = P.PizzaID
        WHERE Z.ZamowienieID = $zamowienie_id";

$result = $baza->query($query);

if ($result && $result->num_rows > 0) {
  $zamowienie = [];
  while ($row = $result->fetch_assoc()) {
      if (empty($zamowienie)) {
          // Dane użytkownika i zamówienia
          $adres = $row['Ulica'] . ' ' . $row['NumerDomu'];
          if (!empty($row['NumerMieszkania'])) {
              $adres .= '/' . $row['NumerMieszkania'];
          }
          $adres .= ', ' . $row['KodPocztowy'] . ' ' . $row['Miasto'];

          $zamowienie = [
              'uzytkownik' => $row['NazwaUzytkownika'],
              'adres' => $adres,
              'kwota' => $row['KwotaCalkowita'],
              'status' => $row['Status'],
              'pizze' => []
          ];
      }
      $zamowienie['pizze'][] = [
          'nazwa' => $row['NazwaPizzy'],
          'ilosc' => $row['Ilosc']
      ];
  }

           // Wyświetlanie
         echo "<h2>Szczegóły zamówienia #$zamowienie_id</h2>";
         echo "<strong>Użytkownik:</strong> " . htmlspecialchars($zamowienie['uzytkownik']) . "<br>";
         echo "<strong>Adres dostawy:</strong> " . htmlspecialchars($zamowienie['adres']) . "<br>";
          echo "<strong>Status:</strong> " . htmlspecialchars($zamowienie['status']) . "<br>";
           echo "<strong>Kwota całkowita:</strong> " . number_format($zamowienie['kwota'], 2) . " PLN<br>";

          echo "<h3>Produkty:</h3><ul>";
           foreach ($zamowienie['pizze'] as $pizza) {
             echo "<li>" . htmlspecialchars($pizza['ilosc']) . "x " . htmlspecialchars($pizza['nazwa']) . "</li>";
            }
          echo "</ul>";
          } else {
          echo "Nie znaleziono zamówienia.";
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