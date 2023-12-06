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
    
    if (isset($_POST[$varname]) && ($_POST[$varname] != "/")) {
        echo "value=$_POST[$varname]";
    }
    
    echo " />";
}

function addListItem($itemName, $itemID) {
    echo "<a href=\"eventdetail.php?eventID=$itemID\" class=\"list-anchor\"><div class=\"models-list-item\">$itemName</div></a>";
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



?>