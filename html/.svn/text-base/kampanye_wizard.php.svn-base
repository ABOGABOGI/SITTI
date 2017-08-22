<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIAdvertiser.php";
include_once $APP_PATH."SITTI/SITTIInventory.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$advertiser = new SITTIAdvertiser($req, $account);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$account->close();

if($isLogin){
	$saldo = $account->getSaldo($info['sittiID']);
	$view->assign("info",$info);
	$view->assign("isLogin",$isLogin);
	$view->assign("SALDO",$saldo['budget']);
	$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
	if($req->getParam("edit")){
		$json = json_decode(base64_decode($_SESSION['campaign_info']));
		$view->assign("campaign_name",$json->campaign_name);
		$view->assign("tanggalAwal",$json->tanggalAwal);
		$view->assign("tanggalAkhir",$json->tanggalAkhir);
		$view->assign("product_name",$json->product_name);
		$view->assign("type",$json->type);
		$view->assign("category",$json->category);
		$view->assign("keyword",$json->keyword);
		$view->assign("urlName",$json->urlName);
		$view->assign("urlLink",$json->urlLink);
	}else{
		//reset session
		$_SESSION['campaign_info'] = null;
		$_SESSION['adbids'] = null;
		$_SESSION['ad_create_step1'] = null;
		$_SESSION['adsetskeys'] = null;
		$_SESSION['keypicks'] = null;
		if ($req->getPost("wizard")){
			//Save Campaign Info in Session
			return $advertiser->CreateCampaignWizard();
		}
	}
	$inventory = new SITTIInventory();
	$adCategory = $inventory->getAdCategory();
	$view->assign("adCategory",$adCategory);
	print $view->toString("SITTI/kampanye_wizard.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>