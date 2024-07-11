
<header>
    <h2 class="logo"><a href="<?php echo BASE_URL; ?>index.php">MSPwŁ</a></h2>
    <div class="hamburger_menu">&#9776;</div>
    <nav class="nawigacja">
        <ul class="nav_link">
            <li><a href="<?php echo BASE_URL; ?>index.php">Strona główna</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/wydarzenia.php">Wydarzenia</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/wyszukiwarka.php">Obiekty</a></li>
            <li><a href="<?php echo BASE_URL; ?>pages/kontakt.php">Kontakt</a></li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2): ?>
                <li><a href="<?php echo BASE_URL; ?>pages/pracownik.php">CMS</a></li>
                <li><a href="<?php echo BASE_URL; ?>pages/uzytkownik.php">Dane użytkownika</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 3)): ?>
                <li><a href="<?php echo BASE_URL; ?>pages/uzytkownik.php">Dane</a></li>
                <li><a href="<?php echo BASE_URL; ?>pages/moje_ulubione.php">Ulubione</a></li>
                <li><a href="<?php echo BASE_URL; ?>pages/wystawione.php">Wystawione</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                <li><a href="<?php echo BASE_URL; ?>/../CMS/cms.php">CMS</a></li>
                <li><a href="<?php echo BASE_URL; ?>pages/uzytkownik.php">Dane użytkownika</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo BASE_URL; ?>pages/logout.php"><button class="btnLogowanie">Wyloguj się</button></a>
    <?php else: ?>
        <a href="<?php echo BASE_URL; ?>pages/logowanie.php"><button class="btnLogowanie">Zaloguj się</button></a>
    <?php endif; ?>
</header>

        
