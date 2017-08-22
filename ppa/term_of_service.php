<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
//include_once $APP_PATH."MOP/MOPClient.php";
$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req);
/*
$rpc = new MOPClient(&$req,0);
$rpc->setSession($req->getParam("id"));

if($rpc->getSession()>0){
	print $view->toString("avo/index.html");
}else{
	print "Access Denied";
}
//--------------------->
 *
 */


//buat drop down static page menu
/*
$sittiHomepage->open();
GetDropDownMenu(&$req,&$sittiHomepage,&$view);
$sittiHomepage->close();
 *
 */

//end dropdown

print $view->toString("SITTI/term_of_service.html");




?>