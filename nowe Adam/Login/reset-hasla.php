<?php
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
    <title>Reset hasła</title>

    
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<Body>
    <?php 

        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $token_hash = hash("sha256", $token); 

            $sql = "SELECT * FROM uzytkownicy WHERE reset_token_hash = ? AND reset_token_zanika_za > NOW()";
            $stmt = $baza->prepare($sql);
            $stmt->bind_param("s", $token_hash);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                echo '<form action="zresetuj-haslo.php" method="POST"> 
                        <input type="hidden" name="token" value="' . $token . '">
                        <label for="new_password">Nowe hasło:</label> 
                        <input type="password" name="new_password" required>
                        <button type="submit">Zresetuj hasło</button>
                    </form>';
            } else {
                echo "Token jest nieważny lub wygasł.";
            }
        } else {
            echo "Brak tokenu.";
        }
    ?>