<?php
session_start();
//header("Set-Cookie: PHPSESSID=" . session_id() . "; path=/");
include_once "../engines/Gummy.php";
include_once "../engines/functions.php";
include_once "../config/api_config.inc.php";
include_once "../com/SITTIAPI/SITTIAPI.php";
include_once "../com/SITTIAPI/PPA_API.php";
$MAIN_TEMPLATE = "sample/default.html";
$req = new RequestManager();

?>