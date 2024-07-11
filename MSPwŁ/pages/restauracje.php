<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>
<main class="mainM">
    <h1>Restauracje</h1>
    <a href="<?php echo BASE_URL; ?>pages/wyszukiwarka.php" class="button-link">Wyszukaj</a>
    <ul class="eList">
        <?php
        $sql = "SELECT * FROM VW_Restauracje ";
        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt)) {
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<li class='mobiekt'>";
                $zdjecie = !is_null($row['NazwaZdjecia']) ? htmlspecialchars($row['NazwaZdjecia']) : 'brak_zdjecia.jpg';
                echo '<div class="imageWrapper"><img src=".././img/' . $zdjecie . '" alt="Zdjęcie obiektu"></div>';
                echo "<div class='info'>";
                echo "<h2><strong>Obiekt:</strong> " . (!is_null($row['Nazwa obiektu']) ? htmlspecialchars($row['Nazwa obiektu']) : 'Brak danych') . "</h2>";
                echo "<p><strong>Opis:</strong> " . (!is_null($row['Opis']) ? htmlspecialchars($row['Opis']) : 'Brak danych') . "</p>";
                echo "<p><strong>Numer lokalu:</strong> " . (!is_null($row['Numer lokalu']) ? htmlspecialchars($row['Numer lokalu']) : 'Brak danych') . "</p>";
                echo "<p><strong>Nazwa ulicy:</strong> " . (!is_null($row['Nazwa ulicy']) ? htmlspecialchars($row['Nazwa ulicy']) : 'Brak danych') . "</p>";
                echo "<p><strong>Numer ulicy:</strong> " . (!is_null($row['Numer ulicy']) ? htmlspecialchars($row['Numer ulicy']) : 'Brak danych') . "</p>";
                echo "<p><strong>Średnia ocena:</strong> " . (!is_null($row['Średnia ocena']) ? htmlspecialchars($row['Średnia ocena']) : 'Brak danych') . "</p>";
                echo "<p><strong>Rodzaj kuchni:</strong> " . (!is_null($row['Kuchnia']) ? htmlspecialchars($row['Kuchnia']) : 'Brak danych') . "</p>";
                echo "<p><strong>Godzina otwarcia:</strong> " . (!is_null($row['Godzina otwarcia']) ? htmlspecialchars($row['Godzina otwarcia']) : 'Brak danych') . "</p>";
                echo "<p><strong>Godzina zamknięcia:</strong> " . (!is_null($row['Godzina zamknięcia']) ? htmlspecialchars($row['Godzina zamknięcia']) : 'Brak danych') . "</p>";
                echo "<p><strong>Osoba zarządająca obiektem </strong></p>";
                echo "<p><strong>Imię :</strong> " . (!is_null($row['Imię']) ? htmlspecialchars($row['Imię']) : 'Brak danych') . "</p>";
                echo "<p><strong>Nazwisko :</strong> " . (!is_null($row['Nazwisko']) ? htmlspecialchars($row['Nazwisko']) : 'Brak danych') . "</p>";
                echo "<p><strong>Stanowisko:</strong> " . (!is_null($row['Stanowisko']) ? htmlspecialchars($row['Stanowisko']) : 'Brak danych') . "</p>";
                echo "<p><strong>Telefon:</strong> " . (!is_null($row['Telefon']) ? htmlspecialchars($row['Telefon']) : 'Brak danych') . "</p>";
                echo "<div class='button-row'>";
                echo "<form action='szczegoly.php' method='get'>";
                echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                echo "<input type='submit' value='Pokaż szczegóły'class='favorites-button'>";
                echo "</form>";
                echo "<form action='opinieOceny.php' method='get'>";
                echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                echo "<input type='submit' value='Pokaż oceny i opinie' class='review-button'>";
                echo "</form>";
                echo "<form action='harmonogram.php' method='get'>";
                echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                echo "<input type='submit' value='Harmonogram otwracia' class='rating-button'>";
                echo "</form>";
                echo "</div>";
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 3) {
                    echo "<div class='button-row'>";
                    echo "<form action='dodaj_do_ulubionych.php' method='get'>";
                    echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                    echo "<input type='submit' value='Dodaj do ulubionych' class='favorites-button'>";
                    echo "</form>";
                    echo "<form action='dodaj_opinie.php' method='get'>";
                    echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                    echo "<input type='submit' value='Dodaj opinię' class='review-button'>";
                    echo "</form>";
                    echo "<form action='dodaj_ocene.php' method='get'>";
                    echo "<input type='hidden' name='Idobiektu' value='" . htmlspecialchars($row['Idobiektu']) . "'>";
                    echo "<input type='submit' value='Dodaj ocenę' class='rating-button'>";
                    echo "</form>";
                    echo "</div>";
                }
                echo "</div>";
                echo "</li>";
            }
        } else {
            echo "<li>BRAK DANYCH</li>";
        }
        sqlsrv_free_stmt($stmt);
        ?>
    </ul>
</main>
<?php
include(__DIR__ . "/../include/footer.php");
?>