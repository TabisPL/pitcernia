<?php


/*
Przydatne linki
https://codeshack.io/secure-login-system-php-mysql/
https://www.w3schools.com/php/php_mysql_prepared_statements.asp

*/

$czyzalogowany = isset($_SESSION['UzytkownikID']);

$serwer = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik = 'root';
$haslo = '';

$conn = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);

$komunikat = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazwa_uzytkownika = trim($_POST['nazwa_uzytkownika']);
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $email = trim($_POST['email']);
    $haslo = $_POST['haslo'];
    $potwierdzenie_hasla = $_POST['potwierdzenie_hasla'];
    $ulica = trim($_POST['ulica']);
    $numer_domu = trim($_POST['numer_domu']);
    $numer_mieszkania = trim($_POST['numer_mieszkania']);
    $kod_pocztowy = trim($_POST['kod_pocztowy']);
    $miasto = trim($_POST['miasto']);

    if (empty($nazwa_uzytkownika) || empty($email) || empty($haslo)) {
        $komunikat = "<p style='color: red;'>Wszystkie pola są wymagane!</p>";
    } elseif ($haslo !== $potwierdzenie_hasla) {
        $komunikat = "<p style='color: red;'>Hasła nie są takie same.</p>";
    } else {
        $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM uzytkownicy WHERE NazwaUzytkownika = ? OR Email = ?");
        $stmt->bind_param('ss', $nazwa_uzytkownika, $email);
        $stmt->execute();
        $stmt->bind_result($liczba_rekordow);
        $stmt->fetch();
        $stmt->close();

        if ($liczba_rekordow > 0) {
            $komunikat = "<p style='color: red;'>Nazwa użytkownika lub e-mail są już w użyciu.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO Uzytkownicy (NazwaUzytkownika, Imie, Nazwisko, Email, HasloHash, DataRejestracji, CzyAktywny) VALUES (?, ?, ?, ?, ?, NOW(), TRUE)");
            $stmt->bind_param('sssss', $nazwa_uzytkownika, $imie, $nazwisko, $email, $haslo_hash);
            
            if ($stmt->execute()) {
                $uzytkownik_id = $stmt->insert_id;
                $stmt->close();
                
                $stmt = $conn->prepare("INSERT INTO adresyuzytkownikow (UzytkownikID, Miasto, Ulica, NumerDomu, NumerMieszkania, KodPocztowy) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('isssss', $uzytkownik_id, $miasto, $ulica, $numer_domu, $numer_mieszkania, $kod_pocztowy);
                $stmt->execute();
                $stmt->close();
                
                // Automatyczne logowanie po rejestracji
                $_SESSION['UzytkownikID'] = $uzytkownik_id;
                $_SESSION['Zalogowany'] = true;
                
                
                header('Location: mojekonto.php');
                exit;
            } else {
                $komunikat = "<p style='color: red;'>Błąd podczas rejestracji użytkownika.</p>";
            }
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>

    
    <link rel="stylesheet" href="login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<?php include '../navbar.php'; ?> <!-- TUTAJ NAVBAR INCLUDE JEST (komentarz dla mnie jakbym zgubil go) -->
    <main>
        <section class="form-container">
        

            <!-- Formularz doo rejestracji -->
            <div id="register" class="form-section">
    <h2>Zarejestruj się</h2>
    <form action="" method="post">
        <!-- Dane osobowe usera -->
        <div class="form-group">
            <label for="nazwa_uzytkownika">Nazwa użytkownika:</label>
            <input type="text" id="nazwa_uzytkownika" name="nazwa_uzytkownika" required>
        </div>
        <div class="form-group">
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required>
        </div>
        <div class="form-group">
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="haslo">Hasło:</label>
            <input type="password" id="haslo" name="haslo" required>
        </div>
        <div class="form-group">
            <label for="potwierdzenie_hasla">Potwierdź hasło:</label>
            <input type="password" id="potwierdzenie_hasla" name="potwierdzenie_hasla" required>
        </div>
        
        <!-- Adres usera -->
        <h3>Adres</h3>
        <div class="form-group">
            <label for="ulica">Ulica:</label>
            <input type="text" id="ulica" name="ulica" required>
        </div>
        <div class="form-group">
            <label for="numer_domu">Numer domu:</label>
            <input type="text" id="numer_domu" name="numer_domu" required>
        </div>
        <div class="form-group">
            <label for="numer_mieszkania">Numer mieszkania:</label>
            <input type="text" id="numer_mieszkania" name="numer_mieszkania">
        </div>
        <div class="form-group">
            <label for="kod_pocztowy">Kod pocztowy:</label>
            <input type="text" id="kod_pocztowy" name="kod_pocztowy" required>
        </div>
        <div class="form-group">
            <label for="miasto">Miasto:</label>
            <input type="text" id="miasto" name="miasto" required>
        </div>

        <button type="submit" class="form-button">Zarejestruj się</button>

        
        <?php if (!empty($komunikat)): ?>
                <div class="komunikat <?php echo strpos($komunikat, 'sukcesem') !== false ? 'success' : 'error'; ?>">
                    <?php echo $komunikat; ?>
                </div>
            <?php endif; ?>
    </form>
</div>
        </section>
    </main>
</body>
</html>