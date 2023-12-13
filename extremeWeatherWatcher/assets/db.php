<?php
//db connection details
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "extremeweatherwatcher";

//connect to the db
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    echo "not connected" . mysqli_connect_error();
    exit;
}
$_SESSION['db'] = $db; //set the db for the session

?>