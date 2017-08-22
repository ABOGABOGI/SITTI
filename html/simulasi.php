<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTISimulation.php";

$view = new BasicView();
$page = new SITTISimulation(&$req);
print $page->getKeywordData($req->getParam("k"));
?>