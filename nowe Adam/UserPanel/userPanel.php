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

// Funkcja tworzy tabelę do wyświetlania zamówień i pobiera je z bazy danych
function get_orders($baza, $logged_user) {
  // Definiowanie zmiennych
  $total_amount = 0;
  $cur_order = -1;

  $sql = "SELECT zamowienia.KwotaCalkowita, zamowienia.Status, zamowienia.DataUtworzenia, zamowienia.DataAktualizacji, szczegolyzamowienia.Ilosc, pizze.Nazwa, pizze.Rozmiar, zamowienia.ZamowienieID FROM `zamowienia`
  JOIN szczegolyzamowienia ON szczegolyzamowienia.ZamowienieID = zamowienia.ZamowienieID JOIN pizze ON pizze.PizzaID = szczegolyzamowienia.PizzaID
  WHERE zamowienia.UzytkownikID = '$logged_user' ORDER BY zamowienia.Status;";
  $orders = mysqli_query($baza, $sql);

  // Tworzenie tabeli dla każdego zamówienia osobno
  foreach($orders as $order) {
    if ($order["Status"] != "Anulowane") { // Sprawdzenie czy zamówienie nie było anulowane
      $total_amount += $order["KwotaCalkowita"]; // Sumowanie kwot zamówień
    }
    // Jeżeli kilka pizz składa się na jedno zamówienie to wyświetl je w jednej tabeli
    if($cur_order != $order["ZamowienieID"]) { // Sprawdzenie czy zamówienie jest to samo co poprzednie
      if ($cur_order != -1) { // Zakończenie tabeli z poprzedniej pętli
        echo "<tr><td>Cena:</td><td>".$order_sum."</td><td>Status:</td><td>".$order_status."</td>";
        if ($order_status == "Oczekujace") {
          echo "<td colspan='4'><form action='userPanel.php' method='post'><button class='btn btn-warning' name='cancelOrder' value=".$cur_order.">Anuluj zamówienie</button></form></tr></table></div></br>";
        }
        else echo "</tr></table></div></br>";
      }
      $cur_order = $order["ZamowienieID"];
      //Tworzenie nowej tabeli
      echo "<div class='bg-light justify-content-center rounded p-1'><table class='table'>";
      echo "<tr><td>Data utworzenia:</td><td>".$order["DataUtworzenia"]."</td><td>Ostatnia aktualizacja:</td><td>".$order["DataAktualizacji"]."</td></tr>";
    }
    echo "<tr><td>Pizza:</td><td>".$order["Nazwa"]."</td></tr>";
    echo "<tr><td>Rozmiar:</td><td>".$order["Rozmiar"]."</td></tr>";
    echo "<tr><td>Ilość:</td><td>".$order["Ilosc"]."</td></tr>";
    
    // Zapisanie kwoty i statusu zamówienia do następnej pętli
    $order_sum = $order["KwotaCalkowita"];
    $order_status = $order["Status"];
  }
  // Zakończenie tabeli z ostatniej pętli
  echo "<tr><td>Cena:</td><td>".$order_sum."</td><td>Status:</td><td>".$order_status."</td>";
  if ($order_status == "Oczekujace") {
    echo "<td colspan='4'><form action='userPanel.php' method='post'><button class='btn btn-warning' name='cancelOrder' value=".$cur_order.">Anuluj zamówienie</button></form></tr></table></div></br>";
  }
  else echo "</tr></table></div></br>";

  return $total_amount; // Zwrócenie łącznej kwoty zamówień
}

// Funkcja sprawdza czy zamówienie nie jest w trakcie realizacji
function get_status($baza, $order_id) {
  $sql = "SELECT Status FROM zamowienia WHERE ZamowienieID = '$order_id';";
  $status = mysqli_query($baza, $sql);
  if ($status == "W trakcie realizacji" or $status == "Zakonczone") {
    return true;
  }
  else return false;
}

