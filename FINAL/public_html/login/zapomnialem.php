<?php


$czyzalogowany = isset($_SESSION['UzytkownikID']);

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zapomniałem hasło</title>
    <link rel="icon" type="image/x-icon" href="../img.png">
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../navbar.css">
</head>
<body class="bg-dark bg-gradient text-white">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php include '../navbar.php'; ?>

    <main>
        <section class="form-container">
            <div id="login" class="form-section p-4 border rounded bg-secondary shadow text-center">
                <h2>Podaj adres email</h2>
                <form method="post" action="wyslij-reset-hasla.php">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <button type="submit" name="potwierdz" class="form-button">Potwierdź</button>
                </form>
            
            </div>
        </section>
    </main>

</body>
</html>