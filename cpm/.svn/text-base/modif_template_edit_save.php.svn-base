<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template_Edit.php";

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

	$custom_template_edit = new Custom_Template_Edit(&$req,&$account);
    
	//cek apakah baru pertama kali login ke sitti ??
	if($isFirstTimer){
		//$view->assign("mainContent",$beranda->gettingStartedPage());
        $view->assign("mainContent",$custom_template_edit->process());
	}else{
        $view->assign("mainContent",$custom_template_edit->process());
	}
	//end dropdown
	//munculkan submenu khusus beranda
	$view->assign("isBeranda","1");
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	$view->assign("is_cpm",true);
    print $view->toString("SITTI/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>