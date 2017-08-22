
<?php
// Revision Notes
// 11/04/11 - changed post back url from https://www.paypal.com/cgi-bin/webscr to https://ipnpb.paypal.com/cgi-bin/webscr
// For more info see below:https://www.x.com/content/bulletin-ip-address-expansion-paypal-services
// "ACTION REQUIRED: if you are using IPN (Instant Payment Notification) for Order Management and your IPN listener script is behind a firewall that uses ACL (Access Control List) rules which restrict outbound traffic to a limited number of IP addresses, then you may need to do one of the following: 
// To continue posting back to https://www.paypal.com  to perform IPN validation you will need to update your firewall ACL to allow outbound access to *any* IP address for the servers that host your IPN script
// OR Alternatively, you will need to modify  your IPN script to post back IPNs to the newly created URL https://ipnpb.paypal.com using HTTPS (port 443) and update firewall ACL rules to allow outbound access to the ipnpb.paypal.com IP ranges (see end of message)."

include_once "common.php";
include_once $APP_PATH."SITTI/SITTIPaypal.php";
include_once $APP_PATH."foreignExchange/fx.php";

global $CONFIG;

$paypal = new SITTIPaypal();

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// If testing on Sandbox use:
// $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
// if not use:
// $fp = fsockopen ('ssl://ipnpb.paypal.com', 443, $errno, $errstr, 30);

$fp = fsockopen ($CONFIG['IPN_SSL_ADDRESS'], 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$transaction_code = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$transaction_id = $_POST['custom'];

if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that transaction_code has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			
			//logging
			$field = "";
			$query = "";
			foreach ($_POST as $key => $value){
				if ($field!=""){
					$field .= ",";
				}
				if ($query!=""){
					$query .= ",";
				}
				$field .= $key;
				$query .= "'".$value ."'";
			}
			$rs = $paypal->insertlogIPNData($field,$query);
			
			if ($payment_status == 'Completed'){
				$currency_to = "IDR";
				$fx = new ForeignExchange($payment_currency, $currency_to);
				$amount_idr = $fx->toForeign($payment_amount);
				
				$flag = $paypal->insertPaypal($transaction_id, $amount_idr, $transaction_code);
			}

			// $mail_From = "From: me@mybiz.com";
			// $mail_To = "deto@sitti.co.id";
			// $mail_Subject = "VERIFIED IPN";
			// $mail_Body = $req;

			// foreach ($_POST as $key => $value){
				// $emailtext .= $key . " = " .$value ."\n\n";
			// }
			
			// $emailtext .= $flag."\n\n";

			// mail($mail_To, $mail_Subject, $rs . "\n\n" . $mail_Body);

		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation

			// $mail_From = "From: me@mybiz.com";
			// $mail_To = "deto@sitti.co.id";
			// $mail_Subject = "INVALID IPN";
			// $mail_Body = $req;

			// foreach ($_POST as $key => $value){
				// $emailtext .= $key . " = " .$value ."\n\n";
			// }

			// mail($mail_To, $mail_Subject, $emailtext . "\n\n" . $mail_Body);

		}
	}
	fclose ($fp);
}
?>

