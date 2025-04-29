<?php

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$conn = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);

$email = $_POST["email"];

$sql = "SELECT * FROM Uzytkownicy WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);

    
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    
    $sql_update = "UPDATE Uzytkownicy SET reset_token_hash = ?, reset_token_zanika_za = ? WHERE Email = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sss", $token_hash, $expiry, $email);
    $stmt_update->execute();
    $stmt_update->close();

    
    $link = "https://srv82461.seohost.com.pl/login/reset-hasla.php?token=" . $token;

    
    $temat = "Reset hasła";
    $tresc = "Kliknij w poniższy link, aby zresetować swoje hasło:\n\n" . $link;
    $naglowek = "From: srv82461@srv82461.seohost.com.pl";

    
    mail($email, $temat, $tresc, $naglowek);
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset</title>
    <link rel="icon" type="image/x-icon" href="../img.png">
    
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #28242c;
            color: white;
            margin-top: 10%;
            margin-left: 14%;
        }
    </style>
</head>
<body class="bg-dark bg-gradient text-white">
    <h2>Na Twój adres e-mail został wysłany link do resetu hasła.</h2><br>
    <h2>Proszę sprawdzić skrzynkę odbiorczą.</h2>
</body>