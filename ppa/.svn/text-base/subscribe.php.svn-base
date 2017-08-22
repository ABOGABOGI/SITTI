<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTINewsletter.php";

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
	
	$newsletter = new SITTINewsletter(&$req,&$account);
	//$newsletter->subscribe();
	//$newsletter->createCampaign();
	if($req->getParam("save")=="1"){
		$view->assign("mainContent",$newsletter->subscribe($info));
	}else{
		$view->assign("mainContent",$newsletter->register());
	}
	$view->assign("is_ppa",true);
	print $view->toString("SITTI/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>