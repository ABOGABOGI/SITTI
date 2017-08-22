<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getParam("stats")){
	$category_id = $req->getParam("cat_id");
	if ($req->getParam("brand")){
		echo $social->fetchStatsData($category_id, $req->getParam("brand"));
	}else{
		echo $social->fetchStatsData($category_id);
	}
}
?>