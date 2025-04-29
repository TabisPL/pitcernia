<?php
session_start();

if (!isset($_SESSION['UzytkownikID'])) {
    
    header('Location: login/login.php');
    exit();
}
$pizza_id = $_POST['pizza_id'];
$nazwa = $_POST['nazwa'];
$cena = $_POST['cena'];
$rozmiar = $_POST['rozmiar'];
$ilosc = $_POST['ilosc'];


$koszyk = isset($_COOKIE['koszyk']) ? json_decode($_COOKIE['koszyk'], true) : [];


$found = false;
foreach ($koszyk as &$item) {
    if ($item['pizza_id'] == $pizza_id && $item['rozmiar'] == $rozmiar) {

        $item['ilosc'] += $ilosc;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {

    $koszyk[] = [
        'pizza_id' => $pizza_id,
        'nazwa' => $nazwa,
        'cena' => $cena,
        'rozmiar' => $rozmiar,
        'ilosc' => $ilosc
    ];
}

setcookie('koszyk', json_encode($koszyk), time() + (86400 * 7), "/");


header('Location: index.php');
exit();
?>
