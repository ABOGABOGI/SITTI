<?php
/**
 * 
 * Helper class untuk dipakai oleh SITTIVoucher
 * @author duf
 *
 */
class SITTIVoucherHelper extends SQLData{
	var $err_no;
	//constructor
	function SITTIVoucherHelper(){
		parent::SQLData();
		// variabel for validate code voucher
		// 0: no error, 1: error already redeem same event, 2: error voucher code no longer available, 3: not yet create ad
		$this->err_no = 0;
	}
	/**
	 * fungsi untuk menggenerate kode voucher
	 * menghasilkan 12 digit kode unique untuk penggunaan 1x pakai
	 */
	function generateCode($type){
		$isOk = false;
		$n = "";//new code
		
		
		$this->open(4);
		//print $n;
		$retry=0;
		while(!$isOk){
			$rand = rand(100,999);
			$rand2 = rand(1000,9999);
			$str = "".$type.$rand.$rand2;
			//print $str." ";
			$n = $str;
			//$n = (float)$str;
			$sql = "INSERT INTO db_billing.redeem_code(kode,create_date,n_status)
					VALUES('".$str."',NOW(),0)";
			//print $sql;
			
			$q = $this->query($sql);
			if($q){
				//print " [OK]<br/>";
				$isOk = true;
			}else{
				//print mysql_error();
				//print " [ERROR]<br/>";
			}
			$retry++;
			if($retry==99){
				$n=-1;
				break;
			}
		}
		$this->close();
		return $n;
	}
	/**
	 * getting the last id
	 */
	function getLatestId(){
		/*$this->open(4);
		
		$this->close();*/
		return 1;
	}
	/**
	 * 
	 * validasi kode voucher.
	 *
	 * @param $kode
	 * @param $type 1->voucher bonus, 2-> voucher topup
	 */
	function ValidateCodeVoucher($sitti_id,$kode,$type=1){
		
		//pastikan kode yang dimasukkan itu integer
		if(!$this->isFormatValid($kode)){
			//print "format gak valid";
			return false;
		}
		$isalreadycreatead = $this->isAlreadyCreateAd($sitti_id);
		if (!$isalreadycreatead){
			$this->err_no = 3;
			return false;
		}
		$this->open(4);
		$event = $this->getCodeEvent($kode);
		if (!$event){
			$this->close();
			return false;
		}
		$isalreadyredeem = $this->isAlreadyRedeemForEvent($sitti_id,$event);
		if (!$isalreadyredeem){
			$rs = $this->getCode($kode,$type);
			
			$isValid = false;
			if($rs['kode']==$kode&&$rs['n_status']==0){
				if($this->disableCode($kode,$type)){
					$isValid = true;
				}
			}
			if (!$isValid){
				$this->err_no = 2;
			}
			$this->close();
			return $isValid;
		}else{
			$this->err_no = 1;
			$this->close();
			return false;
		}
		
	}
	function ValidateCode($kode,$type=1){
		
		//pastikan kode yang dimasukkan itu integer
		
		if(!$this->isFormatValid($kode)){
			//print "format gak valid";
			return false;
		}
		$this->open(4);
		$rs = $this->getCode($kode,$type);
			
		$isValid = false;
		if($rs['kode']==$kode&&$rs['n_status']==0){
			if($this->disableCode($kode,$type)){
				$isValid = true;
			}
		}
		$this->close();
		return $isValid;
		
	}
	function UnvalidateCode($kode,$type=1){
		//pastikan kode yang dimasukkan itu integer
		
		if(!$this->isFormatValid($kode)){
			//print "format gak valid";
			return false;
		}
		$this->open(4);
		$rs = $this->getCode($kode,$type);
		$isValid = false;
		if($rs['kode']==$kode&&$rs['n_status']==1){
			if($this->enableCode($kode,$type)){
				$isValid = true;
			}
		}
		$this->close();
		return $isValid;
		
	}
	function isFormatValid($kode){
		if(eregi("([0-9]+)", $kode)){
			return true;
		}
	}
	function getCode($kode,$type=1){
		$sql = "SELECT kode,n_status,amount FROM db_billing.redeem_code WHERE kode=".$kode." AND type=".$type." LIMIT 1";
		$rs = $this->fetch($sql);
		return $rs;
	}
	function getCodeEvent($kode){
		$sql = "SELECT event FROM db_billing.redeem_code WHERE kode=".$kode." LIMIT 1";
		$rs = $this->fetch($sql);
		return $rs['event'];
	}
	function isAlreadyRedeemForEvent($sitti_id,$event){
		$sql = "SELECT kode FROM db_billing.tbl_voucher WHERE send_to='".$sitti_id."'";
		$rs = $this->fetch($sql,1);
		$codes = "";
		if (count($rs)==0){
			return false;
		}
		for($i=0;$i<count($rs);$i++){
			if ($codes!=""){
				$codes .= ",";
			}
			$codes .=$rs[$i]['kode'];
		}
		$sql = "SELECT kode FROM db_billing.redeem_code WHERE kode IN (".$codes.") AND event='".$event."'";
		$rs = $this->fetch($sql);
		if ($rs['kode']){
			return true;
		}else{
			return false;
		}
	}
	function isAlreadyCreateAd($sitti_id){
		$this->open();
		$sql = "SELECT COUNT(*) as jmlh FROM db_web3.sitti_ad_inventory WHERE advertiser_id='".$sitti_id."' AND ad_flag IN (0,1)";
		$rs = $this->fetch($sql);
		$this->close();
		if ($rs['jmlh']>0){
			return true;
		}else{
			return false;
		}
	}
	function disableCode($kode,$type=1){
		$sql = "UPDATE db_billing.redeem_code SET n_status=1 WHERE kode=".$kode."";
		if($this->query($sql)){
			$rs = $this->getCode($kode,$type);
			//pastikan vouchernya benar2 uda di update statusnya ke 1
			if($rs['kode']==$kode&&$rs['n_status']==1){
				return true;
			}
		}
	}
	function enableCode($kode,$type=1){
		$this->open(4);
		$sql = "UPDATE db_billing.redeem_code SET n_status=0 WHERE kode=".$kode."";
		if($this->query($sql)){
			$rs = $this->getCode($kode,$type);
			//pastikan vouchernya benar2 uda di update statusnya ke 1
			if($rs['kode']==$kode&&$rs['n_status']==0){
				return true;
			}
		}
		$this->close();
	}
	function registerVoucher($sittiID,$kode,$is_approved=1,$vtype=1){
		$this->open(4);
		$rs = $this->getCode($kode,$vtype);
		$client_id = 0;
		$document_id = "SITTI VOUCHER";
		$name = "VOUCHER #".$kode;
		$created_by = "redeem system";
		$amount = $rs['amount'];
		
		$type = 2;
		$send_to = $sittiID;
		$sql = "INSERT INTO db_billing.tbl_voucher(client_id,document_id,name,created_by,amount,apply_date,created_date,type,send_to,is_approved,kode)
				VALUES('".$client_id."','".$document_id."','".$name."','".$created_by."','".$amount."',NOW(),NOW(),".$type.",'".$sittiID."',".$is_approved.",".$kode.")";
		
		$q = $this->query($sql);
		$voucher_id = mysql_insert_id();
		$this->close();
		if($q){
			return array(1,$client_id,$document_id,$name,$created_by,$amount,$type,$sittiID,$voucher_id);
		}else{
			$this->enableCode($kode,$vtype);
			return array(0);
		}
		
	}
	function unregisterVoucher($voucher_id){
		$this->open(4);
		$sql = "DELETE FROM db_billing.tbl_voucher WHERE id = ".$voucher_id;
		$q = $this->query($sql);
		$this->close();
		return $q;
	}
	
}
?>