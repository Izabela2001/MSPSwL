<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idKonta'])) {
    $idKonta = $_POST['idKonta'];

    $sql = "{CALL up_UsunUzytkownika(?, ?)}";
    $params = array(
        array($idKonta, SQLSRV_PARAM_IN),
        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING('UTF-8'))
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "<p>$outputMessage</p>";
    }
}

$query = "SELECT * FROM uf_WyszukajUzytkowników()";
$result = sqlsrv_query($conn, $query);

if ($result === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<main class="mainCMS">
    <div class="Uzytkownicy">
        <h2>Lista użytkowników</h2>
        <table>
            <thead>
            <tr>
                <th>Nr</th>
                <th>Login użytkownika</th>
                <th>Hasło użytkownika</th>
                <th>Imię użytkownika</th>
                <th>Nazwisko użytkownika</th>
                <th>Telefon użytkownika</th>
                <th>E-mail</th>
                <th>Data urodzenia</th>
                <th>Akcje</th>
            </tr>
            </thead>
            <div>
            <?php
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Identyfikator użytkownika']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Login użytkownika']) . "</td>";
                echo "<td>" . htmlspecialchars(base64_encode($row['Haslo_uzytkownika'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['Imie użytkownika']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Nazwisko użytkownika']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Telefon użytkownika']) . "</td>";
                echo "<td>" . htmlspecialchars($row['E-mail']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Data urodzenia']->format('Y-m-d')) . "</td>";
                echo "<td>
                            <form method='post' action=''>
                                <input type='hidden' name='idKonta' value='" . htmlspecialchars($row['Identyfikator użytkownika']) . "'>
                                <input type='submit' id =details-button  value='Usuń'>
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

