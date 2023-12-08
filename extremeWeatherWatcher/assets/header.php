<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
        <!-- echo time() to resolve caching issue -->
    </head>
    <body>
        <div class="nav-bar">
            <a href="index.php"><div class="nav-button">Extreme Weather Watcher</div></a>

            <a href="allevents.php"><div class="nav-button">Show All Events</div></a> 

            <a href="index.php"><div class="nav-button">Events by Continent</div></a>

            <?php
                if(isset($_SESSION['userEmail'])) {
                    echo "<a href=\"watchlist.php\"><div class=\"nav-button\">Show my watchlist</div></a>";
                }
                
            ?>

            <?php
                if (isset($_SESSION['userEmail'])) {
                    echo "<a href=\"assets/logout.php\"><div class=\"nav-button\">Log out</div></a>";
                }
                else {
                    echo "<a href=\"login.php\"><div class=\"nav-button\">Log in</div></a>";
                }

            ?>

        </div>
    </body>
</html>