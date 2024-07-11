<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Nr'])) {
    $idObiektu = $_POST['Nr'];

    $sql = "{CALL up_UsunOrganizatora(?, ?)}";
    $params = array(
        array($idObiektu, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        
        echo "<p>$outputMessage</p>";
    }
}

$sql = "SELECT * FROM uf_Organizatorzy()";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Lista Organizatorów</h2>
        <table>
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Nazwa</th>
                    <th>NIP</th>
                    <th>E-mail</th>
                    <th>Telefon</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Stanowisko</th>
                    <th>Akcja</th> 
                </tr>
            </thead>
            <div>
                <?php
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Identyfikator organizatora']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwa']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['NIP']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['E-mail']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Telefon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Imie']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwisko']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Stanowisko']) . "</td>";
                    echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='Nr' value='" . htmlspecialchars($row['Identyfikator organizatora']) . "'>
                                <input type='submit'  id =details-button value='Usuń'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </div>
        </table>
        <div>
            <a href="dodajOrganizatora.php" class="buttonDodajPracownika">Dodaj organizatora</a>
        </div>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>

