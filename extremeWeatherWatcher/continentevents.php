<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

updateMediaTable(3);

$invalidContinent = true;

if (isset($_GET["continent"])) $eventCountry = $_GET["continent"];

//query to check if continent exists in db
$query_continents = "SELECT continent FROM `location`";
$cont_result = mysqli_query($db, $query_continents);
if (!$cont_result) {
    die("query failed");
}

if (mysqli_num_rows($cont_result) != 0) {
    while ($row = mysqli_fetch_assoc($cont_result)) {
        if (isset($_GET["continent"]) && $row['continent'] === $eventCountry) {
            $invalidContinent = false;
            $selectedCont = $row['continent'];
            break;
        }
    }
}

mysqli_free_result($cont_result);

?>

<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>EWW - Weather Events in <?php echo $selectedCont; ?></title>
</head>

<body>
    <div class="events-container generic-event-container">

        <h2> Extreme weather in <?php echo $selectedCont; ?></h2>

        <div id="eventTable ">
            <?php
            showEventBasedOnContinent($selectedCont, 10, 10);
            ?>
        </div>
    </div>

</body>
<?php $db->close(); ?>
<script src="js/locationFilter.js" defer></script>

</html>