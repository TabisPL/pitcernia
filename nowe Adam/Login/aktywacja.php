<?php
$serwer = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik = 'root';
$haslo = '';

$conn = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);


    $token = $_GET['token']; 
    $sql = "SELECT * FROM uzytkownicy WHERE token_akt = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();
        $token_akt_wyg = $user['token_akt_wyg'];
    
        if (strtotime($token_akt_wyg) > time()) {

            $sql_update = "UPDATE uzytkownicy SET CzyAktywny = TRUE, token_akt = NULL, token_akt_wyg = NULL WHERE token_akt = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('s', $token);
            if ($stmt_update->execute()) {
                echo "<h2>Twoje konto zostało aktywowane. Możesz teraz się zalogować.</h2>";
            } else {
                echo "<h2>Wystąpił błąd podczas aktywacji konta.</h2>";
            }
        } else {
            echo "<h2>Link aktywacyjny wygasł.</h2>";
        }
    } else {
        echo "<h2>Link aktywacyjny jest nieprawidłowy.</h2>";
    }

$conn->close();
?>