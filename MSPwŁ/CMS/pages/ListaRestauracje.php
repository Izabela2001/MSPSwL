<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

function getData($conn) {
    $obiekty = [];
    $sql = "SELECT * FROM uf_ObiektyRestauracje()";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $obiekty[] = $row;
        }
    }
    return $obiekty;
}
function delete($conn, $idObiektu) {
    $output_message = '';
    $params = array(
        array(&$idObiektu, SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT)
    );
    $stmt = sqlsrv_query($conn, "{CALL up_UsunObiekt(?, ?)}", $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    return $output_message;
}

$obiekty = getData($conn);

if (isset($_POST['delete'])) {
    $idObiektu = $_POST['IdObiektu'];
    $message = delete($conn, $idObiektu);
    echo "<script>alert('$message');</script>";
    echo "<script>window.location.replace('ListaRestauracje.php');</script>";
    exit;
}
?>

<main class="ListyObiektow">
    <h1>Restauracje</h1>
    <table>
        <thead>
            <tr>    
                <th></th>
                <th></th>
                <th>Identyfikator restauracji</th>
                <th>Identyfikator obiektu</th>
                <th>Nazwa obiektu</th>
                <th>Ulica</th>
                <th>Numer ulicy</th>
                <th>Numer lokalu</th>
                <th>Kuchnia</th>
                <th>Email</th>
                <th>Opis</th>
                <th>Średnia ocena</th>
                <th>Telefon</th>
                <th>Dla dzieci</th>
                <th>Dla niepełnosprawnych</th>
                <th>Ogródek</th>
                <th>Strefa dla dzieci</th>
                <th>Strefa palacza</th>
                <th>Wpuszczanie zwierząt</th>
                <th>Darmowe wejście</th>
                <th>Imie osoby zarządzającej</th>
                <th>Nazwisko osoby zarzadzającej</th>
                <th>Stanowisko</th>
            </tr>
        </thead>
        <div id="ListaObiektow">
            <form class="FormularzPrzekazywania" action="updateRestuaracja.php" method="get">
                <label for="IdRestauracji">Wybierz numer restauracji:</label>
                <select id="IdRestauracji" name="IdRestauracji" required>
                    <?php foreach ($obiekty as $obiekt): ?>
                        <option value="<?= htmlspecialchars($obiekt['IdRestauracji']) ?>"><?= htmlspecialchars($obiekt['IdRestauracji']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn" type="submit">Aktualizuj Restauracje</button>
            </form>
            <?php foreach ($obiekty as $obiekt): ?>
                <tr>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="IdObiektu" value="<?= $obiekt['Identyfikator obiektu'] ?>">
                            <button class="btn" type="submit" name="delete">Usuń</button>
                        </form>
                    </td>
                    <td>
                        <form action="harmonogramobiektu.php" method="get">
                            <input type="hidden" name="IdObiektu" value="<?= $obiekt['Identyfikator obiektu'] ?>">
                            <button class="btn" type="submit">Harmonogram obiektu</button>
                        </form>
                     </td>
                    <td><?= htmlspecialchars($obiekt['IdRestauracji']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Identyfikator obiektu']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Nazwa obiektu']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Ulica']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Numer ulicy']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Numer lokalu']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Kuchnia']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Email']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Opis']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Średnia ocena']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Telefon']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Dla dzieci']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Dla niepełnosprawnych']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Ogródek']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Strefa dla dzieci']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Strefa palacza']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Wpuszczanie zwierząt']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Darmowe wejście']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Imie']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Nazwisko']) ?></td>
                    <td><?= htmlspecialchars($obiekt['Stanowisko']) ?></td>
                </tr>
            <?php endforeach; ?>
        </div>
    </table>
    <form action="dodajRestauracje.php">
        <button class="btn" type="submit">Dodaj nową restauracje</button>
    </form>
</main>
<?php include(__DIR__ . "/../../include/footer.php"); ?>