// Sprawdza czy użytkownik chce anulować zamówienie
if (isset($_POST['cancelOrder'])) {
  $cur_order = $_POST['cancelOrder'];
  if (get_status($baza, $cur_order)) {
    echo "<script>alert('Nie możesz anulować zamówienia, ponieważ jest w trakcie realizacji!');</script>";
  }
  else {
    $sqlU = "UPDATE `zamowienia` SET `Status` = 'Anulowane' WHERE `zamowienia`.`ZamowienieID` = ".$cur_order.";";
    mysqli_query($baza, $sqlU);
    header("Location: userPanel.php");
  }
}

// Funkcja sprawdza czy zamówienie nie jest aktywne
function check_status($baza, $logged_user) {
  $is_active = false;
  $sql = "SELECT Status FROM zamowienia WHERE UzytkownikID = '$logged_user';";
  $orders = mysqli_query($baza, $sql);
  foreach ($orders as $status) {
    if ($status["Status"] == "Oczekujace" or $status["Status"] == "WRealizacji") {
      $is_active = true;
    }
  }
  return $is_active;
}

// Sprawdza czy użytkownik chce usunąć konto
if (isset($_POST['deleteAccount'])) {
  if (check_status($baza, $logged_user)) {
    echo "<script>alert('Nie możesz usunąć konta, ponieważ masz aktywne zamówienie!');</script>";
  }
  else {
    $sql = "UPDATE `uzytkownicy` SET `CzyAktywny` = '0' WHERE `uzytkownicy`.`UzytkownikID` = $logged_user;";
    if (mysqli_query($baza, $sql)) {
      mySqli_close($baza);
      header("Location: ../UserPanel/logout.php");
    }
    else {
      echo "<script>alert('Błąd podczas usuwania konta!');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pitcernia</title>
  <link rel="stylesheet" href="../navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark text-white">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php include '../navbar.php'; ?>
<main>
<!-- Okienko do anulowania zamówienia -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-dark">
        <h5 class="modal-title" id="cancelModalLabel">Czy napewno chcesz anulować zamówienie?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
      </div>
      <div class="modal-footer">
        <form action="userPanel.php" method="post">
          <button class="btn btn-secondary" name="cancelOrder" id="cancelOrder">Tak</button>
        </form>
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Nie</button>
      </div>
    </div>
  </div>
</div>
<!-- Okienko do usuwania konta -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountlLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-dark">
        <h5 class="modal-title" id="deleteAccountlLabel">Czy napewno chcesz usunąć konto?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
      </div>
      <div class="modal-body text-dark">
        <p>Uwaga! Tej akcji nie można cofnąć!</p>
      </div>
      <div class="modal-footer">
        <form action="userPanel.php" method="post">
          <button class="btn btn-secondary" name="deleteAccount" id="deleteAccount">Usuń</button>
        </form>
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Nie</button>
      </div>
    </div>
  </div>
</div>
</br>
<!-- Wyświetlanie zamówień -->
<div class="row">
  <div class="col-lg-8">
    <?php 
    if ($czyzalogowany) {
      $total_amount = get_orders($baza, $logged_user);
      mySqli_close($baza);
    }
    else {
      echo  "Użytkownik nie zalogowany!";
    }?>
  </div>
  <div class="col-lg-4 align-items-right">
    <!-- Wylogowanie -->
    <div class="bg-light p-2 text-dark text-center rounded">
      <h4>Kliknij poniżej aby się wylogować:</h4>
      <a href="logout.php" class="btn btn-warning">Wyloguj się</a>
    </div>
    </br>
    <!-- Wyświetlanie łącznej kwoty zamówień -->
    <div class="bg-light text-dark text-center p-3 rounded">
      <h3>Łączna kwota zamówień: </h3>
      <?php
        echo "<h2>$total_amount zł</h2>";
      ?>
    </div>
    </br>
    <div class="bg-light text-dark text-center p-3 rounded">
      <h4>Usuń konto:</h4>
      <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target='#deleteAccountModal'>Usuń</button>
      <p>Uwaga! Konta nie można usunąć jeżeli są aktywne zamówienia.</p>
    </div>
  </div>
</div>
<main>
</body>
</html>