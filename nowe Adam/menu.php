<?php
session_start(); // Start sesji
$czyzalogowany = isset($_SESSION['UzytkownikID']);

// Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

// Połączenie z bazą danych
$baza = mysqli_connect($serwer, $uzytkownik, $haslo, $baza_danych);
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MENU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
    <link rel="icon" herf="/img.png" type="image/x-icon">
</head>
<body class="bg-dark text-white">
<?php include 'navbar.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php include_once ("menu.php"); ?> 
    <!-- Formularz wybory pizzy po cenie, wielkości -->
    <div class="formularz">
      <form method="post" >  
          <div class="input-group col-auto">
            <span class="input-group-text">Cena od do</span>
            <input type="number" aria-label="cena_min" class="form-control" name="cena_min" id="cena_min" value="0">
            <input type="number" aria-label="cena_max" class="form-control" name="cena_max" id="cena_max" value="100">
          </div>

        <div class="input-group mb-3">
          <label class="input-group-text" for="rozmiar">Rozmiar</label>
          <select class="form-select" id="rozmiar" name="rozmiar">
            <option value="%" selected>Wybierz rozmiar...</option>
            <option value="Mala">Mała</option>
            <option value="Srednia">Średnia</option>
            <option value="Duza">Duża</option>
          </select>
        </div> 

        <?php
        // Tworzenie formularza do wyboru składników

          // $sql_skladniki = 'SELECT * FROM skladniki';
          // $skladniki = $baza->query($sql_skladniki);
          // var_dump($skladniki);
          // foreach ($skladniki as $s ){
          //   $skladnik = $s["NazwaSkladnika"];
          //   // wybrac ID skladnika zamiast nazwy
          //   $skladnik_id = $s["SkladnikID"];
          //   echo('
          //   <div class="form-check">
          //     <input class="form-check-input" type="checkbox" value="" id="'. $skladnik .'">
          //     <label class="form-check-label" for="'. $skladnik .'">
          //       '. $skladnik .'
          //     </label>
          //   </div>
          //   ');
          // }
        ?>

        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Wyszukaj</button> 
        </div>
      </form>
    </div>


    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
      <?php 

        if (isset($_POST['cena_min'])){
          $cena_min = $_POST['cena_min'];
        } else {
          $cena_min = 0;
        }
        if (isset($_POST['cena_max'])){
          $cena_max = $_POST['cena_max'];
        } else {
          $cena_max = 110;
        }
        if (isset($_POST['rozmiar'])){
          $rozmiar = $_POST['rozmiar'];
        } else {
          $rozmiar = "%";
        }

        //SQL na składniki 
        // SELECT * FROM `PizzaSkladniki` JOIN Pizze ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID  WHERE Skladniki.Nazwa LIKE 'koń'
        // SELECT * FROM Pizze JOIN PizzaSkladniki ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'"';
        $sql = 'SELECT * FROM Pizze WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'"';
        // var_dump($sql);
        $result = $baza->query($sql);
        // var_dump($result);
        foreach($result as $p) {
          $nazwa = $p["Nazwa"];
          $image = $p["ObrazekURL"];
          $opis = $p["Opis"];
          $cena=$p["Cena"];
          $rozmiar = $p["Rozmiar"];
          $pizza_id = $p["PizzaID"];
           // Create card with clickable image
           echo '<div class="col">';
           echo '<div class="card shadow-sm">';
           echo '<div class="card-body">';
           echo '<img class="card-img-top" src="' . $image . '" alt="image" data-bs-toggle="modal" data-bs-target="#pizzaModal' . $pizza_id . '">';
           echo '<h3 class="card-title">' . $nazwa . '</h3>';
           echo '<p class="card-text">' . $opis . '</p>';
           echo '<ul class="list-group list-group-flush">';
           echo '<li class="list-group-item">Cena: ' . $cena . ' zł</li>';
           echo '<li class="list-group-item">Rozmiar: ' . $rozmiar . '</li>';
           echo '</ul>';
           echo '<div class="card-body">';
           echo '<form method="post" action="dodaj_do_koszyka.php" class="mt-2">
                  <input type="hidden" name="pizza_id" value="' . $pizza_id . '">
                  <input type="hidden" name="nazwa" value="' . htmlspecialchars($nazwa) . '">
                  <input type="hidden" name="cena" value="' . $cena . '">
                  <input type="hidden" name="rozmiar" value="' . htmlspecialchars($rozmiar) . '">
                  <input type="hidden" name="ilosc" value="1">
                  <button type="submit" class="btn btn-success mt-2">Dodaj do koszyka</button>
                 </form>';
           echo '</div>';
           echo '</div>';
           echo '</div>';
           echo '</div>';

           // Modal for each pizza
           echo '<div class="modal fade" id="pizzaModal' . $pizza_id . '" tabindex="-1" aria-labelledby="pizzaModalLabel" aria-hidden="true">';
           echo '<div class="modal-dialog">';
           echo '<div class="modal-content">';
           echo '<div class="modal-header">';
           echo '<h5 class="modal-title" id="pizzaModalLabel">' . $nazwa . ' - Szczegóły</h5>';
           echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
           echo '</div>';
           echo '<div class="modal-body">';
           echo '<img class="img-fluid mb-3" src="' . $image . '" alt="Pizza Image">';
           echo '<p><strong>Opis:</strong> ' . $opis . '</p>';
           echo '<p><strong>Cena:</strong> ' . $cena . ' zł</p>';
           echo '<p><strong>Rozmiar:</strong> ' . $rozmiar . '</p>';
           echo '</div>';
           echo '<div class="modal-footer">';
           echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>';
           echo '<form method="post" action="dodaj_do_koszyka.php" class="mt-2">
                  <input type="hidden" name="pizza_id" value="' . $pizza_id . '">
                  <input type="hidden" name="nazwa" value="' . htmlspecialchars($nazwa) . '">
                  <input type="hidden" name="cena" value="' . $cena . '">
                  <input type="hidden" name="rozmiar" value="' . htmlspecialchars($rozmiar) . '">
                  
                  <div class="input-group">Ilość
                      <input type="number" name="ilosc" value="1" min="1" class="form-control" style="width: 80px;">
                      <button type="submit" class="btn btn-success">Dodaj do koszyka</button>
                  </div>
                </form>';
           echo '</div>';
           echo '</div>';
           echo '</div>';
           echo '</div>';
        // var_dump($p);
        }
      ?>

      </div>
    </div>
    
</body>
</html>
