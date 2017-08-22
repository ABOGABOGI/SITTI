<?php
class ReferReportPage {
	var $Account;
	var $Profile;
	var $View;

	function ReferReportPage($profile, $account) {
		$this->Account = $account;
		$this->Profile = $profile;
		$this->View = new BasicView();
	}
	
	function getReferReport(){
		$this->Account->open(2);
		$rs = $this->Account->fetch("SELECT referred_id, referred_name, income FROM db_report.advertiser_referral_topup WHERE advertiser_id='".$this->Profile['sittiID']."'", 1);
		$this->Account->close();
		return $rs;
	}

	function showReportPage($isMember) {
		if ($isMember){
			global $CONFIG;
			$rs = $this->getReferReport();
			$this->View->assign("referred_list",$rs);
			$this->View->assign("refid",$this->Profile['sittiID']);
			$this->View->assign("WebsiteURL",$CONFIG['WebsiteURL']);
			return $this->View->toString("SITTI/refer_report.html");
		}else{
			return $this->View->toString("SITTI/daftar_affiliate.html");
		}
	}
}
?>