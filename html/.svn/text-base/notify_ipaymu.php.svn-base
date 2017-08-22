<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIIpaymu.php";

$ipaymu = new SITTIIpaymu();

$transaction_id = $req->getPost('product');
$transaction_code = $req->getPost('buyer');
$nominal = $req->getPost('total');

$req_str = print_r($req, true);

$ipaymu->insertPostData($req_str);
$ipaymu->insertIpaymu($transaction_id, $nominal, $transaction_code);

?>
