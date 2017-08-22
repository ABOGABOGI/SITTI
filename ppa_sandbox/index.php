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
$sittiHomepage = new SITTIHomepage(&$req,$system->broadcast(),'ppa');
$account = new SITTIAccount(&$req);

$pageID = $req->getParam("id");
$loginParam = $req->getParam("login");
$contactParam = $req->getParam("contact");
$registrationParam = $req->getParam("registration");
$simulation = $req->getPost("simulation");
$isContent = false;
/** cek login status **/
$account->open(0);
$isLogin = $account->isLogin();
if($isLogin){
	$info = $account->getProfile();
	
}
$sittiHomepage->View->assign("isLogin",$account->isLogin());
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
	$isContent = true;
	include_once $APP_PATH."SITTI/SITTISimulation.php";
	$sim = new SITTISimulation($req);
	$sim->View->assign("isLogin",$isLogin);
	$sittiHomepage->View->assign("mainContent", $sim->SimulationPage());
	
	$sittiHomepage->View->assign("SALDO",$saldo['budget']);
}else if($req->getParam("logout")=='1'){
    //$isContent = true;
    if (isset($_SESSION['api_key']))
    {
        unset($_SESSION['api_key']);
    }
    if($account->logout()){
    	print $sittiHomepage->showLoginForm();
        //$sittiHomepage->View->assign("mainContent", $sittiHomepage->View->showMessage("Anda berhasil keluar dari akun.","index.php"));
    }
}else{
    $getPage = $sittiHomepage->getRootID(1);
    
}


$sittiHomepage->close();


$sittiHomepage->View->assign("info",$info);
$sittiHomepage->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);


if($isContent){
	
    //print $sittiHomepage->showContentPage();
    return $this->View->toString("SITTIPPA_sandbox/content.html");

}else if($isLoginForm){
    print $sittiHomepage->showLoginForm();
    //langsung assign ke formnya
}else{
    print $sittiHomepage->showIndexPage();  
}
?>