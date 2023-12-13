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
<div>
    <?php echo "<h1>$eventTitle</h1>"; ?>
</div>
<table class="event-detail-container">
    <tr>
        <td>
            <div class="image-container">
                <?php
                if (!empty($eventImage)) {
                    echo "<img src=\"$eventImage\" />";
                } else {
                    echo "<div class=\"monospace-text\">No image</div>";
                }
                ?>
            </div>
        </td>
        <td>
            <table>
                <tr>
                    <td class = "bold-text"><?php echo "Event type: $eventType"; ?></td>
                </tr>
                <tr>
                    <td class = "bold-text"><?php echo "Event severity: $eventSeverity"; ?></td>
                </tr>
                <tr>
                    <td class = "bold-text"><?php echo "Event date: $eventDate"; ?></td>
                </tr>
                <!-- Need to fix locationID -->
                <tr>
                    <td>
                    <table class = "location-container">
                        <td><?php echo "State: $eventState"?></td>
                        <td><?php echo "Continent: $eventContinent"?></td>
                       
                    </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class = "location-container">
                        <td><?php echo "Country: $eventCountry"?></td>
                        <td><?php makeWatchlistButton($eventCountry);?></td>
                        </table>
                    </td>
                </tr>
            
                <tr>
                    <td>
                        <div><?php echo $eventDesc; ?></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>


    
</body>
<?php $db->close(); ?>
</html>