<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if (isset($_GET["IdMuralu"])) {
    $id = $_GET["IdMuralu"];
} else {
    die("Błąd: brak parametru IdMuralu.");
}
function getDays($conn) {
    $days = [];
    $sql = "SELECT IdDniaTygodnia, NazwaDniaTygodnia FROM tbl_dzien_tygodnia";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $days[] = $row;
        }
    }
    return $days;
}
function getData($conn, $id) {
    $obiekt_data = [];
    $sql = "SELECT * FROM uf_SzukanieInformacjiMural(?)";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $obiekt_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
    return $obiekt_data;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $output_message = '';
    
    $params = array(
        array(&$id, SQLSRV_PARAM_IN),
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
        array(&$_POST["Historia"], SQLSRV_PARAM_IN),
        array(&$_POST["OpisDotarcia"], SQLSRV_PARAM_IN),
        array(isset($_POST["Ogrodek"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(isset($_POST["StrefaPalacza"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(isset($_POST["StrefaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(isset($_POST["WpuszczanieZwierzat"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(isset($_POST["DlaNiepelnosprawnych"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(isset($_POST["DlaDzieci"]) ? 1 : 0, SQLSRV_PARAM_IN),
        array(&$_POST["IdDarmowegoWejscia"], SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT)
    );

    $stmt = sqlsrv_query($conn, "{CALL up_AktualizujMural(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}", $params);
    if ($stmt === false) {
        die("Błąd wykonania procedury składowanej: " . print_r(sqlsrv_errors(), true));
    }
    
    echo htmlspecialchars($output_message);
    header("Location: ListaMurali.php");
    exit(); 
}

$obiekt_data = getData($conn, $id);
$days = getDays($conn);

?>

<main id="FromUpdate">
    <h1>Edycja muralu:</h1>
    <form action="" method="post">
        <div class ="left-column">
        <input type="hidden" name="IdMuralu" value="<?= htmlspecialchars($id) ?>">
        <label>Imię:</label>
        <input type="text" name="Imie" value="<?php echo $obiekt_data["Imie"]; ?>"><br>
        <label>Nazwisko:</label>
        <input type="text" name="Nazwisko" value="<?php echo $obiekt_data["Nazwisko"]; ?>"><br>
        <label>Stanowisko:</label>
        <input type="text" name="Stanowisko" value="<?php echo $obiekt_data["Stanowisko"]; ?>"><br>
        <label>Nazwa obiektu:</label>
        <input type="text" name="NazwaObiektu" value="<?php echo $obiekt_data["NazwaObiektu"]; ?>"><br>
        <label>Nazwa ulicy:</label>
        <input type="text" name="NazwaUlicy" value="<?php echo $obiekt_data["NazwaUlicy"]; ?>"><br>
        <label>Numer ulicy:</label>
        <input type="text" name="NumerUlicy" value="<?php echo $obiekt_data["NumerUlicy"]; ?>"><br>
        <label>Telefon:</label>
        <input type="text" name="Telefon" value="<?php echo $obiekt_data["Telefon"]; ?>"><br>
        <label>E-mail:</label>
        <input type="text" name="E_mail" value="<?php echo $obiekt_data["E_mail"]; ?>"><br>
        <label>Średnia ocena:</label>
        <input type="text" name="SredniaOcena" value="<?php echo $obiekt_data["SredniaOcena"]; ?>"><br>
        <label>Opis:</label>
        <textarea name="Opis"><?php echo $obiekt_data["Opis"]; ?></textarea><br>
        </div>
        <div class ="right-column">
        
        <label>Historia:</label>
        <textarea name="Historia"><?php echo $obiekt_data["Historia"]; ?></textarea><br>
        <label>Opis dotarcia:</label>
        <textarea name="OpisDotarcia"><?php echo $obiekt_data["OpisDotarcia"]; ?></textarea><br>
        <label>Ogródek:</label>
        <input type="checkbox" name="Ogrodek" <?php if ($obiekt_data["Ogrodek"]) echo "checked"; ?>><br>
        <label>Strefa palacza:</label>
        <input type="checkbox" name="StrefaPalacza" <?php if ($obiekt_data["StrefaPalacza"]) echo "checked"; ?>><br>
        <label>Strefa dzieci:</label>
        <input type="checkbox" name="StrefaDzieci" <?php if ($obiekt_data["StrefaDzieci"]) echo "checked"; ?>><br>
        <label>Wpuśczenie zwierząt:</label>
        <input type="checkbox" name="WpuszczanieZwierzat" <?php if ($obiekt_data["WpuszczanieZwierzat"]) echo "checked"; ?>><br>
        <label>Dla niepełnosprawnych:</label>
        <input type="checkbox" name="DlaNiepelnosprawnych" <?php if ($obiekt_data["DlaNiepelnosprawnych"]) echo "checked"; ?>><br>
        <label>Dla dzieci:</label>
        <input type="checkbox" name="DlaDzieci" <?php if ($obiekt_data["DlaDzieci"]) echo "checked"; ?>><br>
        <label for="IdDarmowegoWejscia">Darmowe Wejście:</label>
        <select id="IdDarmowegoWejscia" name="IdDarmowegoWejscia">
            <option value="">Wybierz dzień tygodnia</option>
            <?php foreach ($days as $day): ?>
                <option value="<?= $day['IdDniaTygodnia'] ?>" <?= ($obiekt_data['IdDarmowegoWejscia'] ?? '') == $day['IdDniaTygodnia'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($day['NazwaDniaTygodnia']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit">Zapisz zmiany</button>
            </div>
    </form>
</main>

<?php
sqlsrv_close($conn);
?>
