<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();
?>


<html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
        <?php
        if (isset($_SESSION['userEmail'])) {    
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_POST['newWatchListCountryName'])){
                    addItemToWatchList($_POST['newWatchListCountryName']);
                    unset($_POST['newWatchListCountryName']);
                }
            }
            echo "<table>";
            showWatchlistWithRemoveButton();
            echo "</table>";
        }
        else {
            $_SESSION['callback_url'] = 'watchlist.php';
            header("Location: login.php");
        }
        ?>

    </body>
    <script src = "js/removeWatchlistItem.js" defer></script>    
</html>