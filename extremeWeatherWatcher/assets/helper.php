<?php
//create/update media table
function updateMediaTable($versionNum) {
    $db = $_SESSION['db'];

    //query table version number
    $query_version = "SELECT tableVersion FROM media";
    $result = mysqli_query($db, $query_version);

    if (!$result) {
        die("unable to query media table version");
    }

    if (mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_assoc($result);
        $currVersionNum = $row['tableVersion'];

        if ($versionNum > $currVersionNum || $currVersionNum = null) {
            //delete all currently saved images from db table
            $query_delete = "DELETE FROM media where tableVersion=$currVersionNum";
            mysqli_query($db, $query_delete);

            saveImages($db, $versionNum);
        }
        mysqli_free_result($result);
    }
    else {
        saveImages($db, $versionNum);
    }
    
}

//save images in folder to db, called in updateMediaTable
function saveImages($db, $versionNum) {
    //prepare files to save
    $dir = new DirectoryIterator("images/");
    foreach ($dir as $fileinfo) { //loop through all files in images/ directory
        if (!$fileinfo->isDot()) { //exclude directory items, . items, .. items
            $file = $fileinfo->getFilename(); //get the file

            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $filePath = "images/$file";

            // echo '<img src="images/'.$file.'">'; //display image
            echo '<img src="'.$filePath.'">'; //display image

            $query = "INSERT INTO media VALUES ('null', '$fileName', '$filePath','image', $versionNum)";

            //run insert into the table
            mysqli_query($db, $query);
        }
    }
}

