<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
if (!isset($_GET['Idobiektu'])) {
    echo "Błąd: Brak przekazanego IdObiektu.";
    exit();
}

$IdObiektu = intval($_GET['Idobiektu']);
$IdUzytkownika = intval($_SESSION['user_id']);
$DataWystawienia = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Ocena'])) {
        $Ocena = intval($_POST['Ocena']);

        if ($conn === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $sql = "{CALL up_DodajOcene (?, ?, ?, ?)}";
        $params = array(
            array($Ocena, SQLSRV_PARAM_IN),
            array($IdObiektu, SQLSRV_PARAM_IN),
            array($IdUzytkownika, SQLSRV_PARAM_IN),
            array($DataWystawienia, SQLSRV_PARAM_IN)
        );

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
           die(print_r(sqlsrv_errors(), true));
        } else {
            header("Location: wystawione.php");
            exit();
        }
    }
}
?>

<main class="mainDodajOcene">
    <h1>Wystaw ocenę</h1>
    <form method="post">
        <label>Ocena:</label><br>
        <select name="Ocena" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>
        <button type="submit">Wstaw ocenę</button>
    </form>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>
