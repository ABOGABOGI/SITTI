<?php
include_once 'common.php';
include_once $ENGINE_PATH . 'Utility/SessionManager.php';
include_once $APP_PATH . 'SITTI/SITTIAccount.php';
include_once $APP_PATH . 'SITTI/SITTIMailer.php';
include_once $APP_PATH . 'SITTI/ReferFriendPage.php';

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$profile = $account->getProfile();
$account->close();
if ($isLogin) {
  //print_r($profile);
  //die();
//$nama = $req->getPost("nama");
  $nama = $req->getPost("nama");
//$email = $req->getPost("email");
  $email = $req->getPost("email");

  if (!$nama || !$email) {
    $mailer = new SITTIMailer($req);
    $referFriendPage = new ReferFriendPage($profile, $account, $mailer);
    $view->assign("mainContent", $referFriendPage->showForm());
   // print $view->toString("SITTI/refer.html");
  } else {
    $mailer = new SITTIMailer($req);
    $referFriendPage = new ReferFriendPage($profile, $account, $mailer);
    $message = "test test test";
    if ($referFriendPage->sendEmail($nama, $email, $message)) {
      $msg = "Sukses mengirimkan email!";
    } else {
      $msg = "Gagal mengirimkan email!";
    }
    $view->assign("mainContent",$view->showMessage($msg, "beranda.php"));
   // $view->assign("mainContent", $referFriendPage->showForm());
   
  }
  $view->assign("is_ppa",true);
   print $view->toString("SITTI/content.html");
} else {
  sendRedirect("index.php?login=1");
}
?>