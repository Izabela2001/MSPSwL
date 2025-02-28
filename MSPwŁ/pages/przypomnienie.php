<?php
session_start();
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_SESSION['reset_email'];
unset($_SESSION['reset_email']);

if (sendConfirmationEmail($email)) {
    $redirect_url = getEmailProviderUrl($email);
    if ($redirect_url) {
        echo '<script>window.open("' . $redirect_url . '");</script>';
    } else {
        echo '<script>alert("Nie można automatycznie przekierować do poczty. Sprawdź swoją skrzynkę e-mail."); window.history.back();</script>';
    }
    echo '<script>window.onload = function() { setTimeout(function() { window.open("", "_self").close(); }, 3000); }</script>'; // Zamknięcie okna po 3 sekundach
    exit;
} else {
    $reset_message = "Wysłanie e-maila z przypomnieniem hasła nie powiodło się.";
    $_SESSION['reset_message'] = $reset_message;
}

function sendConfirmationEmail($email) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'example@outlook.com';
        $mail->Password = '*******';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('example@outlook.com', 'MSPwŁ');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Zmiana hasła';
        $mail->Body = "Witaj, <br>Konto o adresie e-mail: $email nie pamięta hasła<br>
                            W celu zmiany hasła :<br>
                            http://localhost/MSPw%C5%81/pages/zmianahasla.php<br>
                            Jeżeli to nie ty zignoruj wiadomość.<br>Pozdrawiamy,<br>Zespół MSPwŁ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo 'Błąd wysyłania wiadomości e-mail: ', $mail->ErrorInfo;
        return false;
    }
}

function getEmailProviderUrl($email) {
    $domain = substr(strstr($email, '@'), 1);
    switch ($domain) {
        case 'gmail.com':
            return 'https://mail.google.com/';
        case 'outlook.com':
            return 'https://outlook.com/';
        case 'yahoo.com':
            return 'https://mail.yahoo.com/';
        case 'aol.com':
            return 'https://mail.aol.com/';
        case 'protonmail.com':
            return 'https://mail.protonmail.com/';
        case 'wp.pl':
                return 'https://poczta.wp.pl/';
        default:
            return null; 
    }
}

include(__DIR__ . "/../include/footer.php");
?>




