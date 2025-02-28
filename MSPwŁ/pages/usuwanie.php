<?php
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendDeletionEmail($email, $imie, $nazwisko, $login) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'example@outlook.com'; 
        $mail->Password = '******'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587; 
        $mail->setFrom('example@outlook.com', 'MSPwŁ');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; 
        $mail->Subject = 'Usunięcie konta';
        $mail->Body    = "Witaj $imie $nazwisko,<br><br>Twoje konto zostało usunięte. <br><br>Pozdrawiamy,<br>Zespół MSPwŁ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo 'Błąd wysyłania wiadomości e-mail: ', $mail->ErrorInfo;
        return false;
    }
}
?>
