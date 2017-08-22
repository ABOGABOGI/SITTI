<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
class PPA_Sandbox extends SITTIApp{
	var $_expiry;
	var $_interval;
	var $_max_request_per_interval;
	function __construct($req,$acc){
		parent::SITTIApp($req,$acc);
	}
	function main($req){
		global $PS_CONFIG;
		global $CONFIG;
		
			if($req->getPost("api_key")!=NULL){
				$api_key = mysql_escape_string($req->getPost('api_key'));
				$sql = "SELECT a.advertiser_id,b.* FROM zz_sitti.ppa_api_key a
						INNER JOIN db_web3.sitti_ad_inventory b
						ON a.advertiser_id = b.advertiser_id
						WHERE a.api_key = '".$api_key."' 
						AND b.serve_type=1
						LIMIT 100";
				$this->open(0);
				$ads = $this->fetch($sql,1);
				$this->close();
				if(sizeof($ads)==0){
					print "<script>alert('iklan tidak ditemukan !');</script>";
				}else{
					$_GET['rand'] = "148416642565280";
					$_GET['sitti_pub_id'] = "BC0001095";
					$_GET['type'] = "10";
					$_GET['sitti_ad_number'] = "3";
					$_GET['d'] = "1248";
					
					$_REQUEST['rand'] = "148416642565280";
					$_REQUEST['sitti_pub_id'] = "BC0001095";
					$_REQUEST['type'] = "10";
					$_REQUEST['sitti_ad_number'] = "3";
					$_REQUEST['d'] = "1248";
				}
				
				foreach($ads as $ii=>$val){
					
					 $params = array("a"=>$val['id'],
									"p"=>"BC0001095",
									"dest"=>"".$val['urlLink'],
									"ref"=>"sitti.sandbox",
									"k"=>"test",
									"d"=>"0",
									"r"=>"".rand(0,9999),
					 				"debug_mode"=>1,
									"v"=>"");
					
					$p = json_encode($params);
					$clickURL = $PS_CONFIG['tracker_uri']."/ppa.php?rc=".urlencode64($p);
					$ads[$ii]['clickURL'] = $clickURL;
				}
				$this->View->assign("ads",$ads);
			}
		
		return $this->View->toString("sandbox/ppa.html");
	}
}
?>