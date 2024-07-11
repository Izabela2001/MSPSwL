<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$IdObiektu = $_GET['Idobiektu'] ?? null; 
$IdUzytkownika = $_SESSION['user_id'] ?? null; 
$DataUtworzenia = date("Y-m-d"); 
$outputMessage = "";

$sql_check_favorite = "SELECT * FROM tbl_ulubione WHERE IdUzytkownika = ? AND IdObiektu = ?";
$params_check_favorite = array($IdUzytkownika, $IdObiektu);
$stmt_check_favorite = sqlsrv_query($conn, $sql_check_favorite, $params_check_favorite); 

if ($stmt_check_favorite === false) {
    $errors = sqlsrv_errors();
    foreach ($errors as $error) {
        echo "Błąd wykonania zapytania: " . $error['message'] . "<br>";
    }
    exit();
}

$added_to_favorites = sqlsrv_has_rows($stmt_check_favorite);

if($added_to_favorites) {
    header("Location: moje_ulubione.php?Idobiektu=" . $IdObiektu);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['DODAJuLUBIONE'])) {
    $sql = "{CALL up_DodajUlubione (?, ?, ?, ?)}";
    $params = array(
        array($IdUzytkownika, SQLSRV_PARAM_IN),
        array($IdObiektu, SQLSRV_PARAM_IN),
        array($DataUtworzenia, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        foreach ($errors as $error) {
            echo "Błąd wykonania procedury: " . $error['message'] . "<br>";
        }
    } else {
        header("Location: moje_ulubione.php?Idobiektu=" . $IdObiektu);
        exit();
    }
}

?>

<main class="mainDodajUlubione">
    <div id="dodajUlubione">
        <h2>Dodaj do ulubionych</h2>
        <form id="dodajUlubione"method="post">
            <input type="hidden" name="Idobiektu" value="<?php echo $IdObiektu; ?>">
            <div id ="buttonDodawanieUlubione">
            <button type="submit"  name="DODAJuLUBIONE">Dodaj do ulubionych</button>
            </div>
        </form>
    </div>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>

