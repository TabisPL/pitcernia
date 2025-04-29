<?php
session_start();
// Dane do połączenia z bazą danych
$serwer      = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik  = 'srv82461_pizza3test';
$haslo       = '12345678';

// Nawiązanie połączenia
$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);
if ($baza->connect_error) {
    die("Błąd połączenia: " . $baza->connect_error);
}

if (!isset($_GET['id'])) {
    die("Brak identyfikatora zamówienia.");
}

$orderId = intval($_GET['id']);

// Pobieramy główne dane zamówienia wraz z informacją o użytkowniku
$sqlOrder = "SELECT z.ZamowienieID AS orderId, z.UzytkownikID, u.NazwaUzytkownika AS username, 
                    z.KwotaCalkowita, z.Status, z.DataUtworzenia, z.DataAktualizacji
             FROM Zamowienia z
             JOIN Uzytkownicy u ON z.UzytkownikID = u.UzytkownikID
             WHERE z.ZamowienieID = $orderId";

$resultOrder = $baza->query($sqlOrder);
if ($resultOrder && $resultOrder->num_rows > 0) {
    $order = $resultOrder->fetch_assoc();
} else {
    die("Zamówienie nie zostało znalezione.");
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Szczegóły Zamówienia #<?php echo htmlspecialchars($order['orderId']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h1>Szczegóły Zamówienia #<?php echo htmlspecialchars($order['orderId']); ?></h1>
    <p><strong>Użytkownik:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Kwota:</strong> <?php echo htmlspecialchars($order['KwotaCalkowita']); ?> zł</p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['Status']); ?></p>
    <p><strong>Data utworzenia:</strong> <?php echo htmlspecialchars($order['DataUtworzenia']); ?></p>
    
    <h3>Pozycje zamówienia</h3>
    <?php
    // Pobranie pozycji zamówienia – jakie pizze oraz ich ilości
    $sqlDetails = "SELECT sz.Ilosc, p.Nazwa 
                   FROM SzczegolyZamowienia sz
                   JOIN Pizze p ON sz.PizzaID = p.PizzaID
                   WHERE sz.ZamowienieID = $orderId";
    $resultDetails = $baza->query($sqlDetails);
    
    if ($resultDetails && $resultDetails->num_rows > 0) {
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Pizza</th><th>Ilość</th></tr></thead><tbody>";
        while ($detail = $resultDetails->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($detail['Nazwa']) . "</td>";
            echo "<td>" . htmlspecialchars($detail['Ilosc']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Brak pozycji w tym zamówieniu.</p>";
    }
    ?>

    <h3>Adres Dostawy</h3>
    <?php
    // Pobieramy adres dostawy na podstawie UzytkownikID
    $sqlAddress = "SELECT Ulica, NumerDomu, NumerMieszkania, Miasto, KodPocztowy 
                   FROM AdresyUzytkownikow 
                   WHERE UzytkownikID = " . intval($order['UzytkownikID']);
    $resultAddress = $baza->query($sqlAddress);
    if ($resultAddress && $resultAddress->num_rows > 0) {
        $address = $resultAddress->fetch_assoc();
        echo "<p>" . htmlspecialchars($address['Ulica']) . " " . htmlspecialchars($address['NumerDomu']);
        if (!empty($address['NumerMieszkania'])) {
            echo "/" . htmlspecialchars($address['NumerMieszkania']);
        }
        echo ", " . htmlspecialchars($address['Miasto']) . ", " . htmlspecialchars($address['KodPocztowy']) . "</p>";
    } else {
        echo "<p>Adres dostawy nie został znaleziony.</p>";
    }
    ?>
    <a href="admin-panel.php?view=active" class="btn btn-secondary mt-3">Powrót do panelu</a>
</div>
</body>
</html>
<?php
$baza->close();
?>
