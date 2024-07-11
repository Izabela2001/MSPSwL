<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if (isset($_GET["pub_id"])) {
    $pub_id = $_GET["pub_id"];
} else {
    die("Błąd: brak parametru pub_id.");
}


function getDays($conn) {
    $days = [];
    $sql = "SELECT IdDniaTygodnia, NazwaDniaTygodnia FROM tbl_dzien_tygodnia";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $days[] = $row;
        }
    } else {
        die(print_r(sqlsrv_errors(), true)); 
    }
    return $days;
}

function getPubData($conn, $pub_id) {
    $pub_data = [];
    $sql = "SELECT * FROM uf_SzukanieInformacjiPub(?)";
    $params = [$pub_id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $pub_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
    return $pub_data;
}


$days = getDays($conn);

$pub_data = getPubData($conn, $pub_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $output_message = '';

    $params = array(
        array(&$pub_id, SQLSRV_PARAM_IN),
        array(&$_POST["Imie"], SQLSRV_PARAM_IN),
        array(&$_POST["Nazwisko"], SQLSRV_PARAM_IN),
        array(&$_POST["Stanowisko"], SQLSRV_PARAM_IN),
        array(&$_POST["NazwaObiektu"], SQLSRV_PARAM_IN),
        array(&$_POST["NazwaUlicy"], SQLSRV_PARAM_IN),
        array(&$_POST["NumerUlicy"], SQLSRV_PARAM_IN),
        array(&$_POST["Telefon"], SQLSRV_PARAM_IN),
        array(&$_POST["E_mail"], SQLSRV_PARAM_IN),
        array(&$_POST["SredniaOcena"], SQLSRV_PARAM_IN),
        array(&$_POST["Opis"], SQLSRV_PARAM_IN),
        array(&$_POST["NumerLokalu"], SQLSRV_PARAM_IN),
        array(isset($_POST["Ogrodek"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["StrefaPalacza"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["StrefaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["WpuszczanieZwierzat"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["DlaNiepelnosprawnych"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(isset($_POST["DlaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_INT),
        array(&$_POST["IdDarmowegoWejscia"], SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT, null, SQLSRV_SQLTYPE_NVARCHAR('max'))
    );

    $stmt = sqlsrv_query($conn, "{CALL  up_AktualizujPub(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}", $params);
    if ($stmt === false) {
        die("Błąd wykonania procedury składowanej: " . print_r(sqlsrv_errors(), true));
    }
    
    echo htmlspecialchars($output_message);
    header("Location: ListaPub.php");
    exit(); 
}
?>

<main id="FromUpdate">
    <h1>Edytuj Pub</h1>
    <form action="" method="post">
        <div class ="left-column">
        <input type="hidden" name="pub_id" value="<?= htmlspecialchars($pub_id) ?>">

        <label for="Imie">Imię właściciela:</label>
        <input type="text" id="Imie" name="Imie" value="<?= htmlspecialchars($pub_data['Imie'] ?? '') ?>">

        <label for="Nazwisko">Nazwisko właściciela:</label>
        <input type="text" id="Nazwisko" name="Nazwisko" value="<?= htmlspecialchars($pub_data['Nazwisko'] ?? '') ?>">

        <label for="Stanowisko">Stanowisko:</label>
        <input type="text" id="Stanowisko" name="Stanowisko" value="<?= htmlspecialchars($pub_data['Stanowisko'] ?? '') ?>">

        <label for="NazwaObiektu">Nazwa obiektu:</label>
        <input type="text" id="NazwaObiektu" name="NazwaObiektu" value="<?= htmlspecialchars($pub_data['Nazwa obiektu'] ?? '') ?>">

        <label for="NazwaUlicy">Nazwa ulicy:</label>
        <input type="text" id="NazwaUlicy" name="NazwaUlicy" value="<?= htmlspecialchars($pub_data['Ulica'] ?? '') ?>">

        <label for="NumerUlicy">Numer ulicy:</label>
        <input type="text" id="NumerUlicy" name="NumerUlicy" value="<?= htmlspecialchars($pub_data['Numer ulicy'] ?? '') ?>">

        <label for="NumerLokalu">Numer lokalu:</label>
        <input type="text" id="NumerLokalu" name="NumerLokalu" value="<?= htmlspecialchars($pub_data['Numer lokalu'] ?? '') ?>"><br>

        <label for="Telefon">Telefon:</label>
        <input type="text" id="Telefon" name="Telefon" value="<?= htmlspecialchars($pub_data['Telefon'] ?? '') ?>">
        </div>
        <div class ="right-column">
        <label for="E_mail">E-mail:</label>
        <input type="text" id="E_mail" name="E_mail" value="<?= htmlspecialchars($pub_data['Email'] ?? '') ?>">

        <label for="SredniaOcena">Średnia ocena:</label>
        <input type="text" id="SredniaOcena" name="SredniaOcena" value="<?= htmlspecialchars($pub_data['Średnia ocena'] ?? '') ?>">

        <label for="Opis">Opis:</label>
        <textarea id="Opis" name="Opis"><?= htmlspecialchars($pub_data['Opis'] ?? '') ?></textarea>
        <label for="Ogrodek">Ogródek:</label>
        <input type="checkbox" id="Ogrodek" name="Ogrodek" <?= isset($pub_data['Ogródek']) && $pub_data['Ogródek'] ? 'checked' : '' ?>><br>

        <label for="StrefaPalacza">Strefa palacza:</label>
        <input type="checkbox" id="StrefaPalacza" name="StrefaPalacza" <?= isset($pub_data['Strefa palacza']) && $pub_data['Strefa palacza'] ? 'checked' : '' ?>><br>

        <label for="StrefaDzieci">Strefa dzieci:</label>
        <input type="checkbox" id="StrefaDzieci" name="StrefaDzieci" <?= isset($pub_data['Strefa dla dzieci']) && $pub_data['Strefa dla dzieci'] ? 'checked' : '' ?>><br>

        <label for="WpuszczanieZwierzat">Wpuszczanie zwierząt:</label>
        <input type="checkbox" id="WpuszczanieZwierzat" name="WpuszczanieZwierzat" <?= isset($pub_data['Wpuszczanie zwierząt']) && $pub_data['Wpuszczanie zwierząt'] ? 'checked' : '' ?>><br>

        <label for="DlaNiepelnosprawnych">Dla niepełnosprawnych:</label>
        <input type="checkbox" id="DlaNiepelnosprawnych" name="DlaNiepelnosprawnych" <?= isset($pub_data['Dla niepełnosprawnych']) && $pub_data['Dla niepełnosprawnych'] ? 'checked' : '' ?>><br>

        <label for="DlaDzieci">Dla dzieci:</label>
        <input type="checkbox" id="DlaDzieci" name="DlaDzieci" <?= isset($pub_data['Dla dzieci']) && $pub_data['Dla dzieci'] ? 'checked' : '' ?>><br>

        <label for="IdDarmowegoWejscia">Darmowe Wejście:</label>
        <select id="IdDarmowegoWejscia" name="IdDarmowegoWejscia">
            <option value="">Wybierz dzień tygodnia</option>
            <?php foreach ($days as $day): ?>
                <option value="<?= $day['IdDniaTygodnia'] ?>" <?= ($pub_data['IdDarmowegoWejscia'] ?? '') == $day['IdDniaTygodnia'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($day['NazwaDniaTygodnia']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Aktualizuj Pub</button>
            </div>
    </form>
    
 
    
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>




