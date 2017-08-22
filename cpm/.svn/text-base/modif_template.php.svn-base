<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($isLogin){
	//Account Profile
    $view->assign("info",$info);

	$custom_template = new Custom_Template(&$req,&$account);
    
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	print $view->toString("SITTI/custom_template/add_layout.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>