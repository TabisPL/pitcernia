<?php

session_start();

if (!isset($_SESSION['UzytkownikID'])) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="koszyk.css"> 
    <link rel="icon" type="image/x-icon" href="../img.png">
</head>
<body class="bg-dark bg-gradient text-white">
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h1>Koszyk</h1>

    <?php
    if (isset($_COOKIE['koszyk'])) {
        $koszyk = json_decode($_COOKIE['koszyk'], true);

        if (count($koszyk) > 0) {
            echo '<table class="table table-dark table-striped">';
            echo '<thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Rozmiar</th>
                        <th>Cena</th>
                        <th>Ilość</th>
                        <th>Suma</th>
                        <th></th>
                    </tr>
                  </thead>';
            echo '<tbody>';

            $total = 0;
            foreach ($koszyk as $index => $produkt) {
                $cena = (float) $produkt['cena'];
                $ilosc = (int) $produkt['ilosc'];
                $suma = $cena * $ilosc;
                $total += $suma;

                echo '<tr>';
                echo '<td>' . htmlspecialchars($produkt['nazwa']) . '</td>';
                echo '<td>' . (isset($produkt['rozmiar']) ? htmlspecialchars($produkt['rozmiar']) : '-') . '</td>';
                echo '<td>' . number_format($cena, 2) . ' zł</td>';
                echo '<td>';
                echo '<form method="post" action="zmien_koszyk.php" class="d-flex align-items-center">';
                echo '<input type="hidden" name="index" value="' . $index . '">';
                echo '<input type="number" name="nowa_ilosc" value="' . $ilosc . '" min="1" class="form-control form-control-sm" style="width: 70px;">';
                echo '<button type="submit" class="btn btn-primary btn-sm ms-2">Zmień</button>';
                echo '</form>';
                echo '</td>';
                echo '<td>' . number_format($suma, 2) . ' zł</td>';
                echo '<td>';
                echo '<form method="post" action="usun_z_koszyka.php" style="display:inline-block;">';
                echo '<input type="hidden" name="index" value="' . $index . '">';
                echo '<button type="submit" class="btn btn-danger btn-sm">Usuń</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            echo '<h3 class="mt-3">Łącznie: ' . number_format($total, 2) . ' zł</h3>';
            echo '<a href="zamowienie.php" class="btn btn-success mt-3">Złóż zamówienie</a>';
        } else {
            echo '<p>Twój koszyk jest pusty!</p>';
        }
    } else {
        echo '<p>Twój koszyk jest pusty!</p>';
    }
    ?>
</div>

</body>
</html>
