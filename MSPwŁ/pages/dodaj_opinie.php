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
$output_message = ""; 

$sql_check_opinion = "SELECT * FROM tbl_opinia WHERE IdUzytkownika = ? AND IdObiektu = ?";
$params_check_opinion = array($IdUzytkownika, $IdObiektu);
$stmt_check_opinion = sqlsrv_query($conn, $sql_check_opinion, $params_check_opinion); 

if ($stmt_check_opinion === false) {
    $errors = sqlsrv_errors();
    foreach ($errors as $error) {
        echo "Błąd wykonania zapytania: " . $error['message'] . "<br>";
    }
    exit();
}

if (sqlsrv_has_rows($stmt_check_opinion)) {
    header("Location: wystawione.php?IdObiektu=" . $IdObiektu);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['TrescOpinii'])) {
        $TrescOpinii = $_POST['TrescOpinii'];

        $sql = "{CALL up_DodajOpinie (?, ?, ?, ?, ?)}";
        $params = array(
            array($TrescOpinii, SQLSRV_PARAM_IN),
            array($IdUzytkownika, SQLSRV_PARAM_IN),
            array($IdObiektu, SQLSRV_PARAM_IN),
            array($DataWystawienia, SQLSRV_PARAM_IN),
            array(&$output_message, SQLSRV_PARAM_OUT)
        );

        $stmt = sqlsrv_query($conn, $sql, $params); 
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            foreach ($errors as $error) {
                echo "Błąd wykonania procedury: " . $error['message'] . "<br>";
            }
        } else {
            header("Location: wystawione.php");
            exit();
        }
    }
}
?>

<main class="mainDodajOcene">
    <h1>Wystaw opinię</h1>
    <form method="post">
        <label for="trescOpinii">Treść opinii:</label><br>
        <textarea id="trescOpinii" name="TrescOpinii" rows="4" cols="50" required></textarea><br>
        <button type="submit">Wstaw opinię</button>
    </form>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>








