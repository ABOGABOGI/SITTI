<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTICampaign.php";
include_once $APP_PATH."SITTI/SITTIInventory.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";

class NurbayaAPI extends SITTIApp{
	var $_logger = "../logs/NurbayaAPI.log";
	var $nurbaya_api_key = "8b4423c11538eae42d0a55d562e24c74";
	var $pass = "sittinurbaya";
	var $rate_limit = 5000;
	var $params;
	
	function __construct($req,$acc){
		parent::SITTIApp($req,$acc);
		$this->params = $this->GetParams($this->g('p'));
	}
	
	function GetParams($chipertext){
		$params = array();
		$plaintext = decrypt_parameter($chipertext);
		$items = explode("&",$plaintext);
		foreach ($items as $item){
			$param = explode("=",$item);
			$params[$param[0]] = $param[1];
		}
		return $params;
	}
	
	function authenticated(){
		$flag = 0;
		$message = "";
		//validate request parameter
		if ($this->g('key')&&$this->params['email']&&$this->params['t']){
			//validate api key
			$api_key = $this->g('key');
			if (strlen($api_key)==32 && $api_key==$this->nurbaya_api_key){
				//check rate limit
				if ($this->getAccessRate($api_key)<$this->rate_limit){
					//under rate limit
					$unix = $this->params['t'];
					$unix_now = time();
					if($unix_now<=$unix+60){
						// request valid
						$message = "Authentication Success!";
						$flag = 1;
					}else{
						// request expired
						$message = "Authentication Failed, Request Expired.";
						$flag = 0;
					}
				}else{
					//over rate limit
					$message = "Authentication Failed, Rate Limit Reached.";
					$flag = 0;
				}
			}else{
				//api key not valid
				$message = "Authentication Failed, API Key Not Valid.";
				$flag = 0;
			}
		}else{
			//request parameter not valid
			$message = "Authentication Failed, API Parameter Not Found";
			$flag = 0;
		}
		return array($flag,$message);
	}
	
	function getAccessRate($api_key){
		$this->open();
		$rs = $this->fetch("SELECT rate FROM zz_sitti.api_rate_monitor WHERE api_key='".$api_key."'");
		$this->close();
		return $rs['rate'];
	}
	
	function updateAccessRate($api_key){
		$this->open();
		$rs = $this->query("UPDATE zz_sitti.api_rate_monitor SET rate = rate+1 WHERE api_key='".$api_key."'");
		$this->close();
	}
	
	function checkEmailExistence($email){
		$this->Account->open();
		$is_email_exist = $this->Account->isEmailExist("", $email);
		$this->Account->close();
		return $is_email_exist;
	}
	
	function getSITTIIDFromEmail($email){
		$this->Account->open();
		$rs = $this->Account->getCredentialByEmail($email);
		$this->Account->close();
		return $rs['sittiID'];
	}
	
