<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
        <!-- echo time() to resolve caching issue -->
    </head>
    <body>
        <div class="nav-bar">
        <a id="logo" href="index.php">
        <div class="nav-button">
            <span style="font-size: 3rem;">E</span>xtreme <span style="font-size: 3rem;">W</span>eather <span style="font-size: 3rem;">W</span>atcher
            <br>
            <div class="welcome">
            <?php if (isset($_SESSION['userEmail'])) { echo "Welcome back,  <span class=\"username\">". showUsername(). "</span>"; }?>
            </div>
        </div>
        </a>


            <a href="allevents.php"><div class="nav-button">Show All Events</div></a> 

            <div class="nav-button" id="dropdown-continents">
                <div class="dropbtn">Events by Continent</div>
                <div class="dropdown-content">
                    <?php generateDropdownItem() ?>
                </div>
            </div>

            <?php
                if(isset($_SESSION['userEmail'])) {
                    echo "<a href=\"watchlist.php\"><div class=\"nav-button\">Show my Watchlist</div></a>";

                    echo "<a href=\"userprofile.php\"><div class=\"nav-button\">Edit Profile</div></a>";
                }
                
            ?>

            <?php
                if (isset($_SESSION['userEmail'])) {
    
                    echo "<a href=\"assets/logout.php\"><div class=\"nav-button\">Log out</div></a>";
                }
                else {
                    echo "<a href=\"login.php\"><div class=\"nav-button\">Log in/Register</div></a>";
                }

            ?>

        </div>
    </body>
</html>