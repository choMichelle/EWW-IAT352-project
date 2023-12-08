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
    <div class="welcome"> <?php showUsername();?> </div>
    <div class = "events-container user-country-event-container">
        <?php
        if (isset($_SESSION['userEmail'])) {
            echo "<h2>Extreme weather events from your country</h2>";
            showEventBasedOnCountries(getUserCountry(),6);
        }
        else {
            echo "<h2>Latest extreme weather events around the world</h2>";
            showEventByNewestDate(5);
        }
        ?>
    </div>

    <div class="events-container generic-event-container">
        
        <h2> Extreme weather around the world</h2>
        <?php makeCountryDropdown("Country filter","filterCountry","filteredCountry");?>

        <div id = "eventTable ">
            <?php
            showEventBasedOnCountries("",12); 
            ?>
        </div>
    </div>
    <?php $db->close(); ?>
    <script src = "js/locationFilter.js" defer></script>    
    </body>
</html>

