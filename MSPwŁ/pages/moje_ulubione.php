<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

function get_favorite_objects($user_id, $conn) {
    $sql = "SELECT * FROM uf_WyszukajUlubione(?)";
    $params = array($user_id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $favorite_objects = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $favorite_objects[] = $row;
    }

    return $favorite_objects;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $favorite_objects = get_favorite_objects($user_id, $conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $favoriteId = $_POST['favorite_id'];
        $outputMessage = '';

        $deleteQuery = "{CALL up_UsunUlubione(?, ?)}";
        $params = array(
            array($favoriteId, SQLSRV_PARAM_IN),
            array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR))
        );

        $deleteStmt = sqlsrv_query($conn, $deleteQuery, $params);

        if ($deleteStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        echo "<script>alert('$outputMessage');</script>";

        $favorite_objects = get_favorite_objects($user_id, $conn);
    }
} else {
    header("Location: login.php");
    exit;
}
?>

<main class="Ulubione">
    <h1>Twoje ulubione obiekty</h1>
    <div class="listaUlubionych">
        <?php if (!empty($favorite_objects)): ?>
            <table>
                <thead>
                <tr>
                    <th>Numer</th>
                    <th>Obiekt</th>
                    <th>Opis</th>
                    <th>Data utworzenia</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($favorite_objects as $object): ?>
                    <tr>
                        <td><?php echo $object['IdUlubionych']; ?></td>

                        <td><?php echo $object['Nazwa obiektu']; ?></td>
                        <td><?php echo $object['Opis']; ?></td>
                        <td><?php echo $object['Data utworzenia']->format('Y-m-d'); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="favorite_id" value="<?php echo $object['IdUlubionych']; ?>">
                                <div class="UsuwanieUlubionego">
                                    <button type="submit">Usuń</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak ulubionych obiektów.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>


