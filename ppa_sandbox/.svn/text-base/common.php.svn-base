<?php
session_start();
include_once "../config/config.ppa_sandbox.php";

$MULTI_LANGUAGE = true;
if($MULTI_LANGUAGE&&$_SESSION['lang']=="en"){
	include_once "../locale/locale_en.inc.php";
}else{
	include_once "../locale/locale.inc.php";
}

include_once $ENGINE_PATH."View/BasicView.php";
include_once $ENGINE_PATH."Database/SQLData.php";
include_once $ENGINE_PATH."Utility/RequestManager.php";
include_once $ENGINE_PATH."System/System.php";
//include_once "../engines/Gummy.php";
include_once "../engines/functions.php";
$MAIN_TEMPLATE = "sample/default.html";
header('Content-Type: text/html; charset=utf-8'); 
$req = new RequestManager();
$system = new System();
if($system->isMaintenance()){
	sendRedirect("maintenance.php");
	die();
}
//$HOME = "SITTI/index.html";
//$CONTENT = "SITTI/content.html";
?>