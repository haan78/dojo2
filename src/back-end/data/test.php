<?php
require_once __DIR__ . "/../lib/SQLite3Ex.php";

$link = new SQLite3("dojo.db");
$sql = "SELECT y.tarih,COUNT(1) AS sayi FROM yoklama y WHERE y.tarih BETWEEN COALESCE(:ts,y.tarih) AND COALESCE(:te,y.tarih) GROUP BY y.tarih ORDER BY y.tarih ASC";

$paging = new SQLite3Paging($link,$sql,0,5);
$ts = "2019-01-01";
$te = "2019-05-01";
$paging->bindParam("ts",$ts,SQLITE3_TEXT);
$paging->bindParam("te",$te,SQLITE3_TEXT);

$arr = $paging->result($rc);


print_r($arr);
echo "Sayi $rc";
