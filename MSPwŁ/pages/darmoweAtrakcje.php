<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>
<main class="mainWyszukiwarki">
    <div class="containerDarmoweAtrkacje">
        <h2>Atrakcje z darmowym wejściem</h2>
        <form  method="GET">
            <label for="IdDarmowegoWejscia">Wybierz dzień tygodnia:</label>
            <select name="IdDarmowegoWejscia" id="IdDarmowegoWejscia">
                <option value="">-- Wybierz dzień tygodnia --</option>
                <option value="1">Poniedziałek</option>
                <option value="2">Wtorek</option>
                <option value="3">Środa</option>
                <option value="4">Czwartek</option>
                <option value="5">Piątek</option>
                <option value="6">Sobota</option>
                <option value="7">Niedziela</option>
            </select>

            <input type="submit" name="submit" value="Filtruj">
        </form> 
        <ul class="listDarmoweAtrkacje">
            <?php
            if(isset($_GET['IdDarmowegoWejscia']) && $_GET['IdDarmowegoWejscia'] !== '') {
                $IdDarmowegoWejscia = $_GET['IdDarmowegoWejscia'];
                $sql = "SELECT * FROM FN_FilterDarmowych(?)"; 
                $params = array(
                    array(&$IdDarmowegoWejscia, SQLSRV_PARAM_IN)
                );
                $stmt = sqlsrv_query($conn, $sql, $params);
            } else {
                $sql = "SELECT * FROM uf_DarmoweAtrakacje()";
                $stmt = sqlsrv_query($conn, $sql);
            }
            
            

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_has_rows($stmt)) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<li class='item'>";
                    $zdjecie = !is_null($row['NazwaZdjecia']) ? htmlspecialchars($row['NazwaZdjecia']) : 'brak_zdjecia.jpg';
                    echo '<div class="imageWrapper"><img src=".././img/' . $zdjecie . '" alt="Zdjęcie obiektu"></div>';
                    echo "<div class='info'>";
                    echo "<h3>".(!is_null($row['Nazwa obiektu']) ? htmlspecialchars($row['Nazwa obiektu']) : 'Brak danych')."</h3>";
                    echo "<p><strong>Nazwa ulicy:</strong> ".(!is_null($row['Nazwa ulicy']) ? htmlspecialchars($row['Nazwa ulicy']) : 'Brak danych')."</p>";
                    echo "<p><strong>Numer ulicy:</strong> ".(!is_null($row['Numer ulicy']) ? htmlspecialchars($row['Numer ulicy']) : 'Brak danych')."</p>";
                    echo "<p><strong>Telefon:</strong> ".(!is_null($row['Telefon']) ? htmlspecialchars($row['Telefon']) : 'Brak danych')."</p>";
                    echo "<p><strong>E-mail:</strong> ".(!is_null($row['E-mail']) ? htmlspecialchars($row['E-mail']) : 'Brak danych')."</p>";
                    echo "<p><strong>Średnia ocena:</strong> ".(!is_null($row['Średnia ocena']) ? htmlspecialchars($row['Średnia ocena']) : 'Brak danych')."</p>";
                    echo "<p><strong>Opis:</strong> ".(!is_null($row['Opis']) ? htmlspecialchars($row['Opis']) : 'Brak danych')."</p>";
                    echo "<p><strong>Rodzaj obiektu:</strong> ".(!is_null($row['Rodzaj obiektu']) ? htmlspecialchars($row['Rodzaj obiektu']) : 'Brak danych')."</p>";
                    echo "<p><strong>Godzina otwarcia:</strong> ".(!is_null($row['Godzina otwarcia']) ? htmlspecialchars($row['Godzina otwarcia']) : 'Brak danych')."</p>";
                    echo "<p><strong>Godzina zamknięcia:</strong> ".(!is_null($row['Godzina zamknięcia']) ? htmlspecialchars($row['Godzina zamknięcia']) : 'Brak danych')."</p>";
                    echo "<p><strong>Darmowe wejście:</strong> ".(!is_null($row['Darmowe wejście']) ? htmlspecialchars($row['Darmowe wejście']) : 'Brak danych')."</p>";
                    echo "</div>";
                    echo "</li>";
                }
            } else {
                echo "<li>Brak danych</li>";
            }
            sqlsrv_free_stmt($stmt);
            ?>
        </ul>
    </div>
</main>
<?php
include(__DIR__ . "/../include/footer.php");
?>
