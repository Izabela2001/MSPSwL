<header>
    <h2 class="logo"><a href="<?php echo BASE_URL; ?>CMS/cms.php">MSPwŁ -CMS</a></h2>
    <div class="hamburger_menu">&#9776;</div>
    <nav class="nawigacja">
        <ul class="nav_link">
            <li><a href="<?php echo BASE_URL; ?>index.php">Powórt do serwisu</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/organizatorzy.php">Organizatorzy</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/obiekty.php">Obiekty</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/opinie.php">Opinie</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/oceny.php">Oceny</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/zgloszenia.php">Zgłoszenia</a></li>
            <li><a href="<?php echo BASE_URL; ?>CMS/pages/wydarzenie.php">Wydarzenia</a></li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                <li><a href="<?php echo BASE_URL; ?>CMS/pages/uzytkownicy.php">Użytkownicy</a></li>
                <li><a href="<?php echo BASE_URL; ?>CMS/pages/pracownicy.php">Pracownicy</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo BASE_URL; ?>pages/logout.php"><button class="btnLogowanie">Wyloguj się</button></a>
    <?php else: ?>
        <a href="<?php echo BASE_URL; ?>pages/logowanie.php"><button class="btnLogowanie">Zaloguj się</button></a>
    <?php endif; ?>
</header>
