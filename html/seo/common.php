<?php
$CONFIG['DATABASE'][0]['HOST'] = "192.168.0.12";
$CONFIG['DATABASE'][0]['USERNAME'] = "juragansitti";
$CONFIG['DATABASE'][0]['PASSWORD'] = "cotaxEdonatagosE";
$CONFIG['DATABASE'][0]['DATABASE'] = "zz_sitti";

$GLOBAL_PATH = "../../";
$ENGINE_PATH = "../../engines/";
$APP_PATH = "../../com/";
include_once $ENGINE_PATH."Database/SQLData.php";
include_once $ENGINE_PATH."View/BasicView.php";
include_once $ENGINE_PATH."Utility/RequestManager.php";
$MAIN_TEMPLATE = "SITTI/seo/index.html";
$req = new RequestManager();
?>