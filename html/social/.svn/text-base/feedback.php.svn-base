<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
$name = "anonymous";
if ($req->getPost("name")){
	$name = $req->getPost("name");
}
$telp = $req->getPost("telp");
$email = $req->getPost("email");
$feedback = $req->getPost("feedback");
$social->insertFeedback($name, $telp, $email, $feedback);
?>