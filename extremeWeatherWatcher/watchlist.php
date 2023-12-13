<!DOCTYPE html>
<!-- shows countries the user added to their watchlist, can remove countries via remove button -->

<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Your Watchlist</title>
</head>

<body>
    <h1>Your Watchlist</h1>
    <?php
        //if logged in, show add to watchlist button and watchlist countries
        if (isset($_SESSION['userEmail'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['newWatchListCountryName'])) {
                    addItemToWatchList($_POST['newWatchListCountryName']);
                    unset($_POST['newWatchListCountryName']);
                }
            }
            echo "<table>";
            showWatchlistWithRemoveButton();
            echo "</table>";
        }
    ?>

</body>
<script src="js/removeWatchlistItem.js" defer></script>

</html>