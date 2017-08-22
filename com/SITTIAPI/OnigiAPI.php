<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTICampaign.php";
include_once $APP_PATH."SITTI/SITTIInventory.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";

class OnigiAPI extends SITTIApp{
	var $_logger = "../logs/OnigiAPI.log";
	var $onigi_api_key = "c01864402a311265679c843f76b94b02";
	var $pass = "sittionigi";
	var $topup_value = 100000;
	var $rate_per_day_limit = array("account"=>100, "ad"=>500);
	var $rate_per_hour_limit = array("account"=>30, "ad"=>150);
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
	
	function authenticated($tipe=false){
		$flag = 0;
		$message = "";
		//validate request parameter
		if ($this->g('key')&&$this->params['email']&&$this->params['t']){
			//validate api key
			$api_key = $this->g('key');
			if (strlen($api_key)==32 && $api_key==$this->onigi_api_key){
				if ($tipe){
					//check rate limit
					$rate = $this->getAccessRate($api_key, $tipe);
					if ($rate['day']<$this->rate_per_day_limit[$tipe] && $rate['hour']<$this->rate_per_hour_limit[$tipe]){
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
	
	function getAccessRate($api_key, $tipe){
		$this->open();
		$rs = $this->fetch("SELECT ".$tipe."_rate_per_day FROM zz_sitti.api_rate_monitor WHERE api_key='".$api_key."' AND hari=DATE(NOW())");
		if ($rs){
			$rate['day'] = $rs[$tipe."_rate_per_day"];
		}else{
			$rs = $this->query("UPDATE zz_sitti.api_rate_monitor SET hari=DATE(NOW()), jam=HOUR(NOW()), account_rate_per_day=0, account_rate_per_hour=0, ad_rate_per_day=0, ad_rate_per_hour=0 WHERE api_key='".$api_key."'");
			$rate['day'] = 0;
		}
		
		$rs = $this->fetch("SELECT ".$tipe."_rate_per_hour FROM zz_sitti.api_rate_monitor WHERE api_key='".$api_key."' AND jam=HOUR(NOW())");
		if ($rs){
			$rate['hour'] = $rs[$tipe."_rate_per_hour"];
		}else{
			$rs = $this->query("UPDATE zz_sitti.api_rate_monitor SET jam=HOUR(NOW()), account_rate_per_hour=0, ad_rate_per_hour=0 WHERE api_key='".$api_key."'");
			$rate['hour'] = 0;
		}
		$this->close();
		return $rate;
	}
	
	function updateAccessRate($api_key, $tipe){
		$this->open();
		$rs = $this->query("UPDATE zz_sitti.api_rate_monitor SET ".$tipe."_rate_per_day = ".$tipe."_rate_per_day+1, ".$tipe."_rate_per_hour = ".$tipe."_rate_per_hour+1 WHERE api_key='".$api_key."'");
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
		$response = $this->authenticated("account");
		if($response[0]){
			$arr = $this->run_RegisterAdvertiser();
			$this->updateAccessRate($this->g('key'), "account");
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
				// $this->immediateTopUp($sittiID,$this->topup_value);
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
		
		$insert = $this->Account->create('4',$email, $fix_pass, $enc);
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
	
	function immediateTopUp($sittiID,$nominal){
		$this->open(4);
		$tgl_submit = date("Y-m-d H:i:s");
		
		$transaction_id = "";
		$random12digit = "".time();
		$random12digit = $random12digit."".rand(10,99);
		$transaction_id = "VOUCHER-ONIGI-V100-".$random12digit;
		
		$flag = $this->query("INSERT INTO tbl_credit (tgl_submit, sitti_id, vendor_id, cash_number, balance_type, payment_method, transaction_code, transaction_date, klikbca_login, mobile_number) VALUES('".$tgl_submit."', '".$sittiID."', 'SITTI', '".$nominal."', '2', '0', '".$transaction_id."', '".$tgl_submit."', '', '')");
		if ($flag){
			$flag = $this->query("INSERT INTO tbl_balance_stack(sittiID, amount, submit_date, last_update, balance_type, credit_trans_id) VALUES('".$sittiID."', '".$nominal."', '".$tgl_submit."', '".$tgl_submit."', '2', '".$transaction_id."')");
			if ($flag){
				$now = date("Y-m-d H:i:s");
				
				$flag = $this->query("INSERT INTO sitti_account_balance(sittiID, budget, last_update) VALUES('".$sittiID."', '".$nominal."', '".$now."') ON DUPLICATE KEY UPDATE budget = budget+".$nominal.", last_update = '".$now."'");
				if ($flag==null){
					// echo "INSERT INTO sitti_account_balance(sittiID, budget, last_update) VALUES('".$sittiID."', '".$nominal."', '".$now."') ON DUPLICATE KEY UPDATE budget = budget+".$nominal.", last_update = ".$now."";
					$this->query("DELETE FROM tbl_balance_stack WHERE credit_trans_id='".$transaction_id."'");
					$this->query("DELETE FROM tbl_credit WHERE transaction_code='".$transaction_id."'");
					//return $flag;
				}else{
					//return $flag;
				}
			}else{
				$this->query("DELETE FROM tbl_transaction WHERE transaction_code='".$transaction_id."'");
				$this->query("DELETE FROM tbl_credit WHERE transaction_code='".$transaction_id."'");
				//return $flag;
			}
		}
		$this->close();
		return $flag;
	}
	////
	
	////
	// CreateAd & CreateAdAlt
	////
	function CreateAd(){
		$response = $this->authenticated("ad");
		if($response[0]){
			$arr = $this->run_CreateAd();
			$this->updateAccessRate($this->g('key'), "ad");
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
			$keys = $this->g('keywords');
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
				$keywords = $this->getKeywords(strtolower(substr($keys,0,50)),10, ",");
				$flag = $this->addAd($sittiID,$nama,$nama,$deskripsi,$keywords,$budget,$url,$campaign_id,$durasi);
				$c_id = $sittiID."_".$campaign_id;
				$c_id = urlencode64($c_id);
				if ($flag){
					$arr = array("status"=>"1","message"=>"Create Ad Success!","id"=>$c_id);
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
	
	function CreateAdAlt(){
		$response = $this->authenticated("ad");
		if($response[0]){
			$arr = $this->run_CreateAdAlt();
			$this->updateAccessRate($this->g('key'), "ad");
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_CreateAdAlt(){
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
				$keywords = $this->getKeywords(strtolower($deskripsi),10, " ");
				$flag = $this->addAd($sittiID,$nama,$nama,$nama,$keywords,$budget,$url,$campaign_id,$durasi);
				$c_id = $sittiID."_".$campaign_id;
				$c_id = urlencode64($c_id);
				if ($flag){
					$arr = array("status"=>"1","message"=>"Create Ad Success!","id"=>$c_id);
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
			$this->open();
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
	
	function getKeywords($string,$n,$separator){
		$keywords = explode($separator,$string);
		$keywords = array_unique($keywords);
		$keywords = $this->trimArray($keywords);
		if (sizeof($keywords)>$n){
			$keywords = array_slice($keywords,0,$n);
		}
		return $keywords;
	}
	
	function trimArray($array){
		$ret_array = array();
		foreach ($array as $string){
			array_push($ret_array,trim($string));
		}
		return $ret_array;
	}
	
	function addAd($sittiID,$nama,$judul,$baris1,$keywords,$budget,$url,$campaign_id,$durasi){
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
					mysql_escape_string($judul),
					mysql_escape_string($iklan->category),
					mysql_escape_string($baris1),
					mysql_escape_string($iklan->baris2),
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
			$c_id = $this->g('c_id');
			$retval = $this->retrieveReport($sittiID,$tipe,$tgl_awal,$tgl_akhir,$c_id);
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
	
	function retrieveReport($sittiID,$tipe,$tgl_awal,$tgl_akhir,$c_id){
		if ($tipe==1){
			return $this->accountReport($sittiID,$tgl_awal,$tgl_akhir);
		}else if ($tipe==2){
			return $this->campaignReport($sittiID,$tgl_awal,$tgl_akhir,$c_id);
		}else if ($tipe==3){
			return $this->adReport($sittiID,$tgl_awal,$tgl_akhir,$c_id);
		}
	}
	
	function accountReport($sittiID,$tgl_awal,$tgl_akhir){
		$query = "";
		$this->open(2);
		if ($tgl_akhir){
			$query = "SELECT SUM(jum_imp) imps, SUM(jum_hit) clicks, IFNULL(SUM(jum_hit)*100/SUM(jum_imp),0) ctrs 
			FROM db_web3.sitti_ad_inventory A
			INNER JOIN db_report_raw.ps_report_daily B
			ON A.id = B.id_iklan
			WHERE B.datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
			AND A.advertiser_id = '".$sittiID."'";
		}else{
			$query = "SELECT jum_imp imps, jum_hit clicks, ctr ctrs
			FROM db_web3.sitti_ad_inventory A
			INNER JOIN db_report_raw.ps_report_daily B
			ON A.id = B.id_iklan
			WHERE B.datee = '".$tgl_awal."'
			AND A.advertiser_id ='".$sittiID."'";
		}
		//echo $query;
		$rs = $this->fetch($query);
		$this->close();
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
			}
		}
		return array(0,array());
	}
	
	function campaignReport($sittiID,$tgl_awal,$tgl_akhir,$c_id){
		$campaign_id = urldecode64($c_id);
		$campaign_id = explode("_",$campaign_id);
		$campaign_id = $campaign_id[1];
		
		$this->open(2);
		$query = "SELECT kampanye_id AS id,kampanye AS campaign_name,a.budget_total AS budget_total
				FROM db_report.tbl_performa_akun_kampanye_total a
				WHERE advertiser_id='".$sittiID."'
				AND kampanye_id = '".$campaign_id."' 
				AND status <> 2
				ORDER BY kampanye_id ASC";
		$campaign_list = $this->fetch($query);
		$this->close();
		
		if ($campaign_list){
			$this->open(4);
			$query = "SELECT IFNULL(SUM(debit),0.00) AS expenditure
				FROM db_billing_report.adv_campaign_detail
				WHERE advertiser_id='".$sittiID."' AND campaign_id = '".$campaign_id."'";
			//echo $query;
			$rs = $this->fetch($query);
			$this->close();
			$rs['expenditure'] = number_format($rs['expenditure'],2);
			$campaign_list = array_merge($campaign_list,$rs);
			
			if ($tgl_akhir){
				$query = "SELECT datee as date,a.jum_imp AS imps,a.jum_klik AS clicks,IFNULL((a.jum_klik)*100/(a.jum_imp),0) AS ctrs
					FROM db_report.tbl_performa_akun_kampanye a INNER JOIN db_web3.sitti_campaign b
					ON a.kampanye_id = b.ox_campaign_id
					WHERE a.advertiser_id='".$sittiID."' AND status <> 2 AND a.kampanye_id = '".$campaign_id."'
					AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
					ORDER BY datee ASC";
				$query2 = "SELECT datee as date, IFNULL(debit,0.00) AS expenditure
						FROM db_billing_report.adv_campaign_detail
						WHERE advertiser_id='".$sittiID."' AND campaign_id = '".$campaign_id."' 
						AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'
						ORDER BY datee ASC";
			}else{
				$query = "SELECT datee as date,a.jum_imp AS imps,a.jum_klik AS clicks,IFNULL((a.jum_klik)*100/(a.jum_imp),0) AS ctrs
					FROM db_report.tbl_performa_akun_kampanye a INNER JOIN db_web3.sitti_campaign b
					ON a.kampanye_id = b.ox_campaign_id
					WHERE a.advertiser_id='".$sittiID."' AND status <> 2 AND a.kampanye_id = '".$campaign_id."'
					AND datee = '".$tgl_awal."'
					ORDER BY datee ASC";
				$query2 = "SELECT datee as date, IFNULL(debit,0.00) AS expenditure
						FROM db_billing_report.adv_campaign_detail
						WHERE advertiser_id='".$sittiID."' AND campaign_id = '".$campaign_id."' 
						AND datee = '".$tgl_awal."'
						ORDER BY datee ASC";
			}
			// echo $query."<br/>";
			// echo $query2."<br/>";
			$campaign_list['report'] = array();
			$this->open(2);
			$rs = $this->fetch($query,1);
			$this->close();
			$this->open(4);
			$rs2 = $this->fetch($query2,1);
			$this->close();
			if ($rs || $rs2){
				foreach ($rs as $row){
					$flag = false;
					$index = -1;
					for ($i=0;$i<sizeof($rs2);$i++){
						if($row['date'] == $rs2[$i]['date']){
							$flag = true;
							$index = $i;
							break;
						}
					}
					if ($flag){
						$data = array_merge($row,array("expenditure"=>"".$rs2[$index]['expenditure']));
					}else{
						$data = array_merge($row,array("expenditure"=>"0.00"));
					}
					array_push($campaign_list['report'],$data);
				}
			}else{
				array_push($campaign_list['report'],"no data");
			}
			// if ($rs){
				// foreach ($rs as $key=>$value){
					// if ($value==null){
						// $rs[$key]="0";
					// }
				// }
				// $campaign_list = array_merge($campaign_list,$rs);
			// }else{
				// if (!$tgl_akhir){
					// $campaign_list = array_merge($campaign_list,array("imps"=>"0","clicks"=>"0","ctrs"=>"0"));
				// }
			// }
			$campaign_list['id'] = $c_id;
			$campaign_list['budget_total'] = number_format($campaign_list['budget_total'],2);
			return array(1,$campaign_list);
		}else{
			return array(0,null);
		}
	}
	
	function adReport($sittiID,$tgl_awal,$tgl_akhir,$c_id){
		$campaign_id = urldecode64($c_id);
		$campaign_id = explode("_",$campaign_id);
		$campaign_id = $campaign_id[1];
		
		$this->open(2);
		$query = "SELECT id_iklan as id,b.nama as ad_name, keywords, a.budget_total as budget_total
				FROM db_report.tbl_performa_iklan_total a
				INNER JOIN db_web3.sitti_ad_inventory b
				ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."'
				AND b.ox_campaign_id = '". $campaign_id ."'
				AND a.status <> 2
				ORDER BY a.id_iklan ASC";
		$ad_list = $this->fetch($query);
		$this->close();
		
		if ($ad_list){
			if ($tgl_akhir){
				$query = "SELECT datee as date,a.jum_imp AS imps,a.jum_klik AS clicks,IFNULL((a.jum_klik)*100/(a.jum_imp),0) AS ctrs
		 		FROM db_report.tbl_performa_iklan a 
		 		INNER JOIN db_web3.sitti_ad_inventory b
		 		ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."' 
				AND b.ox_campaign_id = '". $campaign_id ."'
				AND a.datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				AND a.status <> 2
				ORDER BY datee ASC";
			}else{
				$query = "SELECT datee as date,a.jum_imp AS imps,a.jum_klik AS clicks,IFNULL((a.jum_klik)*100/(a.jum_imp),0) AS ctrs
		 		FROM db_report.tbl_performa_iklan a 
		 		INNER JOIN db_web3.sitti_ad_inventory b
		 		ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."' 
				AND b.ox_campaign_id = '". $campaign_id ."'
				AND a.datee = '".$tgl_awal."'
				AND a.status <> 2
				ORDER BY datee ASC";
			}
			// echo $query;
			$ad_list['report'] = array();
			$this->open(2);
			$rs = $this->fetch($query,1);
			$this->close();
			if ($rs){
				foreach($rs as $row){
					array_push($ad_list['report'],$row);
				}
			}else{
				array_push($ad_list['report'],"no data");
			}
			$ad_list['id'] = $c_id;
			$ad_list['budget_total'] = number_format($ad_list['budget_total'],2);
			return array(1,$ad_list);
		}else{
			return array(0,null);
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
		$this->open(4);
		$query = "SELECT saldo
		FROM db_billing_report.adv_detail
		WHERE sitti_id = '".$sittiID."'
		ORDER BY row_id DESC
		LIMIT 1;";
		//echo $query;
		$rs = $this->fetch($query);
		$this->close();
		if ($rs){
			return array(1,number_format($rs['saldo'],2));
		}else{
			return array(1,"0.00");
		}
		return array(0,null);
	}
	////
	
	////
	// CheckCredential
	////
	function CheckCredential(){
		$response = $this->authenticated();
		if($response[0]){
			$arr = $this->run_CheckCredential();
		}else{
			$arr = array("status"=>"-1","message"=>$response[1]);
		}
		$this->write_log($this->_logger,'DEBUG',json_encode($arr));
		return json_encode($arr);
	}
	
	function run_CheckCredential(){
		$email = $this->params['email'];
		$is_email_exist = $this->checkEmailExistence($email);
		if ($is_email_exist){
			$password = $this->params['password'];
			$fix_pass = md5($password);
			$this->Account->open();
			$check = $this->Account->login($email, $fix_pass, 1);
			$this->Account->close();
			if($check['status']==1){
				$arr = array("status"=>"1","message"=>"Success, Valid SITTI Account.");
			}else{
				$arr = array("status"=>"0","message"=>"Failed, Invalid SITTI Account.");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Failed, Email Not Exist.");
		}
		return $arr;
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