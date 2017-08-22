<?php

class SITTIIpaymu extends SQLData{
	function SITTIPaypal(){
		parent::SQLData();
	}

	function insertIpaymu($transaction_id, $nominal, $transaction_code){
		$this->open(4);
		$rs = $this->updateLookup($transaction_id, $transaction_code);
		$this->insertData($rs, $transaction_id, $nominal);
		$this->close();
	}

	function checkIpaymu($transaction_id){
		$this->open(4);
		$rs = $this->fetch("SELECT sitti_id FROM tbl_transaction_lookup " .
				"WHERE transaction_id_sitti='".$transaction_id."' AND flag = 1");
		$this->close();

		return !empty($rs);

	}

	function get_transaction($transaction_id){
		$this->open(4);
		$rs = $this->fetch("SELECT sitti_id, nominal, transaction_id FROM tbl_transaction_lookup " .
				"WHERE transaction_id_sitti='".$transaction_id."' AND flag = 1");
		$this->close();

		return $rs;

	}

	function insertData($rs, $transaction_id, $nominal){
		$sittiID = $rs['sitti_id'];
		$tgl_submit = date("Y-m-d H:i:s");

		$flag = $this->query("INSERT INTO tbl_credit (tgl_submit, sitti_id, vendor_id, cash_number, balance_type, payment_method, transaction_code, transaction_date, klikbca_login, mobile_number) VALUES('".$tgl_submit."', '".$sittiID."', 'IPAYMU', '".$nominal."', '1', '8', '".$transaction_id."', '".$tgl_submit."', '', '')");
		if ($flag){
			$flag = $this->query("INSERT INTO tbl_transaction(tgl_submit, sitti_id, vendor_id, cash_number, balance_type, payment_method, transaction_code, transaction_date, transaction_type) VALUES('".$tgl_submit."', '".$sittiID."', 'IPAYMU', '".$nominal."', '1', '8', '".$transaction_id."', '".$tgl_submit."', '0')");
			if ($flag){
				$flag = $this->query("INSERT INTO tbl_balance_stack(sittiID, amount, submit_date, last_update, balance_type, credit_trans_id) VALUES('".$sittiID."', '".$nominal."', '".$tgl_submit."', '".$tgl_submit."', '1', '".$transaction_id."')");
				if ($flag){
					$now = date("Y-m-d H:i:s");

					$flag = $this->query("INSERT INTO sitti_account_balance(sittiID, budget, last_update) VALUES('".$sittiID."', '".$nominal."', '".$now."') ON DUPLICATE KEY UPDATE budget = budget+".$nominal.", last_update = '".$now."'");
					if (!$flag){
						echo "INSERT INTO sitti_account_balance(sittiID, budget, last_update) VALUES('".$sittiID."', '".$nominal."', '".$now."') ON DUPLICATE KEY UPDATE budget = budget+".$nominal.", last_update = ".$now."";
						$this->query("DELETE FROM tbl_balance_stack WHERE credit_trans_id='".$transaction_id."'");
						$this->query("DELETE FROM tbl_transaction WHERE transaction_code='".$transaction_id."'");
						$this->query("DELETE FROM tbl_credit WHERE transaction_code='".$transaction_id."'");
						return flag;
					}else{
					}
				}else{
					$this->query("DELETE FROM tbl_transaction WHERE transaction_code='".$transaction_id."'");
					$this->query("DELETE FROM tbl_credit WHERE transaction_code='".$transaction_id."'");
					return flag;
				}
			}else{
				$this->query("DELETE FROM tbl_credit WHERE transaction_code='".$transaction_id."'");
				return flag;
			}
		}
	}

	function updateLookup($transaction_id, $transaction_code){
		# update tabel lookup flag=1
		$this->query("UPDATE tbl_transaction_lookup SET flag=1, transaction_id='".$transaction_code."' WHERE transaction_id_sitti='".$transaction_id."'");

		return $this->fetch("SELECT sitti_id FROM tbl_transaction_lookup WHERE transaction_id_sitti='".$transaction_id."'");
	}

	function insertLookup($sittiID, $nominal){
		$this->open(4);
		$flag = false;
		$transaction_id = "";
		while (!$flag){
			$random12digit = "".time();
			$random12digit = $random12digit."".rand(10,99);
			$transaction_id = "SITTI-IPAYMU-".$random12digit;
			$flag = $this->insertLookupData($sittiID, $transaction_id, $nominal);
		}
		$this->close();
		return $transaction_id;
	}

	function insertLookupData($sittiID, $transaction_id, $nominal){
		return $this->query("INSERT INTO tbl_transaction_lookup(sitti_id, transaction_id_sitti, nominal) VALUES('".$sittiID."', '".$transaction_id."', '".$nominal."')");
	}

	function insertPostData($post_str){
		$this->open(4);
		$str_query = "INSERT INTO zz_sitti.sitti_ipaymu_notify(post_value) VALUES('".mysql_real_escape_string($post_str)."')";
		$this->query($str_query);
		$this->close();

	}
}

?>