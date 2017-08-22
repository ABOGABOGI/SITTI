<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";

$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req);

$account = new SITTIAccount(&$req);
if($account->confirmAdvertiser($_GET['token'])){
	$msg = "Selamat, akun anda sudah aktif. Silahkan login.";
}else{
	$msg = "Maaf, akun anda tidak berhasil diaktifkan.";
}
$view->assign('mainContent',$view->showMessage($msg,'index.php'));
$view->assign("is_ppa",true);
print $view->toString("SITTI/content.html");
?>