<?php
//used to remove watchlist records (when remove from watchlist button is clicked)
//used in watchlist.php

require_once("assets/initializer.php");
if (isset($_POST['removedCountryName'])) {

  $db = $_SESSION['db'];
  $query = "DELETE FROM watchlist WHERE country = ? AND userEmail = ?";
  $stmt = mysqli_prepare($db, $query);

  //replace _ with " " because the db countries use space but attributes can't have spaces
  $fixed_str = str_replace('_', ' ', $_POST['removedCountryName']);

  if ($stmt) {
    //remove from watchlist
    mysqli_stmt_bind_param($stmt, "ss", $fixed_str, $_SESSION['userEmail']);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
  }
} else {
  header('HTTP/1.1 500 Internal Server Error');
}
