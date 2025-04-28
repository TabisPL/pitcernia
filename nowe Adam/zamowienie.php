<?php
session_start(); 

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'root';
$haslo = '';

$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);

if (isset($_COOKIE['koszyk']) && isset($_SESSION['UzytkownikID'])) {

    $koszyk = json_decode($_COOKIE['koszyk'], true);
    $uzytkownikID = $_SESSION['UzytkownikID'];
    

    $total = 0;
    foreach ($koszyk as $produkt) {
        $total += (float)$produkt['cena'] * (int)$produkt['ilosc'];
    }


    $dataUtworzenia = date('Y-m-d H:i:s');
    $sql = "INSERT INTO zamowienia (UzytkownikID, KwotaCalkowita, Status, DataUtworzenia, DataAktualizacji) 
            VALUES ('$uzytkownikID', '$total', 'Oczekujace', '$dataUtworzenia', '$dataUtworzenia')";
    
    if ($baza->query($sql)) {
        $zamowienieID = $baza->insert_id;  
        

        foreach ($koszyk as $produkt) {
            $pizzaID = $produkt['pizza_id'];
            $ilosc = $produkt['ilosc'];
            $suma = (float)$produkt['cena'] * $ilosc;
            
            $sql_szczegol = "INSERT INTO szczegolyzamowienia (ZamowienieID, PizzaID, Ilosc, Suma)
                             VALUES ('$zamowienieID', '$pizzaID', '$ilosc', '$suma')";
            $baza->query($sql_szczegol);
        }


        setcookie('koszyk', '', time() - 3600);  
        

        header("Location: ../UserPanel/userPanel.php");
        exit();
    } else {
        echo "Błąd podczas składania zamówienia: " . $baza->error;
    }
} else {
    echo "Brak koszyka lub użytkownika!";
}
?>