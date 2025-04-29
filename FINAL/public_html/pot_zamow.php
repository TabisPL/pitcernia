<?php
if (isset($_GET['zamowienie_id'])) {
    $zamowienieID = $_GET['zamowienie_id'];
    

    $sql = "SELECT * FROM Zamowienia WHERE ZamowienieID = '$zamowienieID'";
    $result = $baza->query($sql);
    $zamowienie = $result->fetch_assoc();
    
    echo "<h1>Potwierdzenie zamówienia</h1>";
    echo "<p>Numer zamówienia: " . $zamowienie['ZamowienieID'] . "</p>";
    echo "<p>Kwota całkowita: " . $zamowienie['KwotaCalkowita'] . " zł</p>";
    echo "<p>Status: " . $zamowienie['Status'] . "</p>";
    

    $sql_szczegoly = "SELECT * FROM SzczegolyZamowienia WHERE ZamowienieID = '$zamowienieID'";
    $result_szczegoly = $baza->query($sql_szczegoly);
    
    echo "<h3>Produkty:</h3><ul>";
    while ($szczegol = $result_szczegoly->fetch_assoc()) {
        echo "<li>Pizza ID: " . $szczegol['PizzaID'] . ", Ilość: " . $szczegol['Ilosc'] . ", Suma: " . $szczegol['Suma'] . " zł</li>";
    }
    echo "</ul>";
} else {
    echo "Brak numeru zamówienia!";
}
?>
