<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nazwa = $_POST['nazwa'];
    $nip = $_POST['nip'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $stanowisko = $_POST['stanowisko'];

    if (!preg_match("/^\d{11}$/", $nip)) {
        die("Błędny format NIPu. NIP musi składać się z 11 cyfr.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Błędny format adresu e-mail.");
    }
    if (!preg_match("/^\+48\d{3}-\d{3}-\d{3}$/", $telefon)) {
        die("Błędny format numeru telefonu. Numer telefonu musi być w formacie +48111-111-111.");
    }
    $sql = "{CALL up_DodajOrganizatora(?, ?, ?, ?, ?, ?, ?, ?)}";
    $params = array(
        array($nazwa, SQLSRV_PARAM_IN),
        array($nip, SQLSRV_PARAM_IN),
        array($telefon, SQLSRV_PARAM_IN),
        array($email, SQLSRV_PARAM_IN),
        array($imie, SQLSRV_PARAM_IN),
        array($nazwisko, SQLSRV_PARAM_IN),
        array($stanowisko, SQLSRV_PARAM_IN),
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

<main class="mainDodajOgloszenie">
    <div class="contenerOrgnizatorów">
        <h1>Dodaj nowego organizatora</h1>
        <form method="post" action="">
            <label for="nazwa">Nazwa:</label>
            <input type="text" id="nazwa" name="nazwa" required><br><br>
            <label for="nip">NIP:</label>
            <input type="text" id="nip" name="nip" pattern="\d{11}" title="NIP musi składać się z 11 cyfr" maxlength="11" required><br><br>
            <label for="telefon">Telefon:</label>
            <input type="tel" id="telefon" name="telefon" pattern="\+48\d{3}-\d{3}-\d{3}" title="Numer telefonu musi być w formacie +48111-111-111" placeholder="+48" required><br><br>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="imie">Imię:</label>
            <input type="text" id="imie" name="imie" required><br><br>
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" id="nazwisko" name="nazwisko" required><br><br>
            <label for="stanowisko">Stanowisko:</label>
            <input type="text" id="stanowisko" name="stanowisko" required><br><br>
            <input type="submit" value="Dodaj organizatora">
        </form>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>
