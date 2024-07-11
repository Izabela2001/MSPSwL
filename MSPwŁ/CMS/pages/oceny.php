<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Nr'])) {
    $idOceny = $_POST['Nr'];

    $sql = "{CALL up_UsunOcene(?, ?)}";
    $params = array(
        array($idOceny, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<p>$outputMessage</p>";
    }
}

$sql = "SELECT * FROM uf_Oceny()";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Lista Ocen</h2>
        <table>
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Data wystawienia</th>
                    <th>Wystawiona ocena</th>
                    <th>Imie</th>
                    <th>Nazwisko</th>
                    <th>Login użytkownika</th>
                    <th>Nazwa obiektu</th>
                    <th>Akcja</th> 
                </tr>
            </thead>
            <div>
                <?php
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Identyfikator oceny']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Data wystawienia']->format('Y-m-d')) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Wystawiona ocena']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Imie']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwisko']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Login']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwa obiektu']) . "</td>";
                    echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='Nr' value='" . htmlspecialchars($row['Identyfikator oceny']) . "'>
                                <input type='submit' id =details-button value='Usuń'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </div>
        </table>
    </div>
</main>

<?php
include(__DIR__ . "/../../include/footer.php");
?>
