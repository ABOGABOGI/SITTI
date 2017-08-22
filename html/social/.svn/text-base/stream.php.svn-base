<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getParam("refresh")){
	$cat_id = 0;
	if ($req->getParam("cat_id")>0){
		$cat_id = $req->getParam("cat_id");
	}
	if ($req->getParam("last_id_stream")>0){
		echo $social->fetchDataStream($cat_id,$req->getParam("last_id_stream"));
	}else{
		echo $social->fetchDataStream($cat_id);
	}
}
?>