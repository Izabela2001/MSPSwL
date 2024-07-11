<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>

<div class="mainMurale">
    <h1>Top 5 Obiektów</h1>
    <a href="<?php echo BASE_URL; ?>pages/wyszukiwarka.php" class="button-link">Wyszukaj</a>
    <ul class="muraleList">
        <?php
        $sql = "SELECT * FROM uf_Top5Obiektow()";
        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $zdjecie = !is_null($row['NazwaZdjecia']) ? htmlspecialchars($row['NazwaZdjecia']) : 'brak_zdjecia.jpg';
            echo '<li class="mural">';
            echo '<div class="imageWrapper"><img src=".././img/' . $zdjecie . '" alt="Zdjęcie obiektu"></div>';
            echo '<div class="info">';
            echo "<p><strong>Obiekt:</strong> " . (!is_null($row['Nazwa obiektu']) ? $row['Nazwa obiektu'] : 'Brak danych') . "</p>";
            echo "<p><strong>Nazwa ulicy:</strong> " . (!is_null($row['Nazwa ulicy']) ? $row['Nazwa ulicy'] : 'Brak danych') . "</p>";
            echo "<p><strong>Numer ulicy:</strong> " . (!is_null($row['Numer ulicy']) ? $row['Numer ulicy'] : 'Brak danych') . "</p>";
            echo "<p><strong>Telefon:</strong> " . (!is_null($row['Telefon']) ? $row['Telefon'] : 'Brak danych') . "</p>";
            echo "<p><strong>E-mail:</strong> " . (!is_null($row['E-mail']) ? $row['E-mail'] : 'Brak danych') . "</p>";
            echo "<p><strong>Średnia ocena:</strong> " . (!is_null($row['Średnia ocena']) ? $row['Średnia ocena'] : 'Brak danych') . "</p>";
            echo "<p><strong>Opis:</strong> " . (!is_null($row['Opis']) ? $row['Opis'] : 'Brak danych') . "</p>";
            echo "<p><strong>Rodzaj obiektu:</strong> " . (!is_null($row['Rodzaj obiektu']) ? $row['Rodzaj obiektu'] : 'Brak danych') . "</p>";
            echo '</div>';
            echo '</li>';
        }
        sqlsrv_free_stmt($stmt);
        ?>
    </ul>
</div>
<?php
include(__DIR__ . "/../include/footer.php");
?>
