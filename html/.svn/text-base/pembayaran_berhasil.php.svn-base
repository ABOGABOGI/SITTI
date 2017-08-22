<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";

$view = new BasicView();

$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$account->close();

$view->assign("info",$info);
$saldo = $account->getSaldo($info['sittiID']);

if($isLogin){
	$view->assign("isLogin",$isLogin);
	$view->assign("SALDO",$saldo['budget']);
	
	$metode = '';
	$topupval = '';
	$kode_transaksi = '';
	if($req->getPost("metode")){
		$metode = $req->getPost("metode");
	}
	if($req->getPost("topupval")){
		$topupval = $req->getPost("topupval");
	}
	if($req->getPost("kode_transaksi")){
		$kode_transaksi = $req->getPost("kode_transaksi");
	}
	if ($metode!='' && $topupval!=''){
		$view->assign("metode",$metode);
		$view->assign("topupval",$topupval);
		$view->assign("kode_transaksi",$kode_transaksi);
		$view->assign("mainContent",$view->toString("SITTI/pembayaran_berhasil.html"));
	}
	if($req->getParam("metode")=='paypal' || $req->getParam("metode")=='ipaymu'){
		$view->assign("metode",$req->getParam("metode"));
		$view->assign("topupval",$req->getParam("topup"));
		$view->assign("kode_transaksi",$req->getParam("txn"));
		$view->assign("mainContent",$view->toString("SITTI/pembayaran_berhasil.html"));
	}
	echo $view->toString("SITTI/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>