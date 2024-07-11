<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");
?>
<main class="mainDODWANIE">
    <div class="containerDodawanie">
        <div>
            <a href="ListaPub.php" class="btn">Puby</a>
        </div>
        <div>
            <a href="ListaRestauracje.php" class="btn">Restauracje</a>
        </div>
        <div>
            <a href="ListaMurali.php" class="btn">Murale</a>
        </div>
        <div>
            <a href="ListaMiejscKulturowych.php" class="btn">Miejsca kulturowe</a>
        </div>
       
        
    </div>
</main>
<?php
include(__DIR__ . "/../../include/footer.php");
?>
