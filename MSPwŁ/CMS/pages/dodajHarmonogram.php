<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

function getDays($conn) {
    $days = [];
    $sql = "SELECT IdDniaTygodnia, NazwaDniaTygodnia FROM tbl_dzien_tygodnia";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die("Błąd wykonania zapytania: " . print_r(sqlsrv_errors(), true));
    }
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $days[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    return $days;
}

function getHours($conn) {
    $hours = [];
    $sql = "SELECT IdGodziny, Wartosc FROM tbl_godzina ORDER BY IdGodziny";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die("Błąd wykonania zapytania: " . print_r(sqlsrv_errors(), true));
    }
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $hours[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    return $hours;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $output_message = '';

    $dataUtworzenia = $_POST["DataUtworzenia"];
    $dataWarznosci = $_POST["DataWarznosci"];
    $harmonogramy = $_POST["harmonogramy"];

    $params = array();

    foreach ($harmonogramy as $idDniaTygodnia => $harmonogram) {
        if (isset($harmonogram['IdGodzinyOtwarcia']) && isset($harmonogram['IdGodzinyZamkniecia'])) {
            $idGodzinyOtwarcia = $harmonogram['IdGodzinyOtwarcia'];
            $idGodzinyZamkniecia = $harmonogram['IdGodzinyZamkniecia'];

            $params[] = array(
                array($dataUtworzenia, SQLSRV_PARAM_IN),
                array($dataWarznosci, SQLSRV_PARAM_IN),
                array($idGodzinyOtwarcia, SQLSRV_PARAM_IN),
                array($idGodzinyZamkniecia, SQLSRV_PARAM_IN),
                array($idDniaTygodnia, SQLSRV_PARAM_IN),
                array(&$output_message, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_NVARCHAR('max'))
            );
        }
    }
    foreach ($params as $param) {
        $stmt = sqlsrv_query($conn, "{CALL up_DodajHarmonogram(?,?,?,?,?,?)}", $param);
        if ($stmt === false) {
            die("Błąd wykonania procedury składowanej: " . print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmt);
    }
    header("Location: obiekty.php");
    exit();
}

$days = getDays($conn);
$hours = getHours($conn);
?>

<main id="FromUpdate">
    <form method="post">
        <div class="left-column">
            <label for="DataUtworzenia">Data Utworzenia:</label>
            <input type="date" id="DataUtworzenia" name="DataUtworzenia" required>
            <label for="DataWarznosci">Data Ważności:</label>
            <input type="date" id="DataWarznosci" name="DataWarznosci" required>
        </div>
        <div class="right-column">
            <?php foreach ($days as $day): ?>
                <div>
                    <h3><?= htmlspecialchars($day['NazwaDniaTygodnia']) ?></h3>
                    <input type="hidden" name="harmonogramy[<?= $day['IdDniaTygodnia'] ?>][IdDniaTygodnia]" value="<?= $day['IdDniaTygodnia'] ?>">
                    <label for="IdGodzinyOtwarcia_<?= $day['IdDniaTygodnia'] ?>">Godzina Otwarcia:</label>
                    <select id="IdGodzinyOtwarcia_<?= $day['IdDniaTygodnia'] ?>" name="harmonogramy[<?= $day['IdDniaTygodnia'] ?>][IdGodzinyOtwarcia]">
                        <option value="">Wybierz godzinę otwarcia</option>
                        <?php foreach ($hours as $hour): ?>
                            <option value="<?= $hour['IdGodziny'] ?>"><?= htmlspecialchars($hour['Wartosc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="IdGodzinyZamkniecia_<?= $day['IdDniaTygodnia'] ?>">Godzina Zamknięcia:</label>
                    <select id="IdGodzinyZamkniecia_<?= $day['IdDniaTygodnia'] ?>" name="harmonogramy[<?= $day['IdDniaTygodnia'] ?>][IdGodzinyZamkniecia]">
                        <option value="">Wybierz godzinę zamknięcia</option>
                        <?php foreach ($hours as $hour): ?>
                            <option value="<?= $hour['IdGodziny'] ?>"><?= htmlspecialchars($hour['Wartosc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; ?>
            <button class="btn" type="submit">Dodaj Harmonogramy</button>
        </div>
    </form>
</main>

<?php include(__DIR__ . "/../../include/footer.php"); ?>


