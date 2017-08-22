<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getPost("brand")){
	$brand = $req->getPost("brand");
	$name = $req->getPost("name");
	$telp = $req->getPost("telp");
	$email = $req->getPost("email");
	$message = $req->getPost("message");
	$social->insertBrandContact($brand, $name, $telp, $email, $message);
}
?>