<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

require_once __DIR__ . '/send_email.php';


function checkUniqueLogin($login, $conn) {
    $check_login_sql = "SELECT Liczba FROM dbo.uf_SprawdzenieLoginu(?)";
    $check_login_params = array($login);
    $check_login_stmt = sqlsrv_query($conn, $check_login_sql, $check_login_params);

    if ($check_login_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $login_row = sqlsrv_fetch_array($check_login_stmt, SQLSRV_FETCH_ASSOC);
    return $login_row['Liczba'] > 0 ? false : true;
}


function checkUniqueEmail($email, $conn) {
    $check_email_sql = "SELECT Liczba FROM  uf_SprawdzEmail(?) ";
    $check_email_params = array($email);
    $check_email_stmt = sqlsrv_query($conn, $check_email_sql, $check_email_params);

    if ($check_email_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $email_row = sqlsrv_fetch_array($check_email_stmt, SQLSRV_FETCH_ASSOC);
    return $email_row['Liczba'] > 0 ? false : true;
}

function validatePassword($password) {
    $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/'; 
    return preg_match($pattern, $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
   
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];
    $imie = $_POST['imie']; 
    $nazwisko = $_POST['nazwisko'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];
    $data_urodzenia = $_POST['data_urodzenia'];

    $output_message = '';

    if (!checkUniqueLogin($login, $conn)) {
        $output_message = 'Login już istnieje. Wybierz inny login.';
    } else {
       
        if (!checkUniqueEmail($email, $conn)) {
            $output_message = 'E-mail już istnieje. Wybierz inny e-mail.';
        } elseif (!validatePassword($haslo)) {
            $output_message = 'Hasło nie spełnia wymagań bezpieczeństwa.';
        } else {
           
            $sql = "EXEC up_DodajUzytkownika ?, ?, ?, ?, ?, ?, ?, ?";
            $params = array(
                array($login, SQLSRV_PARAM_IN),
                array($haslo, SQLSRV_PARAM_IN),
                array(&$imie, SQLSRV_PARAM_INOUT),
                array(&$nazwisko, SQLSRV_PARAM_INOUT),
                array($email, SQLSRV_PARAM_IN),
                array($telefon, SQLSRV_PARAM_IN),
                array($data_urodzenia, SQLSRV_PARAM_IN),
                array(&$output_message, SQLSRV_PARAM_OUT)
            );

            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            
            if (sendConfirmationEmail($email, $imie, $nazwisko)) {
                $output_message = 'Rejestracja zakończona sukcesem. Potwierdzenie zostało wysłane na Twój e-mail.';
            } else {
                $output_message = 'Rejestracja zakończona sukcesem, ale nie udało się wysłać potwierdzenia na e-mail.';
            }

            header("Location: .././pages/logowanie.php");
            exit;
        }
    }

    if (!empty($output_message)) {
        echo '<div id="modal" class="modal">';
        echo '<div class="modal-content">';
        echo '<span class="close-button">&times;</span>';
        echo '<p>' . htmlspecialchars($output_message) . '</p>';
        echo '</div>';
        echo '</div>';
    }
}
?>

<main class="mainRejestracji">
    <form method="post" action="">
        <?php
        if (!empty($output_message)) {
            echo '<div id="messageContainer"><p>' . htmlspecialchars($output_message) . '</p></div>';
        }
        ?>
        <div>
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" required>
       
            </div>
        <div>
            <label for="haslo">Hasło:</label>
            <input type="password" name="haslo" id="haslo" required>
        </div>
        <div>
            <label for="imie">Imię:</label>
            <input type="text" name="imie" id="imie" required>
        </div>
        <div>
            <label for="nazwisko">Nazwisko:</label>
            <input type="text" name="nazwisko" id="nazwisko" required>
        </div>
        <div>
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="telefon">Telefon:</label>
            <input type="tel" id="telefon" name="telefon" pattern="\+48\d{3}-\d{3}-\d{3}" title="Numer telefonu musi być w formacie +48111-111-111" placeholder="+48" required><br><br>
        </div>
        <div>
            <label for="data_urodzenia">Data urodzenia:</label>
            <input type="date" name="data_urodzenia" id="data_urodzenia" required>
        </div>
        <div>
            <input type="submit" name="submit" value="Zarejestruj">
        </div>
        
    </form>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>

