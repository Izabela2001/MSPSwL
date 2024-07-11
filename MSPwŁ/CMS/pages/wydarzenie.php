<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IdWydarzenia'])) {
    $idWydarzenia = $_POST['IdWydarzenia'];

    $sql = "{CALL up_UsunWydarzenie(?, ?)}";
    $params = array(
        array($idWydarzenia, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<p>$outputMessage</p>";
    }
}

$query = "SELECT * FROM uf_Wydarzenia()";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Lista wydarzeń</h2>
        <table>
            <thead>
            <tr>
                <th>Identyfikator wydarzenia</th>
                <th>Nazwa organizatora</th>
                <th>Rodzaj wydarzenia</th>
                <th>Nazwa obiektu</th>
                <th>Nazwa organizatora</th>
                <th>E-mail organizatora</th>
                <th>Numer telefonu organizatora</th>
                <th>Data wydarzenia</th>
                <th>Akcje</th>
            </tr>
            </thead>
            <div>
            <?php
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Identyfikator wydarzenia']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Nazwa']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Rodzaj wydarzenia']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Nazwa obiektu']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Nazwa organizatora']) . "</td>";
                echo "<td>" . htmlspecialchars($row['E-mail organizatora']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Numer telefonu organizatora']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Data wydarzenia']->format('Y-m-d')) . "</td>";
                echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='IdWydarzenia' value='" . htmlspecialchars($row['Identyfikator wydarzenia']) . "'>
                                <input type='submit' id =details-button value='Usuń'>
                            </form>
                          </td>";
                echo "</tr>";
            }
            ?>
            </div>
        </table>
        <div >
            <a href="dodajWydrazenie.php" class="buttonDodajPracownika">Dodaj wydarzenie</a>
        </div>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>