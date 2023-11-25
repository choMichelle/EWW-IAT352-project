<?php
//force page to use HTTPS
function require_SSL() {
    if ($_SERVER["HTTPS"] != "on") {
        header("Location: https://" .$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"]);
        exit();
    }
}

//force page to use HTTP
function SSLtoHTTP() {
    if (isset($_SERVER["HTTPS"])) {
        header("Location: http://" .$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"]);
        exit();
    }
}
?>