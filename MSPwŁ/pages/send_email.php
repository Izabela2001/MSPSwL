<?php
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendConfirmationEmail($email, $imie, $nazwisko) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; 
        $mail->SMTPAuth = true;
         $mail->Username = 'mspwmulti@outlook.com'; 
        $mail->Password = 'Najiza1414';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587; 
        $mail->setFrom('mspwmulti@outlook.com', 'MSPwŁ');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 
        $mail->Subject = 'Potwierdzenie rejestracji';
        $mail->Body    = "Witaj $imie $nazwisko,<br><br>Dziękujemy za rejestrację w naszym serwisie.<br>
                            Teraz możesz się zalogować <br>
                                http://localhost/MSPw%C5%81/pages/logowanie.php <br>
                            <br>Pozdrawiamy,<br>Zespół MSPwŁ";

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
?>
