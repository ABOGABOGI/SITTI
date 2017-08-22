<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBeranda.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();

$account->close();
//$account->open(4);
$saldo = $account->getSaldo($info['sittiID']);
$isFree = $account->isFreePromo($info['sittiID']);
//$account->close();

//early topup mods
if($_SERVER['QUERY_STRING']=="topup"){
	$early_topup = true;
}
//->

if($isLogin){
	//Account Profile
    $view->assign("info",$info);
	$beranda = new SITTIBeranda(&$req,&$account);
	//cek apakah baru pertama kali login ke sitti ??
	if($isFirstTimer){
		//$view->assign("mainContent",$beranda->gettingStartedPage());
		if($early_topup){
			$view->assign("content",$view->toString("SITTI/reporting/beranda_tab9.html"));
			$view->assign("mainContent",$view->toString("SITTI/early_topup.html"));
		}else{
			$view->assign("mainContent",$beranda->showPage());
		}
	}else{
		$view->assign("mainContent",$beranda->showPage());
		//print $view->toString("SITTI/beranda2.html");
	}
	//end dropdown
	//munculkan submenu khusus beranda
	$view->assign("isBeranda","1");
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	//broadcast message
	$view->assign("broadcast",$system->broadcast());
	$view->assign("SALDO",$saldo['budget']);
	$view->assign("isFree",$isFree);
	

  //if($req->getRequest("step")!="4"&&$req->getPost('do')!="step4"){
	// print $view->toString("SITTI/content.html");
  //}else{
   
   // if($_SESSION['ad_create_step1']['target_web']=="sitti"){
   //   print $view->toString("common/blank.html");
   // }else{
   	   $view->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
       print $view->toString("SITTI/content.html");
    //}
  //}
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>