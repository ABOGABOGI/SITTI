<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIIpaymu.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";

global $CONFIG;

$ipaymu = new SITTIIpaymu();
$account = new SITTIAccount(&$req);

if ($req->getPost('topup_ipaymu')){
	$account->open(0);
	$info = $account->getProfile();
	$account->close();
	$amount = $req->getPost('topup_ipaymu');
	$transaction_id = $ipaymu->insertLookup($info['sittiID'],$amount);
	$_SESSION['transaction_id'] = $transaction_id;
}else{
	header("Location: index.php");
}

// URL Payment IPAYMU
$url = $CONFIG['PATH_URL_IPAYMU'];

// Prepare Parameters
$params = array(
            'key'      => $CONFIG['SITTI_IPAYMU_API_KEY'], // API Key SITTI
            'action'   => 'payment',
            'product'  => $transaction_id,
            'price'    => $amount, // Total Harga
            'quantity' => 1,
            'comments' => 'Pembayaran Voucher SITTI. Transaction ID: '.$transaction_id.' jumlah: '.$amount, // Optional           
            'ureturn'  => $CONFIG['PATH_RETURN_IPAYMU_RETURN'],
            'unotify'  => $CONFIG['PATH_RETURN_IPAYMU_NOTIFY'],
            'ucancel'  => $CONFIG['PATH_RETURN_IPAYMU_CANCEL'],
            'format'   => 'json' // Format: xml / json. Default: xml 
        );

$params_string = http_build_query($params);

//open connection
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST,count($params));
curl_setopt($ch, CURLOPT_POSTFIELDS,$params_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 

//execute post
$request = curl_exec($ch);
$result = json_decode($request, true);


if( isset($result['url']) )
    header('location: '. $result['url']);
else {
    $error_code = $result['Status'];
    $error_desc = $result['Keterangan'];
}

//close connection
curl_close($ch);

?>

