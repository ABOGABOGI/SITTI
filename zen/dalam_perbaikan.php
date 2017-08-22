
<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBeranda.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin(1);
$isFirstTimer = $account->isFirstTimer(1);
$info = $account->getProfile();
$account->close();

	print $view->toString("SITTIZEN/karir.html");

?>