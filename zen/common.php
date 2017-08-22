<?php
session_start();
//header("Set-Cookie: PHPSESSID=" . session_id() . "; path=/");
//include_once "../config/openx_config.php";
include_once "../locale/locale.inc.php";
include_once "../config/config_zen.inc.php";
include_once $ENGINE_PATH."View/BasicView.php";
include_once $ENGINE_PATH."Database/SQLData.php";
include_once $ENGINE_PATH."Utility/RequestManager.php";
include_once $ENGINE_PATH."System/System.php";
include_once "../engines/functions.php";
$MAIN_TEMPLATE = "sample/default.html";
header('Content-Type: text/html; charset=utf-8'); 
$req = new RequestManager();
$system = new System();
if($system->isMaintenance()){
	sendRedirect("maintenance.php");
	die();
}
?>