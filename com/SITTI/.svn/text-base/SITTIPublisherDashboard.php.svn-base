<?php 
/**
 * SITTIBeranda
 * class untuk halaman Beranda.
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIApp.php";
include_once "SITTIPublisher.php";
include_once "SITTIReporting.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIBilling.php";

class SITTIPublisherDashboard extends StaticPage{
	var $Account;
    var $request;
    function SITTIPublisherDashboard($req,$account){
        parent::StaticPage(&$req);
        $this->Account = $account;
        $this->request = $req;
    }
    
    function Reporting1($user,$slotid){
    	$slotid = mysql_escape_string($slotid);
    	
    	$reporting = new SITTIReporting(null);
    	//summary
    	$rs = $reporting->getPubSlotPerformance($user['sittiID'],
    											$slotid,$this->Request->getParam("type"),
    											$this->Request->getParam("startFrom"),
    											$this->Request->getParam("endTo"));
    	$this->View->assign("pages",$rs);
    	$this->open(0);
    	$slot = $this->fetch("SELECT * FROM db_web3.sitti_deploy_setting WHERE id='".$slotid."' AND sittiID = '".$user['sittiID']."' LIMIT 1");
    	
    	$this->close();
    	
    	$this->View->assign("slotName",$slot['name']);
    	$this->View->assign("sittiID",$user['sittiID']);
    	
    	return $this->View->toString("SITTIZEN/reporting/slot_daily.html");
    }

    function ReportingMutasiKredit($from = false, $to = false)
    {
        $billing = new SITTIBilling($this->request, $this->Account);

        $sitti_id = $_SESSION['sittiID'];
        $from = (bool) $from ? $from : '2011-01-01';
        $to = (bool) $to ? $to : date("Y-m-d", strtotime("-1 day"));

        $this->View->assign("from",$from);
        $this->View->assign("to",$to);

        $result = $billing->publisherSummary($sitti_id, $from, $to);
        if (is_array($result))
        {
            $total_debit = $total_kredit = $total_saldo = 0;
            foreach ($result as $row)
            {
                $total_debit += intval($row['debit']);
                $total_kredit += intval($row['credit']);
            }
            $this->View->assign("list",$result);
            $this->View->assign("total_debit",$total_debit);
            $this->View->assign("total_credit",$total_kredit);
        }
        else
        {
            $this->View->assign("message",'Tidak ditemukan data untuk tanggal ' . $from . 'sampai dengan ' .$to);
        }

        return $this->View->toString("SITTIZEN/reporting/mutasi_kredit.html");
    }

    function ReportingSlotChart($type = false)
    {
        $reporting = new SITTIReporting(null);
        $return_data = FALSE;
        
        if ($type == 'imp')
        {
            $return_data = $reporting->getPublisherSlotTopImp($_SESSION['sittiID']);
        }
        elseif ($type == 'click')
        {
            $return_data = $reporting->getPublisherSlotTopClick($_SESSION['sittiID']);
        }
        elseif ($type == 'ctr')
        {
            $return_data = $reporting->getPublisherSlotTopCtr($_SESSION['sittiID']);
        }
        elseif ($type == 'share')
        {
            $return_data = $reporting->getPublisherSlotTopShare($_SESSION['sittiID']);
        }

        return json_encode($return_data);
    }
   
}
?>