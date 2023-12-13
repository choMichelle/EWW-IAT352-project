<?php
//used to filter events by country, used in all events.php

require_once("assets/initializer.php");
//If country name filter is used, show just those countries
if (isset($_POST['filterCountryName'])) {
  if (isset($_POST['page']) && !empty($_POST['page'])) {
    $page_num = $_POST['page'];
    $start_from = ($page_num - 1) * 10;
    //If filter is used on a page > 2, show filtered countries starting from page 2
    showEventBasedOnCountries($_POST['filterCountryName'], 10, 10, $start_from);
  } else {
    showEventBasedOnCountries($_POST['filterCountryName'], 10, 10, $start_from = 0);
  }
} else {
  //same thing here, just without contries filtering. Use to show when no filter option or when the web finishes loading
  if (isset($_POST['page'])) {
    $page_num = $_POST['page'];
    $start_from = ($page_num - 1) * $limit;
    showEventBasedOnCountries("", 10, 10, $start_from);
  } else {
    showEventBasedOnCountries("", 10, 10, $start_from = 0);
  }
}
