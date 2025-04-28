<?php
session_start();
// Dane do połączenia z bazą danych
$serwer      = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik  = 'root';
$haslo       = '';

$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);
if ($baza->connect_error) {
    die("Błąd połączenia: " . $baza->connect_error);
}

// Sprawdzamy, czy przekazano identyfikator zamówienia
if (!isset($_GET['id'])) {
    die("Brak identyfikatora zamówienia.");
}

$orderId = intval($_GET['id']);

// Aktualizacja statusu zamówienia
$sql = "UPDATE Zamowienia SET Status = 'wykonane', DataAktualizacji = NOW() WHERE ZamowienieID = $orderId";
if ($baza->query($sql) === TRUE) {
    // Przekierowanie do panelu aktywnych zamówień (zmodyfikuj adres, jeśli potrzebujesz)
    header("Location: admin.php?view=active");
    exit;
} else {
    die("Błąd aktualizacji: " . $baza->error);
}

$baza->close();
?>
