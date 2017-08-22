<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";


$view = new BasicView();
$account = new SITTIAccount(&$req);
$dashboard = new SITTIBilling($req,$account);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($isLogin){
	print "billing nih";
}else{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"login.php");
}
?>