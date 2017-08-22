<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIFeedback.php";


$view = new BasicView();
$account = new SITTIAccount(&$req);

$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();

$app = new SITTIFeedback($req,$account);

$content = $app->run();
$view->assign("mainContent",$content);
print $view->toString("SITTI/content3.html");
?>