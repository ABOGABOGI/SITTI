<?php
	include_once "common.php";
	include_once $APP_PATH."SITTI/SITTIIpaymu.php";

	$view = new BasicView();
	$ipaymu = new SITTIIpaymu();


	$transaction_id = $_SESSION['transaction_id'];
	$flag = $ipaymu->checkIpaymu($transaction_id);

	if ($flag){
		$rs = $ipaymu->get_transaction($transaction_id);
		$amount = $rs['nominal'];

		$message = "Topup menggunakan iPaymu berhasil dilakukan sebesar " .
				"Rp ".$amount." <br/> <br/> " .
				"Berikut adalah Transaction ID anda :<br/>".$transaction_id.
				"<br/><br/>Saldo anda akan terupdate sebentar lagi. " .
				"Apabila dalam waktu 30 menit belum terupdate, " .
				"silahkan email kami di support@sitti.co.id.";
		sendRedirect("pembayaran_berhasil.php?metode=ipaymu&topup=".$amount."&txn=".$transaction_id);
		die();
	}else{
		$message = "Ada masalah dengan transaksi Anda. " .
				"Silahkan menghubungi bagian support kami. " .
				"Transaction ID=".$transaction_id;
	}
	$view->assign("mainContent", $view->showMessage($message, "index.php"));

	echo $view->toString("SITTI/content4.html");
?>
