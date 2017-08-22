<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIPaypal.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";

global $CONFIG;

$paypal = new SITTIPaypal();
$account = new SITTIAccount(&$req);

if ($req->getPost('topup_paypal')){
	$account->open(0);
	$info = $account->getProfile();
	$account->close();
	$amount = $req->getPost('topup_paypal');
	$transaction_id = $paypal->insertLookup($info['sittiID'],$amount);
}else{
	header("Location: index.php");
}

?>
<p>Anda akan di redirect ke situs PayPal sesaat lagi</p>
<form action="<? echo $CONFIG['PATH_URL_PAYPAL']; ?>" method="post" id="PayPalForm">
<input type="hidden" name="item_name" value="Voucher SITTI USD">
<input type="hidden" name="amount" value="<? echo $amount;?>">
<input type="hidden" name="item_number" value="SITTIUSD">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="business" value="<? echo $CONFIG['SITTI_PAYPAL_CREDENTIAL'];?>">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="custom" value="<? echo $transaction_id; ?>">
<input type="hidden" name="return" value="<? echo $CONFIG['PATH_RETURN_PAYPAL_CONFIRM']; ?>">
<input type="hidden" name="cancel_return" value="<? echo $CONFIG['PATH_RETURN_PAYPAL_CANCEL']; ?>">
<input type="hidden" name="rm" value="2">
</form>

<script type="text/javascript">
document.getElementById("PayPalForm").submit();
</script>