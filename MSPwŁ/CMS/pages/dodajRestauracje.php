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
function getKuchnia($conn) {
    $kuchnie = [];
    $sql = "SELECT IdTypuKuchni, NazwaTypu FROM tbl_typ_kuchni";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $kuchnie[] = $row;
        }
    }
    return $kuchnie;
}
$days = getDays($conn);
$kuchnie = getKuchnia($conn);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $output_message = '';
    $imie = isset($_POST["Imie"]) ? $_POST["Imie"] : '';
    $nazwisko = isset($_POST["Nazwisko"]) ? $_POST["Nazwisko"] : '';
    $stanowisko = isset($_POST["Stanowisko"]) ? $_POST["Stanowisko"] : '';
    $nazwaObiektu = isset($_POST["NazwaObiektu"]) ? $_POST["NazwaObiektu"] : '';
    $nazwaUlicy = isset($_POST["NazwaUlicy"]) ? $_POST["NazwaUlicy"] : '';
    $numerUlicy = isset($_POST["NumerUlicy"]) ? $_POST["NumerUlicy"] : '';
    $telefon = isset($_POST["Telefon"]) ? $_POST["Telefon"] : '';
    $email = isset($_POST["E_mail"]) ? $_POST["E_mail"] : '';
    $sredniaOcena = isset($_POST["SredniaOcena"]) ? $_POST["SredniaOcena"] : '';
    $opis = isset($_POST["Opis"]) ? $_POST["Opis"] : '';
    $numerLokalu = isset($_POST["NumerLokalu"]) ? intval($_POST["NumerLokalu"]) : null;
    $idDarmowegoWejscia = isset($_POST["IdDarmowegoWejscia"]) ? intval($_POST["IdDarmowegoWejscia"]) : null;
    $IdTypuKuchni =  isset($_POST["IdTypuKuchni"]) ? $_POST["IdTypuKuchni"] : '';
    

    $params = array(
        array(&$imie, SQLSRV_PARAM_IN),
        array(&$nazwisko, SQLSRV_PARAM_IN),
        array(&$stanowisko, SQLSRV_PARAM_IN),
        array(&$nazwaObiektu, SQLSRV_PARAM_IN),
        array(&$nazwaUlicy, SQLSRV_PARAM_IN),
        array(&$numerUlicy, SQLSRV_PARAM_IN),
        array(&$telefon, SQLSRV_PARAM_IN),
        array(&$email, SQLSRV_PARAM_IN),
        array(&$sredniaOcena, SQLSRV_PARAM_IN),
        array(&$opis, SQLSRV_PARAM_IN),
        array(&$IdTypuKuchni,SQLSRV_PARAM_IN),
        array($numerLokalu, SQLSRV_PARAM_IN),
        array(isset($_POST["Ogrodek"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["StrefaPalacza"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["StrefaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["WpuszczanieZwierzat"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["DlaNiepelnosprawnych"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["DlaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array($idDarmowegoWejscia, SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_NVARCHAR('max'))
    );

    $stmt = sqlsrv_query($conn, "{CALL up_DodajRestauracje(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}", $params);
    if ($stmt === false) {
        die("Błąd wykonania procedury składowanej: " . print_r(sqlsrv_errors(), true));
    }
    header("Location: dodajHarmonogram.php");
    exit();
}
?>

<main id="FromUpdate">
    <form method="post">
        <div class="left-column">
            <input type="text" name="Imie" placeholder="Imię" required>
            <input type="text" name="Nazwisko" placeholder="Nazwisko" required>
            <input type="text" name="Stanowisko" placeholder="Stanowisko" required>
            <input type="text" name="NazwaObiektu" placeholder="Nazwa Obiektu" required>
            <input type="text" name="NazwaUlicy" placeholder="Nazwa Ulicy" required>
            <input type="text" name="NumerUlicy" placeholder="Numer Ulicy" required>
            <input type="text" name="Telefon" placeholder="Telefon" required pattern="\+48\d{3}-\d{3}-\d{3}">
            <input type="email" name="E_mail" placeholder="E-mail" required>
        </div>
        <div class="right-column">
            <input type="text" name="SredniaOcena" placeholder="Średnia Ocena">
            <textarea name="Opis" placeholder="Opis"></textarea>
            <input type="text" name="NumerLokalu" placeholder="Numer Lokalu">
            <input type="checkbox" name="Ogrodek"> Ogródek
            <input type="checkbox" name="StrefaPalacza"> Strefa Palacza
            <input type="checkbox" name="StrefaDzieci"> Strefa Dzieci
            <input type="checkbox" name="WpuszczanieZwierzat"> Wpuszczanie Zwierząt
            <input type="checkbox" name="DlaNiepelnosprawnych"> Dla Niepełnosprawnych
            <input type="checkbox" name="DlaDzieci"> Dla Dzieci
            <label for="IdDarmowegoWejscia">Darmowe Wejście:</label>
            <select id="IdDarmowegoWejscia" name="IdDarmowegoWejscia">
                <option value="">Wybierz dzień tygodnia</option>
                <?php foreach ($days as $day): ?>
                    <option value="<?= $day['IdDniaTygodnia'] ?>"><?= htmlspecialchars($day['NazwaDniaTygodnia']) ?></option>
                <?php endforeach; ?>
            </select><br>
            <select id="IdTypuKuchni" name="IdTypuKuchni">
                <option value="">Rodzaj kuchni</option>
                <?php foreach ($kuchnie as $kuchnia): ?>
                    <option value="<?= $kuchnia['IdTypuKuchni'] ?>"><?= htmlspecialchars($kuchnia['NazwaTypu']) ?></option>
                <?php endforeach; ?>
            </select><br>
            <button class="btn" type="submit">Dalej</button>
        </div>
    </form>
</main>

<?php include(__DIR__ . "/../../include/footer.php"); ?>
