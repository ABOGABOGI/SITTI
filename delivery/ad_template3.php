<html xmlns="http://www.w3.org/1999/xhtml"><head>
</head><body style="margin: 0pt; padding: 0pt;">
<div style="width: 450px; min-height: 40px; overflow: hidden; padding: 2px; display: table; border: 1px solid rgb(204, 204, 204); background-color: rgb(255, 255, 255); font-family: Arial,Helvetica,sans-serif;">
<?php for($i=0;$i<sizeof($list);$i++):?> 
    <div style="padding: 0pt 0px 0pt 0pt; display: table; width: 190px; float: left;">
    <h1 style="color: #f60; font-size: 11px; margin: 0pt; font-weight:bold;"><?=stripslashes($list[$i]['judul'])?></h1>
    <p style="color: rgb(0, 0, 0); font-size: 11px; margin: 0pt;"><?=substr(stripslashes($list[$i]['baris1']),0,34)?></p>
    <?php
if($list[$i].baris2):
?>
<p style="color:#000; font-size:11px; margin:0pt;"> <?=substr(stripslashes($list[$i]['baris2']),0,34)?> </p>
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
    <a style="color: rgb(23, 165, 217); font-size: 11px; font-family: Arial,Helvetica,sans-serif; text-decoration: none; float: left;" href="<?=$clickURL?>" target="_blank"><?=stripslashes($list[$i]['linkName'])?> </a> 
      <img src="<?=$viewLogURL?>" width='1' height='1'/>
    </div>
  
   <?php endfor;?>   
<a style="color: rgb(23, 165, 217); margin: -15px 0pt 0pt 0pt; padding-right:2px;font-size: 10px; float: right; display: table; clear: both;" href="http://www.sittibelajar.com" target="_blank">Iklan SITTI</a> 

</div>
</body></html>
