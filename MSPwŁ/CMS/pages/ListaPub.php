<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");

function getPubData($conn) {
    $pubs = [];
    $sql = "SELECT * FROM uf_ObiektPuby()";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $pubs[] = $row;
        }
    }
    return $pubs;
}


function deletePub($conn, $pub_id) {
    $output_message = '';
    $params = array(
        array(&$pub_id, SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT)
    );
    $stmt = sqlsrv_query($conn, "{CALL up_UsunObiekt(?, ?)}", $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    return $output_message;
}

$pubs = getPubData($conn);

if (isset($_POST['delete_pub'])) {
    $pub_id = $_POST['IdObiektu'];
    $message = deletePub($conn, $pub_id);
    echo "<script>alert('$message');</script>";
    echo "<script>window.location.replace('ListaPub.php');</script>";
    exit;
}
?>

<main class="ListyObiektow">
    <h1>Puby</h1>
    <table>
        <thead>
            <tr>    
                <th></th>
                <th></th>
                <th>Identyfikator pubu</th>
                <th>Identyfikator obiektu</th>
                <th>Nazwa obiektu</th>
                <th>Ulica</th>
                <th>Numer ulicy</th>
                <th>Numer lokalu</th>
                <th>Email</th>
                <th>Opis</th>
                <th>Średnia ocena</th>
                <th>Telefon</th>
                <th>Dla dzieci</th>
                <th>Dla niepełnosprawnych</th>
                <th>Ogródek</th>
                <th>Strefa dla dzieci</th>
                <th>Strefa palacza</th>
                <th>Wpuszczanie zwierząt</th>
                <th>Darmowe wejście</th>
                <th>Imie osoby zarządzającej</th>
                <th>Nazwisko osoby zarzadzającej</th>
                <th>Stanowisko</th>
                
            </tr>
        </thead>
        <div id = "ListaObiektow">
        <form  class="FormularzPrzekazywania" action="update_pub.php" method="get">
                <label for="pub_id">Wybierz numer pubu:</label>
                <select id="pub_id" name="pub_id" required>
                    <?php foreach ($pubs as $pub): ?>
                        <option value="<?= htmlspecialchars($pub['pub_id']) ?>"><?= htmlspecialchars($pub['pub_id']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button  class="btn" type="submit">Aktualizuj Pub</button>
                
        </form>
        
            <?php foreach ($pubs as $pub): ?>
                <tr>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="IdObiektu" value="<?= $pub['Identyfikator obiektu'] ?>">
                            <button class="btn" type="submit" name="delete_pub">Usuń</button>
                        </form>
                        
                    </td>
                    <td>
                        <form action="harmonogramobiektu.php" method="get">
                            <input type="hidden" name="IdObiektu" value="<?= $pub['Identyfikator obiektu'] ?>">
                            <button class="btn" type="submit">Harmonogram obiektu</button>
                        </form>
                     </td>
                    <td><?= htmlspecialchars($pub['pub_id']) ?></td>
                    <td><?= htmlspecialchars($pub['Identyfikator obiektu']) ?></td>
                    <td><?= htmlspecialchars($pub['Nazwa obiektu']) ?></td>
                    <td><?= htmlspecialchars($pub['Ulica']) ?></td>
                    <td><?= htmlspecialchars($pub['Numer ulicy']) ?></td>
                    <td><?= htmlspecialchars($pub['Numer lokalu']) ?></td>
                    <td><?= htmlspecialchars($pub['Email']) ?></td>
                    <td><?= htmlspecialchars($pub['Opis']) ?></td>
                    <td><?= htmlspecialchars($pub['Średnia ocena']) ?></td>
                    <td><?= htmlspecialchars($pub['Telefon']) ?></td>
                    <td><?= htmlspecialchars($pub['Dla dzieci']) ?></td>
                    <td><?= htmlspecialchars($pub['Dla niepełnosprawnych']) ?></td>
                    <td><?= htmlspecialchars($pub['Ogródek']) ?></td>
                    <td><?= htmlspecialchars($pub['Strefa dla dzieci']) ?></td>
                    <td><?= htmlspecialchars($pub['Strefa palacza']) ?></td>
                    <td><?= htmlspecialchars($pub['Wpuszczanie zwierząt']) ?></td>
                    <td><?= htmlspecialchars($pub['NazwaDnia']) ?></td>
                    <td><?= htmlspecialchars($pub['Imię']) ?></td>
                    <td><?= htmlspecialchars($pub['Nazwisko']) ?></td>
                    <td><?= htmlspecialchars($pub['Stanowisko']) ?></td>
                </tr>
            <?php endforeach; ?>
            </div>
    </table>
    <form action="dodajPub.php" >
        <button class="btn" type="submit">Dodaj nowy pub</button>
    </form>

</main>
<?php include(__DIR__ . "/../../include/footer.php"); ?>

