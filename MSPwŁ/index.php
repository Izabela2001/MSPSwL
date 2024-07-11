<?php
session_start();
    if (file_exists("./include/header.php")) {
        include("./include/header.php");
    }
    if (file_exists("./include/nav.php")) {
        include("./include/nav.php");
    } 
    if (file_exists("./include/banner.php")) {
        include("./include/banner.php");
    }
    if (file_exists("./include/main.php")) {
        include("./include/main.php");
    }
    if (file_exists("./include/footer.php")) {
        include("./include/footer.php");
    }
?>
