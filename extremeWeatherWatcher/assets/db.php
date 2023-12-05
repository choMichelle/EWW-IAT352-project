<?php
//TODO - consider creating a user account instead of using root (if needed)
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "eww_projectdb";

//connect to the db
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    echo "not connected" . mysqli_connect_error();
    exit;
}
$_SESSION['db'] = $db;

?>