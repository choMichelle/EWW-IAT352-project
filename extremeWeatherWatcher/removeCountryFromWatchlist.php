<?php
require_once("assets/initializer.php");
if(isset($_POST['removedCountryName'])) {

  $db = $_SESSION['db'];
  $query= "DELETE FROM watchlist WHERE country = ? AND userEmail = ?" ;
  $stmt = mysqli_prepare($db, $query);

  

  if ($stmt) {

    mysqli_stmt_bind_param($stmt, "ss",  $_POST['removedCountryName'], $_SESSION['userEmail']);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
} else {
}

} else {
  header('HTTP/1.1 500 Internal Server Error');
}

?>