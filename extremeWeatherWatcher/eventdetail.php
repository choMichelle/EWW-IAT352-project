<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

updateMediaTable(3);

$invalidID = true;

if(isset($_GET["eventID"])) $eventID = $_GET["eventID"];

//query to check if eventID exists in the db
$query_weather_eventID = "SELECT eventID FROM weatherevents";
$weather_eventID_result = mysqli_query($db, $query_weather_eventID);
if (!$weather_eventID_result) {
    die("query failed");
}

if (mysqli_num_rows($weather_eventID_result) != 0) {
    while ($row = mysqli_fetch_assoc($weather_eventID_result)) {
        if (isset($_GET["eventID"]) && $row['eventID'] === $eventID) {
            $invalidID = false;
            break;
        }
    }
}

mysqli_free_result($weather_eventID_result);

//get weather event details if ID is valid
if ($invalidID) {
    echo "Event not found.";
    exit;
}
else {

    $result = getSpecificEventDetails($eventID);

    if(mysqli_num_rows($result) != 0){
        while ($row = mysqli_fetch_assoc($result)){
            $eventID = $row['eventID'];
            $eventDate = $row['date'];
            $eventDesc = $row['description'];
            $eventTitle = $row['title'];
            $eventType = $row['type'];
            $eventSeverity = $row['severity'];
            $eventContinent = $row['continent'];
            $eventCountry = $row['country'];
            $eventState = $row['stateOrProvince'];
            $eventImage = $row['mediaURL'];
        }
    }
}

mysqli_free_result($result);

?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Detail</title>
</head>
<body>
    <div><?php echo "<h1>$eventTitle</h1>"; ?></div>
    <div class=image-container>
        <?php
            if (!empty($eventImage)) {
                echo "<img src=\"" . $eventImage . "\" />";
            } else {
                echo "<div class=monospace-text>No image</div>";
            }
        ?>        
    </div>
    <div><?php echo "Event type: $eventType"; ?></div>
    <div><?php echo "Event severity: $eventSeverity"; ?></div>
    <div><?php echo "Event date: $eventDate"; ?></div>
    <!-- Need to fix locationID -->
    <div><?php echo "Event location: $eventState, $eventCountry, $eventContinent"?></div>
    <div>
        <?php
        if (isset($_SESSION['userEmail']) && !isInWatchlist($eventCountry)) {
            echo "<form action=\"watchlist.php\" method=\"post\">";
            echo "<input type = \"hidden\" name=\"newWatchListCountryName\" value=\"$eventCountry\">";
            echo "<input type=\"submit\" name=\Add country to Watchlist\ value=\"Add country to Watchlist\">";
            echo "</form>";
        }
        else if (isset($_SESSION['userEmail'])) {
            echo "The country of this event is already in your watchlist";
        }
        else {
            echo "Log in to add this event's country to your watchlist.";
        }
        ?>
    </div>
    <div><?php echo $eventDesc; ?></div>

    
</body>
<?php $db->close(); ?>
</html>