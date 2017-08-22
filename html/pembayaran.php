<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBeranda.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."SITTI/SITTICampaign.php";
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
	if ($req->getPost("save"))
	{
		$view->assign("mainContent",$app->save($info));	
	}elseif($req->getRequest('multiply')){
		print $app->redeemMultiply($info,$req->getRequest('redeem'));
		//print json_encode(array("status"=>"2","transaction_code"=>"123123","topup"=>200000));
		exit();
	}
	elseif ($req->getParam("howto")=="1")
	{
		$view->assign("content",$view->toString("SITTI/reporting/beranda_tab9.html"));
		$view->assign("mainContent",$view->toString("SITTI/early_topup.html"));
		
	} 
	elseif ($req->getParam("popup")=="1") 
	{

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
		elseif ($req->getParam("type")=="multiply")
		{
			print $view->toString("SITTI/topup/popup_multiply.html");
			exit();
		}
		elseif ($req->getParam("type")=="paypal")
		{
			print $view->toString("SITTI/topup/popup_paypal.html");
			exit();
		}
		elseif ($req->getParam("type")=="ipaymu")
		{
			print $view->toString("SITTI/topup/popup_ipaymu.html");
			exit();
		}
	}
	elseif ($req->getParam("mutasi"))
	{
		$view->assign("mainContent",$view->toString("SITTI/reporting/mutasi_kredit.html"));
	} 
	else 
	{
		$_SESSION['v_promo'] = intval($_REQUEST['v']);
		$view->assign("mainContent",$app->topup($info));
	}
	$view->assign("SALDO",$saldo['budget']);
	//-->
    print $view->toString("SITTI/content.html");
}else{
			
	sendRedirect("index.php?login=1");
	die();
	
}
?>
