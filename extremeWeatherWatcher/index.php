<!DOCTYPE html>
<?php
//TODO - landing page i guess, personalized for logged in users, standard content for not logged in
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

updateMediaTable(3);

?>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title> Extreme Weather Watcher</title>
</head>

<body>
    <div class="welcome"> <?php showUsername(); ?> </div>
    <div class="events-container user-country-event-container">
        <?php
        //If logged in, show entries based on their country and countries in their watchlist. Else, show entries based on recency
        if (isset($_SESSION['userEmail'])) {
            echo "<h2>Extreme weather events from your country</h2>";
            showEventBasedOnCountries(getUserCountry(), 4, 10);

            echo "<h2>Extreme weather events from your watched countries</h2>";
            showEventBasedOnWatchlist(4);
        } else {
            echo "<h2>Latest extreme weather events around the world</h2>";
            showEventByNewestDate(8);
        }
        ?>
    </div>

</body>
<?php $db->close(); ?>
<script src="js/locationFilter.js" defer></script>

</html>