<?php
class SITTIInvoice extends SQLData{
	
	function __construct($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
	}

	/*function InvoicePage(){
		$id = $this->Request->getParam("id");
		$bulan = $this->Request->getParam("bulan");
		$tahun = $this->Request->getParam("tahun");
		$st = $this->Request->getParam("st");
		if($st==null){$st=0;}
		if($this->isNumber($bulan)&&$this->isNumber($tahun)&&$this->isNumber($st)){
			$list = $this->invoice_data($id,$bulan,$tahun,$st,100);
			$this->assign("list",$list);
			$this->assign("search","1");
		}
		return $this->View->toString("Billing/invoice_list.html");
	}*/
	
	function Download(){
		global $ENGINE_PATH;
		include_once $ENGINE_PATH."Utility/html2fpdf/html2fpdf.php";
		include_once $ENGINE_PATH."Utility/NumbersWordsUtil.php";
		
		$n2words = new NumbersWordsUtil();
		//include_once $ENGINE_PATH."Utility/dompdf/dompdf_config.inc.php";
		$a_bln = array(null,"Januari","Februari","Maret","April",
		
						"Mei","Juni","Juli","Agustus","September",
						"Oktober","November","Desember");
		
		$id = $this->Request->getParam("id");

		if ($this->Request->getParam('bulantahun')) {
			$bulantahun = $this->Request->getParam('bulantahun');
			$arr_bulantahun = explode('-',$bulantahun);
			$bulan_tahun_query = " AND a.bulan = '" . $arr_bulantahun[0] . "' AND a.tahun = '" . $arr_bulantahun[1] . "' ";
		}

		if ($this->Request->getParam('campaign')) {
			$campaign_id = $this->Request->getParam('campaign');
			$campaign_query = " AND a.campaign_id = '" . $campaign_id . "' ";
		}

		//if($this->isNumber($id)){
			$sql = "SELECT a.*,b.username,c.name as campaign_name
					FROM db_report.advertiser_invoice a, db_web3.sitti_account b,db_web3.sitti_campaign c
					WHERE a.advertiser_id = '".$id."' 
					AND a.advertiser_id = b.sittiID 
					AND b.sittiID = c.sittiID 
					AND a.campaign_id = c.ox_campaign_id ". $bulan_tahun_query . $campaign_query . " LIMIT 1";
			
			//echo $sql;
			$this->open(2);
			$rs = $this->fetch($sql);
			//account profile
			$sql2 = "SELECT b.*,b.name as up_name FROM db_web3.sitti_account a, db_web3.sitti_account_profile b 
					WHERE a.sittiID = '".$rs['advertiser_id']."' AND b.user_id = a.id";
			//business profile
			$sql3 = "SELECT b.* FROM db_web3.sitti_account a, db_web3.sitti_business_profile b 
					WHERE a.sittiID = '".$rs['advertiser_id']."' AND b.user_id = a.id";
			$rs2 = $this->fetch($sql2);
			$rs3 = $this->fetch($sql3);
			
			$this->close();
			if(is_array($rs2)){
				$rs = array_merge($rs,$rs2);
			}
			if(is_array($rs3)){
				$rs = array_merge($rs,$rs3);
			}
			$rs['harga_total'] = $rs['harga']+$rs['ppn'];
			$rs['bln'] = $a_bln[$rs['bulan']];
			$rs['generate_date'] = date("d/m/Y");
			$rs['terbilang'] = $n2words->toWords($rs['harga_total']);
			
			$this->View->assign("rs",$rs);
			
			$str = $this->View->toString("Billing/invoice_template3.html");
			
			//HTML2PDF Procedures
			$pdf = new HTML2FPDF();
			$pdf->AddPage();
			$pdf->WriteHTML($str);
			$pdf->Output("sitti_sys_".$rs['bulan']."_".$rs['tahun'].".pdf","D");
			
			/*
			$dompdf = new DOMPDF();
			//$dompdf->set_paper('A4');
			$dompdf->load_html($str);
			$dompdf->render();
			$dompdf->stream("sitti_sys_".$rs['bulan']."_".$rs['tahun'].".pdf");
			*/
			die();
			
			//return $str;
		//}
		
	}
	
	//helpers
	function invoice_data($sitti_id,$bulan=1,$tahun=2011,$start=0,$total=100){
		$a_bln = array(null,"Januari","Februari","Maret","April",
		
						"Mei","Juni","Juli","Agustus","September",
						"Oktober","November","Desember");
		if($sitti_id!=NULL&&eregi("([A-C0-9a-c]+)",$sitti_id)){
			$sql = "SELECT a.*,b.username,c.name 
					FROM db_report.advertiser_invoice a, db_web3.sitti_account b,db_web3.sitti_campaign c
					WHERE a.advertiser_id = '".$sitti_id."' 
					AND a.tahun = ".$tahun." AND a.bulan=".$bulan."
					AND a.advertiser_id = b.sittiID 
					AND b.sittiID = c.sittiID 
					AND a.campaign_id = c.ox_campaign_id LIMIT ".$start.",".$total;
		
			$sql2 = "SELECT COUNT(a.id) as total
				FROM db_report.advertiser_invoice a, db_web3.sitti_account b,db_web3.sitti_campaign c
				WHERE a.advertiser_id = '".$sitti_id."' 
				AND a.tahun = ".$tahun." AND a.bulan=".$bulan."
				AND a.advertiser_id = b.sittiID 
				AND b.sittiID = c.sittiID 
				AND a.campaign_id = c.ox_campaign_id";
		}else{
			
			$sql = "SELECT a.*,b.username,c.name 
					FROM db_report.advertiser_invoice a, db_web3.sitti_account b,db_web3.sitti_campaign c
					WHERE a.tahun = ".$tahun." AND a.bulan=".$bulan."
					AND a.advertiser_id = b.sittiID 
					AND b.sittiID = c.sittiID 
					AND a.campaign_id = c.ox_campaign_id LIMIT ".$start.",".$total;
		
			$sql2 = "SELECT COUNT(a.id) as total
				FROM db_report.advertiser_invoice a, db_web3.sitti_account b,db_web3.sitti_campaign c
				WHERE a.tahun = ".$tahun." AND a.bulan=".$bulan."
				AND a.advertiser_id = b.sittiID 
				AND b.sittiID = c.sittiID 
				AND a.campaign_id = c.ox_campaign_id";
		}
		$this->open(2);
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		$this->close();
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+$start+1;
			$rs[$i]['bulan'] = $a_bln[$rs[$i]['bulan']];
		}
		
		$this->getPaging('paging',$start,$total,$rows['total'],"?s=invoice&id=".$sitti_id."&bulan=".$bulan."&tahun=".$tahun);
		return $rs;
	}
	
}
?>