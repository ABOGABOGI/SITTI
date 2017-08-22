<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
class PPA_API extends SITTIApp{
	var $_expiry;
	var $_interval;
	var $_max_request_per_interval;
	var $_logger = "../logs/ppa_api.log";
	function __construct($req,$acc){
		parent::SITTIApp($req,$acc);
	}
	
	function save_transaction(){
		$req = $this->Request;
		$advertiser_id = mysql_escape_string($_REQUEST['sitti_id']);
		$enc_token = mysql_escape_string($req->getPost("token"));
		$token_id = urldecode64($enc_token);
		$transaction_id = mysql_escape_string($req->getPost('transaction_id'));
		$order_name = mysql_escape_string($req->getPost('order_name'));
		$total_price = intval($req->getPost('total_price'));
		$this->open(0);
		
		//validate token id
		$sql = "SELECT token_id,advertiser_id FROM zz_sitti.ppa_token WHERE token_id = ".mysql_escape_string($token_id)." LIMIT 1";
		$token_data = $this->fetch($sql);
		
		if($token_data['token_id']!=$token_id&&$token_data['advertiser_id']!=$advertiser_id){
			$arr = array("status"=>"401","message"=>"Invalid Token");
			return $arr;
		}
		//check token id.. ada apa tidak.
		$sql = "INSERT IGNORE INTO zz_sitti.ppa_log_checkout (token_id, transaction_id, item_name, item_value, item_price, item_subtotal)
				VALUES(".$token_id.",".$transaction_id.",'".mysql_escape_string($order_name)."',1,".$total_price.",".$total_price.")";
		$q = $this->query($sql);
		$log_id = mysql_insert_id();
		
		if($q){
			if($log_id>0){
				$arr = array("status"=>"1","message"=>"Transaction Recorded Successfully !",
						"response_id"=>$log_id,"token_id"=>$enc_token,"transaction_id"=>$transaction_id,
						"order_name"=>$order_name,"total_price"=>$total_price);
			}else{
				$arr = array("status"=>"400","message"=>"this token id has already been recorded");
			}
		}else{
			$arr = array("status"=>"0","message"=>"Transaction Failed");
		}
		
		$this->close();
		return $arr;
	}
	
	
	
	function get_order_list($limit=1000){
		$since_id = $this->Request->getPost('since_id');
		$sitti_id = $this->Request->getPost('sitti_id');
		$this->open(0);
		$sql = "SELECT b.id AS since_id,b.token_id,b.transaction_id,b.item_name AS order_name,b.item_subtotal AS order_value,b.transaction_time AS log_time
				FROM 
				zz_sitti.ppa_token a
				INNER JOIN
				zz_sitti.ppa_log_checkout b
				ON a.token_id = b.token_id
				WHERE a.advertiser_id = '".mysql_escape_string($sitti_id)."' 
				AND b.id>".intval($since_id)."
				ORDER BY b.id ASC
				LIMIT ".intval($limit);
		$rs = $this->fetch($sql,1);
		$this->close();
		$last_id = $rs[sizeof($rs)-1]['since_id'];
		$rs['next_since_id'] = $last_id;
		return $rs;
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
	function run(){
		if($this->Request->getPost('method')=="save_transaction"){
			$arr = $this->save_transaction();
		}else if($this->Request->getPost('method')=="history"){
			$arr = $this->get_order_list();
		}else{
			$arr = array("status"=>"0","message"=>"Invalid API Call");
		}
		return $arr;
	}
	function get_api_key($sittiID){
		$this->open(0);
		$sql = "SELECT * FROM zz_sitti.ppa_api_key WHERE advertiser_id='".$sittiID."' LIMIT 1";
		$rs = $this->fetch($sql);
		$this->close();
		if($rs['advertiser_id']==$sittiID&&strlen($rs['api_key'])==40){
		 	return $rs['api_key'];	
		}
	}
	function authenticated(){
		global $CONFIG;
		//print strlen($CONFIG['SITTI_PPA_SECRET']);
		//$advertiser_id = "AC0000003";
		//$salt = 1111;
		//print "<br/>".$advertiser_id.$salt.$CONFIG['SITTI_PPA_SECRET']."<br/>";
		//$api_key = sha1($advertiser_id.$salt.$CONFIG['SITTI_PPA_SECRET']);
		
		//$api_key = mysql_escape_string($api_key);
		$api_key = $_REQUEST['api_key'];
		$sittiID = $_REQUEST['sitti_id'];
		
		if(strlen($sittiID)>5&&strlen($api_key)==40&&$api_key==$this->get_api_key($sittiID)){
			//print $api_key;
			$realm = "SITTI_PPA";
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
			    header('WWW-Authenticate: Basic  realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
			    header('HTTP/1.0 401 Unauthorized');
			    exit;
			} else {
				$username = mysql_escape_string($_SERVER['PHP_AUTH_USER']);
				$password = md5(mysql_escape_string($_SERVER['PHP_AUTH_PW']));
				$this->Account->open(0);
			    $arr = $this->Account->login($username,$password,1,'ppa');
			    $this->Account->close();
			    //print $username."<br/>".$password."<br/>";
			   
			    if($arr['status']=="1"){
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