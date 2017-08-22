<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";

include_once $APP_PATH."SITTIAPI/PPA_Sandbox.php";
$view = new BasicView();
$account = new SITTIAccount(&$req);
$api = new PPA_Sandbox($req, new SITTIAccount($req));
print $api->main($req);

?>