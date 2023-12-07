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
    <?php
        if(isset($_SESSION['userEmail'])){
            $query = "SELECT username FROM users WHERE userEmail = ?";
            $stmt = mysqli_prepare($db,$query);
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['userEmail']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $username = mysqli_fetch_assoc($result);
            echo "Welcome " . $username['username'];
            mysqli_stmt_free_result($stmt);
            mysqli_stmt_close($stmt);
        }
        

    ?>
    <div class="models-container">
        <?php makeCountryDropdown("Country filter","filterCountry","filteredCountry");?>
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

    <script src = "js/locationFilter.js" defer></script>    
    </body>
</html>


$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        event.preventDefault();
    
        var country = $(this).closest('li').attr('data-country');
        var listItem = $('#' + country);
    
        // Show loading indicator
        listItem.html('Removing...');
    
        $.ajax({
            url: 'removeCountryFromWatchlist.php',
            type: 'post',
            data: {removedCountryName: country},
            success: function(data){
                if (country) {
                    listItem.hide('slow', function() {
                        console.log(country + ' removed successfully.');
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
                // Handle error and update UI accordingly
                listItem.html('Error removing country.');
            }
        });
    });
});