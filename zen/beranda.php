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
$info = $account->getPublisherProfile();
$account->close();
if($isLogin){
	//Account Profile
	$info['name'] = $info['username'];
    $view->assign("info",$info);
	//print_r($info);
	$beranda = new SITTIBeranda(&$req,&$account);
	
	//cek apakah baru pertama kali login ke sitti ??
	if($isFirstTimer){
		//$view->assign("mainContent",$beranda->gettingStartedPage());
		$view->assign("mainContent",$beranda->showPage2());
	}else{
		$view->assign("mainContent",$beranda->showPage2());
	}
	
	//end dropdown
	//munculkan submenu khusus beranda
	if($req->getPost("do")!="create"){
		$view->assign("isBeranda","1");
	}
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL_2']);
	
	//notifikasi data publisher belum lengkap
	if($_SESSION['pflag']=="1"){
		$view->assign("broadcast","Data Penerimaan Pembayaran Anda Belum Lengkap");
	}
	
	print $view->toString("SITTIZEN/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>