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
    <script>
        $(document).ready(function(){
        // Assuming you have an element with id "filterCountry"
        $("#filterCountry").on("change", function() {
            var selected = $(this).val().toLowerCase();
            $("#eventTable a").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(selected) > -1);
            });
        });
    });
    </script>    
    <title> Extreme Weather Watcher</title>
    </head>
    <body>
    <div class="models-container">
        <?php makeCountryDropdown("filterCountry");?>
        <div id = "eventTable">
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


    </body>
</html>