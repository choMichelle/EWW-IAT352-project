<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

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

//get weather event details
$query_event = "SELECT * FROM weatherevents WHERE eventID = ?";
$stmt_event = mysqli_prepare($db, $query_event);

if(!$stmt_event){
    die("Error:" .mysqli_error($db));
}

if ($invalidID) {
    echo "Event not found.";
    exit;
}
else{
    mysqli_stmt_bind_param($stmt_event,"i",$eventID);
    mysqli_stmt_execute($stmt_event);

    $result = mysqli_stmt_get_result($stmt_event);

    if(mysqli_num_rows($result) != 0){
        while ($row = mysqli_fetch_assoc($result)){
            $eventID = $row['eventID'];
            $eventDate = $row['date'];
            $eventDesc = $row['description'];
            $eventTitle = $row['title'];
            $eventType = $row['type'];
            $eventSeverity = $row['severity'];
            $eventLocationID = $row['locationID'];
        }
    }
}

mysqli_free_result($result);

//query to get location details
$query_location = "SELECT continent, country, stateOrProvince FROM `location` WHERE locationID=?";
$stmt_location = mysqli_prepare($db, $query_location);

if (!$stmt_location) {
    die("Error:" .mysqli_error($db));
}
else {
    mysqli_stmt_bind_param($stmt_location, "i", $eventLocationID);
    mysqli_stmt_execute($stmt_location);

    $result = mysqli_stmt_get_result($stmt_location);

    if (mysqli_num_rows($result) != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $eventContinent = $row['continent'];
            $eventCountry = $row['country'];
            $eventState = $row['stateOrProvince'];
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
    <div><?php echo "Event type: $eventType"; ?></div>
    <div><?php echo "Event severity: $eventSeverity"; ?></div>
    <div><?php echo "Event date: $eventDate"; ?></div>
    <!-- Need to fix locationID -->
    <div><?php echo "Event location: $eventState, $eventCountry, $eventContinent"?></div>
    <div>
        <?php
        if ((!isset($_SESSION['userEmail']))
        || ((isset($_SESSION['userEmail'])) && !isInWatchlist($eventCountry))){
            echo "<form action=\"watchlist.php\" method=\"post\">";
            echo "<input type = \"hidden\" name=\"newWatchListCountryName\" value=\"$eventCountry\">";
            echo "<input type=\"submit\" name=\Add country to Watchlist\ value=\"Add country to Watchlist\">";
            echo "</form>";
        }
        else{
            echo "The country of this event is already in your watchlist";
        }
        ?>
    </div>
    <div><?php echo $eventDesc; ?></div>

</body>
</html>