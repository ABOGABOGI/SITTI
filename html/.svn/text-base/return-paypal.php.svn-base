<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."SITTI/SITTIPaypal.php";
include_once $APP_PATH."foreignExchange/fx.php";

$view = new BasicView();
$paypal = new SITTIPaypal();

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
	
	$payment_status = $req->getPost('payment_status');

	if ($payment_status == 'Completed'){
		$transaction_code = $req->getPost("txn_id");
		$payer_name = $req->getPost("first_name")." ".$req->getPost("last_name");
		$amount = $req->getPost("payment_gross");
		$currency_forpay = $req->getPost('mc_currency');
		$currency_to = "IDR";
		$fx = new ForeignExchange($currency_forpay, $currency_to);
		$amount_idr = $fx->toForeign($amount);
		$transaction_id = $req->getPost('custom');
		
		$flag = $paypal->insertPaypal($transaction_id, $amount_idr, $transaction_code);
		if ($flag){
			$message = "Topup menggunakan PayPal berhasil dilakukan sebesar USD ".$amount." <br/> atau setara dengan IDR ".$amount_idr.". <br/><br/> Berikut adalah Transaction ID anda :<br/>".$transaction_id."<br/><br/>Saldo anda akan terupdate sebentar lagi. Apabila dalam waktu 30 menit belum terupdate, silahkan email kami di support@sitti.co.id";
			sendRedirect("pembayaran_berhasil.php?metode=paypal&topup=".$amount_idr."&txn=".$transaction_id);
			die();
		}else{
			$message = "Ada masalah dengan transaksi Anda. Silahkan menghubungi bagian support kami. Transaction ID=".$transaction_id;
		}
		$view->assign("mainContent", $view->showMessage($message, "index.php"));
	}else{
		if ($req->getPost("txn_id")){
			$transaction_code = $req->getPost("txn_id");
			$message = "Topup menggunakan PayPal tidak berhasil. Transaction ID=".$transaction_code;
			$view->assign("mainContent", $view->showMessage($message, "pembayaran.php"));
		}else{
			$message = "Anda mengakses halaman yang tidak valid.";
			$view->assign("mainContent", $view->showMessage($message, "index.php"));
		}
	}
	echo $view->toString("SITTI/content.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>
