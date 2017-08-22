<?php 
/**
 * SITTIBilling
 * class untuk informasi billing
 */

include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIApp.php";
include_once "SITTIAdvertiser.php";
include_once "SITTIReporting.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "Model/SITTIVoucherHelper.php";
include_once $APP_PATH."INDOMOG/IndomogAPI.php";
include_once "../engines/Utility/XML2JSON/xml2json.php";
class SITTIBilling extends StaticPage{
	var $Account;
	var $Paging;
    function SITTIBilling($req,$account){
        parent::StaticPage(&$req);
        $this->Account = $account;
        
    }
    function save($info){
    	$req = $this->Request;
    	$klikbca_login = $req->getPost("klikbca_login");
    	$mandiri = $req->getPost("mandiri");
    	$mandiri_internet = $req->getPost("mandiri_internet");
    	$redeem_code = $req->getPost("redeem");
    	$kupon = $req->getPost("kupon");
    	
    	$token = $req->getPost("token");
    	if(eregi("([0-9]+)",$redeem_code)){
    		$rs = $this->redeemVoucher($info,$redeem_code);
    		print $rs;
    	}else if(eregi("([A-Za-z0-9]+)",$klikbca_login)){
    		$topup = $req->getPost("topup");
    		settype($topup, "integer");
            if(!$this->isTopupValueValid($topup)){
    			//print "-1";
                print json_encode(array("status"=>"-1"));
    		}else{
    			
    			$rs = $this->updateBCA($info,$klikbca_login,$topup,$kupon);
    			print $rs;
    			/*if($this->updateBCA($info,$klikbca_login,$topup)){
	    			print "1";
    			}else{
	    			print "0";
    			}*/
    		}
    			
    		
    	}else if(eregi("([0-9]+)",$mandiri)&&$mandiri_internet==NULL){
    		$topup = $req->getPost("topup");
    		settype($topup, "integer");
    		if(!$this->isTopupValueValid($topup)){
    			print json_encode(array("status"=>"-1"));
    		}else{
    			$rs = $this->updateMandiri($info,$mandiri,$topup,$kupon);
    			print $rs;
    			/*
	    		if($this->updateMandiri($info,$mandiri,$topup)){
	    			print "1";
	    		}else{
	    			print "0";
	    		}*/
    		}
    	}else if(eregi("([0-9]+)",$mandiri_internet&&$mandiri==NULL)){
    		$topup = $req->getPost("topup");
    		settype($topup, "integer");
    		
    		if(!$this->isTopupValueValid($topup)){
    			print json_encode(array("status"=>"-1"));
    		}else{
				$trans_id = $req->getPost("trans_id");
    			$rs = $this->updateMandiriInternet($info,$mandiri_internet,$trans_id,$topup,$token,$kupon);
    			print $rs;
    			/*
	    		if($this->updateMandiriInternet($info,$mandiri_internet,$topup,$token)){
	    			print "1";
	    		}else{
	    			print "0";
	    		}
	    		*/
    		}
    	}else if($req->getPost("manual")){
    		$topup = $req->getPost("topup");
    		settype($topup, "integer");
    		if($this->updateManual($info,$topup)){
    			print "1";
    		}
    	}else{
    		print "0";
    	}
    	die();
    }
    function updateBCA($info,$klikbca,$topup,$kupon){
    	$this->open(4);
    	$rs = $this->fetch("SELECT sittiID,alt_id FROM db_billing.sitti_account_balance WHERE sittiID='".mysql_escape_string($info['sittiID'])."' LIMIT 1");
    	$this->close();
    	$klikbca = mysql_escape_string($klikbca);
    	$isKupon = false;
    	$voucher = new SITTIVoucherHelper();
    	$isKuponError = false;
    	if(eregi("([0-9]+)",$kupon)&&strlen($kupon)>5){
    		$arr = array("500000"=>1000000,"3500000"=>5000000,"8000000"=>10000000);
    		$this->open(4);
    		$kk = $voucher->getCode($kupon,1);
    		$this->close();
    		if($arr[$kk['amount']]>0&&$topup==$arr[$kk['amount']]){
    			$isKupon = true;
    			$check = $voucher->ValidateCode($kupon,1);
    			if($check){
    				$isKupon = true;
    				//do something here.
    				
    				//harus masukin ke dalam queue khusus topup
    			}else{
    				$isKuponError = true;
    				$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    			}
    		}else if($kk['kode']!=$kupon){
    			$isKuponError = true;
    			$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    		}else{
    			$q = json_encode(array("status"=>"66","message"=>"Kode voucher ini hanya berlaku untuk topup sebesar Rp.".number_format($arr[$kk['amount']]),"transaction_code"=>""));
    			$isKuponError = true;
    		}
    	}
    	if($isKuponError){
    		return $q;
    	}
    	if($rs['sittiID']==$info['sittiID']){
    		$api = new IndomogAPI(false);
    		$info['indomog_id'] = $rs['alt_id'];
			$out = $api->topupBCA($info, $klikbca, $topup);
			$js = json_decode($out);
			//var_dump($js);
			$this->log($rs['sittiID']."-"."klikbca--".$out."\n----------------------------\n");
			if($js->response->data->rc=="00"){
				$this->open(4);
    			$r = $this->query("UPDATE db_billing.sitti_account_balance SET klikbca_login = '".$klikbca."' WHERE sittiID='".$info['sittiID']."' LIMIT 1");
    			$this->close();
    			if($r){
    				if($isKupon&&!$isKuponError){
    					$this->QueueVoucher($info['sittiID'],$kupon,$kk['amount'],$topup);
    				}
    				$q = json_encode(array("status"=>"1","transaction_code"=>""));
    			}
			}else{
				$q = json_encode(array("status"=>"0","rc"=>$js->response->data->rc,"rd"=>$js->response->data->rd));
				if($isKupon&&!$isKuponError){
					$voucher->enableCode($kupon);
				}
			}
    	}
    	
    	return $q;
    }
    function QueueVoucher($sittiID,$kode,$amount,$topup){
    	$voucher = new SITTIVoucherHelper();
    	$rs = $voucher->registerVoucher($sittiID, $kode,0);
    	$sql = "INSERT IGNORE INTO db_billing.tbl_voucher_available(kode,sittiID,amount,topup,tglisidata,voucher_id)
    			VALUES($kode,'".$sittiID."',".$amount.",".$topup.",NOW(),".$rs[8].")";
    	$this->open(4);
    	$q = $this->query($sql);
    	$this->close();
    	return $q;
    }
	function redeemVoucher($info,$redeem_code){
    	$this->open(4);
    	$rs = $this->fetch("SELECT sittiID,alt_id FROM db_billing.sitti_account_balance WHERE sittiID='".mysql_escape_string($info['sittiID'])."' LIMIT 1");
    	//settype($redeem_code,'int');
    	if($rs['sittiID']==$info['sittiID']){
    		$rs = $this->PerformRedeem($info,$redeem_code,2);
    	}
    	$this->close();
    	
    	return $rs;
    }
    function PerformRedeem($info,$kode,$type){
    	global $CONFIG;
    	$voucher = new SITTIVoucherHelper();
    	if($voucher->ValidateCodeVoucher($info['sittiID'],$kode,$type)){
    		$rs = $voucher->registerVoucher($info['sittiID'], $kode,1,2);
    		//print_r($rs);
    		if($rs[0]==1){
    			$params = array(
    				"sitti_id"=>$info['sittiID'],
    				"vendor_id"=>"SITTI",
    			    "cash_number"=>$rs['5'],
    				"payment_method"=>0,
    				"balance_type"=>2,
    				"transaction_code"=>"VOUCHER-CASH-".$kode,
    				"transaction_date"=>date("Y-m-d H:i:s"),
    				"klikbca_login" => "",
    			    "mobile_number" => "",
    				"voucher_id"=>$rs[8],
    				"api_key"=>$CONFIG['SITTI_PAYMENT_SERVICE_KEY']
    			);
    			$this->log($rs['sittiID']."-".$info['sittiID']."-sitti_voucher--Init-".json_encode($params)."\n----------------------------\n");
    			$call = $this->paymentServiceCall($params);
    			if($call){
    				$resp = json_decode($call);
    				
    				if($resp->status==1){
	    				$this->log($rs['sittiID']."-".$info['sittiID']."-sitti_voucher--Payment Service berhasil-".$call."\n----------------------------\n");
	    				return json_encode(array("status"=>"1","topup"=>$rs[5],"transaction_code"=>"VOUCHER-CASH-".$kode));
    				}else{
    					$this->log($rs['sittiID']."-".$info['sittiID']."-sitti_voucher--Payment Service Gagal-".$call."\n----------------------------\nRequest was : ".json_encode($params)."\n....................................\n");
    					$voucher->enableCode($kode);
    					$voucher->unregisterVoucher($rs[8]);
	    				return json_encode(array("status"=>"99","topup"=>"0","transaction_code"=>"-1"));
    				}
    			}else{
    				$voucher->enableCode($kode);
    				$voucher->unregisterVoucher($rs[8]);
    				$this->log($rs['sittiID']."-".$info['sittiID']."-sitti_voucher--Payment Service call gagal\n----------------------------\n");
    				return json_encode(array("status"=>"99","topup"=>"0","transaction_code"=>"-1"));
    			}
    		}else{
    			$this->log($rs['sittiID']."-".$info['sittiID']."-sitti_voucher--Gagal register voucher\n----------------------------\n");
    			return json_encode(array("status"=>"0"));
    		}
    	}else{
			if ($voucher->err_no==1){
				$msg = "sitti_voucher--Already Redeem Voucher for the same event\n----------------------------\n";
			}else if ($voucher->err_no==3){
				$msg = "sitti_voucher--Advertiser Belum Pernah Membuat Iklan\n----------------------------\n";
			}else{
				$msg = "sitti_voucher--Kode Voucher invalid atau expired\n----------------------------\n";
			}
    		$this->log($rs['sittiID']."-".$info['sittiID']."-".$msg);
    		return json_encode(array("status"=>"0","transaction_code"=>"","topup"=>"0","err_no"=>"".$voucher->err_no));
    	}
    }
    function paymentServiceCall($params){
    	global $CONFIG;
    	$returnType = "json";
    	$callName = "Credit";
    	$url = $CONFIG['SITTI_PAYMENT_SERVICE_URI']."/API/".$returnType."/".$callName."";
    	//print $url;
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getSerializedParams($params)); // add POST fields
		$result = curl_exec($ch); // run the whole process
		//print $this->getSerializedParams($params);
		curl_close($ch);
		//print_r($result);
		//echo $result;
		return $result;
    }
	function getSerializedParams($params){
		$strQuery = "";
		foreach($params as $name=>$val){
			$strQuery.=$name."=".urlencode($val)."&";
		}
		$strQuery.="r=".rand(0,999999);
		//print $strQuery;
		return $strQuery;
	}
    function isTopupValueValid($topup){
    	settype($topup,'integer');
    	
        if ( ($topup >= 100000) && ($topup <=1000000) && ($topup % 100000 == 0) )
        {
            return TRUE;
        }
        elseif ( ($topup > 1000000) && ($topup % 1000000 == 0) )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
	function updateMandiri($info,$mandiri,$topup,$kupon){
    	$this->open(4);
    	$rs = $this->fetch("SELECT sittiID,alt_id FROM db_billing.sitti_account_balance WHERE sittiID='".mysql_escape_string($info['sittiID'])."' LIMIT 1");
    	$this->close();
    	$mandiri = mysql_escape_string($mandiri);
    	
    	//voucher check
		$isKupon = false;
    	$voucher = new SITTIVoucherHelper();
    	$isKuponError = false;
    	if(eregi("([0-9]+)",$kupon)&&strlen($kupon)>5){
    		$arr = array("500000"=>1000000,"3500000"=>5000000,"8000000"=>10000000);
    		$this->open(4);
    		$kk = $voucher->getCode($kupon,1);
    		$this->close();
    		if($arr[$kk['amount']]>0&&$topup==$arr[$kk['amount']]){
    			$isKupon = true;
    			$check = $voucher->ValidateCode($kupon,1);
    			if($check){
    				$isKupon = true;
    				//do something here.
    				
    				//harus masukin ke dalam queue khusus topup
    			}else{
    				$isKuponError = true;
    				$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    			}
    		}else if($kk['kode']!=$kupon){
    			$isKuponError = true;
    			$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    		}else{
    			$q = json_encode(array("status"=>"66","message"=>"Kode voucher ini hanya berlaku untuk topup sebesar Rp.".number_format($arr[$kk['amount']]),"transaction_code"=>""));
    			$isKuponError = true;
    		}
    	}
    	if($isKuponError){
    		return $q;
    	}
    	//--->
    	if($rs['sittiID']==$info['sittiID']){
    		$api = new IndomogAPI(false);
    		$info['indomog_id'] = $rs['alt_id'];
			$out = $api->topupMandiri($info, $mandiri, $topup);
			$js = json_decode($out);
			$this->log($rs['sittiID']."-"."smsmandiri--".$out."\n----------------------------\n");
			if($js->response->data->rc=="00"){
				$this->open(4);
    			$q = $this->query("UPDATE db_billing.sitti_account_balance SET sms_mandiri_no = '".$mandiri."' WHERE sittiID='".$info['sittiID']."' LIMIT 1");
    			$this->close();
    			if($q){
    				if($isKupon&&!$isKuponError){
    					$this->QueueVoucher($info['sittiID'],$kupon,$kk['amount'],$topup);
    				}
    				$rs = json_encode(array("status"=>"1","transaction_code"=>""));
    			}else{
    				$rs = json_encode(array("status"=>"0","transaction_code="=>""));
    			}
			}else{
				if($isKupon&&!$isKuponError){
					$voucher->enableCode($kupon);
				}
				$rs = json_encode(array("status"=>"0","transaction_code="=>""));
			}
    	}
    	
    	return $rs;
    }
	function updateMandiriInternet($info,$mandiri,$trans_id,$topup,$token,$kupon){
    	$this->open(4);
    	$rs = $this->fetch("SELECT sittiID,alt_id FROM db_billing.sitti_account_balance WHERE sittiID='".mysql_escape_string($info['sittiID'])."' LIMIT 1");
    	$this->close();
    	$mandiri = mysql_escape_string($mandiri);
    	//voucher check
		$isKupon = false;
    	$voucher = new SITTIVoucherHelper();
    	$isKuponError = false;
    	if(eregi("([0-9]+)",$kupon)&&strlen($kupon)>5){
    		$arr = array("500000"=>1000000,"3500000"=>5000000,"8000000"=>10000000);
    		$this->open(4);
    		$kk = $voucher->getCode($kupon,1);
    		$this->close();
    		if($arr[$kk['amount']]>0&&$topup==$arr[$kk['amount']]){
    			$isKupon = true;
    			$check = $voucher->ValidateCode($kupon,1);
    			if($check){
    				$isKupon = true;
    				//do something here.
    				
    				//harus masukin ke dalam queue khusus topup
    			}else{
    				$isKuponError = true;
    				$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    			}
    		}else if($kk['kode']!=$kupon){
    			$isKuponError = true;
    			$q = json_encode(array("status"=>"66","message"=>"Maaf, kode voucher ini tidak ditemukan.","transaction_code"=>""));
    		}else{
    			$q = json_encode(array("status"=>"66","message"=>"Kode voucher ini hanya berlaku untuk topup sebesar Rp.".number_format($arr[$kk['amount']]),"transaction_code"=>""));
    			$isKuponError = true;
    		}
    	}
    	if($isKuponError){
    		return $q;
    	}
    	//--->
    	if($rs['sittiID']==$info['sittiID']){
    		$api = new IndomogAPI(false);
    		$info['indomog_id'] = $rs['alt_id'];
    		
			$out = $api->topupMandiriInternet($info, $mandiri, $trans_id, $topup, $token);
			$js = json_decode($out);
			$this->log($rs['sittiID']."-"."internetmandiri--".$out."\n----------------------------\n");
			if($js->response->data->rc=="00"){
				$this->open(4);
    			$q = $this->query("UPDATE db_billing.sitti_account_balance SET sms_mandiri_no = '".$mandiri."' WHERE sittiID='".$info['sittiID']."' LIMIT 1");
    			$this->close();
				if($q){
					if($isKupon&&!$isKuponError){
    					$this->QueueVoucher($info['sittiID'],$kupon,$kk['amount'],$topup);
    				}
    				$rs = json_encode(array("status"=>"1","transaction_code"=>""));
    			}else{
    				$rs = json_encode(array("status"=>"0","transaction_code"=>""));
    			}
			}else{
				if($isKupon&&!$isKuponError){
					$voucher->enableCode($kupon);
				}
				$rs = json_encode(array("status"=>"0","transaction_code"=>""));
			}
    	}
    	
    	return $rs;
    }
	function updateManual($info){
    	$this->open(4);
    	$rs = $this->fetch("SELECT sittiID FROM db_billing.sitti_account_balance WHERE sittiID='".mysql_escape_string($info['sittiID'])."' LIMIT 1");
    	
    	$flag = false;
    	if($rs['sittiID']==$info['sittiID']){
    		$flag = true;
    	}
    	$this->close();
    	return $flag;
    }
    function Summary(){
    	$req = $this->Request;
        //var_dump($req);
     	$start = $req->getParam("start");
    	if($start==NULL){
    		$start=0;
    	}

        //account balance
    	$sittiID = mysql_escape_string($this->Account['sittiID']);
    	$this->open(4);
        
        $xlsfilename = 'billing-' . $sittiID; // exported xls filename
        $tbl_name = 'adv_detail';
        $advid_column = 'sitti_id';
        $array_xls_header = array('Tanggal','SITTI ID','Jenis','Kredit','Debet','Saldo');

        // filter tanggal
        if ($req->getParam("from") && $req->getParam("to")) {
            $tanggalAwalMutasi = $req->getParam("from");
            $tanggalAkhirMutasi = $req->getParam("to");
            $this->View->assign("tglAwal",$tanggalAwalMutasi);
            $this->View->assign("tglAkhir",$tanggalAkhirMutasi);
            $tglAwalArray = explode("/", $tanggalAwalMutasi);
            $tglAkhirArray = explode("/", $tanggalAkhirMutasi);
            $tglAwal = date('Y-m-d', mktime(0,0,0,$tglAwalArray[1],$tglAwalArray[0],$tglAwalArray[2]));
            $tglAkhir = date('Y-m-d', mktime(0,0,0,$tglAkhirArray[1],$tglAkhirArray[0],$tglAkhirArray[2]));
            $dateQuery = " AND datee BETWEEN '" .$tglAwal. "' AND '" .$tglAkhir. "' ";
            $xlsfilename .= '-' . date('dmy');
        }

        if ($req->getParam('campaign')) {
            $campaign_id = $req->getParam('campaign');
            $tbl_name = 'adv_campaign_detail';
            $advid_column = 'advertiser_id';

            $campaignQuery = " AND campaign_id ='".urldecode64($campaign_id)."' ";
            
            $sql = "SELECT
                DATE_FORMAT(datee,'%d/%m/%Y') as tgl,
                `advertiser_id`,
                `jenis`,
                `debit`
                FROM `db_billing_report`." .$tbl_name. "
                WHERE advertiser_id = '".$sittiID."' " .$dateQuery.$campaignQuery. "ORDER BY row_id LIMIT 100;";

            $this->View->assign('selected_campaign', $campaign_id);
            $xlsfilename .= '-' . $campaign_id;
            $array_xls_header = array('Tanggal','SITTI ID','Jenis','Debet');
            
        } else {
            $sql = "SELECT
                DATE_FORMAT(datee,'%d/%m/%Y') as tgl,
                `sitti_id`,
                `jenis`,
                `credit`,
                `debit`,
                `saldo`
                FROM 
                ( SELECT * FROM `db_billing_report`." .$tbl_name. "
                    WHERE ". $advid_column ." = '".$sittiID."' " .$dateQuery. "ORDER BY datee DESC, row_id DESC LIMIT 100) A
                ORDER BY datee ASC, row_id ASC;";   
        }

        /*
    	$ge = "SELECT sittiID,jenis,credit,debet,DATE_FORMAT(db_report.tbl_rekap_transaksi.tanggal,'%d/%m/%Y') as tgl 
    			FROM db_report.tbl_rekap_transaksi WHERE sittiID = '".$sittiID."' LIMIT 100";
    	*/
        
    	$sql2 = "SELECT
				DATE_FORMAT(datee,'%d/%m/%Y') as tgl
                FROM `db_billing_report`." .$tbl_name. "
				WHERE " .$advid_column. " = '".$sittiID."' 
				ORDER BY datee DESC, row_id DESC LIMIT 1";
    	
    	$list = $this->fetch($sql,1);
    	$rs = $this->fetch($sql2);
    	
        $sql_list_campaign_id = "SELECT DISTINCT 
                                `campaign_id` 
                                FROM db_billing_report.adv_campaign_detail 
                                WHERE advertiser_id = '". $sittiID ."'
                                ORDER BY campaign_id DESC LIMIT 100";
        $list_campaign_id = $this->fetch($sql_list_campaign_id, 1);
        $arr_campaign_id = array();
        foreach ($list_campaign_id as $campaign) {
            array_push($arr_campaign_id, $campaign['campaign_id']); 
        }
        $str_campaign_id = implode(',', $arr_campaign_id);

        $sql_list_campaign = "SELECT 
                            `ox_campaign_id`, `name` 
                            FROM db_web3.sitti_campaign 
                            WHERE `ox_campaign_id` IN (" . $str_campaign_id . ")
                            ORDER BY ox_campaign_id
                            LIMIT 100";
        $this->close();
        $this->open(0);
        $list_campaign = $this->fetch($sql_list_campaign, 1);
		for($i=0;$i<sizeof($list_campaign);$i++){
			$list_campaign[$i]['enc_campaign_id'] = urlencode64($list_campaign[$i]['ox_campaign_id']);
    	}
        
        $this->close();
    
        if ($req->getParam("xls")) {
            $this->ExportExcel($list, $array_xls_header, $xlsfilename);
            exit();    
        }
    	
    	$n = sizeof($list);
    	
        $total_debet = 0;
        $total_kredit = 0;

        for($i=0;$i<sizeof($list);$i++){
    		$list[$i]['no'] = $start+$i+1;
    		$list[$i]['debit'] = round($list[$i]['debit'],0);
            
            $total_debet += round($list[$i]['debit'],0);
            if (!$req->getParam('campaign')) {
                $list[$i]['credit'] = round($list[$i]['credit'],0);
                $total_kredit += round($list[$i]['credit'],0);   
            }
    	}

    	$this->View->assign("list",$list);
        $this->View->assign("sitti_id",$sittiID);
    	//$this->View->assign("account_balance",$balance['budget']);
    	$this->View->assign("LAST_UPDATE",$rs['tgl']);
        $this->View->assign('list_campaign', $list_campaign);
        $this->View->assign('total_debet', $total_debet);
        $this->View->assign('total_kredit', $total_kredit);
    	
   		return $this->View->toString("SITTI/reporting/beranda_tab8.html");
    }

    function ExportExcel($data, $arrayheader, $filename = 'billing') 
    {
        global $ENGINE_PATH;
        include_once $ENGINE_PATH."Utility/PHPExcelWrapper.php";
        
        $excel = new PHPExcelWrapper();
        $excel->setGlobalBorder(true, 'allborders', '00000000');
        $excel->setHeader($arrayheader);
        $excel->getExcel($data,$filename);            
    }
	function redeemMultiply($profile,$redeem){
		global $saldo;  	
    	$sittiID = $profile['sittiID'];
    	$this->open(4);
    	$sql = "SELECT * FROM db_billing.sitti_account_balance WHERE sittiID='".$sittiID."' LIMIT 1";
    	$rs = $this->fetch($sql);
    	
    	if($rs['sittiID']==$sittiID){
			$sql = "SELECT * FROM db_billing.voucher_multiply_redeem WHERE kode=".intval($redeem)." LIMIT 1";
			$rs = $this->fetch($sql);
			if($rs['n_status']==0){
				$transaction_code = "SITTI-M-".$redeem;
				$amount = intval($rs['amount']);
				//insert into credit
				$sql = "INSERT IGNORE INTO `db_billing`.`tbl_credit_multiply`
            (
             `tgl_submit`,
             `sitti_id`,
             `vendor_id`,
             `cash_number`,
             `current_cash`,
             `balance_type`,
             `payment_method`,
             `transaction_code`,
             `transaction_date`,
             `klikbca_login`,
             `mobile_number`,
             `credit_flag`,
             `flag_delete`)
				VALUES (
				        NOW(),
				        '".$sittiID."',
				        '0',
				        ".$amount.",
				         ".$amount.",
				        '2',
				        '0',
				        '".$transaction_code."',
				        NOW(),
				        '',
				        '',
				        '0',
				        '0')";
				$q = $this->query($sql,1);
				
				$redeem_id = $this->lastInsertId;
				if($q){
					
					//create voucher cash
					$cash_amount = intval($rs['cash_amount']);
//					$sql = "INSERT INTO `db_billing`.`tbl_voucher`
//				            (`id`,
//				             `client_id`,
//				             `document_id`,
//				             `name`,
//				             `created_by`,
//				             `amount`,
//				             `apply_date`,
//				             `created_date`,
//				             `approve_key`,
//				             `is_approved`,
//				             `type`,
//				             `approved_date`,
//				             `kode`,
//				             `send_to`
//				             )
//							VALUES ('id',
//							        '0',
//							        '".$transaction_code."',
//							        'Voucher-Multiply-".$transaction_code."',
//							        'REDEEM SYSTEM',
//							        '".$cash_amount."',
//							        NOW(),
//							        NOW(),
//							        '',
//							        '1',
//							        '2',
//							        NOW(),
//							        '".$transaction_code."',
//							        '".$sittiID."'
//							        )";
//					//print $sql;
//					$q2 = $this->query($sql);
//					$v_id = mysql_insert_id();
//					
//					
//					$sql = "INSERT INTO `db_billing`.`tbl_balance_stack`
//				            (`sittiID`,
//				             `amount`,
//				             `submit_date`,
//				             `last_update`,
//				             `balance_type`,
//				             
//				             `voucher_id`,
//				             `credit_trans_id`)
//							VALUES ('".$sittiID."',
//							        '".$cash_amount."',
//							        NOW(),
//							        NOW(),
//							        2,
//							        
//							        ".intval($v_id).",
//							        '".$transaction_code."')";
//					
//						$q2b = $this->query($sql);
					$this->close();
					$response = json_decode($this->PerformRedeem($profile, intval($redeem), 2));
					//var_dump($response);
					$this->open(4);
					if($response->status=="1"){
						//flag the code
						$sql = "UPDATE db_billing.voucher_multiply_redeem SET n_status=1 WHERE id=".intval($rs['id']);
						$q3 = $this->query($sql);
						$sql = "UPDATE db_billing.tbl_credit_multiply SET vcash_trans_id='".$response->transaction_code."' WHERE id=".intval($redeem_id);
						$q3b = $this->query($sql);
						if($q3){
							$results = array("status"=>"1","topup"=>intval($rs['cash_amount']),"transaction_code"=>$transaction_code);
						}else{
							//rollback everything !
							$sql = "DELETE FROM db_billing.tbl_credit_multiply WHERE id=".intval($redeem_id);
							//print $sql."<br/>";
							$this->query($sql);
							$sql = "DELETE FROM db_billing.tbl_voucher WHERE id=".intval($v_id);
							//print $sql."<br/>";
							$this->query($sql);
							$results = array("status"=>"99","topup"=>intval($rs['amount']),"transaction_code"=>$transaction_code);
						}	
					}else{
						//rollback !
						$sql = "DELETE FROM db_billing.tbl_credit_multiply WHERE id=".intval($redeem_id);
						//print $sql;
						$this->query($sql);
						$results = array("status"=>"99","topup"=>intval($rs['amount']),"transaction_code"=>$transaction_code);
					}
					//==>
					
				}else{
					$results = array("status"=>"99","topup"=>intval($rs['amount']),"transaction_code"=>$transaction_code);
				}
			}else{
				$results = array("status"=>"2","topup"=>intval($rs['amount']),"transaction_code"=>"-");
			}
    	}else{
    		
    	}
    	$this->close();
    	return json_encode($results);
	}
    function topup($profile){  
    	global $saldo;  	
    	$sittiID = $profile['sittiID'];
    	$this->open(4);
    	$sql = "SELECT * FROM db_billing.sitti_account_balance WHERE sittiID='".$sittiID."' LIMIT 1";
    	$rs = $this->fetch($sql);
    	$this->close();
		$this->View->assign("sittiID",$sittiID);
    	
    	if($rs['sittiID']==$sittiID){
    		if(intval($_SESSION['v_promo'])>0){
    			$v_promo = intval($_SESSION['v_promo']);
    			switch($v_promo){
    				case 1:
    					$this->View->assign("INIT_TOPUP","200000");
    				break;
    				case 2:
    					$this->View->assign("INIT_TOPUP","300000");
    				break;
    				case 3:
    					$this->View->assign("INIT_TOPUP","500000");
    				break;
    				default:
    					
    				break;
    			}
    		}
    		$this->View->assign("SALDO",$saldo['budget']);
    		//print_r($rs);
    		if(strlen($rs['alt_id'])>0){
				
    			return $this->View->toString("SITTI/pembayaran.html");
    		}else{
    			$alt_id = $this->getAlternateId($profile);
    			
    			if($alt_id>0){
    				$this->open(4);
					$q = $this->query("UPDATE db_billing.sitti_account_balance SET alt_id='".$alt_id."'
    							WHERE sittiID='".$rs['sittiID']."'");
					$this->close();
					
					return $this->View->toString("SITTI/pembayaran.html");
    			}else{
    				$js = $this->registerIndomog($profile);
					if($js->response->data->rc=="00"){
						$alt_id = $js->response->data->algid;
						$this->open(4);
						$q = $this->query("UPDATE db_billing.sitti_account_balance SET alt_id='".$alt_id."'
    							WHERE sittiID='".$rs['sittiID']."'");
						$this->close();
						return $this->View->toString("SITTI/pembayaran.html");	
					}else{
						//$msg = "Mohon maaf, tidak dapat mengirimkan permintaan anda ke server. Silahkan coba kembali beberapa saat lagi!";
						//return $this->View->showMessage($msg,"beranda.php");
						if($js->response->data->rc=="89"||$js->response->data->rc=="909"||$js->response->data->rc=="98"){ 
						//datanya sudah teregistrasi di indomog sebelumnya.
						//jadi kita update aja alternate_idnya
						$this->open(4);
						$alt_id = $this->getAlternateId($profile);

						$q = $this->query("UPDATE db_billing.sitti_account_balance SET alt_id='".$alt_id."'
    							WHERE sittiID='".$rs['sittiID']."'");


						$this->close();
						return $this->View->toString("SITTI/pembayaran.html");
					}else{
						//print $js->response->data->rc;
						$msg = "Mohon maaf, tidak dapat mengirimkan permintaan anda ke server. Silahkan coba kembali beberapa saat lagi!";
						return $this->View->showMessage($msg,"beranda.php");
					}
					return $this->View->showMessage($msg,"beranda.php");
					}
					
    			}
    			/*
    			$js = $this->registerIndomog($profile);
    			if($js->response->data->rc=="00"){
					$alt_id = $js->response->data->algid;
					$this->open(4);
					$q = $this->query("INSERT db_billing.sitti_account_balance(sittiID,alt_id)
    							VALUES('".$profile['sittiID']."','".$alt_id."')");
					$this->close();
					return $this->View->toString("SITTI/pembayaran.html");
				}else{
					if($js->response->data->rc=="89"){
						return $this->View->toString("SITTI/pembayaran.html");
					}else{
						$msg = "Mohon maaf, tidak dapat mengirimkan permintaan anda ke server. Silahkan coba kembali beberapa saat lagi!";
						return $this->View->showMessage($msg,"beranda.php");
					}
				}
				*/
    		}
    		
    	}else{

    			$js = $this->registerIndomog($profile);
			
				if($js->response->data->rc=="00"){
					
					$alt_id = $js->response->data->algid;
					$this->open(4);
					$q = $this->query("INSERT db_billing.sitti_account_balance(sittiID,alt_id)
    							VALUES('".$profile['sittiID']."','".$alt_id."')");
					$this->close();
					return $this->View->toString("SITTI/pembayaran.html");
				}else{
					if($js->response->data->rc=="89"||$js->response->data->rc=="909"){ 
						//datanya sudah teregistrasi di indomog sebelumnya.
						//jadi kita update aja alternate_idnya
						$this->open(4);
						$alt_id = $this->getAlternateId($profile);
						if($alt_id>0){
							$q = $this->query("INSERT db_billing.sitti_account_balance(sittiID,alt_id)
	    							VALUES('".$profile['sittiID']."','".$alt_id."')");
							$this->close();
							return $this->View->toString("SITTI/pembayaran.html");
						}else{
							$msg = "Mohon maaf, tidak dapat mengirimkan permintaan anda ke server. Silahkan coba kembali beberapa saat lagi!";
							return $this->View->showMessage($msg,"beranda.php");
						}
					}else{
						//print $js->response->data->rc;
						$msg = "Mohon maaf, tidak dapat mengirimkan permintaan anda ke server. Silahkan coba kembali beberapa saat lagi!";
						return $this->View->showMessage($msg,"beranda.php");
					}
					return $this->View->showMessage($msg,"beranda.php");
				}
    		}
    	
    }
    function registerIndomog($profile){
    	$api = new IndomogAPI(false);
		$out = $api->register($profile);
		$js = json_decode($out);
		$this->log($profile['sittiID']."-"."register -- ".$out."\n----------------------------\n");
		return $js;
    }
    function hasBillingProfile($sittiID){
    	$this->open(4);
    	$sql = "SELECT * FROM db_billing.sitti_account_balance 
				WHERE sittiID = '".mysql_escape_string($sittiID)."' LIMIT 1";
    	$rs = $this->fetch($sql);
    	$this->close();
    	
    	if($rs['sittiID']==$sittiID&&($rs['budget']>0||$rs['real_cash']>0)){
    		return true;
    	}
    	
    }
    function getAlternateId($profile){
    	$api = new IndomogAPI(false);
		$out = $api->lookup($profile);
		$js = json_decode($out);
		$this->log($profile['sittiID']."-"."getID -- ".$out."\n----------------------------\n");
		return $js->response->data->mi;
    }
    /**
     * display today's saldo.
     * today's saldo = last_real_cash + today's credit amounts
     * @param $sittiID
     * @return int
     */
    function getTodaySaldo($sittiID){
    	/*$sql="SELECT sitti_id, balance, tgl_submit
    	FROM db_billing_report.adv_temporar
    	WHERE sitti_id = '".$sittiID."'";
    	*/
    	$sql = "SELECT saldo as balance 
    			FROM db_billing_report.adv_detail
    			WHERE sitti_id = '".$sittiID."' 
    			ORDER BY datee DESC, row_id DESC LIMIT 1";
    
    	$this->open(4);
    	$rs = $this->fetch($sql);
    	$this->close();
    	$budget = $rs['balance'];
    	settype($budget,'integer');
    	/*
    	//last day budget
    	$sql = "SELECT * FROM db_billing_report.balance_daily 
    			WHERE sittiID='".$sittiID."' 
    			AND datee = DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 1 DAY)";
    	$this->open(4);
    	$lday = $this->fetch($sql);
    	$this->close();
    	
    	//get today credit amount
    	$sql = "SELECT SUM(cash_number) AS amount FROM db_billing.tbl_credit WHERE sitti_id='".$sittiID."' AND DATE(tgl_submit) = DATE(NOW())";
    	$this->open(4);
    	$tcredit = $this->fetch($sql);
    	$this->close();
    	$budget = $lday['cash'] + $tcredit['amount'];
    	settype($budget,'integer');
    	print mysql_error();
    	return $budget;
    	**/
    	return $budget;
    }
    /**
     * 
     * check if the user is under 100% Voucher Discount promo.
     * @param $sittiID
     */
    function isFreePromo($sittiID){
    	$sql = "SELECT COUNT(id) as total FROM db_billing.tbl_discount WHERE sitti_id='".$sittiID."' AND n_status=2 AND discount=100";
    	$this->open(4);
    	$promo = $this->fetch($sql);
    	$this->close();
    	if($promo['total']>0){
    		return true;
    	}
    }
    function log($msg){
    	$path = "../logs/topup.log";
    	$fp = fopen($path,"a+");
    	$msg = "[".date("Y-m-d H:i:s")."]".$msg;
    	fwrite($fp,$msg,strlen($msg));
    	fclose($fp);
    }

    function publisherSummary($sitti_id, $from, $to)
    {
        $query = "SELECT * FROM (SELECT * 
                    FROM db_billing_report.pub_account_history 
                    WHERE publisher_id = '". $sitti_id ."' 
                    AND datee BETWEEN '" .$from. "' AND '" .$to. "' 
                    ORDER BY datee DESC LIMIT 100) A ORDER BY datee ASC, row_id ASC";
        
        $this->open(4);
        $result = $this->fetch($query, 1);
        $this->close();

        return $result;
    }

    function isHaveRedeemCode($email,$event)
    {
        $query = "SELECT redeem_code 
                    FROM db_web3.tbl_redeem2 
                    WHERE email = '".$email."' AND event='".$event."'
                    LIMIT 1";
        
        $this->open(0);
        $result = $this->fetch($query);
        $this->close();

        return (is_array($result) && count($result) > 0);
    }

    function setRedeemCode($event, $email, $nama, $telp, $voucher_amount,$ref_id=0)
    {
        /* get redeem code */
        $redeem_code = '';
        $query_get_redeem_code = "SELECT kode
                                    FROM db_billing_report.redeem_code2
                                    WHERE n_status = '0' AND event = '".$event."'
                                    LIMIT 1";
		
        $this->open(4);
        $result_redeem_code = $this->fetch($query_get_redeem_code);
        $this->close();

        if (is_array($result_redeem_code) && count($result_redeem_code) > 0)
        {
            $redeem_code = $result_redeem_code['kode'];
        }
		
        $result_redeem2 = $result_update_redeem_code2 = false;
        if ($redeem_code != '')
        {
            $query_insert_redeem2 = "INSERT INTO db_web3.tbl_redeem2 
                                    (email, nama, telp, redeem_code, redeem_date,ref_id,event) 
                                    VALUES
                                    ('". $email ."', '". $nama ."', '". $telp ."', '".$redeem_code."', NOW(),".intval($ref_id).",'".$event."')";

            $this->open(0);
            $result_redeem2 = $this->query($query_insert_redeem2);
           
            $this->close();
			
			if ($result_redeem2){
				$query_update_redeem_code2 = "UPDATE db_billing_report.redeem_code2 
												SET n_status = '1' 
												WHERE kode = '". $redeem_code ."' 
												AND n_status = '0'";

				$this->open(4);
				$result_update_redeem_code2 = $this->query($query_update_redeem_code2);
			  
				$this->close();
			}
        }
        return ($result_redeem2 && $result_update_redeem_code2) ? $redeem_code : false;
    }
}
?>
