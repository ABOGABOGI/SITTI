<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";

include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBeranda.php";
include_once $APP_PATH."SITTI/SITTIProfile.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$account->close();

$saldo = $account->getSaldo($info['sittiID']);
$isFree = $account->isFreePromo($info['sittiID']);
	//Account Profile
    $view->assign("info",$info);
	$profile = new SITTIProfile(&$req,&$account);
	$view->assign("mainContent",$profile->showPage());
	
	//end dropdown
	//munculkan submenu khusus beranda
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	$view->assign("SALDO",$saldo['budget']);
	$view->assign("isFree",$isFree);
	$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
	print $view->toString("SITTI/tentang_kami.html");
?>