<?php
$serverName = "IZABELA";
$database = "MULTIWYSZUKIWARKA";
define('BASE_URL', '/MSPwŁ/');

try {
    $conn = sqlsrv_connect($serverName, array(
        "Database" => $database,
        "TrustServerCertificate" => true,
        "CharacterSet" => "UTF-8"
    ));
    
    if ($conn === false) {
        die("Błąd połączenia: " . print_r(sqlsrv_errors(), true));
    } else {
       // echo "Połączono z bazą danych!";
    }
} catch (Exception $e) {
    echo "Błąd połączenia: " . $e->getMessage();
}
?>







