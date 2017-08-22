<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTICreateAd.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."StaticPage/StaticPage.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$app = new SITTICreateAd($req,$account);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$account->close();

if($isLogin){
	$saldo = $account->getSaldo($info['sittiID']);
	$view->assign("isBeranda","1");
    $view->assign("info",$info);
	$view->assign("isLogin",$isLogin);
	$view->assign("broadcast",$system->broadcast());
	$view->assign("SALDO",$saldo['budget']);
	$content = $app->run_cpcbanner();
	$view->assign("mainContent",$content);
	print $view->toString("SITTI/content.html");
}else{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"login.php");
}
?>