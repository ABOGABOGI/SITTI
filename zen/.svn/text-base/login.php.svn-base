<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
$page = new SITTIHomepage($req);

$view = new BasicView();
$account = new SITTIAccount(&$req);
$isContent = false;
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();

if($req->getPost("login")=="1"){
	$isContent = true;
	$view->assign("mainContent",$page->PublisherLogin($account));
}else if($req->getParam("resend")=="1"){
	$isContent = true;
	$token = $req->getParam("token");
	$param = unserialize(urldecode64($token));
	$confirm_url = $param[0];
	
	$p = unserialize(urldecode64($param[1]));
	 //email notifikasi pendaftaran
    $smtp = new SITTIMailer();
    $smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
    $smtp->setRecipient($p['email']);
    
    $view->assign("username",$p['email']);
    $view->assign("ori_pass","******");
    $view->assign("website",$p['website']);
    $view->assign("confirm_url",$confirm_url);
    $smtp->setMessage($view->toString("SITTIZEN/email/notifikasi_pendaftaran.html"));
    //print $view->toString("SITTIZEN/email/notifikasi_pendaftaran.html");
	if($smtp->send()){
			$msg = "Email Konfirmasi telah dikirim ulang ke email anda. <br/>Silahkan periksa email anda beberapa saat lagi.";
	    }else{
	    	$msg.="Maaf, request anda tidak berhasil kami proses. Silahkan coba kembali.";
	    }
	 $view->assign("mainContent",$view->showMessage($msg, "http://www.sitti.co.id"));
}
if($isContent){
	print $view->toString("SITTIZEN/content.html");
}else{
	sendRedirect("beranda.php");
}
?>