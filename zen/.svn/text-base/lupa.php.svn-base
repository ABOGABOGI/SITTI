<?php
/**
 * Aplikasi Lupa Password
 */
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
//captcha
$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
$view = new BasicView();


$account = new SITTIAccount(&$req);


if($req->getPost("check")=="1"&&$captcha->verify()){
	//$username = $req->getPost("sitti_usr");
	$email = $req->getPost("sitti_email");
	$account->open(0);
	//validasi email
	if($account->isPublisherEmailExist($email)){
		$request_key = md5($username.$email.date("YmdHis"));
		if($account->registerPublisherRequestKey($request_key, $email)){
			//email notifikasi dikirim -->
			$params = array("e"=>urlencode64($email),
							"r"=>urlencode64($request_key));
			$params = urlencode64(json_encode($params));
			$ConfirmUrl = $CONFIG['WebsiteURL2']."lupa.php?confirm=1&rc=".urlencode64($params);
			//$ConfirmUrl = $CONFIG['WebsiteURL2']."lupa.php?confirm=1&&e=".urlencode($email)."&r=".$request_key;
			$view->assign("CONFIRMATION_URL",$ConfirmUrl);
			$strEmail = $view->toString("SITTIZEN/email/konfirmasi_ubah_password.html");
			//print $strEmail;
			//email notifikasi pendaftaran
            $smtp = new SITTIMailer();
            $smtp->setSubject("[SITTI] Konfirmasi Perubahan Password");
    		$smtp->setRecipient($email);
    		$smtp->setMessage($strEmail);
    		$smtp->send();
    		
			//---->
			$view->assign("mainContent",$view->showMessage($LOCALE['LUPA_PASSWORD_EMAIL_SEND'], "http://www.sitti.co.id"));
		}else{
				
			$view->assign("mainContent",$view->showMessageError($LOCALE['LUPA_PASSWORD_KEY_FAILED'], "http://www.sitti.co.id"));
		}
		
	}else{
		print mysql_error();
		$view->assign("mainContent",$view->showMessageError($LOCALE['LUPA_PASSWORD_EMAIL_ERROR'], "http://www.sitti.co.id"));
	}
	$account->close();
	print $view->toString("SITTI/content.html");
}else if($req->getPost("update_password")=="1"){
//	$email = $req->getPost("e");
//	$key = $req->getPost("r");
	$rc = json_decode(urldecode64($req->getPost("rc")));
	
	if($rc->confirmed=="1"){
		$key = urldecode64($rc->req);
		$email = urldecode64($rc->e);
		$account->open(0);
		//$profile = $account->getCredentialByEmail($username,$email);
		$profile = $account->getPublisherCredentialByEmail($email);
		$isValid = $account->isRequestValid(cleanString($key), '',cleanString($email));
		$account->close();
	}
	/*$account->open(0);
	$profile = $account->getPublisherCredentialByEmail($email);
	$account->close();*/
	if($isValid){
		if(md5($req->getPost("newpass"))==md5($req->getPost("newpass2"))){
			$account->open();
			$rs = $account->updatePublisherCredential($profile['id'],$profile['email'], $req->getPost("newpass"));
			$account->close();
			if($rs){
				//hapus request key
				$account->open();
				$account->removeRequestKey($key, $email);
				$account->close();
				$view->assign("mainContent",$view->showMessage($LOCALE['UPDATE_PASSWORD_SUCCESS'], "http://www.sitti.co.id"));
			}else{
				$view->assign("mainContent",$view->showMessageError($LOCALE['UPDATE_PASSWORD_FAILED'], "http://www.sitti.co.id"));
			}
		}else{
			$view->assign("mainContent",$view->showMessageError($LOCALE['LUPA_PASSWORD_WRONG'], "http://www.sitti.co.id"));
		}
	}else{
		$view->assign("mainContent",$view->showMessageError($LOCALE['UPDATE_PASSWORD_FAILED'], "http://www.sitti.co.id"));
	}
	print $view->toString("SITTI/content.html");
	
}else if($req->getParam("confirm")=="1"){
	
	$rc = $req->getParam("rc");
	$rc = json_decode(urldecode64(urldecode64($rc)));
	$params['r'] = urldecode64($rc->r);
	$params['e'] = urldecode64($rc->e);
	
	$account->open(0);
	$rs = $account->isRequestValid($params['r'], $params['e'],$params['e']);
	$account->close();
	if($rs){
		//tampilkan form ganti password
		$new_key = md5($rc->r.$rc->e.date("YmdHis"));
		$account->open(0);
		$sql = "UPDATE db_web3.sitti_change_password_queue SET request_key='".$new_key."' 
				WHERE request_key='".$params['r']."' AND email='".$params['e']."'";
		$account->query($sql);
		$account->close();
		$fake = md5("fakeparamsfordecoy");
		$params = json_encode(array($fake=>"decoy","confirmed"=>"1","req"=>urlencode64($new_key),"e"=>$rc->e));
		
		$view->assign("rc",urlencode64($params));
		//$view->assign("rs",$params);
		print $view->toString("SITTIZEN/update_password.html");
	}else{
		$view->assign("mainContent",$view->showMessageError($LOCALE['LUPA_PASSWORD_REQUEST_EXPIRED'], "http://www.sitti.co.id"));
		print $view->toString("SITTI/content.html");
	}
	
}else{
//captcha
	$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
	$view->assign("CAPTCHA",$captcha->getHtml());
	if($req->getPost('check')=="1"&&!$captcha->verify()){
		$view->assign("CaptchaError",$LOCALE['CAPTCHA_ERROR']);
	}
	print $view->toString("SITTIZEN/forgot.html");
}
?>