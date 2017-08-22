<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBeranda.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
$view = new BasicView();
$app = new SITTIBilling($req, $account);
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$isFirstTimer = $account->isFirstTimer();
$account->close();
//Account Profile
$view->assign("info",$info);
$saldo = $account->getSaldo($info['sittiID']);
if($isLogin){
	$view->assign("isLogin",$isLogin);
	$view->assign("isBeranda","1");
	$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
	//broadcast message
	$view->assign("broadcast",$system->broadcast());
	//content
	if($req->getPost("save")){
		$view->assign("mainContent",$app->save($info));	
	}else if($req->getParam("howto")=="1"){
		$view->assign("content",$view->toString("SITTI/reporting/beranda_tab9.html"));
		$view->assign("mainContent",$view->toString("SITTI/early_topup.html"));
		
	} elseif ($req->getParam("popup")=="1") {

		if ($req->getParam("type")=="bca")
		{
			print $view->toString("SITTI/topup/popup_bca.html");
			exit();
		} 
		elseif ($req->getParam("type")=="mandiri")
		{
			print $view->toString("SITTI/topup/popup_mandiri.html");
			exit();
		}
		elseif ($req->getParam("type")=="imandiri")
		{
			print $view->toString("SITTI/topup/popup_imandiri.html");
			exit();
		}
		elseif ($req->getParam("type")=="voucher")
		{
			print $view->toString("SITTI/topup/popup_voucher.html");
			exit();
		}

	}else{
		
		$view->assign("mainContent",$app->topup($info));
	}
	$view->assign("SALDO",$saldo['budget']);
	//-->
	$view->assign("is_cpm",true);
    print $view->toString("SITTI/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>
