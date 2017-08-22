<?php 
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$sittiHomepage = new SITTIHomepage(&$req,$system->broadcast());

$referral = "";
$validID = "1";
if ($req->getParam('refid')){
	$referral = $req->getParam('refid');
	if (!$account->isReferralMember($referral)){
		$validID = "0";
	}
	if ($req->getParam('cek')){
		echo $validID;
		die();
	}
}
$sittiHomepage->View->assign("valid",$validID);
$sittiHomepage->View->assign("referral",$referral);
$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
$view->assign("mainContent", $sittiHomepage->showRegistrationForm());

print $view->toString("SITTI/content5.html");
?>