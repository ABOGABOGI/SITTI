<?php
include_once "SITTI_OX_RPC.php";
$ox = new SITTI_OX_RPC("admin","admin");
$ox->linkBannerToZone(21,10);
$ox->linkCampaignToZone(21,4);
//print $ox->getStatus();
?>