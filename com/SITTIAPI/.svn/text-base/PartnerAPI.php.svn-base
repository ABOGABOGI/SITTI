<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
class PartnerAPI extends SITTIApp{
	var $_expiry;
	var $_interval;
	var $_max_request_per_interval;
	var $_logger = "../logs/partner_api.log";
	function __construct($req,$acc){
		parent::SITTIApp($req,$acc);
	}
	
	
	function execute(){
		if($this->authenticated()){
			$arr = $this->run();
		}else{
			$arr = array("status"=>"-1","message"=>"Access Denied !");
		}
		$advertiser_id = mysql_escape_string($_REQUEST['sitti_id']);
		$this->write_log($this->_logger,'DEBUG',$advertiser_id." - ".json_encode($arr));
		if($_REQUEST['return_type']=="xml"){
			header("content-type: text/xml");
			return $this->toXML($arr);
		}else{
			return json_encode($arr);
		}
	}
	function foo(){
		return json_encode(array("foo"=>array("status"=>"1")));
	}
	function get_stats(){
		$start = intval($_REQUEST['last_id']);
		$limit = intval($_REQUEST['limit']);
	
		$sitti_id = mysql_escape_string($_REQUEST['sitti_id']);
		if(eregi("([A-Z0-9]+)",$sitti_id)){
			if($limit==0){
				$limit = 100;
			}
			if($limit>1000){
				$limit = 1000;
			}
			$this->open(0);
			$sql = "SELECT a.partner_iklan_id,b.id_iklan AS sitti_iklan_id,a.judul,a.content,b.jum_imp AS impresi,
					b.jum_klik AS klik,b.ctr,b.last_update 
					FROM db_web3.sitti_partner_inventory a
					INNER JOIN db_report.tbl_performa_iklan_total b
					ON a.iklan_id = b.id_iklan
					WHERE a.partner_id = '".$sitti_id."' LIMIT ".$start.",".$limit;
			
			$rs = $this->fetch($sql,1);
			
			$this->close();
			
			if(sizeof($rs)>0){
				$arr = array("status"=>"1","data"=>$rs);
			}else{
				$arr = array("status"=>"0","message"=>"Tidak ada data yang tersedia.");
			}
		}else{
			$arr = array("status"=>"-1","message"=>"format sitti_id yang dimasukkan, salah");
		}
		return $arr;
	}
	/*
	function get_inventory(){
		$sitti_id = mysql_escape_string($_REQUEST['sitti_id']);
		if(eregi("([A-Z0-9]+)",$sitti_id)){
			$arr = array("status"=>"1","data"=>$rs);
		}else{
			$arr = array("status"=>"-1","message"=>"format sitti_id yang dimasukkan, salah");
		}
		return $arr;
	}*/
	function run(){
		if($this->Request->getPost('method')=="get_stats"){
			$arr = $this->get_stats();
		}else{
			$arr = array("status"=>"0","message"=>"Invalid API Call");
		}
		return $arr;
	}
	function get_api_key($sittiID){
		$this->open(0);
		$sql = "SELECT * FROM db_api.partner_api_key WHERE advertiser_id='".$sittiID."' LIMIT 1";
		$rs = $this->fetch($sql);
		$this->close();
		
		if($rs['advertiser_id']==$sittiID&&strlen($rs['api_key'])==40){
		 	return $rs['api_key'];	
		}
	}
	function authenticated(){
		global $CONFIG;
		//print strlen($CONFIG['SITTI_PPA_SECRET']);
		//$advertiser_id = "AC0007470";
		//$salt = 1111;
		//print "<br/>".$advertiser_id.$salt.$CONFIG['SITTI_PPA_SECRET']."<br/>";
		//$api_key = sha1($advertiser_id.$salt.$CONFIG['SITTI_PPA_SECRET']);
		//print $api_key;
		//$api_key = mysql_escape_string($api_key);
		$api_key = $_REQUEST['api_key'];
		$sittiID = $_REQUEST['sitti_id'];
		
		
			$realm = "SITTI_PARTNER";
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
			    header('WWW-Authenticate: Basic  realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			    header('HTTP/1.0 401 Unauthorized');
			    exit;
			} else {
				$username = mysql_escape_string($_SERVER['PHP_AUTH_USER']);
				$password = md5(mysql_escape_string($_SERVER['PHP_AUTH_PW']));
				$this->Account->open(0);
			    $arr = $this->Account->login($username,$password,1,'ppc');
			    
			    $this->Account->close();
			    //print $username."<br/>".$password."<br/>";
			    if($arr['status']=="1"){
			    	if(strlen($sittiID)>5&&strlen($api_key)==40&&
			    		$api_key==$this->get_api_key($sittiID)&&
			    		strcmp($_SESSION['sittiID'],$sittiID)==0){
			    		return true;
			    	}
			    }
			}
		
	}
	function toXML($arr){
		$str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$str.="<xml>\n";
		$str.="<response>\n";
		foreach($arr as $name=>$val){
			$str.="<".$name.">".$val."</".$name.">\n";
		}
		$str.="</response>\n";
		$str.="</xml>\n";
		return ($str);
	}
	function write_log($log_file,$type, $log_item) {
		if (is_array($log_item)) {
			foreach ($log_item AS $name=>$value) {
				$log_item_temp_arr[] = $name . ': ' . $value;
			}
			$log_line = date('Y-m-d H:i:s') . ' [' . ucfirst($type) . '] ' . implode('; ', $log_item_temp_arr);
		}
		else {
			$log_line = date('Y-m-d H:i:s') . ' [' . ucfirst($type) . '] ' . $log_item;
		}
		//if ($type == 'stats') //echo "\n";
		//echo $log_line . "\n";
		$fh = fopen($log_file, 'a') or die("Can't open log file\n");
		fwrite($fh, $log_line . "\n");
		fclose($fh);
	}
	
}
?>