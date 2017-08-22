<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIPPASandbox.php";
$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$handler = new SITTIPPASandbox();
$account->close();

if($isLogin)
{	
	switch($req->getParam('action'))
	{
		case 'detail_iklan':
			$id_iklan = $req->getParam('id_iklan');
			return $handler->getAdsDetail($id_iklan);
			break;
		case 'token_date':
			return $handler->getLastTokenTime();
			break;
	}
}
else
{
	sendRedirect("index.php?login=1");
	die();
}

?>