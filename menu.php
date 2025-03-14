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
    <title>Logowanie</title>
    <link rel="stylesheet" href="menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/navbar.css">
</head>
<body>
<?php include '/navbar.php'; ?>
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
          echo '<div class="card-deck">';
          echo '
          <div class="col">
          <div class="card shadow-sm ">
            <div class="card-body">
              <img class="card-img-top" src="'. $image .'" alt="image">
              <h3 class="card-title">' . $nazwa . '</h3>
              <p class="card-text">' . $opis . '</p>
            
            <ul class="list-group list-group-flush">
              <li class="list-group-item">Cena: ' . $cena . ' zł</li>
              <li class="list-group-item">Rozmiar: '.$rozmiar.'</li>
            </ul>
            <div class="card-body">
              <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="card-link">Kup</a>
            </div>
          </div>
          </div>
        </div>
        ';
        echo '</div>';
        // var_dump($p);
        }
      ?>

      </div>
    </div>
    
</body>
</html>