//force page to use HTTPS
function require_SSL() {
    if ($_SERVER["HTTPS"] != "on") {
        header("Location: https://" .$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//force page to use HTTP
function SSLtoHTTP() {
    if (isset($_SERVER["HTTPS"])) {
        header("Location: http://" .$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//checks if there is input in the form field (general use)
function validateTextInput($inputName) {
    if (isset($_POST[$inputName]) && !empty($_POST[$inputName])) {
        return true;
    }
}

//create form field
function makeTextEntry($type, $label, $text, $varname) {
    echo "<label for=\"$label\">$text:</label>";
    echo "<input type=\"$type\" id=\"$varname\" name=\"$varname\"";
    
    if (isset($_POST[$varname]) && ($_POST[$varname] != "/") && !empty($_POST[$varname])) {
        echo "value=$_POST[$varname]";
    }
    
    echo " />";
}

function addListItem($itemName, $itemID, $itemLocation) {
    echo "<a href=\"eventdetail.php?eventID=$itemID\" class=\"list-anchor\"><div class=\"models-list-item\">"  . $itemLocation['continent'] . " - " . $itemLocation['country']  . ": " .  "$itemName</div></a>";
}

function isInWatchlist($country){
    if(!isset($_SESSION['db'])){
        echo "can't fetch database";
    }
    else{
        $db = $_SESSION['db'];
        $query = "SELECT * FROM watchlist WHERE country=? AND userEmail =?";
        $stmt = $db->prepare($query);
		$stmt->bind_param('ss',$country, $_SESSION['userEmail']);
        $stmt->execute();
        $stmt->store_result();

        if($stmt -> num_rows > 0) {
            return true;
        }
        else {
            return false;
        }
    }
}

function addItemToWatchList($country){
    $db = $_SESSION['db'];
    //GPT taught me INSERT IGNORE INTO
    $insert_query = "INSERT IGNORE INTO watchlist (country, userEmail) VALUES (?,?)";
    $insert_stmt = mysqli_prepare($db, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "ss", $country, $_SESSION['userEmail']);
    if (mysqli_stmt_execute($insert_stmt)) {
        // Item added successfully
    } else {
        // Handle the error
        echo "Error: " . mysqli_stmt_error($insert_stmt);
    }
    
}

function makeCountryDropdown($label,$htmlID,$varname){
    $db = $_SESSION['db'];
    $query_all_countries = "SELECT DISTINCT location.country FROM location";
    $all_countries_result = mysqli_query($db,$query_all_countries);
    if (!$all_countries_result) {
        die("query failed");
    }
    echo "<label for=\"$htmlID\">$label:</label>";
    echo "<select id=\"$htmlID\" name=\"$varname\">";
    echo "<option value=\"\"></option>";
    if(mysqli_num_rows($all_countries_result) != 0){
        while($row = mysqli_fetch_assoc($all_countries_result)){
            $selected = ((isset($_POST[$varname])) && ($_POST[$varname] == $row['country'])) ? 'selected' : '';
            echo "<option value='{$row['country']}' $selected>{$row['country']}</option>";
        }
    }
    echo "</select>";
    mysqli_free_result($all_countries_result);
}




// function showUserWatchlist($userEmail){
//     $db = $_SESSION['db'];
//     $query = "SELECT * FROM watchlist WHERE userEmail =?";
//     $stmt = $db->prepare($query);
//     $stmt->bind_param('s', $userEmail);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $bro = $stmt->num_rows;
//     if ($result->num_rows != 0) {
//         while ($row = $result->fetch_assoc()) {
//             addListItem($row['productName']);
//         }
//     } else {
//         echo "Watchlist is empty";
//     }
// }

//takes db variable, event's locationID
//returns an associative array with location. keys are: continent, country, state
function getEventLocation($db, $eventLocationID) {
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
            $eventLocation = array("continent"=>$eventContinent, "country"=>$eventCountry, "state"=>$eventState);
        }
        mysqli_free_result($result);
        return $eventLocation;
    }
}

function showWatchlistWithRemoveButton(){
    $db = $_SESSION['db'];
    $query_all_countries = "SELECT DISTINCT country FROM watchlist WHERE userEmail = ?";
    $stmt = mysqli_prepare($db, $query_all_countries);
   
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['userEmail']);
        mysqli_stmt_execute($stmt);    
        $result = mysqli_stmt_get_result($stmt);
            while ($country = mysqli_fetch_assoc($result)) {
                echo "<li id=\"". str_replace(' ', '_', $country['country']) . "\" data-country=\"" . str_replace(' ', '_', $country['country']) . "\">";
                echo htmlspecialchars($country['country']);
                echo "<a class=\"removeButton\">Remove</a>";
                echo "</li>";
            }
            mysqli_stmt_free_result($stmt);
            mysqli_stmt_close($stmt);   
        }
    else {
        echo "Error: Unable to prepare statement";
    }
}

function showUsername(){
    $db = $_SESSION['db'];
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
}

function getUserCountry(){
    $db = $_SESSION['db'];
    if(isset($_SESSION['userEmail'])){
        $query = "SELECT country FROM users WHERE userEmail = ?";
        $stmt = mysqli_prepare($db,$query);
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['userEmail']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $username = mysqli_fetch_assoc($result);
        $country = $username['country'];
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        return $country;
    }
}

function generateEventPreview($queryResult, $count) {
    echo "<table>";
    for ($i = 0; $i < $count && $row = mysqli_fetch_assoc($queryResult); $i++) {
        
        if($i % 3 == 0){
            echo "<tr class = \"event-container-row\">";
        }
    
        // Display title, quick data, location, etc.
        echo "<td class = \"event-container\">";
        echo "<table class=\"event-header\">";
        echo "<tr>";
        echo "<td class=\"event-title\">" . $row['title'] . "</td>";
        echo "<td><a href=\"eventdetail.php?eventID=". $row['eventID'] ."\" class=\"event-title\">" . "Show more" . "</a></td>";
        echo "</tr>";
    
        echo "<tr>";
    
        echo "<td>";
        echo "<div>" . $row['severity'] . "</div>";
        echo "<div>" . $row['type'] . "</div>";
        echo "</td>";
    
        echo "<td>";
        echo "<div>" . $row['stateOrProvince'] . ", " . $row['country'] ."</div>";
        echo "<div>" . $row['date'] . "</div>";
        echo "</td>";
    
        echo "</tr>";
        echo "</table class = \"event-content\">";
    
        echo "<table>";
        echo "<tr>";
        echo "<td>";
        echo "<div>imageplaceholder</div>";
        echo "</td>";
        echo "</tr>";
    
        echo "<tr>";
        // Display a preview of the description (e.g., first 100 characters)
        $descriptionPreview = substr($row['description'], 0, 100);
        echo "<td>" . $descriptionPreview . "..." . "</td>";
        echo "</tr>";
    
        echo "</table>";
        echo "</td>";
    
    
    }
    
    echo "</table>";
}

function showEventBasedOnCountries($country, $count){
    $db = $_SESSION['db'];
    if(empty($country)){
        $query = "SELECT * FROM weatherevents 
          INNER JOIN `location` ON weatherevents.locationID = location.locationID";
    }
    else{
    $query = "SELECT * FROM weatherevents 
          INNER JOIN `location` ON weatherevents.locationID = location.locationID  
          WHERE location.country = ?";
    }
    $stmt = mysqli_prepare($db,$query);
    

    if($stmt){
        if(!empty($country)){
            mysqli_stmt_bind_param($stmt,"s",$country);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (mysqli_num_rows($result) <= 0) {
        echo "<table>";
        echo "<tr><td>Nothing to show here</td></tr>";
        mysqli_free_result($result);
        echo "</table>";
        return;
    }
    else {
        generateEventPreview($result, $count);
    }

}

function showEventByNewestDate($count) {
    $db = $_SESSION['db'];
    $query_date = "SELECT * FROM weatherevents
        INNER JOIN `location` ON weatherevents.locationID = location.locationID
        ORDER BY `date` DESC";
    $stmt = mysqli_prepare($db, $query_date);

    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (mysqli_num_rows($result) > 0) {
        generateEventPreview($result, $count);
    }
}

?>