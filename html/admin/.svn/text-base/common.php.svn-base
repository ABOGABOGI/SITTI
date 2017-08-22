<?php
session_start();
include_once "../../config/config.inc.php";
include_once "../../engines/functions.php";
/** PATH HACK for Admin page **/
$GLOBAL_PATH = "../".$GLOBAL_PATH;
$APP_PATH = "../".$APP_PATH;
$ENGINE_PATH = "../".$ENGINE_PATH;
$WEBROOT = "../".$WEBROOT;
/*******************************/
include_once $ENGINE_PATH."View/BasicView.php";
include_once $ENGINE_PATH."Database/SQLData.php";
include_once $ENGINE_PATH."Utility/RequestManager.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $ENGINE_PATH."Admin/Admin.php";
$MAIN_TEMPLATE = "common/admin/default.html";
$req = new RequestManager();
?>