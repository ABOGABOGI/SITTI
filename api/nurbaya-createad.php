<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";

$api = new NurbayaAPI($req, new SITTIAccount($req));
print $api->CreateAd();
?>