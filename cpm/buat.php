<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTICreateAd.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."SITTI/SITTIBannerManager.php";
include_once $APP_PATH."SITTI/SITTICampaign.php";
include_once $APP_PATH."SITTI/SITTIPPAManager.php";
include_once $APP_PATH."SITTI/Model/KeywordModel.php";
include_once $APP_PATH."SITTI/Model/CategoryKeywordModel.php";
include_once $APP_PATH."SITTI/Model/SITTIPPAModel.php";
include_once $APP_PATH."SITTI/SITTISimulation.php";
include_once $APP_PATH."SITTI/Model/SITTITestQuery.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$app = new SITTICreateAd($req,$account);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();

$redirect = $req->getPost("redirect");
if($redirect=="1"){
  	$_SESSION['adsetskeys'] = base64_encode(stripslashes($req->getPost("d")));
  	$_SESSION['adbids'] = base64_encode(stripslashes($req->getPost("d")));
	print "1";
	die();
}

if($isLogin){
	$saldo = $account->getSaldo($info['sittiID']);
	//munculkan submenu khusus beranda
	$view->assign("isBeranda","1");
	//Account Profile
    $view->assign("info",$info);
	//-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
	//broadcast message
	$view->assign("broadcast",$system->broadcast());
	$view->assign("SALDO",$saldo['budget']);

	if ($req->getParam('make_choice')) 
	{
		$ppa_model = new SITTIPPAModel();
		if ( ! $ppa_model->profileExist())
		{
			global $CONFIG;
			
			$view->assign("ppa_url",$CONFIG['SITTI_PPA_SERVER']);
			$landing_script = $view->toString("SITTI/ads/popup_ppa_landing_script.html");
			$checkout_script = $view->toString("SITTI/ads/popup_ppa_checkout_script.html");
			$thanks_script = $view->toString("SITTI/ads/popup_ppa_thanks_script.html");
			
			$view->assign("landing_script",htmlentities($landing_script));
			$view->assign("checkout_script",htmlentities($checkout_script));
			$view->assign("payment_script",htmlentities($thanks_script));
			
			$popup_ppa = $view->toString("SITTI/ads/popup_ppa.html");
			$view->assign("popup_ppa",$popup_ppa);
		}
		
		// show create banner choice page
		$content = $view->toString("SITTI/ads/choice.html");
	}
	elseif ($req->getParam('ad_banner'))
	{
		// show create banner ad page
		$banner_manager = new SITTIBannerManager($req,$account);
		$content = $banner_manager->addBannerPage();
	}
	elseif ($req->getParam('ad_ppa'))
	{
		$ppa_manager = new SITTIPPAManager($req,$account);
		
		if ($req->getParam('test_url'))
    	{
    		echo $ppa_manager->is_valid_url($req->getParam('url'));
			die();
    	}
    	elseif ($req->getParam('save_ppa_profile'))
    	{
    		return $ppa_manager->saveProfile();
    	}
		
		// show create ppa ad page
		$content = $ppa_manager->ppaHandler();
	}
	else
	{
		// show create text ad page
		//$view->assign("mainContent",$app->run());
		$content = $app->run();		
	}

	$view->assign("is_cpm",true);
	$view->assign("mainContent",$content);
	print $view->toString("SITTI/content.html");
}else{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"login.php");
}
?>
