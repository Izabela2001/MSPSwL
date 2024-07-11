<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

$outputMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitKontakt'])) {
    $email = $_POST['email'];
    $tresc = $_POST['tresc'];
    $dataZgloszenia = date("Y-m-d");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Podaj poprawny adres email.');</script>";
    } elseif (empty($tresc)) {
        echo "<script>alert('Treść wiadomości nie może być pusta.');</script>";
    } else {
        $sql = "{CALL up_DodajZgloszenie(?, ?, ?, ?)}";
        $params = array(
            array($tresc, SQLSRV_PARAM_IN),
            array($dataZgloszenia, SQLSRV_PARAM_IN),
            array($email, SQLSRV_PARAM_IN),
            array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
        );

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            echo "<script>alert('Wystąpił błąd podczas wysyłania wiadomości.');</script>";
            die(print_r(sqlsrv_errors(), true));
        } else {
            $outputMessageEscaped = htmlspecialchars($outputMessage, ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$outputMessageEscaped');</script>";
        }
    }
}
?>

<main class="sKontakty">
    <div class="kontakt">
        <h1>Witaj</h1>
        <p>Jeżeli uważasz, że brakuje czegoś na stronie 
            lub masz jakieś uwagi, prosimy o kontakt<br>
            Chętnie wysłuchamy Twoich sugestii i postaramy się wprowadzić
            odpowiednie zmiany.</p>
        <form class="formKontakt" method="post">
            <div class="poleKontakt">
                <label for="email"> Podaj e-mail: </label>
                <input type="text" name="email" id="email"/>
            </div>
            <div class="poleKontakt">
                <label for="tresc"> Treść wiadomości: </label>
                <textarea name="tresc" id="tresc" cols="30" rows="10"></textarea>
            </div>
            <div class="poleKontakt">
                <input type="submit" name="submitKontakt" value="Wyślij"/>
            </div>
        </form>
    </div>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>

