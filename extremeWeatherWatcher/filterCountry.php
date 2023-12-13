<?php
require_once("assets/initializer.php");
if(isset($_POST['filterCountryName']) ) {
  if(isset($_POST['page']) && !empty($_POST['page'])){
    $page_num = $_POST['page'];
    $start_from = ($page_num - 1) * 10;
    showEventBasedOnCountries($_POST['filterCountryName'], 10, 10, $start_from);
  }
  else{
    showEventBasedOnCountries($_POST['filterCountryName'], 10, 10, $start_from=0);
  }
}
else{
  if(isset($_POST['page'])){
    $page_num = $_POST['page'];
    $start_from = ($page_num - 1) * $limit;
    showEventBasedOnCountries("", 10, 10, $start_from);
  }
  else{
    showEventBasedOnCountries("", 10, 10, $start_from=0);
  }
  
}
?>