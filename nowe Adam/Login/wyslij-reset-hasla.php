<?php

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);

$email = $_POST["email"];

$sql = "SELECT * FROM uzytkownicy WHERE Email = ?";
$stmt = $baza->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);

    
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    
    $sql_update = "UPDATE uzytkownicy SET reset_token_hash = ?, reset_token_zanika_za = ? WHERE Email = ?";
    $stmt_update = $baza->prepare($sql_update);
    $stmt_update->bind_param("sss", $token_hash, $expiry, $email);
    $stmt_update->execute();

    
    $link = "https://srv82461.seohost.com.pl/login/reset-hasla.php?token=" . $token;

    
    $temat = "Reset hasła";
    $tresc = "Kliknij w poniższy link, aby zresetować swoje hasło:\n\n" . $link;
    $naglowek = "From: srv82461@srv82461.seohost.com.pl";

    
    mail($email, $temat, $tresc, $naglowek);
}


echo "<h2>Na Twój adres e-mail został wysłany link do resetu hasła. Proszę sprawdzić skrzynkę odbiorczą.</h2>";
?>