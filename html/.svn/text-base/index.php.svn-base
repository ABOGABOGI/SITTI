<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
//include_once $APP_PATH."MOP/MOPClient.php";
//referral info
setcookie('ref',$_GET['ref']);
if(strlen($_COOKIE['ref'])>0){
	$_SESSION['ref'] = $_COOKIE['ref'];
}
$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req,$system->broadcast());
$account = new SITTIAccount(&$req);

$pageID = $req->getParam("id");
$loginParam = $req->getParam("login");
$contactParam = $req->getParam("contact");
$registrationParam = $req->getParam("registration");
$simulation = $req->getPost("simulation");
$isContent = false;

global $LOCALE;

/** cek login status **/
$account->open(0);
$isLogin = $account->isLogin();
if($isLogin){
	$info = $account->getProfile();
	
}
$sittiHomepage->View->assign("isLogin",$account->isLogin());
$sittiHomepage->View->assign("referral",$_SESSION['referral']);
$_SESSION['referral'] = "";
$account->close();
if($isLogin){
		$saldo = $account->getSaldo($info['sittiID']);
		
}
//--->
$sittiHomepage->open();

if($pageID>0&&$pageID<8){
    $sittiHomepage->View->assign("contentID",$pageID);
    //print $sittiHomepage->showPage($pageID);
    //print mysql_error();
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showPage($pageID));
    $isContent = true;

}else if($loginParam=='1'){
    $isLoginForm = true;
    
}else if($contactParam=='1'){
    $sittiHomepage->View->assign("mainContent", $sittiHomepage->showContactForm());
    $isContent = true;

}else if($registrationParam=='1'){
    $sittiHomepage->View->assign("mainContent", $sittiHomepage->showRegistrationForm());
    $isContent = true;
    
}else if($simulation){
	//reset session
	$_SESSION['campaign_info'] = null;
	$_SESSION['adbids'] = null;
	$_SESSION['ad_create_step1'] = null;
	$_SESSION['adsetskeys'] = null;
	$_SESSION['keypicks'] = null;
	
	include_once $APP_PATH."SITTI/SITTISimulation.php";
	$sim = new SITTISimulation($req);
	
	if($isLogin){
		$isContent = true;
		$sim->View->assign("isLogin",$isLogin);
		$sittiHomepage->View->assign("mainContent", $sim->SimulationPage());
		
		$sittiHomepage->View->assign("SALDO",$saldo['budget']);
	}else{
		$sim->Detil();
		header("Location: login.php?r=buat.php?edit=2&d=1");
		exit();
	}
}else if($req->getParam("logout")=='1'){
    $isContent = true;
    if($account->logout()){
    	$sittiHomepage->View->assign("mainContent", $sittiHomepage->View->showMessage($LOCALE['LOGOUT_SUCCESS'],"index.php"));
    }
}else{
    $getPage = $sittiHomepage->getRootID(1);
    
}


$sittiHomepage->close();


$sittiHomepage->View->assign("info",$info);
$sittiHomepage->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);

if($registrationParam){
	$view->assign("mainContent", $sittiHomepage->showRegistrationForm());
	 //$view->assign("mainContent",$view->toString("SITTI/daftarv2.html"));
	 print $view->toString("SITTI/content5.html");
	 //print $sittiHomepage->showContentPage();
}else if($isContent){
	
    print $sittiHomepage->showContentPage();

}else if($isLoginForm){
    print $sittiHomepage->showLoginForm();
    //langsung assign ke formnya
}else{
    print $sittiHomepage->showIndexPage();  
}
?>