<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIAdvertiserDashboard.php";

$view = new BasicView();
$account = new SITTIAccount(&$req);
$dashboard = new SITTIAdvertiserDashboard($req,$account);
$account->open(0);
$isLogin = $account->isLogin();
$isFirstTimer = $account->isFirstTimer();
$info = $account->getProfile();
$account->close();
if($isLogin){
	$topic = $req->getParam("t");
	//print $topic;
	switch($topic){
		case "1":
			print $dashboard->Reporting1($info);
		break;
		case "2":
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting2CSV($info);
			}else{
				print $dashboard->Reporting2($info);
			}
		break;
		case "3":
			print $dashboard->Reporting3($info);
		break;
		case "4":
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting4CSV($info);
			}else{
				print $dashboard->Reporting4($info);
			}
		break;
		case "6":
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting6CSV($info);
			}else if($req->getParam("status")=="1"){
				print $dashboard->Reporting6Status($info);
			}else{
				print $dashboard->Reporting6($info);
			}
		break;
	//tambahan tab informasi penagihan	
		case "8":
			if ($req->getParam('invoice')) {
				include_once $APP_PATH."SITTI/SITTIInvoice.php";
				
				$invoice = new SITTIInvoice(&$req);
				$invoice->Download();
				exit();
			} else {
				print $dashboard->Reporting8($info);	
			} 
		break;
		case "601":
			$id = $req->getParam("id");
			$type = $req->getParam("type");
			settype($id,'integer');
			settype($type,'integer');
			if($req->getParam("csv")=="1"){
				print $dashboard->getAdDailyReportCSV($info,$id,$type);
			}else{
				print $dashboard->getAdDailyReport($info,$id,$type);
			}
		break;
		case "101":
			print $dashboard->getCampaignDailyChartData($info);
		break;
		case "102":
			$id = $req->getParam("id");
			$n = $req->getParam("n");
			if($n==null){
				$n=5;
			}
			settype($id,'integer');
			settype($n,'integer');
			
			print $dashboard->getDailyAdChart($info,$id,$n);
		break;
		case "103":
			print $dashboard->getKeywordChart($info,$req->getParam("n"));
		break;
		case "104":
			print $dashboard->getGeoChart($info,$req->getParam("id"));
		break;
		case "99":
			print $dashboard->infoTopup($info);
		break;
		case "konversi":
			if ($req->getParam('campaign'))
			{
				$campaign_id = intval($req->getParam('campaign'));
				echo $dashboard->ReportKonversiAdvs($info, $campaign_id);
			}
			elseif ($req->getParam('iklan'))
			{
				$iklan_id = intval($req->getParam('iklan'));
				echo $dashboard->ReportKonversiTransactions($info, $iklan_id);
			}
			else
			{
				echo $dashboard->ReportKonversiCampaigns($info);	
			}
		break;
		default:
			print $view->toString("SITTI/reporting/beranda_tab1.html");
		break;
	}
	
}else{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"login.php");
}
?>