<?php 
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIOptin.php";

$view = new BasicView();
$optin = new SITTIOptin();

if ($req->getParam('id'))
{
	$sittiID = $req->getParam('id');
	$view->assign("sittiID",$sittiID);
	$view->assign("mainContent", $view->toString("SITTI/optin.html"));
}
else if ($req->getPost("sittiID") && $req->getPost("submit_optin")==1)
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))//check ip from share internet
	{
	  $ipaddress=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))//to check ip is pass from proxy
	{
	  $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	  $ipaddress=trim($iplist[sizeof($iplist) - 1]);
	}else
	{
	  $ipaddress=$_SERVER['REMOTE_ADDR'];
	}
	
	$sittiID = $req->getPost("sittiID");
	$flag = $optin->insertOptin($sittiID,$ipaddress);
	if ($flag){
		$message = "Terima kasih atas partisipasi anda.";
		$view->assign("mainContent", $view->showMessage($message, "index.php"));
	}else{
		$message = "Maaf, tidak berhasil mengirimkan konfirmasi anda. Silahkan dicoba kembali !";
		$view->assign("mainContent", $view->showMessage($message, "optin.php?id=".$sittiID));
	}
}
echo $view->toString("SITTI/content4.html");
?>