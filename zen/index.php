<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
$page = new SITTIHomepage($req);

$view = new BasicView();
$account = new SITTIAccount(&$req);
$isContent = false;
$account->open(0);
$isLogin = $account->isLogin(1);
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($req->getPost("login")=="1"){
	$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
	$isContent = true;
	$view->assign("mainContent",$page->PublisherLogin($account));
}else if($req->getParam("logout")=="1"){
	session_destroy();
}

if($isContent){
	
	
	print $view->toString("SITTIZEN/content.html");
}else{
	
	print $page->PublisherHomepage();
}
?>