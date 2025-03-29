<?php
session_start();


$serwer = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik = 'root';
$haslo = '';
$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);



if (!isset($_SESSION['UzytkownikID'])) {
    header("Location: ../login.php");
    exit();
}

$uzytkownik_id = $_SESSION['UzytkownikID'];
$komunikat = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $ulica = $_POST['ulica'];
    $numer_domu = $_POST['numer_domu'];
    $numer_mieszkania = !empty($_POST['numer_mieszkania']) ? $_POST['numer_mieszkania'] : 'NULL';
    $miasto = $_POST['miasto'];
    $kod_pocztowy = $_POST['kod_pocztowy'];
    $nazwa_uzytkownika = $_POST['nazwa_uzytkownika'];
    $email = $_POST['email'];

    $check_query = "SELECT UzytkownikID FROM Uzytkownicy WHERE (NazwaUzytkownika='$nazwa_uzytkownika' OR Email='$email') AND UzytkownikID != $uzytkownik_id";
    $check_result = mysqli_query($baza, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['komunikat'] = "<div class='alert alert-danger text-center'>Nazwa użytkownika lub email są już zajęte!</div>";
    } else {
        $updateUzytkownik = "UPDATE Uzytkownicy 
                            SET NazwaUzytkownika='$nazwa_uzytkownika', Email='$email', Imie='$imie', Nazwisko='$nazwisko' 
                            WHERE UzytkownikID=$uzytkownik_id";
        mysqli_query($baza, $updateUzytkownik);

        $result = mysqli_query($baza, "SELECT AdresID FROM AdresyUzytkownikow WHERE UzytkownikID = $uzytkownik_id");

        if (mysqli_num_rows($result) > 0) {
            $updateAdres = "UPDATE AdresyUzytkownikow 
                            SET Ulica='$ulica', NumerDomu='$numer_domu', NumerMieszkania='$numer_mieszkania', Miasto='$miasto', KodPocztowy='$kod_pocztowy' 
                            WHERE UzytkownikID=$uzytkownik_id";
            mysqli_query($baza, $updateAdres);
        } else {
            $insertAdres = "INSERT INTO AdresyUzytkownikow (UzytkownikID, Ulica, NumerDomu, NumerMieszkania, Miasto, KodPocztowy) 
                            VALUES ($uzytkownik_id, '$ulica', '$numer_domu', '$numer_mieszkania', '$miasto', '$kod_pocztowy')";
            mysqli_query($baza, $insertAdres);
        }

        $_SESSION['komunikat'] = "<div class='alert alert-success text-center'>Dane zaktualizowane pomyślnie!</div>";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT Uzytkownicy.NazwaUzytkownika, Uzytkownicy.Email, Uzytkownicy.Imie, Uzytkownicy.Nazwisko, 
               AdresyUzytkownikow.Ulica, AdresyUzytkownikow.NumerDomu, AdresyUzytkownikow.NumerMieszkania, 
               AdresyUzytkownikow.Miasto, AdresyUzytkownikow.KodPocztowy 
        FROM Uzytkownicy 
        LEFT JOIN AdresyUzytkownikow ON Uzytkownicy.UzytkownikID = AdresyUzytkownikow.UzytkownikID 
        WHERE Uzytkownicy.UzytkownikID = $uzytkownik_id";
$result = mysqli_query($baza, $sql);
$dane = mysqli_fetch_assoc($result) ?? [];

if (isset($_SESSION['komunikat'])) {
    $komunikat = $_SESSION['komunikat'];
    unset($_SESSION['komunikat']); 
}

mysqli_close($baza);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edycja danych</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body class="bg-dark text-light">
<?php include '../navbar.php'; ?>  

<div class="container mt-5">
    <h2 class="text-center">Edytuj swoje dane</h2>
    
    <?= $komunikat ?>

    <form method="POST" class="p-4 border rounded bg-secondary">
    
        <div class="mb-3">
            <label for="nazwa_uzytkownika" class="form-label">Nazwa użytkownika:</label>
            <input type="text" class="form-control" id="nazwa_uzytkownika" name="nazwa_uzytkownika" value="<?= htmlspecialchars($dane['NazwaUzytkownika'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($dane['Email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="imie" class="form-label">Imię:</label>
            <input type="text" class="form-control" id="imie" name="imie" value="<?= htmlspecialchars($dane['Imie'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="nazwisko" class="form-label">Nazwisko:</label>
            <input type="text" class="form-control" id="nazwisko" name="nazwisko" value="<?= htmlspecialchars($dane['Nazwisko'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="ulica" class="form-label">Ulica:</label>
            <input type="text" class="form-control" id="ulica" name="ulica" value="<?= htmlspecialchars($dane['Ulica'] ?? '') ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="numer_domu" class="form-label">Numer domu:</label>
                <input type="text" class="form-control" id="numer_domu" name="numer_domu" value="<?= htmlspecialchars($dane['NumerDomu'] ?? '') ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="numer_mieszkania" class="form-label">Numer mieszkania:</label>
                <input type="text" class="form-control" id="numer_mieszkania" name="numer_mieszkania" value="<?= htmlspecialchars($dane['NumerMieszkania'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="miasto" class="form-label">Miasto:</label>
            <input type="text" class="form-control" id="miasto" name="miasto" value="<?= htmlspecialchars($dane['Miasto'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="kod_pocztowy" class="form-label">Kod pocztowy:</label>
            <input type="text" class="form-control" id="kod_pocztowy" name="kod_pocztowy" value="<?= htmlspecialchars($dane['KodPocztowy'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-warning w-100">Zapisz zmiany</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
