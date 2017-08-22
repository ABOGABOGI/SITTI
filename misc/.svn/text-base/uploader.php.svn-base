<?php
include_once "../config/config.inc.php";
$fp = fopen("text.txt","a+");
$str = "-----------------------\n";
foreach($_POST as $name=>$val){
	$str.=$name." --> ".$val."\n";
}
fwrite($fp,$str,strlen($str));
fclose($fp);

$uploadir = $CONFIG['BANNER_ASSET_PATH'];
$filename = $_REQUEST['fileID'].".jpg";
$target = $uploadir.$filename;
@move_uploaded_file($_FILES['asset']['tmp_name'],$target);
?>