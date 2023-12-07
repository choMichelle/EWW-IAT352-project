<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();
?>

<?php
    if (isset($_SESSION['userEmail'])) {    
        $_POST['newWatchListCountryName'] = "Canada";
        //save user data into the db
        if(isset($_POST['newWatchListCountryName'])){
            addItemToWatchList($_POST['newWatchListCountryName']);
            unset($_POST['newWatchListCountryName']);
        }
    }
    else{
        $_SESSION['callback_url'] = 'watchlist.php';
        header("Location: login.php");
    }

    showWatchlistWithRemoveButton();



?>

<html lang="en">
    <head>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
        

    </body>
    <script src = "js/removeWatchlistItem.js" defer></script>    
</html>