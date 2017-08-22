<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIPublisherDashboard.php";


$view = new BasicView();
$account = new SITTIAccount(&$req);
$dashboard = new SITTIPublisherDashboard($req,$account);
$account->open(0);
$isLogin = $account->isLogin(1);
$isFirstTimer = $account->isFirstTimer();
$info = $account->getPublisherProfile();
$account->close();
if($isLogin){
	$topic = $req->getParam("t");
	
	$type = mysql_escape_string($req->getParam("type"));
	//print $topic;
	switch($topic){
		case "1":
			if ($req->getParam("slotchart"))
			{
				$data_type = $req->getParam("jenis");
				echo $dashboard->ReportingSlotChart($data_type);	
			}
			else
			{
				$id = $req->getParam("id");
				settype($id,'integer');
				settype($type,'integer');
				print $dashboard->Reporting1($info,$id,$type);
			}
		break;
		case "4":
			$from = $req->getParam("from") != '' ? $req->getParam("from") : false;
            $to = $req->getParam("to") != '' ? $req->getParam("to") : false;
			echo $dashboard->ReportingMutasiKredit($from, $to);
		break;
		
	}
	
}else{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"index.php");
}
?>