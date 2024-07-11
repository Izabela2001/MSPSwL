<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

function validate_login($login, $haslo, $conn) {
    $sql = "SELECT * FROM uf_WyszukajUzytkownika(?, ?)";
    $params = array($login, $haslo);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $_SESSION['user_id'] = $row['IdKonta'];
        $_SESSION['user_role'] = $row['IdTypKonta'];
        $_SESSION['uzytkownik_id'] = $row['IdUzytkownika'];
        header("Location: ../index.php");
        exit;
    } else {
        $error_message = "Nieprawidłowy login lub hasło.";
        return $error_message;
    }
}

function sprawdzEmail($email, $conn) {
    $sql = "SELECT Liczba FROM uf_SprawdzEmail(?)";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $row['Liczba'] > 0;
}

$show_reset_form = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        $login = $_POST['login'];
        $haslo = $_POST['haslo'];

        $error_message = validate_login($login, $haslo, $conn);
    }

    if (isset($_POST['reset_password'])) {
        $email = $_POST['email'];
        
        if (sprawdzEmail($email, $conn)) {
            $_SESSION['reset_email'] = $email;
            header("Location: przypomnienie.php");
            exit;
        } else {
            $error_message= "Nie znaleziono użytkownika z podanym e-mailem.";
        }
    }

    if (isset($_POST['show_reset_form'])) {
        $show_reset_form = true;
    }
}
?>

<main class="sLogowanie">
    <div class="logowanie">
        <h1>Zaloguj się</h1>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post" action="logowanie.php">
            <div class="poleLogowanie">
                <label for="login">Login:</label>
                <input type="text" name="login" id="login" required>
            </div>
            <div class="poleLogowanie">
                <label for="haslo">Hasło:</label>
                <input type="password" name="haslo" id="haslo" required>
            </div>
            <div class="poleLogowanie">
                <input type="submit" name="submit" value="Zaloguj się">
            </div>
            <?php if ($show_reset_form): ?>
            <h1>Przypomnienie hasła</h1>
            <?php if (isset($reset_message)): ?>
                <div class="reset-message"><?php echo $reset_message; ?></div>
            <?php endif; ?>
            <form method="post" action="logowanie.php">
                <div class="poleLogowanie">
                    <label for="email">E-mail:</label>
                    <input type="text" name="email" id="email" required>
                </div>
                <div class="poleLogowanie">
                    <input type="submit" name="reset_password" value="Wyślij">
                </div>
            </form>
        <?php else: ?>
            <form method="post" action="logowanie.php">
                <div class="poleLogowanie">
                    <input type="submit" name="show_reset_form" value="Przypomnij hasło">
                </div>
            </form>
        <?php endif; ?>
            <div class="poleLogowanie">
                <p>Nie masz konta? <a href='./rejestracja.php'>Zarejestruj się</a></p>
            </div>
        </form>

       
    </div>
</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>
