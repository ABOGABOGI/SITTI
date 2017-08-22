<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
include_once $APP_PATH."SITTI/SITTIReferral.php";
//include_once $APP_PATH."MOP/MOPClient.php";
$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req);
//captcha
$captcha = new CaptchaUtil($CONFIG['BELAJARSITTI_CAPTCHA_PUBLIC'], $CONFIG['BELAJARSITTI_CAPTCHA_PRIVATE']);

$account = new SITTIAccount(&$req);


$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();

$email = $req->getPost("email");
$website = $req->getPost("website");
$jenis_situs = $req->getPost("jenis_situs");
$jenis_app = $req->getPost("jenis_app");
$jum_pengunjung = $req->getPost("jum_pengunjung");
$username = $req->getPost("email");
$password = $req->getPost("password");
$nama = $req->getPost("nama");
$telp = $req->getPost("telp");
$kota = $req->getPost("kota");

//$role = $req->getPost("role");
$role = 2; //2 --> publisher

if($req->getPost("daftar")=="1"){
	if(!$captcha->verify()){
		$msg = $LOCALE['CAPTCHA_ERROR'];
		$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
		
	}else if(!@isValidUrl($website)){
		$msg = $LOCALE['URL_INVALID'];
		$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
	}else{
		if(!preg_match_all('/^[A-Z0-9._-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i', $email, $result, PREG_PATTERN_ORDER)){
			$msg = $LOCALE['PUBLISHER_EMAIL_INVALID'];
			$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
		}else{
			$account->open(0);
			$isPubExist = $account->isPublisherEmailExist($email);
			if(!$isPubExist){
				$rs = $account->createPublisher($username, $password, $nama, $email,$website,$jenis_situs,$jenis_app,$jum_pengunjung,$kota,$telp);
				$user_id = mysql_insert_id();
				$publisher_profile = $account->getPublisherProfile($user_id);
				$sitti_id = $publisher_profile['sittiID'];
			}
			$account->close();
			if($isPubExist){
				$msg = $LOCALE['PUBLISHER_REGISTRATION_FAILED2'];
				$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
			}else if($rs){
				$msg = $LOCALE['PUBLISHER_REGISTRATION_SUCCESS'];
			
				// insert referral if available
				if ($_SESSION['referral_id'])
				{
					$referral = new SITTIReferral($req, $account);
					if ($referral->refer($sitti_id, $_SESSION['referral_id']))
					{
						unset($_SESSION['referral_id']);
					}
				}

				//email notifikasi pendaftaran
	       	 	$smtp = new SITTIMailer();
	       		$smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
	    		$smtp->setRecipient($email);
	    	
	    		$view->assign("username",$username);
	    		$view->assign("ori_pass",$password);
	    		$view->assign("website",$website);
	    		$view->assign("confirm_url",$CONFIG['WebsiteURL2']."konfirmasi.php?token=".$account->getPubConfirmKey($user_id));
	    		$smtp->setMessage($view->toString("SITTIZEN/email/notifikasi_pendaftaran.html"));
	    		// print $view->toString("SITTIZEN/email/notifikasi_pendaftaran.html");
				// die();
	    		$smtp->send();
	    	
				//-->
				// $view->assign("mainContent",$view->showMessage($msg,"http://www.sitti.co.id"));
				$view->assign("username",$username);
	    		$view->assign("ori_pass",$password);
	    		$view->assign("website",$website);
	    		$view->assign("mainContent",$view->toString("SITTIZEN/pendaftaran_berhasil.html"));
			}else{
				$msg = $LOCALE['PUBLISHER_REGISTRATION_FAILED'];
				$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
			}
		}
	}
	print $view->toString("SITTIZEN/registerv2-2.html");
}else{
	//-->render captcha
	$view->assign("CAPTCHA",$captcha->getHtml());

	if ($req->getPost("referral"))
	{
		$publisher_referral = unserialize(base64_decode($req->getPost('referral')));
		$publisher_referral_token = $publisher_referral['publisher_referral_token'];
		if ($publisher_referral_token == $_SESSION['publisher_referral_token'])
		{
			$_SESSION['referral_id'] = $publisher_referral['publisher_id'];
		}
		else
		{
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = 'index.php';
			header("Location: http://$host$uri/$extra");
		}
	}

	//-->
	//print $view->toString("SITTIZEN/register.html");
	print $view->toString("SITTIZEN/registerv2.html");
}

?>