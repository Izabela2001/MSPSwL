<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['DataWydarzenia'], $_POST['IdTypWydarzenia'], $_POST['NazwaWydarzenia'], $_POST['IdOrganizatora'], $_POST['IdObiektu'], $_POST['Informacje'])) {
        $DataWydarzenia = $_POST['DataWydarzenia'];
        $IdTypWydarzenia = $_POST['IdTypWydarzenia'];
        $NazwaWydarzenia = $_POST['NazwaWydarzenia'];
        $IdOrganizatora = $_POST['IdOrganizatora'];
        $IdObiektu = $_POST['IdObiektu'];
        $Informacje = $_POST['Informacje'];
        $outputMessage = "";

        $sql = "{CALL up_DodajWydarzenie(?, ?, ?, ?, ?, ?, ?)}";
        $params = array(
            array(&$DataWydarzenia, SQLSRV_PARAM_IN),
            array(&$IdTypWydarzenia, SQLSRV_PARAM_IN),
            array(&$NazwaWydarzenia, SQLSRV_PARAM_IN),
            array(&$IdOrganizatora, SQLSRV_PARAM_IN),
            array(&$IdObiektu, SQLSRV_PARAM_IN),
            array(&$Informacje, SQLSRV_PARAM_IN),
            array(&$outputMessage, SQLSRV_PARAM_OUT)
        );

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            echo "<p>$outputMessage</p>";
        }
    } else {
        echo "<p>Missing parameters for adding event.</p>";
    }
}

$sqlTypyWydarzen = "SELECT * FROM tbl_typ_wydarzenia";
$stmtTypyWydarzen = sqlsrv_query($conn, $sqlTypyWydarzen);
if ($stmtTypyWydarzen === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sqlOrganizatorzy = "SELECT * FROM tbl_organizator";
$stmtOrganizatorzy = sqlsrv_query($conn, $sqlOrganizatorzy);
if ($stmtOrganizatorzy === false) {
    die(print_r(sqlsrv_errors(), true));
}
$sqlObiekty = "SELECT IdObiektu, NazwaObiektu FROM tbl_obiekt";
$stmtObiekty = sqlsrv_query($conn, $sqlObiekty);
if ($stmtObiekty === false) {
     die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="DodajWydarzenie">
        <h2>Dodaj Wydarzenie</h2>
        <form method="post" action="">
            <label for="DataWydarzenia">Data Wydarzenia:</label>
            <input type="date" id="DataWydarzenia" name="DataWydarzenia" required><br>

            <label for="IdTypWydarzenia">Typ Wydarzenia:</label>
            <select id="IdTypWydarzenia" name="IdTypWydarzenia" required>
                <?php
                while ($row = sqlsrv_fetch_array($stmtTypyWydarzen, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='" . $row['IdTypuWydarzenia'] . "'>" . $row['NazwaTypu'] . "</option>";
                }
                ?>
            </select><br>

            <label for="NazwaWydarzenia">Nazwa Wydarzenia:</label>
            <input type="text" id="NazwaWydarzenia" name="NazwaWydarzenia" required><br>

            <label for="IdOrganizatora">Organizator:</label>
            <select id="IdOrganizatora" name="IdOrganizatora" required>
                <?php
                while ($row = sqlsrv_fetch_array($stmtOrganizatorzy, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='" . $row['IdOrganizatora'] . "'>" . $row['Nazwa'] . "</option>";
                }
                ?>
            </select><br>

            <label for="IdObiektu">Obiekt:</label>
            <select id="IdObiektu" name="IdObiektu" required>
                <?php
                
                while ($row = sqlsrv_fetch_array($stmtObiekty, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='" . $row['IdObiektu'] . "'>" . $row['NazwaObiektu'] . "</option>";
                }
                ?>
            </select><br>

            <label for="Informacje">Informacje:</label><br>
            <textarea id="Informacje" name="Informacje" rows="4" cols="50" required></textarea><br>

            <input type="submit" value="Dodaj Wydarzenie">
        </form>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>
