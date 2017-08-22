<?php
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIDelivery.php";

$delivery = new SITTIDelivery(&$req);
$delivery->setPublisherId($req->getParam("p"));
$delivery->setRefererURL($req->getParam("r"));
$delivery->show();
?>