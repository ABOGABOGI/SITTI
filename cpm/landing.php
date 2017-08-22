<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/LandingPage/LandingPage.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($isLogin){
	$app = new LandingPage($req,$info);
	if($req->getParam("done")=="1"){
		//do something here.	
	}else{
		print $app->run();
	}
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>