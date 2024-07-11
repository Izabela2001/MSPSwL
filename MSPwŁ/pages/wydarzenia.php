<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>
<main class="wydarzenia">
    <div class="mainWydarzenia"> 
        <form method="post" action="">
            <label for="dataOd">Data od:</label>
            <input type="date" id="dataOd" name="dataOd">
            <label for="dataDo">Data do:</label>
            <input type="date" id="dataDo" name="dataDo">
            <label for="idTypWydarzenia">Rodzaj wydarzenia:</label>
            <select id="idTypWydarzenia" name="idTypWydarzenia">
                <option value="">-- Rodzaj  wydarzenia --</option>
                <?php
                $typyWydarzenQuery = "SELECT IdTypuWydarzenia, NazwaTypu FROM tbl_typ_wydarzenia";
                $typyWydarzenResult = sqlsrv_query($conn, $typyWydarzenQuery);

                if ($typyWydarzenResult !== false) {
                    while ($typ = sqlsrv_fetch_array($typyWydarzenResult, SQLSRV_FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($typ['IdTypuWydarzenia']) . '">' . htmlspecialchars($typ['NazwaTypu']) . '</option>';
                    }
                } else {
                    echo '<option value="">Błąd wczytywania typów wydarzeń</option>';
                }
                ?>
            </select>
            <input type="submit" name="submit" value="Filtruj">
        </form>
    </div>
    <div class="conternerWydarzenia">
    <?php
    try {
        if (isset($_POST['submit'])) {
            $dataOd = !empty($_POST['dataOd']) ? $_POST['dataOd'] : null;
            $dataDo = !empty($_POST['dataDo']) ? $_POST['dataDo'] : null;
            $idTypWydarzenia = !empty($_POST['idTypWydarzenia']) ? $_POST['idTypWydarzenia'] : null;

            $sql = "SELECT * FROM uf_WyszukajWydarzenia(?, ?, ?)";
            $params = array($dataOd, $dataDo, $idTypWydarzenia);
            $stmt = sqlsrv_query($conn, $sql, $params);
        } else {
            $sql = "SELECT * FROM uf_WyswietlWydarzeniaWTerminie()";
            $stmt = sqlsrv_query($conn, $sql);
        }

        if ($stmt === false) {
            die("Błąd zapytania: " . print_r(sqlsrv_errors(), true));
        }

        $row_count = sqlsrv_has_rows($stmt);

        if ($row_count === true) {
            echo '<div class="mainWydarzenia">';
            echo '<table class="tablaWydarzenia">';
            echo '<thead class="table-primary">';
            echo '<tr>
                    <th>Wydarzenie</th>
                    <th>Data wydarzenia</th>
                    <th>Rodzaj wydarzenia</th>
                    <th>Organizator</th>
                    <th>Obiekt</th>
                    <th>Informacje</th>
                </tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['Nazwa wydarzenia']) . '</td>';
                echo '<td>' . $row['Data wydarzenia']->format('Y-m-d') . '</td>';
                echo '<td>' . htmlspecialchars($row['Rodzaj wydarzenia']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Nazwa organizatora']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Nazwa obiektu']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Informacje']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo "<div class='mainWydarzenia'><h2>Brak aktualnie wydarzeń</h2></div>";
        }
    } catch (Exception $e) {
        echo "Błąd połączenia: " . $e->getMessage();
    }
    ?>
    </div>
</main>
<?php
include(__DIR__ . "/../include/footer.php");
?>









