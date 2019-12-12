<?php 

define("SETTING_DATA_PATH","/var/data/dojo");
if ( file_exists(SETTING_DATA_PATH."/settings.php") ) {
    require_once SETTING_DATA_PATH."/settings.php";
    require_once __DIR__ . "/back-end/router.php";
    new router( (isset($_GET["a"]) ? trim($_GET["a"]) : "application" ) );
} else {
    die("Configuration Error!");
}