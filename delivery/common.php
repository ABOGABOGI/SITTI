<?php
session_start();
include_once "../config/config.inc.php";
$CONFIG['HOST'] = $CONFIG['SITTI_DBHOST'];
$CONFIG['USERNAME'] = $CONFIG['SITTI_DBUSER'];
$CONFIG['PASSWORD'] = $CONFIG['SITTI_DBPASS'];
$CONFIG['DATABASE'] = $CONFIG['SITTI_DBNAME'];
include_once $ENGINE_PATH."View/BasicView.php";
include_once $ENGINE_PATH."Database/SQLData.php";
include_once $ENGINE_PATH."Utility/RequestManager.php";
include_once "../engines/functions.php";
$MAIN_TEMPLATE = "sample/default.html";

$req = new RequestManager();
?>