<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
$landing_page = new Custom_Template(&$req);
$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req, $system->broadcast(), 'ppa');
if($_SESSION[urlencode64('block'.date("Ymd"))]==NULL){
	$_SESSION[urlencode64('block'.date("Ymd"))]=0;
}
$account = new SITTIAccount(&$req);


$username = $req->getPost("username");
$password = $req->getPost("password");
$role = $req->getPost("role");
$fix_pass = md5($password);
$jumpTo = mysql_escape_string(($_REQUEST['r']));
$direct = $req->getParam("d");

if($direct=="1"){
	$sittiHomepage->View->assign("jumpTo", $jumpTo);
    print $sittiHomepage->showLoginForm();
}else if($req->getParam("resend")=="1"){
	
	$param = unserialize(urldecode64($req->getParam("token")));
	$confirm_url = $param[0];
	
	$p = unserialize(urldecode64($param[1]));
	
	if(eregi($CONFIG['WebsiteURL']."konfirmasi.php",$confirm_url)){
		//email notifikasi pendaftaran
		$smtp = new SITTIMailer();
		$smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
	    $smtp->setRecipient($p['email']);
	    $view->assign("nama",$p['name']);
	    $view->assign("confirm_url",$confirm_url);
	    $smtp->setMessage($view->toString("SITTI/email/notifikasi_pendaftaran.html"));
	    if($smtp->send()){
			$msg = "Email Konfirmasi telah dikirim ulang ke email anda. Silahkan periksa email anda beberapa saat lagi.";
	    }else{
	    	$msg.="Maaf, request anda tidak berhasil kami proses. Silahkan coba kembali.";
	    }
	}else{
		$msg = "Maaf, request anda tidak berlaku.";
	}
	$view->assign("mainContent",$view->showMessage($msg, "index.php"));
    print $view->toString("SITTI/content.html");
}else if(!$username || !$password){
    $er = "Semua field wajib diisi.";
    $sittiHomepage->View->assign("er", $er);
    $sittiHomepage->View->assign("jumpTo", $jumpTo);
    print $sittiHomepage->showLoginForm();

}else{
    $account->open();
    $check = $account->login($username, $fix_pass, $role, 'ppa');
   	if($_SESSION[urlencode64('block'.date("Ymd"))]>=3){
   		include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
		//captcha
		$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
		if($captcha->verify()){
			$_SESSION[urlencode64('block'.date("Ymd"))]=0;
		}else{
			//var_dump($check);
			$check['status']=3;
		}
   	}
    if($check['status']==1){
    	$account->open(0);
    	$info = $account->getProfile();
    	$account->close();
		$saldo = $account->getSaldo($info['sittiID']);
        $msg = "Anda berhasil masuk.";
        $sittiHomepage->View->assign("msg", $msg);
        //print $sittiHomepage->showLoginForm();
        //redirect ke beranda
        //sendRedirect("beranda.php");
        $landing_page->resetSessionLandingPage();//buat reset session form 'buat iklan baru' setelah create landing page
        if(strlen($jumpTo)==0){
        	$jumpTo = "beranda.php";
        }
        $view->assign("mainContent",$sittiHomepage->View->showMessage($msg,urldecode($jumpTo)));
        $view->assign("isLogin","1");
		$view->assign("SALDO",$saldo['budget']);
        $view->assign("info",$info);
       
        $_SESSION['ppa'] = true;
        $view->assign("is_ppa",true);

        print $view->toString("SITTIPPA/content.html");
        
    }else if($check['status']==2){
    	$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
        $confirm_url = $CONFIG['WebsiteURL']."konfirmasi.php?token=".$check['confirmKey'];
        //var_dump($check['info']);
        $msg = "Maaf, akun Anda belum aktif. <br/>Apabila Anda belum menerima email konfirmasi, 
        periksa spam folder Anda <br/>atau <a href='login.php?resend=1&token=".urlencode64(serialize(array($confirm_url,urlencode64(serialize($check['info'])))))."'>klik disini</a> untuk mengirim ulang.";
        $view->assign("mainContent",$view->showMessageError($msg, "login.php"));
        
        print $view->toString("SITTI/content.html");
    }else if($check['status']==3){
    	$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
        $er = "Mohon maaf, email dan/atau kata sandi anda tidak cocok.";
        $sittiHomepage->View->assign("er", $er);
		$sittiHomepage->View->assign("jumpTo", $jumpTo);
		$sittiHomepage->View->assign("CAPTCHA",$captcha->getHtml());
        print $sittiHomepage->showLoginForm();
    }else if($check['status']==99){
        $_SESSION[urlencode64('block'.date("Ymd"))]+=1;
        $msg = "Maaf, akun Anda telah disuspend.";
        $view->assign("mainContent",$view->showMessageError($msg, "login.php"));
        print $view->toString("SITTI/content.html");
    }else{
    	$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
        $er = "Mohon maaf, email dan/atau kata sandi anda tidak cocok.";
        $sittiHomepage->View->assign("er", $er);
		$sittiHomepage->View->assign("jumpTo", $jumpTo);
        print $sittiHomepage->showLoginForm();
    }
    $account->close();
}
?>