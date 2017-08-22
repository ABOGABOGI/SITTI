<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";

$view = new BasicView();
$customTemplate = new Custom_Template($req);


if($req->getParam("send")=='1'){
    $id = $req->getParam("id");//mengambil id yang di definisikan di url
    if($id){
        $customTemplate->sendTo($flag='0', $id); //flag=0-->buat visitor submit form landing page
    }



}else{
    $id = $req->getParam("id");
    $list = $customTemplate->landingView();

    $field =$customTemplate->viewCustomField();
    //print_r($field);

    $view->assign("layout", $list);
    $view->assign("field", $field);

    $view->assign("id", $id);
    //login status di embed di template content.html
    $view->assign("isLogin",$isLogin);
    //print $view->toString("SITTI/content.html");
    print $view->toString("SITTI/custom_template/landing_user.html");
}
//print_r($list['custom_field'][0]);


?>