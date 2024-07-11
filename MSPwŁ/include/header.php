<?php
if (file_exists(__DIR__ . "/../config.php")) {
    require_once(__DIR__ . "/../config.php");
} else {
    echo "Error 403. Brak pliku konfiguracyjnego";
}
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset = "UTF-8">
        <meta name = "author" content = "Izabela Najder">
        <meta name = "viewport" content = "width=device-width, initial-scale=1">
        <title> MSPwŁ</title>
        <link rel="shortcut icon" href="<?php echo BASE_URL; ?>img/logo.png" type="image/png">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>style/style.css">
     

    </head>
    <body>
    
    