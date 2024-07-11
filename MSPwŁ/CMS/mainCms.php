<main>
    <?php

    $query1 = "SELECT COUNT(*) AS StatystykiUzytkownicy FROM tbl_uzytkownik";
    $query2 = "SELECT COUNT(*) AS StatystykiObiektu FROM tbl_obiekt";
    $query3 = "SELECT COUNT(*) AS StatystykiWydarzen FROM tbl_wydarzenie";
    $query4 = "SELECT COUNT(*) AS StatystykiMiejscKulturowych FROM tbl_miejsce_kulturowe";
    $query5 = "SELECT COUNT(*) AS StatystykiResturacji FROM tbl_restauracja";
    $query6 = "SELECT COUNT(*) AS LiczbaZgloszeń FROM tbl_zgloszenie";

    $result1 = sqlsrv_query($conn, $query1);
    $result2 = sqlsrv_query($conn, $query2);
    $result3 = sqlsrv_query($conn, $query3);
    $result4 = sqlsrv_query($conn, $query4);
    $result5 = sqlsrv_query($conn, $query5);
    $result6 = sqlsrv_query($conn, $query6);

    $totalUsers = sqlsrv_fetch_array($result1)['StatystykiUzytkownicy'];
    $totalObjects = sqlsrv_fetch_array($result2)['StatystykiObiektu'];
    $totalEvents = sqlsrv_fetch_array($result3)['StatystykiWydarzen'];
    $totalCulturalPlaces = sqlsrv_fetch_array($result4)['StatystykiMiejscKulturowych'];
    $totalRestaurants = sqlsrv_fetch_array($result5)['StatystykiResturacji'];
    $totalA = sqlsrv_fetch_array($result6)['LiczbaZgloszeń'];

    ?>

    <main class="mainCMS">
        <h1>Witaj</h1>
        <div class="Statystyki">
            <h2>Statystyki</h2>
            <ul>
                <li>Liczba użytkowników: <?php echo $totalUsers; ?></li>
                <li>Liczba obiektów: <?php echo $totalObjects; ?></li>
                <li>Liczba wydarzeń: <?php echo $totalEvents; ?></li>
                <li>Liczba miejsc kulturowych: <?php echo $totalCulturalPlaces; ?></li>
                <li>Liczba restauracji: <?php echo $totalRestaurants; ?></li>
                <li>Liczba zgłoszeń: <?php echo $totalA; ?></li>
            </ul>
        </div>
    </main>

</main>
