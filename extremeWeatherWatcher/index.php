<!DOCTYPE html>
<?php
//TODO - landing page i guess, personalized for logged in users, standard content for not logged in
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

$query_weather_event = "SELECT * FROM weatherevents";
$weather_event_result = mysqli_query($db,$query_weather_event);
if (!$weather_event_result) {
    die("query failed");
}

?>
<html>
    <head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <title> Extreme Weather Watcher</title>
    </head>
    <body>
    <div class="welcome"> <?php showUsername();?> </div>
    <div>
        <h2>Extreme weather events from your country <?php echo getUserCountry() ?></h2>
        <?php 
            showEventBasedOnCountries(getUserCountry(),5);
        ?>
    </div>

    <div class="models-container">
        
        
            <h2> Extreme weather around the world</h2>
            <?php makeCountryDropdown("Country filter","filterCountry","filteredCountry");?>
        <div id = "eventTable">
            <?php showEventBasedOnCountries("",12); ?>
        <?php
            if(mysqli_num_rows($weather_event_result)!= 0){
                while($row = mysqli_fetch_assoc($weather_event_result)){
                    $eventLocationDetails = getEventLocation($db, $row['locationID']);
                    addListItem($row['title'], $row['eventID'], $eventLocationDetails);
                }
            }
            
            mysqli_free_result($weather_event_result);
            $db->close();
        ?>
        </div>
    </div>

    <script src = "js/locationFilter.js" defer></script>    
    </body>
</html>

