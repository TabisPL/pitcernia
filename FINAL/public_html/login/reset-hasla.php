<?php

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);

if ($baza->connect_error) {
    die("Błąd połączenia z bazą danych: " . $baza->connect_error);
}

$komunikat = "";
$wyswietlic_formularz = false;
$pokaz_powrot = true;

if (!isset($_GET['token'])) {
    $komunikat = "<p style='color: red; font-size: 18px; font-weight: bold; text-align: center;'>Brak tokenu w linku.</p>";
} else {
    $token = $_GET['token'];
    $token_hash = hash("sha256", $token);

    $sql = "SELECT Email, reset_token_hash FROM Uzytkownicy WHERE reset_token_hash = ? AND reset_token_zanika_za > NOW()";
    $stmt = $baza->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $uzytkownik = $result->fetch_assoc();
        if ($uzytkownik['reset_token_hash'] === NULL) {
            $komunikat = "<p style='color: red; font-size: 18px; font-weight: bold; text-align: center;'>Ten link do resetu hasła został już użyty lub jest nieważny.</p>";
        } else {
            $email = $uzytkownik['Email'];
            $wyswietlic_formularz = true;
        }
    } else {
        $komunikat = "<p style='color: red; font-size: 18px; font-weight: bold; text-align: center;'>Nieprawidłowy lub przestarzały token.</p>";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $wyswietlic_formularz) {
    $nowe_haslo = $_POST['nowe_haslo'];
    $potwierdzenie_hasla = $_POST['potwierdzenie_hasla'];

    if ($nowe_haslo !== $potwierdzenie_hasla) {
        $komunikat = "<p style='color: red; font-size: 18px; font-weight: bold; text-align: center;'>Hasła nie są identyczne!</p>";
    } else {
        $nowe_haslo_hash = password_hash($nowe_haslo, PASSWORD_DEFAULT);

        
        $sql = "UPDATE Uzytkownicy SET HasloHash = ?, reset_token_hash = NULL, reset_token_zanika_za = NULL WHERE Email = ?";
        $stmt = $baza->prepare($sql);
        $stmt->bind_param("ss", $nowe_haslo_hash, $email);
        $stmt->execute();

        
        $komunikat = "<p style='color: green; font-size: 18px; font-weight: bold; text-align: center;'>Hasło zmieniono poprawnie! Przekierowanie...</p>";
        $pokaz_powrot = false;
        
        echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 3000);</script>";
        $wyswietlic_formularz = false;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset hasła</title>
    <link rel="icon" type="image/x-icon" href="../img.png">
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #28242c;
        }
    </style>
</head>
<body class="bg-dark bg-gradient">

<main>
    <section class="form-container">
        <div id="reset" class="form-section">
            <?php 
            if (!empty($komunikat)) {
                echo $komunikat;
            }

            if ($wyswietlic_formularz) : ?>
                <h2>Zresetuj hasło</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="nowe_haslo">Nowe hasło:</label>
                        <input type="password" id="nowe_haslo" name="nowe_haslo" required>
                    </div>
                    <div class="form-group">
                        <label for="potwierdzenie_hasla">Potwierdź nowe hasło:</label>
                        <input type="password" id="potwierdzenie_hasla" name="potwierdzenie_hasla" required>
                    </div>
                    <button type="submit" class="form-button">Zresetuj hasło</button>
                </form>
            <?php endif; ?>

            <p><a href="login.php">Powrót do logowania</a></p>
        </div>
    </section>
</main>

</body>
</html>