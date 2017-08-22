<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";


$view = new BasicView();
$account = new SITTIAccount(&$req);

$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($_SESSION['imp_count']==NULL){
	//kita cache per session.. biar dia gak melulu query ke database.
	$app = new SITTIApp($req,$account);
	$app->open(2);
	$sql = "SELECT imp_count FROM db_report.tbl_rekap_admin";
	$rs = $app->fetch($sql);
	$app->close();
	$_SESSION['imp_count'] = $rs['imp_count'];
}
$imp_count = $_SESSION['imp_count'];
print number_format($imp_count);
?>