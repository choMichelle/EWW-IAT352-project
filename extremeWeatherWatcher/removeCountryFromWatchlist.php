<?php
require_once("assets/initializer.php");
if(isset($_POST['removedCountryName'])) {
  $db = $_SESSION['db'];
  echo "All good! " . $_POST['removedCountryName'];
  $query= "DELETE FROM watchlist WHERE country = ? AND userEmail = ?" ;
  $stmt = mysqli_prepare($db, $query);

  if ($stmt) {

    mysqli_stmt_bind_param($stmt, "ss",  $_POST['removedCountryName'], $_SESSION['userEmail']);
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Entry removed successfully.";
    } else {
        echo "No matching entry found.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error in preparing statement: " . mysqli_error($db);
}

} else {
  header('HTTP/1.1 500 Internal Server Error');
}

?>