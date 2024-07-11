<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';
    $imie = $_POST['imie'] ?? '';
    $nazwisko = $_POST['nazwisko'] ?? '';
    $e_mail = $_POST['e_mail'] ?? '';
    $telefon = $_POST['telefon'] ?? null;
    $data_urodzenia = $_POST['data_urodzenia'] ?? '';

    if (!preg_match("/^\+48[0-9]{9}$/", $telefon)) {
        echo "<p>Nieprawidłowy format numeru telefonu!</p>";
        exit();
    }

    if (!filter_var($e_mail, FILTER_VALIDATE_EMAIL)) {
        echo "<p>Nieprawidłowy adres e-mail!</p>";
        exit();
    }

    $sql_check_login = "SELECT * FROM tbl_konto WHERE Login = ?";
    $params_check_login = array($login);
    $stmt_check_login = sqlsrv_query($conn, $sql_check_login, $params_check_login);
    if ($stmt_check_login === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $row_count = sqlsrv_has_rows($stmt_check_login);
    if ($row_count !== false && $row_count > 0) {
        echo "<p>Login już istnieje w bazie danych!</p>";
        exit();
    }

    $sql = "{CALL up_DodajPracownika(?, ?, ?, ?, ?, ?, ?, ?)}";
    $params = array(
        array($login, SQLSRV_PARAM_IN),
        array($haslo, SQLSRV_PARAM_IN),
        array($imie, SQLSRV_PARAM_IN),
        array($nazwisko, SQLSRV_PARAM_IN),
        array($e_mail, SQLSRV_PARAM_IN),
        array($telefon, SQLSRV_PARAM_IN),
        array($data_urodzenia, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<p>$outputMessage</p>";
    }
}
?>

<main class="mainDodajPracownika">
    <div class="dodajPracownika">
        <h2>Dodaj pracownika</h2>
        <form method="post" action="">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required><br>

            <label for="haslo">Hasło:</label>
            <input type="password" id="haslo" name="haslo" required><br>

            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required><br>

            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" required><br>

            <label for="e_mail">E-mail:</label>
            <input type="email" id="e_mail" name="e_mail" required><br>

            <label for="telefon">Telefon:</label>
            <input type="tel" id="telefon" name="telefon" pattern="\+48\d{3}-\d{3}-\d{3}" title="Numer telefonu musi być w formacie +48111-111-111" placeholder="+48" required><br><br>

            <label for="data_urodzenia">Data urodzenia:</label>
            <input type="date" id="data_urodzenia" name="data_urodzenia" required><br>

            <input type="submit" value="Dodaj pracownika">
        </form>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>

