<?php
// error_reporting(0);
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIseo.php";

$flag = 0;
$seo = new SITTIseo();
$view = new BasicView();

if ($req->getPost('daftar')){
	$flag = $seo->insertParticipant($req->getPost('name'),$req->getPost('email'),$req->getPost('url'),$req->getPost('kontak'),$req->getPost('kodepos'));
	if ($flag==null){
		$flag=2;
	}
}
if ($flag==1){
	$view->assign("msg","Anda berhasil mendaftar!");
}else if ($flag==2){
	$view->assign("msg","Pendaftaran tidak berhasil, website Anda sudah terdaftar!");
}
$rs = $seo->getRecentParticipant();
$website = "";
for($i=0;$i<sizeof($rs);$i++){
	$website .= "<li>".$rs[$i]['website']."</li>";
}
$view->assign("website",$website);
// print_r ($website);
print $view->toString("SITTI/seo/daftar.html");
?>