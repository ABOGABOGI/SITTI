<div style="width:90%px; padding:10px; display:table; border:solid 1px #cccccc; background-color:#FFF; font-family:Arial, Helvetica, sans-serif;">
<?php for($i=0;$i<sizeof($list);$i++):?>
<h1 style="color:#000; font-size:14px; margin:0;"><?=stripslashes($list[$i]['judul'])?></h1>
<p style="color:#000; font-size:12px; margin:0;"> <?=stripslashes($list[$i]['baris1'])?> </p>
<?php
if($list[$i].baris2):
?>
<p style="color:#000; font-size:12px; margin:0;"> <?=stripslashes($list[$i]['baris2'])?> </p>
<?php
endif;
?>
 <?php 
if(!eregi("http://",$list[$i]['linkURL'])){
	$list[$i]['linkURL'] = "http://".$list[$i]['linkURL'];
}
$ox_cb = substr(md5(date("YmdHis")),0,10);//OpenX random number
$clickURL = $PS_CONFIG['tracker_uri']."/ck.php?a=".$list[$i]['id']."&p=".$publisherID."&dest=".urlencode($list[$i]['linkURL'])."&r=".$ox_cb;
//PS beacon
$viewLogURL = $PS_CONFIG['tracker_uri']."/view.php?a=".$list[$i]['id']."&p=".$publisherID."&r=".$ox_cb;			
?>
<img src="<?=$viewLogURL?>" width='1' height='1'/>
<div style="display:table; width:100%; clear:both; margin-bottom:20px;"> 
<a style="color:#17A5D9; font-size:12px;  font-family:Arial, Helvetica, sans-serif; text-decoration:none;float:left;" href="<?=$clickURL?>" target="_blank"><?=stripslashes($list[$i]['linkName']	)?></a> 
</div>
<?php endfor;?>
<a style="color:#17A5D9; font-size:10px; float:right; display:table; margin-top:10px;" href="http://www.sittibelajar.com" target="_blank">ads by SITTI</a> 
</div>