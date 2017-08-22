<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";

global $LOCALE;

$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req);

$account = new SITTIAccount(&$req);
if($account->confirmAdvertiser($_GET['token'])){
	$msg = $LOCALE['REGISTER_CONFIRM_SUCCESS'];
}else{
	$msg = $LOCALE['REGISTER_CONFIRM_FAILED'];
}
$view->assign('mainContent',$view->showMessage($msg,'http://www.sitti.co.id'));
print $view->toString("SITTI/content.html");
?>