<?php
//TODO - replace values with our db
// $dbhost = "localhost";
// $dbuser = "root";
// $dbpass = "";
// $dbname = "classicmodels";

//connect to the db
@$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    echo "not connected" . mysqli_connect_error();
    exit;
}
$_SESSION['db'] = $db;

?>