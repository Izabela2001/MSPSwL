<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

require_once __DIR__ . '/usuwanie.php';

$showPassword = isset($_POST['showPassword']) ? true : false;
$toggleEditForm = isset($_POST['toggleEditForm']) ? true : false;
?>

<main class="DaneUzytkownika">
    <?php
    if (isset($_SESSION['user_id'])) {
        $IdKonta = $_SESSION['user_id'];

        $sql = "SELECT * FROM uf_SzukajUzytkownika(?)"; 
        $params = array($IdKonta);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo '<h2>Dane użytkownika</h2>';
            echo '<ul>';
            echo '<li><strong>Imie:</strong> ' . htmlspecialchars($row['Imie']) . '</li>';
            echo '<li><strong>Nazwisko:</strong> ' . htmlspecialchars($row['Nazwisko']) . '</li>';
            echo '<li><strong>E-mail:</strong> ' . htmlspecialchars($row['E_mail']) . '</li>';
            echo '<li><strong>Telefon:</strong> ' . htmlspecialchars($row['Telefon']) . '</li>';
            echo '<li><strong>Data urodzenia:</strong> ' . ($row['DataUrodzenia'] instanceof DateTime ? $row['DataUrodzenia']->format('Y-m-d') : '') . '</li>';
            echo '<li><strong>Login:</strong> ' . htmlspecialchars($row['Login']) . '</li>';
            echo '<li><strong>Hasło:</strong> <span id="passwordDisplay">' . ($showPassword ? htmlspecialchars($row['Hasło']) : '••••••••') . '</span></li>';
            echo '</ul>';
            echo '<form method="post" action="">';
            echo '<input type="checkbox" name="showPassword" onchange="this.form.submit()" ' . ($showPassword ? 'checked' : '') . '> Pokaż hasło<br>';
            echo '</form>';
            echo "<form action='zmianahasla.php'>";
            echo "<input type='submit' value='Zmień hasło' class='rating-button'>";
            echo "</form>";
            echo '<form method="post" action="">';
            echo '<input type="submit" value="Edytuj dane" name="toggleEditForm"></input>';
            echo '</form>';
            if ($toggleEditForm) {
                echo '<div id="editForm">';
                echo '<h2>Edytuj informacje</h2>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="IdUzytkownik" value="' . $IdKonta . '">';
                echo 'Imię: <input type="text" name="imie" value="' . htmlspecialchars($row['Imie']) . '"><br>';
                echo 'Nazwisko: <input type="text" name="nazwisko" value="' . htmlspecialchars($row['Nazwisko']) . '"><br>';
                echo 'E-mail: <input type="text" name="e_mail" value="' . htmlspecialchars($row['E_mail']) . '"><br>';
                echo 'Telefon: <input type="text" name="telefon" value="' . htmlspecialchars($row['Telefon']) . '"><br>';
                echo 'Data urodzenia: <input type="date" name="data_urodzenia" value="' . $row['DataUrodzenia']->format('Y-m-d') . '"><br>';
                echo '<input type="submit" name="submit" value="Zapisz zmiany">';
                echo '</form>';
                echo '</div>';
            }

            

            if (isset($_POST['submit'])) {
                $imie = $_POST['imie'];
                $nazwisko = $_POST['nazwisko'];
                $e_mail = $_POST['e_mail'];
                $telefon = $_POST['telefon'];
                $data_urodzenia = $_POST['data_urodzenia'];
                $IdUzytkownik = $_POST['IdUzytkownik'];

                $data_urodzenia = !empty($data_urodzenia) ? date('Y-m-d', strtotime($data_urodzenia)) : null;

                $sql = "{CALL up_AktualizujUzytkownika(?, ?, ?, ?, ?, ?, ?, ?)}";
                $params = array(null, $imie, $nazwisko, $e_mail, $telefon, $data_urodzenia, $IdUzytkownik, &$output_message);
                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                } else {
                    echo '<div class="success">' . $output_message . '</div>';
                }
            }

            echo '<form method="post" action="">';
            echo '<input type="hidden" name="IdUzytkownik" value="' . $IdKonta . '">';
            echo '<input type="submit" name="usunKonto" value="Usuń konto">';
            echo '</form>';

            if (isset($_POST['usunKonto'])) {
                $IdUzytkownik = $_POST['IdUzytkownik'];

                $output_message = '';
                $sql = "{CALL up_UsunUzytkownika(?, ?)}";
                $params = array($IdUzytkownik, &$output_message);
                $stmt = sqlsrv_query($conn, $sql, $params);

                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                } else {
                    if (sendDeletionEmail($row['E_mail'], $row['Imie'], $row['Nazwisko'], $row['Login'])) {
                        echo '<div class="success">E-mail z potwierdzeniem usunięcia konta został wysłany.</div>';

                        session_unset();
                        session_destroy();

                        header("Location: .././index.php");
                        exit();
                    } else {
                        echo '<div class="error">Wysłanie e-maila z potwierdzeniem usunięcia konta nie powiodło się.</div>';
                    }
                }
            }
        } else {
            echo '<div class="error">Nie znaleziono użytkownika o podanym IdKonta.</div>';
        }
        sqlsrv_close($conn);
    } else {
        echo '<div class="error">Brak sesji użytkownika lub nieprawidłowy identyfikator konta.</div>';
    }
    ?>
</main>
<?php
include(__DIR__ . "/../include/footer.php");
?>
