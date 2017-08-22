<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getPost("category")){
	$category = $req->getPost("category");
	$name = $req->getPost("name");
	$telp = $req->getPost("telp");
	$email = $req->getPost("email");
	$message = $req->getPost("message");
	$social->insertCategoryContact($category, $name, $telp, $email, $message);
}
?>