<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");
$invalidID = true;

if(isset($_GET["eventID"])) $eventID = $_GET["eventID"];

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

$query_event = "SELECT * FROM weatherevents WHERE eventID = ?";
$stmt_event = mysqli_prepare($db, $query_event);

if(!$stmt_event){
    die("Error:" .mysqli_error($db));
}

if ($invalidID) {
    echo "Product not found.";
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
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Detail</title>
</head>
<body>
    <div class = "detail-container">
    <div><?php echo "<h1>$eventTitle</h1>"; ?></div>
            <div class="detail-multi-horizontal">
                <div><?php echo "Event type: $eventType"; ?></div>
                <div><?php echo "Event severity: $eventSeverity"; ?></div>
                <div><?php echo "Event date: $eventDate"; ?></div>
            </div>
            <!-- Need to fix locationID -->
            <div><?php echo "Event location: $eventLocationID"?></div>
            <div>
                    <?php
                    if ((!isset($_SESSION['userEmail']))
                    || ((isset($_SESSION['userEmail'])) && !isInWatchlist($eventLocationID))){
                        echo "<form action=\"addcountrytowatchlist.php\" method=\"post\">";
                        echo "<input type = \"hidden\" name=\"newWatchListCountryName\" value=\"$eventLocationID\">";
                        echo "<input type=\"submit\" name=\Add Location to Watchlist\ value=\"Add Location to Watchlist\">";
                        echo "</form>";
                    }
                    else{
                        echo "Item is already in your watchlist";
                    }
                    ?>
                </div>
            <div class="detail-description"><?php echo $eventDesc; ?></div>

            

            <div class="detail-multi-horizontal">
                
            </div>


    </div>
</body>
</html>