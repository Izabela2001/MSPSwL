<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Idopini'])) {
    $idOpini = $_POST['Idopini'];

    $sql = "{CALL up_UsunOpinie(?, ?)}";
    $params = array(
        array($idOpini, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        
        echo "<p>$outputMessage</p>";
    }
}
$sql = "SELECT * FROM uf_Opinie()";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Opinie</h2>
        <table>
            <thead>
                <tr>
                    <th>Identyfikator opini</th>
                    <th>Treść</th>
                    <th>Data wystawienia</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Login użytkownika</th>
                    <th>Nazwa obiektu</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <div>
                <?php
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Identyfikator opini']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Treść']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Data wystawienia']->format('Y-m-d')) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Imię']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwisko']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Login']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Nazwa obiektu']) . "</td>";
                    echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='Idopini' value='" . htmlspecialchars($row['Identyfikator opini']) . "'>
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
