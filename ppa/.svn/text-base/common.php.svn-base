<?php
session_start();
$MULTI_LANGUAGE = true;
if($MULTI_LANGUAGE&&$_SESSION['lang']=="en"){
	include_once "../locale/locale_en.inc.php";
}else{
	include_once "../locale/locale.inc.php";
}
include_once "../engines/Gummy.php";
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