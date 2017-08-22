<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";

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

	$custom_template = new Custom_Template(&$req,&$account);


    /*
     * AMBIL LANDING PAGE BERDASARKAN USER AKTIF DAN
     * ID LANDING PAGE TERAKHIR (DESC), YANG TELAH DI SIMPAN SEBELUMNYA
     */
    $list = $custom_template->landingView();
    $field = $custom_template->viewCustomField();
    $view->assign("list", $list);
    $view->assign("field", $field);

    /*
     * AMBIL PARAMETER URL SETELAH BUTTON PREVIEW ATAU SAVE DI KLIK
     * UNTUK PARSING KE URL MESSAGE BACK SETELAH BUTTON SAVE DI KLIK,
     * ATAU PARSING URL KE BUTTON KEMBALI
     * tujuan : untuk kembali ke halaman Edit Iklan
     */
    $view->assign("param", $_GET['param']);
    $view->assign("edit_pending","1");
    $view->assign("id_iklan", $_GET['id_iklan']);
    $view->assign("c", $_GET['c']);
    //END
    

    //-->
	//login status di embed di template content.html

	$view->assign("isLogin",$isLogin);
	print $view->toString("SITTI/custom_template/edit_layout_iklan.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>