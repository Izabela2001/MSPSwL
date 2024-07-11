<?php
session_start();
include(__DIR__ . "/../include/header.php");
include(__DIR__ . "/../include/nav.php");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = $_POST['item_id'];
            $itemType = $_POST['item_type'];
            $outputMessage = '';
            if ($itemType === 'opinion') {
                if (isset($_POST['new_content'])) {
                    
                    $newContent = $_POST['new_content'];
                    if (empty(trim($newContent))) {
                        $outputMessage = 'Treść opinii nie może być pusta.';
                    } elseif (strlen($newContent) > 500) {
                        $outputMessage = 'Treść opinii nie może przekraczać 500 znaków.';
                    } else {
                        $editQuery = "{CALL up_EdycjaOpini(?, ?, ?)}";
                        $params = array(
                            array($itemId, SQLSRV_PARAM_IN),
                            array($newContent, SQLSRV_PARAM_IN),
                            array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR))
                        );
                        $stmt = sqlsrv_query($conn, $editQuery, $params);
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                    }
                } else {
                    $deleteQuery = "{CALL PS_UsunOpinie(?, ?)}";
                    $params = array(
                        array($itemId, SQLSRV_PARAM_IN),
                        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR))
                    );
                    $stmt = sqlsrv_query($conn, $deleteQuery, $params);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            } elseif ($itemType === 'rating') {
                if (isset($_POST['Ocena'])) {
                    
                    $newRating = intval($_POST['Ocena']);
                    if (empty($newRating) || !is_numeric($newRating) || $newRating < 1 || $newRating > 5) {
                        $outputMessage = 'Ocena musi być liczbą od 1 do 5.';
                    } else {
                        $editQuery = "{CALL up_EdytujOcene(?, ?, ?)}";
                        $params = array(
                            array($itemId, SQLSRV_PARAM_IN),
                            array($newRating, SQLSRV_PARAM_IN),
                            array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR))
                        );
                        $stmt = sqlsrv_query($conn, $editQuery, $params);
                        if ($stmt === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                    }
                } else {
                    $deleteQuery = "{CALL up_UsunWystawionaOcena(?, ?)}";
                    $params = array(
                        array($itemId, SQLSRV_PARAM_IN),
                        array(&$outputMessage, SQLSRV_PARAM_OUT, SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR))
                    );
                    $stmt = sqlsrv_query($conn, $deleteQuery, $params);
                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }
            }

            echo "<script>alert('$outputMessage');</script>";
        }
        $userId = $_SESSION['user_id'];

        $opinionsQuery = "SELECT * FROM uf_WyszukajOpinie(?)";
        $opinionsParams = array($userId);
        $opinionsResult = sqlsrv_query($conn, $opinionsQuery, $opinionsParams);
        if ($opinionsResult === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        echo '<main class="mainWystawion">';
        echo '<div class="contenerOpinia">';
        echo '<h2>Twoje opinie</h2>';
        echo '<ul>';

        if (sqlsrv_has_rows($opinionsResult)) {
            while ($opinion = sqlsrv_fetch_array($opinionsResult, SQLSRV_FETCH_ASSOC)) {
                $opinionDate = $opinion['Data wystawionej opini']->format('Y-m-d');
                echo "<li>";
                echo "<strong>Numer opini: </strong>" . $opinion['Numer opini'] . "<br>";
                echo "<strong>Treść:</strong> <span class='opinion-content'>" . $opinion['Treść opini'] . "</span>";
                echo "<strong>Obiekt:</strong> <span class='opinion-content'>" . $opinion['Obiekt'] . "</span>";
                echo "<strong>Data wystawionej opini: </strong>" . $opinionDate;
                echo '<br><button class="btn" onclick="toggleEditForm(' . $opinion['Numer opini'] . ')">Edytuj</button>';
                echo '<form method="POST" action="" style="display:none;" id="edit-form-' . $opinion['Numer opini'] . '" onsubmit="return validateForm(' . $opinion['Numer opini'] . ')">';
                echo '<input type="hidden" name="item_id" value="' . $opinion['Numer opini'] . '">';
                echo '<input type="hidden" name="item_type" value="opinion">';
                echo '<textarea id="nowa-opinia-' . $opinion['Numer opini'] . '" name="new_content">' . $opinion['Treść opini'] . '</textarea><br>';
                echo '<button type="submit" class="btn">Zapisz</button>';
                echo '<button type="button" class="btn" onclick="toggleEditForm(' . $opinion['Numer opini'] . ')">Anuluj</button>';
                echo '</form>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="item_id" value="' . $opinion['Numer opini'] . '">';
                echo '<input type="hidden" name="item_type" value="opinion">';
                echo '<br><button type="submit" class="btn">Usuń</button>';
                echo '</form>';
                echo "</li>";
            }
        } else {
            echo "<li>Brak opinii.</li>";
        }

        echo '</ul>';
        echo '</div>';
        $ratingsQuery = "SELECT * FROM uf_WyszukajOcene(?)";
        $ratingsParams = array($userId);
        $ratingsResult = sqlsrv_query($conn, $ratingsQuery, $ratingsParams);
        if ($ratingsResult === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        echo '<div class="contenerOceny">';
        echo '<h2>Twoje oceny</h2>';
        echo '<ul>';

        if (sqlsrv_has_rows($ratingsResult)) {
            while ($rating = sqlsrv_fetch_array($ratingsResult, SQLSRV_FETCH_ASSOC)) {
                $ratingDate = $rating['Data wystawionej oceny']->format('Y-m-d');
                echo "<li>";
                echo "<strong>Numer oceny: </strong>" . $rating['Numer oceny'] . "<br>";
                echo "<strong>Obiekt:</strong> " . $rating['Obiekt'] . "<br>";
                echo "<strong>Wystawiono ocenę: </strong>" . $rating['Wystawiono ocena'] . "<br>";
                echo "<strong>Data wystawionej oceny:</strong> " . $ratingDate;
                echo '<br><button class="btn" onclick="toggleEditFormRating(' . $rating['Numer oceny'] . ')">Edytuj</button>';
                echo '<form method="POST" action="" style="display:none;" id="edit-form-rating-' . $rating['Numer oceny'] . '" onsubmit="return validateRatingForm(' . $rating['Numer oceny'] . ')">';
                echo '<input type="hidden" name="item_id" value="' . $rating['Numer oceny'] . '">';
                echo '<input type="hidden" name="item_type " value="rating">';
                echo '<label>Ocena:</label><br>';
                echo '<select name="Ocena" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select><br>';
                echo '<button type="submit" class="btn">Zapisz</button>';
                echo '<button type="button" class="btn" onclick="toggleEditFormRating(' . $rating['Numer oceny'] . ')">Anuluj</button>';
                echo '</form>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="item_id" value="' . $rating['Numer oceny'] . '">';
                echo '<input type="hidden" name="item_type" value="rating">';
                echo '<br><button type="submit" class="btn">Usuń</button>';
                echo '</form>';
                echo "</li>";
            }
        } else {
            echo "<li>Brak ocen.</li>";
        }

        echo '</ul>';
        echo '</div>';
echo '</main>';

include(__DIR__ . "/../include/footer.php");

?>

