<?php
include_once "../config/config.inc.php";
$fp = fopen("../tmp/upload.log","a+");
$str = "-----------------------\n";
foreach($_POST as $name=>$val){
	$str.=$name." --> ".$val."\n";
}
fwrite($fp,$str,strlen($str));
fclose($fp);

$uploadir = $CONFIG['BANNER_ASSET_PATH'];
if(eregi(".swf",$_FILES['asset']['name'])){
	$filename = $_REQUEST['fileID'].".swf";
}else if(eregi(".gif",$_FILES['asset']['name'])){
	$filename = $_REQUEST['fileID'].".gif";
}else{
	$filename = $_REQUEST['fileID'].".jpg";
}
$target = $uploadir.$filename;
print $target;
@move_uploaded_file($_FILES['asset']['tmp_name'],$target);
?>