<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
        <!-- echo time() to resolve caching issue -->
    </head>
    <body>
        <div class="nav-bar">
            <a href=""><div class="nav-button">Link 1</div></a>
            <a href=""><div class="nav-button">Link 2</div></a>
            <a <?php 
                if (isset($_SESSION['email'])) {
                    echo "href=\"logout.php\"";
                }
                else {
                    echo "href=\"login.php\"";
                } 
            ?>><div class="nav-button">
                <?php
                    if (isset($_SESSION['email'])) {
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