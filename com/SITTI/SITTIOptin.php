<?php

class SITTIOptin extends SQLData{
	function SITTIOptin(){
		parent::SQLData();
	}
	
	function insertOptin($sittiID, $ipaddress){
		$this->open(0);
		$flag = $this->insertData($sittiID, $ipaddress);
		$this->close();
		return $flag;
	}
	
	function insertData($sittiID, $ipaddress){
		$ipaddresslongint = sprintf('%u', ip2long($ipaddress));
		return $this->query("INSERT IGNORE INTO optin(publisher_id, ipaddress, ipaddresslongint) VALUES('".$sittiID."', '".$ipaddress."', '".$ipaddresslongint."')");
	}
}

?>