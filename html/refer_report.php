<?php
include_once 'common.php';
include_once $ENGINE_PATH . 'Utility/SessionManager.php';
include_once $APP_PATH . 'SITTI/SITTIAccount.php';
include_once $APP_PATH . 'SITTI/ReferReportPage.php';

$view = new BasicView();
$account = new SITTIAccount(&$req);

$account->open(0);
$isLogin = $account->isLogin();
$profile = $account->getProfile();
$account->close();

$isRefMember = $account->isReferralMember($profile['sittiID']);

if ($isLogin) {
	$referReportPage = new ReferReportPage($profile, $account);
	$view->assign("mainContent", $referReportPage->showReportPage($isRefMember));
	if ($req->getPost('reg')){
		$account->open(0);
		$profile = $account->getProfile();
		$account->close();
		$account->insertReferralMember($profile['sittiID']);
		$msg = "Pendaftaran Berhasil";
		$view->assign("mainContent", $view->showMessage($msg,"refer_report.php"));
	}
	$view->assign("isLogin",$isLogin);
	$view->assign("info",$profile);
	print $view->toString("SITTI/content.html");
} else {
	sendRedirect("index.php?login=1");
}
?>