	////
	// RegisterAdvertiser
	////
	function RegisterAdvertiser(){
		$response = $this->authenticated();
		if($response[0]){
			$arr = $this->run_RegisterAdvertiser();
			$this->updateAccessRate($this->g('key'));
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_RegisterAdvertiser(){
		$email = $this->params['email'];
		$is_email_exist = $this->checkEmailExistence($email);
		if (!$is_email_exist){
			$nama = $this->g('nama');
			$flag = $this->addAccount($email,$nama);
			if ($flag){
				$sittiID = $this->getSITTIIDFromEmail($email);
				// action log advertiser account create (101)
				$this->Account->ActionLog->actionLog(101,$sittiID,$email);
				$arr = array("status"=>"1","message"=>"Register Advertiser Success!");
			}else{
				$arr = array("status"=>"0","message"=>"Register Advertiser Failed, Unable to Create Account.");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Register Advertiser Failed, Email Already Exist.");
		}
		return $arr;
	}
	
	function addAccount($email,$nama){
		$flag = false;
		
		$this->Account->open();
		$fix_pass = md5($this->pass);
		$enc = md5($email.$fix_pass);
		
		$insert = $this->Account->create('3',$email, $fix_pass, $enc);
		if($insert){
			$user_id = mysql_insert_id();
			if($user_id){
				$insertToProfile = $this->Account->createProfile($user_id, $nama, $email, $alamat, $komplek, $blok, $province, $city, $telepon, $handphone,$twitter);
				
				$sittiID = $this->Account->getSITTIID($user_id);
				
				$insertPPCUser = $this->Account->createCPCProfile($sittiID);
				
				if($insertToProfile && $insertPPCUser){
					$this->Account->createBusinessProfile($user_id, $prefixCompany, $getcompany, $brand, $city_industry);
					
					$flag = $this->Account->createBillingProfile($user_id, $alamat, '99', '99');
					
					if($flag){
						//email notifikasi pendaftaran
						$smtp = new SITTIMailer();
						$smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
						$smtp->setRecipient($email);
						$this->View->assign("nama", $nama);
						$this->View->assign("email", $email);
						$this->View->assign("password", $this->pass);
						$smtp->setMessage($this->View->toString("SITTI/email/notifikasi_pendaftaran_api.html"));
						$smtp->send();
						
						$flag = $this->Account->autoConfirmAdvertiser($sittiID,$email);
						
						// print $this->View->toString("SITTI/email/notifikasi_pendaftaran_api.html");
						// die();
					}
				}
				
				
			}
		}
		$this->Account->close();
		
		return $flag;
	}
	////
	
	////
	// CreateAd
	////
	function CreateAd(){
		$response = $this->authenticated();
		if($response[0]){
			$arr = $this->run_CreateAd();
			$this->updateAccessRate($this->g('key'));
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_CreateAd(){
		$email = $this->params['email'];
		$is_email_exist = $this->checkEmailExistence($email);
		if ($is_email_exist){
			$sittiID = $this->getSITTIIDFromEmail($email);
			$nama = $this->g('nama');
			$deskripsi = $this->g('deskripsi');
			$budget = $this->g('budget');
			$url = $this->g('url');
			$tgl_mulai = $this->g('tgl_mulai');
			$tgl_berakhir = $this->g('tgl_berakhir');
			$ox_advertiser_id = $this->g('ox_advertiser_id');
			$retval = $this->addCampaign($sittiID,$nama,$tgl_mulai,$tgl_berakhir,$deskripsi,$ox_advertiser_id);
			$flag = $retval[0];
			if ($flag){
				$campaign_id = $retval[1];
				$durasi = $this->dateDifference($tgl_mulai,$tgl_berakhir)+1;
				$keywords = $this->getKeywords(strtolower($deskripsi),10);
				$flag = $this->addAd($sittiID,$nama,$keywords,$budget,$url,$campaign_id,$durasi);
				if ($flag){
					$arr = array("status"=>"1","message"=>"Create Ad Success!");
				}else{
					$arr = array("status"=>"0","message"=>"Create Ad Failed, Unable to Create Ad.");
				}
				
			}else{
				$arr = array("status"=>"0","message"=>"Create Ad Failed, Unable to Create Campaign.");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Create Ad Failed, Email Not Exist.");
		}
		return $arr;
	}
	
	function addCampaign($sittiID,$campaign_name,$tgl_mulai,$tgl_berakhir,$deskripsi,$ox_advertiser_id){
		$campaign = new SITTICampaign();
		
		$campaign_id = "";
		$flag = $campaign->addCampaign($sittiID,
								  mysql_escape_string($campaign_name),
								  $tgl_mulai,
								  $tgl_berakhir,
								  mysql_escape_string($deskripsi),
								  $ox_advertiser_id);
		if($flag){
			// masukan data campaign baru ke database reporting
			$campaign->addCampaignToReporting($sittiID, $campaign->lastInsertId, mysql_escape_string($campaign_name));
			// action log create advertiser campaign (111)
			$this->open(0);
			$rs = $this->fetch("SELECT ox_campaign_id FROM sitti_campaign ORDER BY ox_campaign_id DESC LIMIT 1");
			$this->close();
			$campaign_id = $rs['ox_campaign_id'];
			$this->Account->ActionLog->actionLog(111,$sittiID,$campaign_id);
		}
		
		return array((int)$flag,$campaign_id);
	}
	
	function dateDifference($start, $end){
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
	}
	
	function getKeywords($string,$n){
		$keywords = explode(" ",$string);
		$keywords = array_unique($keywords);
		if (sizeof($keywords)>$n){
			$keywords = array_slice($keywords,0,$n);
		}
		return $keywords;
	}
	
	function addAd($sittiID,$nama,$keywords,$budget,$url,$campaign_id,$durasi){
		global $CONFIG;
		
		// echo $sittiID."</br>";
		// echo $nama."</br>";
		// print_r ($keywords);
		// echo "</br>";
		// echo $budget."</br>";
		// echo $campaign_id."</br>";
		// echo $durasi."</br>";
		
		$inventory = new SITTIInventory();
		
		$inventory->open();
		$rs = $inventory->createAd($sittiID,
					mysql_escape_string($nama),
					mysql_escape_string($nama),
					mysql_escape_string($iklan->category),
					mysql_escape_string($nama),
					mysql_escape_string($nama),
					mysql_escape_string($url),
					mysql_escape_string($url),
					mysql_escape_string($campaign_id),
					mysql_escape_string($iklan->target_market));
		
		$iklan_id = $inventory->last_insert_id;
		
		//Geo location restrictions
    	$locations = array(array("kota"=>"ALL","priority"=>"0"));
		$inventory->addLocation($iklan_id,$locations);
		$inventory->close();
		
		$keywords_str = "";
		if($rs){
			//kalau berhasil, tambahkan daftar keyword iklan
			$list = $keywords;
			$n = sizeof($list);
			$budget_keyword = floor($budget/$n);
			$budget_keyword_daily = floor($budget_keyword/$durasi);
			$bid = $CONFIG['MINIMUM_BID'];
			
			for($i=0;$i<$n;$i++){
				$inventory->open();
				$inventory->addKeyword($iklan_id,
											  mysql_escape_string($list[$i]),
											  mysql_escape_string($bid),
											  mysql_escape_string($budget_keyword_daily),
											  mysql_escape_string($budget_keyword));
				$inventory->close();
				if ($keywords_str!=""){
					$keywords_str.=",";
				}
				$keywords_str.=$list[$i];
				
				// action log add keyword to ad (131)
				// $this->ActionLog->actionLog(131, $iklan_id, mysql_escape_string($list[$i]['name']),mysql_escape_string($list[$i]['bid']));
				// replaced with 3 action log (134,135,136)
				$this->Account->ActionLog->actionLog(134, $iklan_id, mysql_escape_string($list[$i]),mysql_escape_string($bid));
				$this->Account->ActionLog->actionLog(135, $iklan_id, mysql_escape_string($list[$i]),mysql_escape_string($budget_keyword_daily));
				$this->Account->ActionLog->actionLog(136, $iklan_id, mysql_escape_string($list[$i]),mysql_escape_string($budget_keyword));
			}
			// action log create ad (121)
			$this->Account->ActionLog->actionLog(121,$sittiID,$iklan_id);
		}
		
		if($rs){
    		//insert data kosong ke table reporting
    		$sql = "INSERT INTO db_report.tbl_performa_iklan_total
    				(advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
    				jum_imp, jum_klik, ctr, harga, 
    				budget_harian, budget_total, last_update)
    				VALUES ('".$sittiID."',".$iklan_id.",'".mysql_escape_string($nama)."','".$keywords_str."',0,
    				0,0,'0.000','0','0','0',NOW())";
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
			$sql = "INSERT INTO db_report.tbl_performa_iklan
    				(datee,advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
    				jum_imp, jum_klik, ctr, avg_cpm, harga, posisi,
    				budget_harian, budget_total, budget_sisa, last_update)
    				VALUES (DATE(NOW()),'".$sittiID."',".$iklan_id.",'".mysql_escape_string($nama)."','".$keywords_str."',0,
    				0,0,'0.0000','0.0000','0','0.00','0','0','0',NOW())";
    		//print $sql;
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
    	}
		
		return true;
	}
	////
	
	////
	// GetReport
	////
	function GetReport(){
		$response = $this->authenticated();
		if($response[0]){
			$arr = $this->run_GetReport();
			$this->updateAccessRate($this->g('key'));
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_GetReport(){
		$email = $this->params['email'];
		$is_email_exist = $this->checkEmailExistence($email);
		if ($is_email_exist){
			$sittiID = $this->getSITTIIDFromEmail($email);
			$tipe = $this->g('tipe');
			$tgl_awal = $this->g('tgl_awal');
			$tgl_akhir = $this->g('tgl_akhir');
			$retval = $this->retrieveReport($sittiID,$tipe,$tgl_awal,$tgl_akhir);
			$flag = $retval[0];
			if ($flag){
				$report = $retval[1];
				$arr = array("status"=>"1","message"=>$report);
			}else{
				$arr = array("status"=>"0","message"=>"Get Report Failed, Unable to Retrieve Report.");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Get Report Failed, Email Not Exist.");
		}
		return $arr;
	}
	
	function retrieveReport($sittiID,$tipe,$tgl_awal,$tgl_akhir){
		if ($tipe==1){
			return $this->accountReport($sittiID,$tgl_awal,$tgl_akhir);
		}else if ($tipe==2){
			
		}else if ($tipe==3){
			
		}
	}
	
	function accountReport($sittiID,$tgl_awal,$tgl_akhir){
		$query = "";
		$this->Account->open(2);
		if ($tgl_akhir){
			$query = "SELECT SUM(jum_imp) imps, SUM(jum_hit) clicks, SUM(jum_hit)*100/SUM(jum_imp) ctrs 
			FROM db_web3.sitti_ad_inventory A
			INNER JOIN db_report_raw.ps_report_daily B
			ON A.id = B.id_iklan
			WHERE B.datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
			AND A.advertiser_id = '".$sittiID."'";
		}else{
			$query = "SELECT jum_imp, jum_hit, ctr 
			FROM db_web3.sitti_ad_inventory A
			INNER JOIN db_report_raw.ps_report_daily B
			ON A.id = B.id_iklan
			WHERE B.datee = '".$tgl_awal."'
			AND A.advertiser_id ='".$sittiID."'";
		}
		//echo $query;
		$rs = $this->Account->fetch($query);
		$this->Account->close();
		if ($rs){
			foreach ($rs as $key=>$value){
				if ($value==null){
					$rs[$key]="0";
				}
			}
			return array(1,$rs);
		}else{
			if (!$tgl_akhir){
				return array(1,array("imps"=>"0","clicks"=>"0","ctrs"=>"0"));
			}else{
				return array(0,array());
			}
		}
	}
	////
	
	////
	// GetCurrentSaldo
	////
	function GetCurrentSaldo(){
		$response = $this->authenticated();
		if($response[0]){
			$arr = $this->run_GetCurrentSaldo();
			$this->updateAccessRate($this->g('key'));
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_GetCurrentSaldo(){
		$email = $this->params['email'];
		$is_email_exist = $this->checkEmailExistence($email);
		if ($is_email_exist){
			$sittiID = $this->getSITTIIDFromEmail($email);
			$retval = $this->getLastSaldo($sittiID);
			$flag = $retval[0];
			if ($flag){
				$saldo = $retval[1];
				$arr = array("status"=>"1","message"=>$saldo);
			}else{
				$arr = array("status"=>"0","message"=>"Get Current Saldo Failed, Unable to Get Saldo.");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Get Current Saldo Failed, Email Not Exist.");
		}
		return $arr;
	}
	
	function getLastSaldo($sittiID){
		$this->Account->open(4);
		$query = "SELECT saldo
		FROM db_billing_report.adv_detail
		WHERE sitti_id = '".$sittiID."'
		ORDER BY row_id DESC
		LIMIT 1;";
		//echo $query;
		$rs = $this->Account->fetch($query);
		$this->Account->close();
		if ($rs){
			return array(1,number_format($rs['saldo'],2));
		}else{
			return array(0,null);
		}
	}
	////
	
	//for write to log file
	function write_log($log_file,$type, $log_item){
		if (is_array($log_item)) {
			foreach ($log_item AS $name=>$value) {
				$log_item_temp_arr[] = $name . ': ' . $value;
			}
			$log_line = date('Y-m-d H:i:s') . ' [' . ucfirst($type) . '] ' . implode('; ', $log_item_temp_arr);
		}
		else {
			$log_line = date('Y-m-d H:i:s') . ' [' . ucfirst($type) . '] ' . $log_item;
		}
		$fh = fopen($log_file, 'a') or die("Can't open log file\n");
		fwrite($fh, $log_line . "\n");
		fclose($fh);
	}
}
?>