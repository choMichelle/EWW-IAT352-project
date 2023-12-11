<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

updateMediaTable(3);

//get current page number
if (isset($_GET['page'])) {
    $page_num = $_GET['page'];
}
else {
    $page_num = 1;
}

$limit = 10; //max number of events to show on page
$start_from = ($page_num - 1) * $limit;

//get number of records 
$sql = "SELECT COUNT(*) FROM weatherevents";   
$rs_result = mysqli_query($db, $sql);   
$row = mysqli_fetch_row($rs_result);   
$total_records = $row[0];   
    
//find number of pages required to show all records
$total_pages = ceil($total_records / $limit);   
$page_link = "";                         

?>

<html lang="en">
    <head>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <title>EWW - All Weather Events</title>
    </head>
    <body>
        <div class="events-container generic-event-container">
            
            <h2> Extreme weather around the world</h2>
            <?php makeCountryDropdown("Country filter","filterCountry","filteredCountry");?>

            <div id="eventTable ">
                <?php
                showEventBasedOnCountries("", 10, $limit, $start_from); 
                ?>
            </div>
            
        </div>

        <ul class="page-numbers">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) { 
                if ($i == $page_num) { //page we are currently on
                    $page_link .= "<li class='active'><a href='allevents.php?page=".$i."'>".$i."</a></li>"; 
                }             
                else { 
                    $page_link .= "<li><a href='allevents.php?page=".$i."'>".$i."</a></li>";   
                } 
            }
            echo $page_link;
            ?>
        </ul>
        
    </body>
    <?php $db->close(); ?>
    <script src = "js/locationFilter.js" defer></script> 
</html>