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

    
    $list = $custom_template->landingView();
    $field = $custom_template->viewCustomField();
    //print_r($field);
    $view->assign("list", $list);
    $view->assign("field", $field);
    //-->
	//login status di embed di template content.html
	$view->assign("isLogin",$isLogin);

    //get param url
    $view->assign("edit_layout", $_GET['param']);
    $view->assign("id_iklan", $_GET['id_iklan']);
    $view->assign("c", $_GET['c']);
    //end
    
	print $view->toString("SITTI/custom_template/edit_layout_iklan.html");
}else{
	sendRedirect("index.php?login=1");
	die();
}
?>