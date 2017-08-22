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
		case "11": // new beranda
			if ($req->getParam("campaigncount"))
			{
				echo $dashboard->Reporting1New_HistoricalCampaign(intval($req->getParam("campaigncount")));
			}
			else
			{
				echo $dashboard->Reporting1New();
			}
		break;
		case "2":
			print $dashboard->Reporting2($info);
		break;
		case "201":
			$tgl_awal = ($req->getParam('tgl_awal')) ? $req->getParam('tgl_awal') : false;
			$tgl_akhir = ($req->getParam('tgl_akhir')) ? $req->getParam('tgl_akhir') : false;
			
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting201CSV($info, $tgl_awal, $tgl_akhir);
			}else if($req->getParam("xls")=="1"){
				print $dashboard->Reporting201XLS($info, $tgl_awal, $tgl_akhir);
			}else{
				print $dashboard->Reporting201($info, $tgl_awal, $tgl_akhir);
			}
		break;
		case "3":
			print $dashboard->Reporting3($info);
		break;
		case "4":
			if ($req->getParam("c_id")){
				$c_id = $req->getParam("c_id");
			}else{
				$c_id = 'none';
			}
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting4CSV($info,$c_id);
			}else if($req->getParam("status")=="1"){
				print $dashboard->Reporting4Status($info,$c_id);
			}else{
				print $dashboard->Reporting4($info,$c_id);
			}
		break;
		case "6":
			$campaign_id = urldecode64($req->getParam("tr") == 'none' ? false : $req->getParam("tr"));
			if($req->getParam("csv")=="1"){
				//$campaign_id = $req->getParam("campaign") == 'none' ? false : $req->getParam("campaign");
				print $dashboard->Reporting6CSV($info, $campaign_id);
			}elseif($req->getParam("status")=="1"){
				print $dashboard->Reporting6Status($info, $campaign_id);
			//} elseif ($req->getParam("tr")) {
				//$campaign_id = urldecode64($req->getParam("tr") == 'none' ? false : $req->getParam("tr"));
				//
				//echo $dashboard->Reporting6_ByCampaign($campaign_id);
			}else{
				//print "no campaign";
				print $dashboard->Reporting6($info, $campaign_id);
			}
		break;
		case "602":
			$campaign_id = urldecode64($req->getParam("campaign"));
			
			$tgl_awal = ($req->getParam('tgl_awal')) ? $req->getParam('tgl_awal') : false;
			$tgl_akhir = ($req->getParam('tgl_akhir')) ? $req->getParam('tgl_akhir') : false;
			
			if($req->getParam("csv")=="1"){
				print $dashboard->Reporting602CSV($info,$campaign_id, $tgl_awal, $tgl_akhir);
			}else if($req->getParam("xls")=="1"){
				print $dashboard->Reporting602XLS($info,$campaign_id, $tgl_awal, $tgl_akhir);
			}else{
				print $dashboard->Reporting602($info,$campaign_id, $tgl_awal, $tgl_akhir);
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
			$tgl_awal = ($req->getParam('tgl_awal')) ? $req->getParam('tgl_awal') : false;
			$tgl_akhir = ($req->getParam('tgl_akhir')) ? $req->getParam('tgl_akhir') : false;

			print $dashboard->getCampaignDailyChartData($info, $tgl_awal, $tgl_akhir);
		break;
		case "102":
			$id = $req->getParam("id");
			$n = $req->getParam("n");
			if($n==null){
				$n=5;
			}
			settype($id,'integer');
			settype($n,'integer');

			$campaign_id = urldecode64($req->getParam("campaign"));
			$tgl_awal = false;
			$tgl_akhir = false;
			if ($req->getParam('tgl_awal')) $tgl_awal = $req->getParam('tgl_awal');
			if ($req->getParam('tgl_akhir')) $tgl_akhir = $req->getParam('tgl_akhir');
			
			print $dashboard->getDailyAdChart($info,$id,$n, $campaign_id, $tgl_awal, $tgl_akhir);
		break;
		case "103":
			if ($req->getParam("c_id")){
				$c_id = $req->getParam("c_id");
			}else{
				$c_id = 'none';
			}
			print $dashboard->getKeywordChart($info,$req->getParam("n"),$c_id);
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