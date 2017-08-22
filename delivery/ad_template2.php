<div style="width:610px; padding:10px; display:table; border:solid 1px #cccccc; background-color:#FFF; font-family:Arial, Helvetica, sans-serif;"> 
 <?php for($i=0;$i<sizeof($list);$i++):?>
    <div style="padding:0 10px 0 0; display:table; width:190px; float:left;"> 
    <h1 style="color:#000; font-size:14px; margin:0;"><?=stripslashes($list[$i]['judul'])?></h1> 

    <p style="color:#000; font-size:12px; margin:0;"><?=stripslashes($list[$i]['baris1'])?></p> 

<?php 
if(!eregi("http://",$list[$i]['linkURL'])){
	$list[$i]['linkURL'] = "http://".$list[$i]['linkURL'];
}
$ox_cb = substr(md5(date("YmdHis")),0,10);//OpenX random number
$ox_zone_id = $list[$i]['ox_zone_id'];
$clickURL = $OX_CONFIG['tracker_uri']."/www/delivery/ck.php?oaparams=2__bannerid=".$list[$i]['ox_banner_id']."__zoneid=".$ox_zone_id."__cb=".$ox_cb."__oadest=".urlencode(stripslashes($list[$i]['linkURL']));
//openX beacon
$viewLogURL = $OX_CONFIG['tracker_uri']."/www/delivery/lg.php?bannerid=".$list[$i]['ox_banner_id']."&campaignid=".$list[$i]['ox_campaign_id']."&zoneid=".$ox_zone_id."&loc=1&referer=".urlencode($uri)."&cb=".$ox_cb;			
?>
<img src="<?=$viewLogURL?>" width='1' height='1'/>
    <a style="color:#17A5D9; font-size:12px;  font-family:Arial, Helvetica, sans-serif; text-decoration:none;float:left;" href="<?=$clickURL?>" target="_blank"><?=stripslashes($list[$i]['linkName'])?></a> 
    </div> 
<?php endfor;?>
<a style="color:#17A5D9; font-size:10px; float:right; display:table; clear:both;" href="http://www.sittibelajar.com">ads by SITTI</a> 
</div> 