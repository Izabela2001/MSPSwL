<?php
session_start();
include(__DIR__ . "/../include/header.php"); 

function zmienHaslo($email, $stareHaslo, $noweHaslo, $conn) {
    $sql = "{CALL up_ZmianaHasla ( ?, ?, ?)}"; 
    $output_message = '';
    $params = array(
        array($email, SQLSRV_PARAM_IN),
        array($noweHaslo, SQLSRV_PARAM_IN),
        array(&$output_message, SQLSRV_PARAM_OUT)
    );
    
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    return $output_message;
}

$output_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['zmien_haslo'])) {
        $email = $_POST['email']; 
        $noweHaslo = $_POST['nowe_haslo']; 
        $output_message = zmienHaslo($email, $stareHaslo, $noweHaslo, $conn);
        if ($output_message === 'Zmieniono hasło.') {
            header("Location: logowanie.php");
            exit();
        }
    }
}
session_destroy();
?>

<main class="StronaZmianaHasla">
    <h1>Zmiana hasła</h1>
    <?php if (!empty($output_message)): ?>
        <p><?php echo $output_message; ?></p>
    <?php endif; ?>
    <form method="post" action="zmianahasla.php">
        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="nowe_haslo">Nowe hasło:</label>
            <input type="password" id="nowe_haslo" name="nowe_haslo" required>
        </div>
        <div>
            <input type="submit" name="zmien_haslo" value="Zmień hasło"> 
        </div>
    </form>
</main>

