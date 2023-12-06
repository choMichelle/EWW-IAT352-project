<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
        <!-- echo time() to resolve caching issue -->
    </head>
    <body>
        <div class="nav-bar">
            <a href="index.php"><div class="nav-button">Show All Events</div></a>
            <?php
                if(isset($_SESSION['userEmail'])) {
                    echo "<a href=\"watchlist.php\"><div class=\"nav-button\">Show my watchlist</div></a>";
                }
                
            ?>
            
            
            <a <?php 
                if (isset($_SESSION['userEmail'])) {
                    echo "href=\"assets/logout.php\"";
                }
                else {
                    echo "href=\"login.php\"";
                } 
            ?>><div class="nav-button">
                <?php
                    if (isset($_SESSION['userEmail'])) {
                        echo "Log out";
                    }
                    else {
                        echo "Log in";
                    }
                ?>
            </div></a>
        </div>
    </body>
</html>