<?php
//create/update media table
function updateMediaTable($versionNum)
{
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
    } else {
        saveImages($db, $versionNum);
    }
}

//save images in folder to db, called in updateMediaTable
function saveImages($db, $versionNum)
{
    //prepare files to save
    $dir = new DirectoryIterator("images/");
    foreach ($dir as $fileinfo) { //loop through all files in images/ directory
        if (!$fileinfo->isDot()) { //exclude directory items, . items, .. items
            $file = $fileinfo->getFilename(); //get the file

            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $filePath = "images/$file";

            $query = "INSERT INTO media 
            VALUES ('null', '$fileName', '$filePath','image', $versionNum)";

            //run insert into the table
            mysqli_query($db, $query);
        }
    }
}

//force page to use HTTPS
function require_SSL()
{
    if ($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//force page to use HTTP
function SSLtoHTTP()
{
    if (isset($_SERVER["HTTPS"])) {
        header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//checks if there is input in the form field (general use)
function validateTextInput($inputName)
{
    if (isset($_POST[$inputName]) && !empty($_POST[$inputName])) {
        return true;
    }
}

//create form field
function makeTextEntry($type, $label, $text, $varname, $isPrefilled = false)
{
    echo "<label for=\"$label\">$text:</label>";
    echo "<input type=\"$type\" id=\"$varname\" name=\"$varname\"";
    if ($isPrefilled == false) {
        if (isset($_POST[$varname]) && ($_POST[$varname] != "/") && !empty($_POST[$varname])) {
            echo "value=$_POST[$varname]";
        }
    } else {
        global $$varname;
        echo "value=" . $$varname;
    }

    echo " />";
}


//check if a country is in the watchlist for a specific user
function isInWatchlist($country)
{
    if (!isset($_SESSION['db'])) {
        echo "can't fetch database";
    } else {
        $db = $_SESSION['db'];
        $query = "SELECT * FROM watchlist WHERE country=? AND userEmail =?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $country, $_SESSION['userEmail']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

//The name says it all, used in event detail, disable button if country is already in the watchlist
function makeWatchlistButton($eventCountry)
{
    if (isset($_SESSION['userEmail']) && !isInWatchlist($eventCountry)) {
        echo "<form class = \"watchlistForm\" action=\"watchlist.php\" method=\"post\">";
        echo "<input class=\"hidden\" type=\"hidden\" name=\"newWatchListCountryName\" value=\"$eventCountry\">";
        echo "<input type=\"submit\" class=\"button\" name=\"Add country to Watchlist\" value=\"Add country to Watchlist\">";
        echo "</form>";
    } else if (isset($_SESSION['userEmail'])) {
        echo "<input type=\"submit\" class=\"deactivated-button\" name=\"Add country to Watchlist\" value=\"Country is already in watchlist\">";
    } else {
        echo "<a class =\"deactivated-button\"> Log in to add this event's country to  watchlist.";
    }
}

//add country to db (watchlist)
function addItemToWatchList($country)
{
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

//creates country dropdown list for filtering
function makeCountryDropdown($label, $htmlID, $varname, $isPrefilled = false)
{
    $db = $_SESSION['db'];
    $query_all_countries = "SELECT DISTINCT location.country FROM location";
    $all_countries_result = mysqli_query($db, $query_all_countries);
    if (!$all_countries_result) {
        die("query failed");
    }
    echo "<label class = \"filterCountry\"for=\"$htmlID\">$label:</label>";
    echo "<select id=\"$htmlID\" name=\"$varname\">";
    echo "<option value=\"\"></option>";
    if (mysqli_num_rows($all_countries_result) != 0 && $isPrefilled == false) {
        while ($row = mysqli_fetch_assoc($all_countries_result)) {
            $selected = ((isset($_POST[$varname])) && ($_POST[$varname] == $row['country'])) ? 'selected' : '';
            echo "<option value='{$row['country']}' $selected>{$row['country']}</option>";
        }
    } else {
        global $$varname;
        while ($row = mysqli_fetch_assoc($all_countries_result)) {
            $selected = ((isset($$varname)) && ($$varname == $row['country'])) ? 'selected' : '';
            echo "<option value='{$row['country']}' $selected>{$row['country']}</option>";
        }
    }
    echo "</select>";
    mysqli_free_result($all_countries_result);
}

//for header's "Event by continent"
function generateDropdownItem()
{
    $db = $_SESSION['db'];
    $query_all_cont = "SELECT DISTINCT location.continent FROM location";
    $all_cont_result = mysqli_query($db, $query_all_cont);
    if (!$all_cont_result) {
        die("query failed");
    }

    if (mysqli_num_rows($all_cont_result) != 0) {
        while ($row = mysqli_fetch_assoc($all_cont_result)) {
            $currCont = $row['continent'];
            echo "<a href=\"continentevents.php?continent=$currCont\"><div>$currCont</div></a>";
        }
    }

    mysqli_free_result($all_cont_result);
}

//takes db variable, event's locationID
//returns an associative array with location. keys are: continent, country, state
function getEventLocation($db, $eventLocationID)
{
    //query to get location details
    $query_location = "SELECT continent, country, stateOrProvince FROM `location` WHERE locationID=?";
    $stmt_location = mysqli_prepare($db, $query_location);

    if (!$stmt_location) {
        die("Error:" . mysqli_error($db));
    } else {
        mysqli_stmt_bind_param($stmt_location, "i", $eventLocationID);
        mysqli_stmt_execute($stmt_location);

        $result = mysqli_stmt_get_result($stmt_location);

        if (mysqli_num_rows($result) != 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $eventContinent = $row['continent'];
                $eventCountry = $row['country'];
                $eventState = $row['stateOrProvince'];
            }
            $eventLocation = array("continent" => $eventContinent, "country" => $eventCountry, "state" => $eventState);
        }
        mysqli_free_result($result);
        return $eventLocation;
    }
}

//Display watchlist entries on the watchlist page with the remove option
function showWatchlistWithRemoveButton()
{
    $db = $_SESSION['db'];
    $query_all_countries = "SELECT DISTINCT country FROM watchlist WHERE userEmail = ?";
    $stmt = mysqli_prepare($db, $query_all_countries);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['userEmail']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        echo "<table class=\"watchlist-container\">";
        echo "<thead><tr><th>Country</th></tr></thead>";
        echo "<tbody>";

        while ($country = mysqli_fetch_assoc($result)) {
            echo "<tr id=\"" . str_replace(' ', '_', $country['country']) . "\" data-country=\"" . str_replace(' ', '_', $country['country']) . "\">";
            echo "<td>" . htmlspecialchars($country['country']) . "</td>";
            echo "<td><a class=\"removeButton\">Remove</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Unable to prepare statement";
    }
}
//Get user name
function showUsername()
{
    $db = $_SESSION['db'];
    if (isset($_SESSION['userEmail'])) {
        $query = "SELECT username FROM users WHERE userEmail = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['userEmail']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $username = mysqli_fetch_assoc($result);
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
        return $username['username'];
    }
}

//retrieve the user's home country
function getUserCountry()
{
    $db = $_SESSION['db'];
    if (isset($_SESSION['userEmail'])) {
        $query = "SELECT country FROM users WHERE userEmail = ?";
        $stmt = mysqli_prepare($db, $query);
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

//creates a brief preview of a weather event
function generateEventPreview($queryResult, $count)
{
    echo "<table>";
    for ($i = 0; $i < $count && $row = mysqli_fetch_assoc($queryResult); $i++) {

        if ($i % 2 == 0) {
            echo "<tr class = \"event-container-row\">";
        }
        //Messy table layout, but hey it works
        // Display title, quick data, location, etc.
        echo "<td class = \"event-container\">";
        echo "<table class=\"event-header\">";
        echo "<tr>";

        echo "<a href=\"eventdetail.php?eventID=" . $row['eventID'] . "\" class=\"event-title\">" . $row['title'] . "</a>";
        echo "</tr>";

        echo "<tr class =\"type-row\">";

        echo "<td>";
        echo "<div>" . $row['severity'] . "</div>";
        echo "<div>" . $row['type'] . "</div>";
        echo "</td>";

        echo "<td>";
        echo "<div>" . $row['stateOrProvince'] . ", " . $row['country'] . "</div>";
        echo "<div>" . $row['date'] . "</div>";
        echo "</td>";

        echo "</tr>";
        echo "</table class = \"event-content\">";

        echo "<table>";

        echo "<tr class = \"content-row\">";
        // Display a preview of the description (e.g., first 100 characters)
        echo "<td>";
        echo "<div class=image-container>";
        if (!empty($row['mediaURL'])) {
            echo "<img src=\"" . $row['mediaURL'] . "\" />";
        } else {
            echo "<div class=monospace-text>No image</div>";
        }


        echo "</div>";
        echo "</td>";
        $descriptionPreview = substr($row['description'], 0, 250);
        echo "<td>" . $descriptionPreview . "... " . "<br><br><a class = \"button\" href=\"eventdetail.php?eventID=" . $row['eventID'] . "\">Read more</a>" . "</td>";

        echo "</tr>";

        echo "</table>";
    }

    echo "</table>";
}

//show specified number of weather events for a specific country
function showEventBasedOnCountries($country, $count, $limit, $start_from = 0)
{
    $db = $_SESSION['db'];
    //If $cuntry is empty, get $limit entries from any country. Else, get entries from specified country
    if (empty($country)) {
        $query = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        ORDER BY weatherevents.date DESC LIMIT $start_from,$limit";
    } else {
        $query = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        WHERE location.country = ?
        ORDER BY weatherevents.date DESC";
    }
    $stmt = mysqli_prepare($db, $query);


    if ($stmt) {
        if (!empty($country)) {
            mysqli_stmt_bind_param($stmt, "s", $country);
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
    } else {
        generateEventPreview($result, $count);
    }
}

//show specified number of weather events for a specific continent, ordered from newest to oldest
//limit and start_from variables used for pagination
function showEventBasedOnContinent($continent, $count, $limit, $start_from = 0)
{
    $db = $_SESSION['db'];
    $query = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        WHERE location.continent = ?
        ORDER BY weatherevents.date DESC LIMIT $start_from,$limit";
    $stmt = mysqli_prepare($db, $query);

    if ($stmt) {
        if (!empty($continent)) {
            mysqli_stmt_bind_param($stmt, "s", $continent);
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
    } else {
        generateEventPreview($result, $count);
    }
}

//show specified number of weather events (from all) from newest to oldest
function showEventByNewestDate($count)
{
    $db = $_SESSION['db'];
    $query_date = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        ORDER BY weatherevents.date DESC";
    $stmt = mysqli_prepare($db, $query_date);

    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (mysqli_num_rows($result) > 0) {
        generateEventPreview($result, $count);
    }
}

//show specified number of weather events for a specific continent, ordered from newest to oldest
function showEventBasedOnWatchlist($count)
{
    $db = $_SESSION['db'];
    $userEmail = $_SESSION['userEmail'];

    $query = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        LEFT JOIN `watchlist` ON location.country = watchlist.country
        WHERE watchlist.userEmail = ?
        ORDER BY weatherevents.date DESC";
    $stmt = mysqli_prepare($db, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (mysqli_num_rows($result) <= 0) {
        echo "<table>";
        echo "<tr><td>Nothing to show here</td></tr>";
        mysqli_free_result($result);
        echo "</table>";
        return;
    } else {
        generateEventPreview($result, $count);
    }
}

//get details of 1 event, for event details page
function getSpecificEventDetails($eventID)
{
    $db = $_SESSION['db'];

    $query = "SELECT weatherevents.*, location.*, media.* 
        FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID 
        LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID 
        LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID
        WHERE weatherevents.eventID = ?
        ORDER BY weatherevents.date DESC";
    $stmt = mysqli_prepare($db, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $eventID);

        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
}
