<?php
session_start();
$czyzalogowany = isset($_SESSION['UzytkownikID']);

$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);

// Zmienna na komunikaty (np będne haslo, nieaktywny gosciu itp)
$komunikat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $haslo = $_POST['haslo'];

                    
    $stmt = $baza->prepare("SELECT UzytkownikID, HasloHash, Czyaktywny FROM Uzytkownicy WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        
        $stmt->bind_result($UzytkownikID, $HasloHash, $Czyaktywny);
        $stmt->fetch();

        
        if ($Czyaktywny == 0) {
            $komunikat = "<p style='color: red;'>Twoje konto jest nieaktywne, ale lipa</p>";
            } elseif (password_verify($haslo, $HasloHash)) {
                            

            #echo "yatta";
            header('Location: ../UserPanel/userPanel.php'); // Przekierowanie na strone konta (user panel)

            $_SESSION['UzytkownikID'] = $UzytkownikID;
            # var_dump($UzytkownikID);
                        
                    
            exit;
            } else {
                $komunikat = "<p style='color: red;'>Nieprawidłowe hasło.</p>";
            }
            } else {
                $komunikat = "<p style='color: red;'>Użytkownik z podanym e-mailem nie istnieje.</p>";
    }
}
                ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" type="image/x-icon" href="../img.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark bg-gradient text-white">
<?php include '../navbar.php'; ?>  <!-- TUTAJ NAVBAR INCLUDE JEST (komentarz dla mnie jakbym zgubil go) -->
    <main>
        <section class="form-container">
            <div id="login" class="form-section p-4 border rounded bg-secondary shadow text-center">
                <h2>Zaloguj się</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="haslo">Hasło:</label>
                        <input type="password" id="haslo" name="haslo" required>
                    </div>
                    <button type="submit" name="login" class="form-button">Zaloguj się</button>
                </form>

                
                
                <p>Nie masz konta? <a href="rejestracja.php">Zarejestruj się</a></p>
                <p>Nie pamiętasz hasła? <a href="zapomnialem.php"> Reset hasła </a></p>
            </div>
            <?php if (!empty($komunikat)): ?>
                <div class="komunikat">
                    <?php echo $komunikat; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
