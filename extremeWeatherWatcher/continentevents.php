<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

updateMediaTable(3);

?>

<html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <title>EWW - Weather Events in </title>
    </head>
    <body>
        <div class="events-container generic-event-container">
            
            <h2> Extreme weather around the world</h2>
            <?php makeCountryDropdown("Country filter","filterCountry","filteredCountry");?>

            <div id="eventTable ">
                <?php
                showEventBasedOnCountries("",9); 
                ?>
            </div>
        </div>
        <?php $db->close(); ?>
        <script src = "js/locationFilter.js" defer></script> 
    </body>
</html>