<?php
global $conn;
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");


try {
    $sql_typy_obiektow = "SELECT IdTypObiektu, NazwaTypu FROM tbl_typ_obiektu";
    $stmt_typy_obiektow = sqlsrv_query($conn, $sql_typy_obiektow);

    if ($stmt_typy_obiektow === false) {
        die("Błąd zapytania typów obiektów: " . print_r(sqlsrv_errors(), true));
    }
?>
<form method="POST">
    <main class="mainWyszukiwarki">
        <label>Rodzaj miejsca</label>
        <select name="typ_obiektu">
            <option value="">--Rodzaj miejsca--</option>
            <?php while ($row_typy_obiektow = sqlsrv_fetch_array($stmt_typy_obiektow, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= $row_typy_obiektow['IdTypObiektu'] ?>"><?= $row_typy_obiektow['NazwaTypu'] ?></option>
            <?php endwhile; ?>
        </select>
        <label>
            Ogórdek:
            <select name="Ogrodek">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label>
        <label>
            Strefa dla palaczy:
            <select name="strefa_palacza">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label>
        <label>
            Strefa dla dzieci:
            <select name="strefa_dziecka">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label><br>
        <label>
            Wpuszczanie zwierząt:
            <select name="WpuszczanieZwierzat">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label>
        <label>
            Dostosowane dla niepełnosprawnych zwierząt:
            <select name="DlaNiepelnosprawnych">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label>
        <label>
            Dostosowane dla dzieci:
            <select name="DlaDzieci">
                <option value="">Wybierz</option>
                <option value="1">Tak</option>
                <option value="0">Nie</option>
            </select>
        </label>
        <label>
            Godzina otwarcia:
            <input type="time" name="godzina_otwarcia">
        </label>
        <label>
            Godzina zamknięcia:
            <input type="time" name="godzina_zamkniecia">
        </label>
        <input type="submit" name="submit" value="Filtruj">
    </main>
</form>
<?php
    if (isset($_POST['submit'])) {
        $selected_typ_obiektu = !empty($_POST['typ_obiektu']) ? $_POST['typ_obiektu'] : null;
        $godzina_otwarcia = !empty($_POST['godzina_otwarcia']) ? $_POST['godzina_otwarcia'] : null;
        $godzina_zamkniecia = !empty($_POST['godzina_zamkniecia']) ? $_POST['godzina_zamkniecia'] : null;
        $strefa_palacza = isset($_POST['strefa_palacza']) && $_POST['strefa_palacza'] !== '' ? $_POST['strefa_palacza'] : null;
        $ogrod = isset($_POST['Ogrodek']) && $_POST['Ogrodek'] !== '' ? $_POST['Ogrodek'] : null;
        $strefa_dziecka = isset($_POST['strefa_dziecka']) && $_POST['strefa_dziecka'] !== '' ? $_POST['strefa_dziecka'] : null;
        $WpuszczanieZwierzat = isset($_POST['WpuszczanieZwierzat']) && $_POST['WpuszczanieZwierzat'] !== '' ? $_POST['WpuszczanieZwierzat'] : null;
        $DlaNiepelnosprawnych= isset($_POST['DlaNiepelnosprawnych']) && $_POST['DlaNiepelnosprawnych'] !== '' ? $_POST['DlaNiepelnosprawnych'] : null;
        $DlaDzieci = isset($_POST['DlaDzieci']) && $_POST['DlaDzieci'] !== '' ? $_POST['DlaDzieci'] : null;

        $params = array(
            $selected_typ_obiektu,
            $ogrod,
            $strefa_palacza,
            $strefa_dziecka,
            $WpuszczanieZwierzat,
            $DlaNiepelnosprawnych,
            $DlaDzieci,
            $godzina_otwarcia,
            $godzina_zamkniecia,
        );


        $sql = "SELECT * FROM uf_WyszukajObiekty(?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die("Błąd zapytania: " . print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt) === true) {
            echo '<div class="mainObiekty">';
            echo '<table class="tablaObiekty">';
            echo '<thead class="table-primary">';
            echo '<tr>
                    <th>Obiekt</th>
                    <th>Nazwa ulicy</th>
                    <th>Numer ulicy</th>
                    <th>Telefon</th>
                    <th>E-mail</th>
                    <th>Średnia ocena</th>
                    <th>Opis</th>
                    <th>Rodzaj obiektu</th>
                    <th>Godzina otwarcia</th>
                    <th>Godzina zamknięcia</th>
                    <th>Szczegóły</th>
                </tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<tr data-idobiektu="' . htmlspecialchars($row['Idobiektu']) . '">';
                echo '<td>' . (!is_null($row['Nazwa obiektu']) ? $row['Nazwa obiektu'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Nazwa ulicy']) ? $row['Nazwa ulicy'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Numer ulicy']) ? $row['Numer ulicy'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Telefon']) ? $row['Telefon'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['E-mail']) ? $row['E-mail'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Średnia ocena']) ? $row['Średnia ocena'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Opis']) ? $row['Opis'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Rodzaj obiektu']) ? $row['Rodzaj obiektu'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Godzina Otwarcia']) ? $row['Godzina Otwarcia'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Godzina zamknięcia']) ? $row['Godzina zamknięcia']: 'Brak danych') . '</td>';
                echo '<td>';
                echo '<form action="szczegoly.php" method="GET">';
                echo '<div class=PrzekazwanieIdObiektu">';
                echo '<input type="hidden" name="Idobiektu" value="' . htmlspecialchars($row['Idobiektu']) . '">';
                echo '<input type="submit" value="Pokaż szczegóły" id="details-button">';
                echo '</div>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo "<div class='mainObiekty'><h2>Brak obiektów spełniających kryteria</h2></div>";
        }
    } else {
        $sql = "SELECT * FROM VW_Obiekty";
        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt === false) {
            die("Błąd zapytania: " . print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt) === true) {
            echo '<div class="mainObiekty">';
            echo '<table class="tablaObiekty">';
            echo '<thead class="table-primary">';
            echo '<tr>
                    <th>Obiekt</th>
                    <th>Nazwa ulicy</th>
                    <th>Numer ulicy</th>
                    <th>Telefon</th>
                    <th>E-mail</th>
                    <th>Średnia ocena</th>
                    <th>Opis</th>
                    <th>Rodzaj obiektu</th>
                    <th>Godzina otwarcia</th>
                    <th>Godzina zamknięcia</th>
                    <th>Szczegóły</th>
                </tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<tr data-idobiektu="' . htmlspecialchars($row['Idobiektu']) . '">';
                echo '<td>' . (!is_null($row['Nazwa obiektu']) ? $row['Nazwa obiektu'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Nazwa ulicy']) ? $row['Nazwa ulicy'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Numer ulicy']) ? $row['Numer ulicy'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Telefon']) ? $row['Telefon'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['E-mail']) ? $row['E-mail'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Średnia ocena']) ? $row['Średnia ocena'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Opis']) ? $row['Opis'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Rodzaj obiektu']) ? $row['Rodzaj obiektu'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Godzina Otwarcia']) ? $row['Godzina Otwarcia'] : 'Brak danych') . '</td>';
                echo '<td>' . (!is_null($row['Godzina zamknięcia']) ? $row['Godzina zamknięcia'] : 'Brak danych') . '</td>';
                echo '<td>';
                echo '<form action="szczegoly.php" method="GET">';
                echo '<div class=PrzekazwanieIdObiektu">';
                echo '<input type="hidden" name="Idobiektu" value="' . htmlspecialchars($row['Idobiektu']) . '">';
                echo '<input type="submit" value="Pokaż szczegóły" id="details-button">';
                echo '</div>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo "<div class='mainObiekty'><h2>Brak obiektów spełniających kryteria</h2></div>";
        }
    }
} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}

include(__DIR__ . "/../include/footer.php");
?>

