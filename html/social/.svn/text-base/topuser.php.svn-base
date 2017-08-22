<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$social = new SITTISocial($req);
if ($req->getParam("topuser")){
	$category_id = $req->getParam("cat_id");
	if ($req->getParam("brand")){
		echo $social->fetchTopUser($category_id, $req->getParam("brand"));
	}else{
		echo $social->fetchTopUser($category_id);
	}
}
?>