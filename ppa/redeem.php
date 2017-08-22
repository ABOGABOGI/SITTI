<?php
include_once 'common.php';
include_once $ENGINE_PATH . 'Utility/SessionManager.php';
include_once $APP_PATH . 'SITTI/SITTIAccount.php';
include_once $APP_PATH . 'SITTI/SITTIApp.php';


$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$profile = $account->getProfile();
$account->close();
$app = new SITTIApp($req, $account);
if ($isLogin) {
   if($req->getParam("confirm")=="1"){
	 $app->open(0);
   	 $sql = "INSERT INTO db_web3.tbl_redeem
   		  	(sittiID,redeem_date) VALUES('".$profile['sittiID']."',NOW())";
   	 $rs = $app->query($sql);
     $app->close();
   }
   $app->open(0);
   $sql = "SELECT COUNT(sittiID) as total 
   		  FROM db_web3.tbl_redeem 
   		  WHERE sittiID = '".$profile['sittiID']."' 
   		  LIMIT 1";
   $rs = $app->fetch($sql);
   $app->close();
   if($rs['total']==1){
   	sendRedirect("beranda.php");
   	die();
   }else{
   	print $view->toString("SITTI/info.html");
   }
} else {
  sendRedirect("index.php?login=1");
}
?>