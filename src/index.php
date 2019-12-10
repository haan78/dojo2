<?php 

if ( file_exists( "/var/data/dojo" ) ) {
    define("SETTING_DATA_PATH","/var/data/dojo"); //Product Machine
} else {
    define("SETTING_DATA_PATH",__DIR__."/../data"); //Developer Machine
}
if ( defined("SETTING_DATA_PATH") && file_exists(SETTING_DATA_PATH."/settings.php") ) {
    require_once SETTING_DATA_PATH."/settings.php";
    require_once __DIR__ . "/back-end/router.php";
    new router( (isset($_GET["a"]) ? trim($_GET["a"]) : "application" ) );
} else {
    die("Configuration Error!");
}