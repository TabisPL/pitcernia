<?php
session_start(); 

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'root';
$haslo = '';

$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);


if (isset($_POST['index']) && isset($_POST['nowa_ilosc'])) {
    $index = (int)$_POST['index'];
    $nowa_ilosc = (int)$_POST['nowa_ilosc'];

    if (isset($_COOKIE['koszyk'])) {
        $koszyk = json_decode($_COOKIE['koszyk'], true);

        if (isset($koszyk[$index])) {
            $koszyk[$index]['ilosc'] = $nowa_ilosc;
            setcookie('koszyk', json_encode($koszyk), time() + (7 * 24 * 60 * 60), '/');
        }
    }
}

header('Location: koszyk.php');
exit;
?>