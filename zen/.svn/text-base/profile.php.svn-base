<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIPublisherProfile.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin(1);
$info = $account->getPublisherProfile();
$account->close();
if($isLogin){
	//Account Profile
	$info['name'] = $info['username'];
	$view->assign("info",$info);
	$profile = new SITTIPublisherProfile($req, $account);
	$view->assign("isLogin",$isLogin);
	if($req->getPost('save')=="1"){
		print $profile->save($info);
	}else if($req->getPost("save_password")){
		print $profile->save_password($info);
	}else if($req->getParam("password")){
		print $profile->ganti_password($info);
	}else{
		//print $view->toString("SITTI/reg1tes.html");
		print $profile->ProfilePage($info);
	}
}
?>