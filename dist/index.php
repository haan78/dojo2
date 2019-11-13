<?php require_once __DIR__ . "/back-end/router.php";
new router( (isset($_GET["a"]) ? trim($_GET["a"]) : "application" ) );