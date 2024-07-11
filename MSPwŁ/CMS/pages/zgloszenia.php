<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IdZgloszenia'])) {
    $IdZgloszenia = $_POST['IdZgloszenia'];

    $sql = "{CALL up_UsunZgloszenia(?, ?)}";
    $params = array(
        array($IdZgloszenia, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        
        echo "<p>$outputMessage</p>";
    }
}
$sql = "SELECT * FROM uf_Zgloszenia()";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Zgłoszenia</h2>
        <table>
            <thead>
                <tr>
                    <th>Numer zgłoszenia</th>
                    <th>Data zgłoszenia</th>
                    <th>E-mail</th>
                    <th>Treść</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <div>
                <?php
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Numer zgłoszenia']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Data zgłoszenia']->format('Y-m-d')) . "</td>";
                    echo "<td>" . htmlspecialchars($row['E-mail']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Treść']) . "</td>";
                    echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='IdZgloszenia' value='" . htmlspecialchars($row['Numer zgłoszenia']) . "'>
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
