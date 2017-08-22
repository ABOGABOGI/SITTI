<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";

$view = new BasicView();
$landing_page = new Custom_Template(&$req);
$sittiHomepage = new SITTIHomepage(&$req);
$account = new SITTIAccount(&$req);

global $LOCALE;

if (checkAPIParameter($req)){
	///echo "Parameter Checked<br/>";
	$api_key = $req->getParam('key');
	$flag = checkApiKey($api_key);
	if ($flag){
		//echo "API Key Valid<br/>";
		$cred = GetParams($req->getParam('p'));
		$email = $cred['email'];
		$unix = $cred['t'];
		$unix_now = time();
		if(checkEmailExistence($email)){
			//echo "Email Exist<br/>";
			if($unix_now<=$unix+60){
				//echo "Valid Call<br/>";
				$sittiID = getSITTIIDFromEmail($email);
				$account->open();
				$account->login_via_onigi($sittiID);
				//print_r (json_decode(urldecode64($_SESSION[base64_encode("sitti_login_session")])));
				$info = $account->getProfile();
				$saldo = $account->getSaldo($info['sittiID']);
				$msg = $LOCALE['LOGIN_SUCCESS_NEXT'];
				$sittiHomepage->View->assign("msg", $msg);
				$landing_page->resetSessionLandingPage();
				$jumpTo = "beranda.php";
				$view->assign("mainContent",$sittiHomepage->View->showMessage($msg,urldecode($jumpTo)));
				$view->assign("isLogin","1");
				$view->assign("SALDO",$saldo['budget']);
				$view->assign("info",$info);
			   
				print $view->toString("SITTI/content.html");
				
				$account->close();
			}else{
				echo "Invalid Call<br/>";
			}
		}else{
			echo "Email not Exist<br/>";
		}
	}else{
		echo "API Key not Valid<br/>";
	}
}else{
	echo "Parameter Error<br/>";
}

function GetParams($chipertext){
	$params = array();
	$plaintext = decrypt_parameter($chipertext);
	$items = explode("&",$plaintext);
	foreach ($items as $item){
		$param = explode("=",$item);
		$params[$param[0]] = $param[1];
	}
	return $params;
}

function checkAPIParameter($req){
	if ($req->getParam('key') && $req->getParam('p')){
		return true;
	}
	return false;
}

function checkApiKey($api_key){
	global $account;
	$account->open();
	$rs = $account->fetch("SELECT api_key FROM zz_sitti.api_rate_monitor WHERE api_key='".$api_key."'");
	$account->close();
	if ($rs){
		return true;
	}else{
		return false;
	}
}

function checkEmailExistence($email){
	global $account;
	$account->open();
	$is_email_exist = $account->isEmailExist("", $email);
	$account->close();
	return $is_email_exist;
}

function getSITTIIDFromEmail($email){
	global $account;
	$account->open();
	$rs = $account->getCredentialByEmail($email);
	$account->close();
	return $rs['sittiID'];
}
?>