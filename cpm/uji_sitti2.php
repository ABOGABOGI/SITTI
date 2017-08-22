<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTITest.php";
//include_once $APP_PATH."MOP/MOPClient.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
$view = new BasicView();
$page = new SITTITest(&$req);

$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin();
$info = $account->getProfile();
$isFirstTimer = $account->isFirstTimer();
$account->close();
//Account Profile
$view->assign("info",$info);

//if($isLogin){
    if($req->getParam("advanced")=="1"){
        //uji sitti advanced
        if($req->getParam("q")!=null){
            $txt = strip_tags($req->getParam("q"));
            if(strlen($txt)<20){
                print $page->searchAdvanced($txt,$req->getParam("t"));
            }else{
            	print "Kata terkait tidak ditemukan.";
            }
            
             die();
        }else{
            $view->assign("mainContent",$page->showAdvanced());
        }
    }else{

        //uji sitti standard
        if($req->getParam("q")!=null){

           // print $req->getParam("q");
            $txt = strip_tags($req->getParam("q"));

            if(strlen($txt)<20){
              if($req->getParam("t")=="1"){
                print $page->search2($txt);
              }else{
                print $page->searchPhrase($txt);
              }
            }else{
            	print "Kata terkait tidak ditemukan.";
            }
             die();
        }else{
            $view->assign("mainContent",$page->show());
        }
    }
    //end dropdown
    $view->assign("isLogin",$isLogin);
    $view->assign("broadcast",$system->broadcast());
    $view->assign("is_cpm",true);
    print $view->toString("SITTI/content.html");
    
//}else{
	//sendRedirect("index.php?login=1");
	//die();
//}


?>