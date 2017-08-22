<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($isLogin){
	//Account Profile
    $view->assign("info",$info);

    // ambil parameter text dari
    $_SESSION['campaignSelected'] = mysql_escape_string($_GET['campaign']);
    $_SESSION['nama_iklan'] = mysql_escape_string($_GET['n']);
    $_SESSION['judulIklan'] = mysql_escape_string($_GET['j']);
    $_SESSION['baris1'] = mysql_escape_string($_GET['b1']);
    $_SESSION['baris2'] = mysql_escape_string($_GET['b2']);

    if($_GET['landing']=='1'){ //kalau SITTI Landing Page di pilih
        $_SESSION['landingSelected'] = mysql_escape_string($_GET['landing']);
    }
    
    if($_GET['url']=='kembali'){ //kalau tombol 'kembali' di klik(tidak jadi bikin landing page
        $_SESSION['landingSelected']="";
        
        //print_r($_SESSION['landingSelected']."petunjuk");
        sendRedirect("beranda.php?buat_iklan=1");
    }
    
    //print_r($_SESSION)."<br>";
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);
    //print $view->toString("SITTI/content.html");
	print $view->toString("SITTI/custom_template/petunjuk.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>