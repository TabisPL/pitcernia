<?php
$serwer = 'localhost';
$baza_danych = 'srv82461_pizza3test';
$uzytkownik = 'srv82461_pizza3test';
$haslo = '12345678';

$baza = new mysqli($serwer, $uzytkownik, $haslo, $baza_danych);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="navbar.css">
</head>
<body class="bg-dark bg-gradient text-white">
<?php include 'navbar.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php include_once ("index.php"); ?>

    <div class="accordion" id="accordionExample" style="padding: 10px; max-width: 1500px;  margin-left: auto; margin-right: auto;">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        Filtry wyszukiwania
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" >
      <div class="accordion-body " style="background-color: dimgray;">
      <div class="formularz" style="padding: 10px; width: 100%;  margin-left: auto; margin-right: auto;">
      <form method="post" class="row gx-3 gy-2 align-items-center">
          <div class="input-group col-auto">
            <span class="input-group-text ">Cena od do</span>
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

          $sql_skladniki = 'SELECT * FROM Skladniki';
          $skladniki = $baza->query($sql_skladniki);
          $lista_skladniki_id=array();
          $lista_skladniki_id_form=array();
          $row_items = 0;
          foreach ($skladniki as $s ){
            $skladnik = $s["NazwaSkladnika"];
            $skladnik_id = $s["SkladnikID"];

            if ($row_items == 0){
              echo ('<ul class="list-group list-group-horizontal ">');
            }

            echo('
            <li class="list-group-item bg-secondary" style="text-align: left; min-width: 25%;">
              <input class="form-check-input me-1" type="checkbox" checked value="" id="'. $skladnik_id .'" name="'. $skladnik_id .'">
              <label class="form-check-label stretched-link" for="'.$skladnik_id.'"> '. $skladnik .'</label>
            </li>
            ');

            if ($row_items == 3){
              echo ('</ul>');
            }
            $row_items = $row_items + 1;
            if ($row_items == 4){
              $row_items = 0;

            }
            $lista_skladniki_id_form[] = $skladnik_id;
          }
        ?>
        </div>

        <div class="">
          <button type="submit" class="btn btn-primary">Wyszukaj</button>
        </div>
      </form>
    </div>
      </div>
    </div>
  </div>

<!-- Wyświetlanie pizzy  -->

      <div class="row">
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

//        $sql = "SELECT DISTINCT Pizze.*
//         FROM Pizze
//         LEFT JOIN PizzaSkladniki ON Pizze.PizzaID = PizzaSkladniki.PizzaID
//         WHERE Pizze.Cena BETWEEN $cena_min AND $cena_max
//         AND Pizze.Rozmiar LIKE '$rozmiar'";
//
//         // Jeśli wybrane są składniki
//         if (!empty($wybrane_skladniki)) {
//             $ids = implode(',', $wybrane_skladniki);
//             $sql .= " AND Pizze.PizzaID IN (
//                 SELECT PizzaID FROM PizzaSkladniki
//                 WHERE SkladnikID IN ($ids)
//                 GROUP BY PizzaID
//             )";
// }

        $sql = 'SELECT * FROM Pizze JOIN PizzaSkladniki ON Pizze.PizzaID=PizzaSkladniki.PizzaID JOIN Skladniki ON Skladniki.SkladnikID=PizzaSkladniki.SkladnikID
        WHERE Cena BETWEEN '.$cena_min.' AND '.$cena_max.' AND rozmiar LIKE "'. $rozmiar.'" AND Skladniki.SkladnikID = "" ';

        foreach ($lista_skladniki_id as $s_id) {
          $sql = $sql.' OR Skladniki.SkladnikID = '.$s_id;
        }
        $result = $baza->query($sql);
        $pizzaID = [];

        foreach($result as $p) {
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

           echo '<div class="col-lg-3 me-auto" style="padding: 10px">';
           echo '<div class="card h-100 sm-shadow bg-secondary">';
           echo '<div class="card-body">';
           echo '<img class="card-img-top" src="' . $image . '" alt="image" data-bs-toggle="modal" data-bs-target="#pizzaModal' . $id . '">';
           echo '<h3 class="card-title">' . $nazwa . '</h3>';
           echo '<p class="card-text">' . $opis . '</p>';
           echo '<ul class="list-group list-group-flush">';
           echo '<li class="list-group-item bg-secondary">Cena: ' . $cena . ' zł</li>';
           echo '<li class="list-group-item bg-secondary">Rozmiar: ' . $rozmiar . '</li>';
           echo '</ul>';
           echo '<div class="card-body">';
           echo '<form method="post" action="dodaj_do_koszyka.php" class="mt-2">
                  <input type="hidden" name="pizza_id" value="' . $id . '">
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

           // Modal
            echo '<div class="modal fade" id="pizzaModal' . $id . '" tabindex="-1" aria-labelledby="pizzaModalLabel' . $id . '" aria-hidden="true">';
            echo '<div class="modal-dialog">';
            echo '<div class="modal-content bg-dark text-white">';
            echo '<div class="modal-header bg-dark text-white">';
            echo '<h5 class="modal-title" id="pizzaModalLabel' . $id . '">' . $nazwa . ' - Szczegóły</h5>';
            echo '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>';
            echo '</div>';
            echo '<div class="modal-body bg-dark text-white">';
            echo '<img class="img-fluid mb-3" src="' . $image . '" alt="Pizza Image">';
            echo '<p><strong>Opis:</strong> ' . $opis . '</p>';
            echo '<p><strong>Cena:</strong> ' . $cena . ' zł</p>';
            echo '<p><strong>Rozmiar:</strong> ' . $rozmiar . '</p>';
            echo '</div>';
            echo '<div class="modal-footer bg-dark text-white">';
            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>';
            echo '<form method="post" action="dodaj_do_koszyka.php" class="mt-2">
                  <input type="hidden" name="pizza_id" value="' . $id . '">
                  <input type="hidden" name="nazwa" value="' . htmlspecialchars($nazwa) . '">
                  <input type="hidden" name="cena" value="' . $cena . '">
                  <input type="hidden" name="rozmiar" value="' . htmlspecialchars($rozmiar) . '">

                  <div class="input-group">
                  <span class="input-group-text">Ilość</span>
                  <input type="number" name="ilosc" value="1" min="1" class="form-control" style="max-width: 80px;">
                  <button type="submit" class="btn btn-success">Dodaj do koszyka</button>
                  </div>
                  </form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';


          }
        }
      ?>
    </div>
</body>
</html>
