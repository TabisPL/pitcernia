!!!Nigdy więcej nie brać Wojtka do grupy!!!

### Dodawanie ikonki ###

W sekcji <header> trzeba dopisać:
<link rel="icon" type="image/x-icon" href="../img.png">

### Dodawanie Navbara ###

Aby dołączyć navbara do strony należy dodać:
1. <link rel="stylesheet" href="navbar.css">
do sekcji <head>
2. <?php include 'navbar.php'; ?>
w miejsce gdzie chcemy mieć wyświetlonego navbara (najlepiej na samym początku sekcji <body>)

Potrzebne skrypty php są już zaimplementowane w pliku więc powinno działać bez żadnego dodatkowego skryptu

### Łączenie z BD na SeoHost ###

// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

### Zmiany w BD ###
ALTER TABLE uzytkownicy
ADD COLUMN token_akt VARCHAR(255) NULL,
ADD COLUMN token_akt_wyg DATETIME NULL;
