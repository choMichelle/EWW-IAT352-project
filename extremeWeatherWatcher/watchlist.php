<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();
?>

<?php
    if (isset($_SESSION['userEmail'])) {    

        //save user data into the db
        if(isset($_POST['newWatchListProdName'])){
            addItemToWatchList($_POST['newWatchListProdName']);
            unset($_POST['newWatchListProdName']);
        }
    }
    else{
        $_SESSION['callback_url'] = 'watchlist.php';
        header("Location: login.php");
    }



?>

<html lang="en">
    <head>

    </head>
    <body>
        

    </body>
</html>