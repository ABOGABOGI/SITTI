<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";


$view = new BasicView();
$account = new SITTIAccount(&$req);
$api = new SITTIAPI($req, new SITTIAccount($req));
print $api->execute($req);

?>