<?php
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
		$stmt->bind_param('is',$country, $_SESSION['userEmail']);
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
    mysqli_stmt_bind_param($insert_stmt, "is", $country, $_SESSION['userEmail']);
    mysqli_stmt_execute($insert_stmt);
}

function makeCountryDropdown($htmlID){
    $db = $_SESSION['db'];
    $query_all_countries = "SELECT DISTINCT location.country FROM location";
    $all_countries_result = mysqli_query($db,$query_all_countries);
    if (!$all_countries_result) {
        die("query failed");
    }
    echo "<select id=\"$htmlID\" name=\"order_num\" id=\"order_num\">";
    echo "<option value=\"\"></option>";
    if(mysqli_num_rows($all_countries_result) != 0){
        while($row = mysqli_fetch_assoc($all_countries_result)){
            $selected = ((isset($_POST['filteredCountry'])) && ($_POST['filteredCountry'] == $row['country'])) ? 'selected' : '';
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


?>