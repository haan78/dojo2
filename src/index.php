<?php 

/* Project Settings */
if ( file_exists( "/var/data/dojo" ) ) {
    define("SETTING_DATA_PATH","/var/data/dojo"); //Product Machine
} else {
    define("SETTING_DATA_PATH",__DIR__."/../data"); //Developer Machine
}
define("SETTING_LOGFILE",SETTING_DATA_PATH . "/dojo.log");
define("SETTING_DBFILE",SETTING_DATA_PATH."/dojo.db");
define("SETTING_TITLE","Ankara Kendo Kulübü Yönetim Paneli");
define("SETTING_PAGE_LOADING","Yükleniyor Lütfen Bekleyin...");
define("SETTING_ID","WEBPACK - ANKARA_KENDO");

require_once __DIR__ . "/back-end/router.php";
new router( (isset($_GET["a"]) ? trim($_GET["a"]) : "application" ) );