<?php
session_start();

if (isset($_POST['index'])) {
    $index = (int)$_POST['index'];

    if (isset($_COOKIE['koszyk'])) {
        $koszyk = json_decode($_COOKIE['koszyk'], true);

        if (isset($koszyk[$index])) {
            unset($koszyk[$index]);
            $koszyk = array_values($koszyk); // reset indeksów
            setcookie('koszyk', json_encode($koszyk), time() + (7 * 24 * 60 * 60), '/');
        }
    }
}

header('Location: koszyk.php');
exit;
?>
