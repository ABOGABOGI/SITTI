<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getParam("sna")){
	$category_id = $req->getParam("cat_id");
	$method = $req->getParam("method");
	echo $social->fetchSNAData($category_id,$method);
}
?>