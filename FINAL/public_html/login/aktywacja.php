
<?php
$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$conn = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);


    $token = $_GET['Token_akt']; 
    $sql = "SELECT * FROM Uzytkownicy WHERE Token_akt = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
        
    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();
        $token_akt_wyg = $user['Token_akt_wyg'];

        

        if (strtotime($token_akt_wyg) > time()) {

            $sql_update = "UPDATE Uzytkownicy SET CzyAktywny = TRUE, Token_akt = NULL, Token_akt_wyg = NULL WHERE Token_akt = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('s', $token);
            if ($stmt_update->execute()) {
                echo "<h2>Twoje konto zostalo aktywowane. Mozesz teraz sie zalogowac.</h2>";
            } else {
                echo "<h2>Wystąpił blad podczas aktywacji konta.</h2>";
            }
        } else {
            echo "<h2>Link aktywacyjny wygasl.</h2>";
        }
    } else {
        echo "<h2>Link aktywacyjny jest nieprawidlowy.</h2>";
    }

        
$conn->close();
?>