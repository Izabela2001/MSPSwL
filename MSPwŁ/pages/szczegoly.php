<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>
<h2 id="nag">Szczegóły obiektu:</h2>
<?php
echo "<a href=\"javascript:history.go(-1)\" class=\"button-link\">Powrót</a>";
?>
<main class="mainSczegol">

    <?php
    if (isset($_GET['Idobiektu'])) {
        $idObiektu = $_GET['Idobiektu'];

        $sql = "EXEC up_SzczegolObiektu ?";
        $params = array($idObiektu);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $zdjecie = !is_null($row['NazwaZdjecia']) ? htmlspecialchars($row['NazwaZdjecia']) : 'brak_zdjecia.jpg';
            echo '<div class="imageWrapperSzcz"><img src=".././img/' . $zdjecie . '" alt="Zdjęcie obiektu"></div>';
            echo "<div class='infoSzczegol'>";
            echo "<p><strong>Obiekt:</strong> " . (!is_null($row['Nazwa obiektu']) ? $row['Nazwa obiektu'] : 'Brak danych') . "</p>";
            echo "<p><strong>Nazwa ulicy:</strong> " . (!is_null($row['Nazwa ulicy']) ? $row['Nazwa ulicy'] : 'Brak danych') . "</p>";
            echo "<p><strong>Numer ulicy:</strong> " . (!is_null($row['Numer ulicy']) ? $row['Numer ulicy'] : 'Brak danych') . "</p>";
            echo "<p><strong>Opis:</strong> " . (!is_null($row['Opis']) ? $row['Opis'] : 'Brak danych') . "</p>";
            echo "<p><strong>Średnia ocena:</strong> " . (!is_null($row['Średnia ocena']) ? $row['Średnia ocena'] : 'Brak danych') . "</p>";
            echo "<p><strong>Telefon:</strong> " . (!is_null($row['Telefon']) ? $row['Telefon'] : 'Brak danych') . "</p>";
            echo "<p><strong>E-mail:</strong> " . (!is_null($row['E-mail']) ? $row['E-mail'] : 'Brak danych') . "</p>";
            echo "<p><strong>Darmowe wejście:</strong> " . (!is_null($row['Darmowe wejście']) ? $row['Darmowe wejście'] : 'Brak danych') . "</p>";
            echo "<p><strong>Dostosowane do dzieci:</strong> " . (!is_null($row['Dostosowane do dzieci']) ? $row['Dostosowane do dzieci'] : 'Brak danych') . "</p>";
            echo "<p><strong>Czy jest ogórdek?</strong> " . (!is_null($row['Czy jest ogórdek?']) ? $row['Czy jest ogórdek?'] : 'Brak danych') . "</p>";
            echo "<p><strong>Czy jest strefa palacza?</strong> " . (!is_null($row['Czy jest strefa palacza?']) ? $row['Czy jest strefa palacza?'] : 'Brak danych') . "</p>";
            echo "<p><strong>Czy wpuszczają zwierzęta?</strong> " . (!is_null($row['Czy wpuszczają zwierzęta?']) ? $row['Czy wpuszczają zwierzęta?'] : 'Brak danych') . "</p>";
            echo "<p><strong>Czy dostosowane do niepełnosprawnych?</strong> " . (!is_null($row['Czy dostosowane do niepełnosprawnych?']) ? $row['Czy dostosowane do niepełnosprawnych?'] : 'Brak danych') . "</p>";
            echo "<p><strong>Strefa dla dzieci?</strong> " . (!is_null($row['Strefa dla dzieci?']) ? $row['Strefa dla dzieci?'] : 'Brak danych') . "</p>";
            echo "<p><strong>Godzina otwarcia:</strong> " . (!is_null($row['Godzina otwarcia']) ? $row['Godzina otwarcia'] : 'Brak danych') . "</p>";
            echo "<p><strong>Godzina zamknięcia:</strong> " . (!is_null($row['Godzina zamknięcia']) ? $row['Godzina zamknięcia'] : 'Brak danych') . "</p>";
            echo "</div>";
            
            

        } else {
            echo "<p>Nie znaleziono szczegółów dla tego obiektu.</p>";
        }

        sqlsrv_free_stmt($stmt);
    } else {
        echo "<p>Nieprawidłowe żądanie. Brak przekazanego ID obiektu.</p>";
    }
    ?>

</main>
<?php
include(__DIR__ . "/../include/footer.php");
?>

