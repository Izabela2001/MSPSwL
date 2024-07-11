<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if (isset($_GET["IdObiektu"])) {
    $idObiektu = $_GET["IdObiektu"];
} elseif (isset($_POST["IdObiektu"])) {
    $idObiektu = $_POST["IdObiektu"];
} else {
    die("Błąd: brak parametru IdObiektu.");
}

function getHarmonogram($conn, $idObiektu) {
    $harmonogram = [];
    $sql = "SELECT * FROM uf_HarmonogramObiektu(?)";  
    $params = array($idObiektu);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));  
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $harmonogram[] = $row;  
    }

    sqlsrv_free_stmt($stmt);  

    return $harmonogram;
}

$harmonogram = getHarmonogram($conn, $idObiektu);

?>
<main class="mainCMS">
    <h1>Harmonogram</h1>
    <table>
        <tr>
            <th>Identyfikator</th>
            <th>Data utworzenia</th>
            <th>Data ważności</th>
            <th>Godzina otwarcia</th>
            <th>Godzina zamknięcia</th>
            <th>Dzień tygodnia</th>
            <th></th>
        </tr>
        
        <?php foreach ($harmonogram as $harmonogramItem): ?>
            <tr>
                <td><?= htmlspecialchars($harmonogramItem['ID']) ?></td>
                <td><?= htmlspecialchars($harmonogramItem['Utworzony']->format('Y-m-d')) ?></td>
                <td><?= htmlspecialchars($harmonogramItem['Waznosc']->format('Y-m-d')) ?></td>
                <td><?= htmlspecialchars($harmonogramItem['GodzinaOtwarcia']) ?></td>
                <td><?= htmlspecialchars($harmonogramItem['GodzinaZamkniecia']) ?></td>
                <td><?= htmlspecialchars($harmonogramItem['Nazwa dnia']) ?></td>
                <td> <form action="edycjaHarmonogramu.php" method="get">
                            <input type="hidden" name="IdPozycji" value="<?= $harmonogramItem['ID'] ?>">
                            <button class="btn" type="submit">Edytuj</button>
                        </form>
                </td>
            </tr>
        <?php endforeach; ?>
        
    </table>

</main>
<?php include(__DIR__ . "/../../include/footer.php"); ?>

