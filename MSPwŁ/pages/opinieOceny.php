<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");
?>

<main class="WystawioneOcenyOpinie">
    <h2>Opinie:</h2>
    <?php
    if (isset($_GET['Idobiektu'])) {
        $idObiektu = $_GET['Idobiektu'];

        $opinieQuery = "SELECT * FROM uf_OpinieObiektu(?)";
        $opinieParams = array($idObiektu);
        $opinieResult = sqlsrv_query($conn, $opinieQuery, $opinieParams);

        if ($opinieResult === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($opinieResult)) {
            while ($opinia = sqlsrv_fetch_array($opinieResult, SQLSRV_FETCH_ASSOC)) {
                echo "<div class='opinieWy'>";
                echo "<p><strong>Treść:</strong> " . htmlspecialchars($opinia['Tresc']) . "</p>";
                echo "<p><strong>Login:</strong> " . htmlspecialchars($opinia['Login']) . "</p>";
                echo "<p><strong>Data wystawienia:</strong> " . $opinia['Data wystawienia']->format('Y-m-d') . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Brak opinii dla tego obiektu.</p>";
        }

        sqlsrv_free_stmt($opinieResult);
    } else {
        echo "<p>Nieprawidłowe żądanie. Brak przekazanego ID obiektu.</p>";
    }
    ?>

    <h2>Wystawione oceny:</h2>
    <?php
    if (isset($_GET['Idobiektu'])) {
        $idObiektu = $_GET['Idobiektu'];

        $ocenyQuery = "SELECT * FROM uf_WystawioneOceny(?)";
        $ocenyParams = array($idObiektu);
        $ocenyResult = sqlsrv_query($conn, $ocenyQuery, $ocenyParams);

        if ($ocenyResult === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($ocenyResult)) {
            while ($ocena = sqlsrv_fetch_array($ocenyResult, SQLSRV_FETCH_ASSOC)) {
                echo "<div class='ocenyWy'>";
                echo "<p><strong>Wystawiona ocena:</strong> " . htmlspecialchars($ocena['Wystawiona ocena']) . "</p>";
                echo "<p><strong>Login:</strong> " . htmlspecialchars($ocena['Login']) . "</p>";
                echo "<p><strong>Data wystawienia:</strong> " . $ocena['Data wystawienia']->format('Y-m-d') . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Brak wystawionych ocen dla tego obiektu.</p>";
        }

        sqlsrv_free_stmt($ocenyResult);
    } else {
        echo "<p>Nieprawidłowe żądanie. Brak przekazanego ID obiektu.</p>";
    }
    ?>

    <a href="javascript:history.go(-1)" class="button-link">Wstecz</a>

</main>

<?php
include(__DIR__ . "/../include/footer.php");
?>
