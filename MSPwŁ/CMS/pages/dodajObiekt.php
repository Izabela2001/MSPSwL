<?php
session_start();
include(__DIR__ . "/../../include/header.php");
include(__DIR__ . "/../navCms.php");
?>
<main class="mainDODWANIE">
    <div class="containerDodawanie">
        <div>
            <a href="dodajPub.php" class="buttonDodaj">Dodaj pub</a>
        </div>
        <div>
            <a href="dodajRestauracja.php" class="buttonDodaj">Dodaj restauracje</a>
        </div>
        <div>
            <a href="dodajMural.php" class="buttonDodaj">Dodaj mural</a>
        </div>
        <div>
            <a href="dodajMiejsceKulturowe.php" class="buttonDodaj">Dodaj miejsce kulturowe</a>
        </div>
    </div>
</main>
<?php
include(__DIR__ . "/../../include/footer.php");
?>
