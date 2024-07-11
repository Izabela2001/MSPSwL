<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if (isset($_GET["IdPozycji"])) {
    $idPozycji = $_GET["IdPozycji"];
} elseif (isset($_POST["IdPozycji"])) {
    $idPozycji = $_POST["IdPozycji"];
} else {
    die("Błąd: brak parametru IdPozycji.");
}

function getHarmonogram($conn, $idPozycji) {
    $harmonogram_data = [];
    $sql = "SELECT * FROM uf_SzczegolHarmonogram(?)";
    $params = [$idPozycji];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die("Błąd w zapytaniu do bazy danych: " . print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $harmonogram_data = $row;
    }
    sqlsrv_free_stmt($stmt); 
    return $harmonogram_data;
}

function getHours($conn) {
    $hours = [];
    $sql = "SELECT IdGodziny, Wartosc FROM tbl_godzina";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $hours[] = $row;
        }
    }
    return $hours;
}

$harmonogram_data = getHarmonogram($conn, $idPozycji);
$hours = getHours($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $idPozycji = $_POST["IdPozycji"];
    $godzinaOtwarcia = $_POST["GodzinaOtwarcia"];
    $godzinaZamkniecia = $_POST["GodzinaZamkniecia"];
    $utworzony = isset($_POST["Utworzony"]) ? $_POST["Utworzony"] : null;  
    $dataWaznosci = isset($_POST["DataWaznosci"]) ? $_POST["DataWaznosci"] : null;

    $output_message = '';

    $params = array(
        array(&$idPozycji, SQLSRV_PARAM_IN),
        array(&$godzinaOtwarcia, SQLSRV_PARAM_IN),
        array(&$godzinaZamkniecia, SQLSRV_PARAM_IN),
        array(&$utworzony, SQLSRV_PARAM_IN),
        array(&$dataWaznosci, SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT) 
    );

    $stmt = sqlsrv_query($conn, "{CALL up_AktualizujHarmonogram(?,?,?,?,?,?)}", $params);
    if ($stmt === false) {
        die("Błąd wykonania procedury składowanej: " . print_r(sqlsrv_errors(), true));
    }

    
    $output_message = $params[5][0];

    echo htmlspecialchars($output_message);

    header("Location: ListaPub.php");
    exit();
}
?>

<main id="FromUpdate">
    <h1>Edycja harmonogramu</h1>
    <form method="post">
        <input type="hidden" name="IdPozycji" value="<?= htmlspecialchars($harmonogram_data['ID']) ?>">
        
        <label for="GodzinaOtwarcia">Godzina otwarcia:</label>
        <select id="GodzinaOtwarcia" name="GodzinaOtwarcia">
            <?php foreach ($hours as $hour) : ?>
                <option value="<?= htmlspecialchars($hour['IdGodziny']) ?>" <?= isset($harmonogram_data['GodzinaOtwarcia']) && $harmonogram_data['GodzinaOtwarcia'] == $hour['IdGodziny'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hour['Wartosc']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="GodzinaZamkniecia">Godzina zamknięcia:</label>
        <select id="GodzinaZamkniecia" name="GodzinaZamkniecia">
            <?php foreach ($hours as $hour) : ?>
                <option value="<?= htmlspecialchars($hour['IdGodziny']) ?>" <?= isset($harmonogram_data['GodzinaZamkniecia']) && $harmonogram_data['GodzinaZamkniecia'] == $hour['IdGodziny'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hour['Wartosc']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="Utworzony">Data utworzenia:</label>
        <input type="date" id="Utworzony" name="Utworzony" value="<?= isset($harmonogram_data['Utworzony']) ? htmlspecialchars($harmonogram_data['Utworzony']->format('Y-m-d')) : '' ?>"><br><br>
        
        <label for="DataWaznosci">Data ważności:</label>
        <input type="date" id="DataWaznosci" name="DataWaznosci" value="<?= isset($harmonogram_data['DataWaznosci']) ? htmlspecialchars($harmonogram_data['DataWaznosci']->format('Y-m-d')) : '' ?>"><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>
</main>

<?php include(__DIR__ . "/../../include/footer.php"); ?>

