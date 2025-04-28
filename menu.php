<?php
session_start(); // Start sesji
$czyzalogowany = isset($_SESSION['UzytkownikID']);

//Dane do połączenia z bazą danych
$serwer = 'localhost';
$baza_danych = 'pizza3test';
$uzytkownik = 'root';
$haslo = '';

//Połączenie z bazą danych
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
    <link rel="stylesheet" href="navbar.css">
</head>
<body style="background-image: linear-gradient(to bottom right, rgb(20,20,20), rgb(40,40,40)); background-size: 1920px 1080px; color: white;">
<?php include 'navbar.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php include_once ("menu.php"); ?>
    <!-- Formularz wybory pizzy po cenie, wielkości -->
    <div class="formularz" style="width: 100%; padding: 20px;">
      <form method="post" class="row gx-3 gy-2 align-items-center">  
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
        <div class="container-fluid text-center overflow-hidden">
        <?php
        //Tworzenie formularza do wyboru składników

          $sql_skladniki = 'SELECT * FROM skladniki';
          $skladniki = $baza->query($sql_skladniki);
          $lista_skladniki_id=array();
          $lista_skladniki_id_form=array();
          foreach ($skladniki as $s ){
            $skladnik = $s["NazwaSkladnika"];
            // wybrac ID skladnika zamiast nazwy
            $skladnik_id = $s["SkladnikID"];
            echo('
            <div class="form-check form-check-inline col-sm-3">
              <input class="btn-check" type="checkbox" value="" id="'. $skladnik_id .'" name="'. $skladnik_id .'">
              <label class="btn btn-primary" for="'. $skladnik_id .'">
                '. $skladnik .'
              </label>
            </div>
            ');
            $lista_skladniki_id_form[] = $skladnik_id;
          }
        ?>
        </div>

        <div class="">
          <button type="submit" class="btn btn-primary">Wyszukaj</button> 
        </div>
      </form>
    </div>

<!-- Wyświetlanie pizzy  -->

      <div class="row row-cols-1 row-cols-md-3 g-4" style="padding: 20px; width: 1500px;  margin-left: auto; margin-right: auto;">
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

        foreach ($lista_skladniki_id_form as $sf_id) {
          if (isset($_POST[$sf_id])){
            $lista_skladniki_id[] = $sf_id;
          }
        }

        //SQL na składniki 

        // SELECT * FROM `PizzaSkladniki` JOIN Pizze ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID  WHERE Skladniki.Nazwa LIKE 'koń'
        // SELECT * FROM Pizze JOIN PizzaSkladniki ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'"';
        // $sql = 'SELECT * FROM Pizze WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'"';
        $sql = 'SELECT * FROM Pizze JOIN PizzaSkladniki ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID 
        WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'" AND Skladniki.SkladnikID = "" ';

        // foreach append do $sql "OR $skladnikid 
        foreach ($lista_skladniki_id as $s_id) {
          $sql = $sql.' OR Skladniki.SkladnikID = '.$s_id;
        }


        $result = $baza->query($sql);


        $pizzaID = [];
      
        foreach($result as $p) {
          $r = rand(0,255);
          $g = rand(0,255);
          $b = rand(0,255);

          $r2 = rand(0,255);
          $g2 = rand(0,255);
          $b2 = rand(0,255);

          $r3 = rand(0,255);
          $g3 = rand(0,255);
          $b3 = rand(0,255);

          // $pizzaID [] = $p["PizzaID"];

          $nazwa = $p["Nazwa"];
          $image = $p["ObrazekURL"];
          $opis = $p["Opis"];
          $cena=$p["Cena"];
          $rozmiar = $p["Rozmiar"];
          $id = $p["PizzaID"];
          if (isset($id) == 0) {
            $id = "";
          }
          
          if (in_array($id, $pizzaID) == 0) {
            $pizzaID [] = $p["PizzaID"];
            
            echo '
            <div class="col">
              <div class="card h-100 sm-shadow ">
                <div class="card-body" style="background-color: rgb('.$r.','.$g.','.$b.')">
                  <img class="card-img-top" src="'. $image .'" alt="image">
                  <h3 class="card-title">' . $nazwa . '</h3>
                  <p class="card-text">' . $opis . '</p>

                  <ul class="list-group list-group-flush">
                    <li class="list-group-item" style="background-color: rgb('.$r2.','.$g2.','.$b2.')">Cena: ' . $cena . ' zł</li>
                    <li class="list-group-item" style="background-color: rgb('.$r3.','.$g3.','.$b3.')">Rozmiar: '.$rozmiar.'</li>
                  </ul>
                  <div class="card-body">
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="card-link">Kup</a>
                  </div>
                </div>
              </div>
            </div>
            ';
          }
        }
      ?>
    </div>
    

    <!-- <div class="row row-cols-1 row-cols-md-3 g-4">
  <div class="col">
    <div class="card h-100">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card h-100">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a short card.</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card h-100">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content.</p>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="card h-100">
      <img src="..." class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
      </div>
    </div>
  </div>
</div> -->


</body>
</html>
