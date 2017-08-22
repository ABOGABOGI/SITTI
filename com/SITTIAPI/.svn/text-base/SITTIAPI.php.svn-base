<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
class SITTIAPI extends SITTIApp{
	var $_expiry;
	var $_interval;
	var $_max_request_per_interval;
	function __construct($req,$acc){
		parent::SITTIApp($req,$acc);
		//$this->expire(15*60);//15 minutes sessions's expiry time
		$this->expire(15*60);
		$this->interval(10);
		$this->max_requests(500);
	}
	/**
	 * getter-setter for access interval limit
	 * @param int expired time in ms
	 * @return mixed
	 */
	function interval($v=NULL){
		if($v==NULL){
			return $this->_interval;
		}else{
			settype($v,'integer');
			$this->_interval = $v;
		}
	}
	/**
	 * getter-setter for maximum requests number per interval
	 * @param int expired time in ms
	 * @return mixed
	 */
	function max_requests($v=NULL){
		if($v==NULL){
			return $this->_max_request_per_interval;
		}else{
			settype($v,'integer');
			$this->_max_request_per_interval = $v;
		}
	}
	/**
	 * getter-setter for expiry
	 * @param int expired time in ms
	 * @return mixed
	 */
	function expire($v=NULL){
		if($v==NULL){
			return $this->_expiry;
		}else{
			settype($v,'integer');
			$this->_expiry = $v;
		}
	}
	function execute($req){
		if($req->getParam("xml")){
			return $this->getXMLResponse($req);
		}else{
			return $this->getJSONResponse($req);
		}
	}
	function getXMLResponse($req){
		$response = array("response"=>array("status"=>"-1","message"=>"Sorry, XML response is not supported yet"));
		return json_encode($response);
	}
	function getJSONResponse($req){
		$req = $this->getRequest(file_get_contents("php://input"));
		if($req!=null){
			$rq = json_decode($req);
			return $this->proceedResponse($rq);
		}else{
			$response = array("response"=>array("status"=>"-1","message"=>"Error Request"));
			return json_encode($response);
		}
	}
	function proceedResponse($rq){
		$apikey = $rq->request->data->apikey;
		$session = $rq->request->session;
		//if($this->isSessionActive($apikey, $session)){
			$response = $this->run($rq->request->methodName,$rq->request->data,$session);
			
		//}else{
		//	$response = array("response"=>array("status"=>"399","message"=>"SESSION INVALID"));
		//}
		return json_encode($response);
	}
	function checkInterval($apikey,$sitti_id,$user_ip){
		//print $session;
		//print $apikey."-".$sitti_id."-".$user_ip."<br/>*******************<br/>";
		$apikey = cleanString($apikey);
		$sitti_id = cleanString($sitti_id);
		$user_ip = cleanString($user_ip);
		$user_ip = ip2long($user_ip);
		$this->open(5);
		$sql = "SELECT * FROM db_api.api_session 
				WHERE api_key='".$apikey."' 
				AND sitti_id='".$sitti_id."' 
				AND user_ip='".$user_ip."' LIMIT 1";
		$s = $this->fetch($sql);
		$this->close();
		$d = datediff("s", $s['start_time'], date("Y-m-d H:i:s"));
		//var_dump($s);
		//print "<br/>----------------------<br/>";
		//print abs($d)." < ".$this->interval();
		if(abs($d)<$this->interval()){
			//print "check interval<br/>";
			
			if($s['attempt']>$this->max_requests()){
				$this->ban($s,5);
				return false;
			}
			$this->open(5);
			$sql = "UPDATE db_api.api_session SET
    				attempt=attempt+1 
    				WHERE api_key='".$apikey."' 
    				AND sitti_id='".$s['sitti_id']."'
    				AND user_ip=".$user_ip."";
			$this->query($sql);
			//print $sql;
			$this->close();
		}
		
		return true;
	}
	/**
	 * 
	 * BAN current session
	 * @param unknown_type $sess
	 * @param unknown_type $expire
	 */
	function ban($sess,$expire){
		
		$sittiID = $sess['sitti_id'];
		$api_key = $sess['api_key'];
		//print $sess;
		$block_time = date("Y-m-d H-i-s",mktime(date("H"),date("i"),date("s"),date("n"),date("j"),date("Y")));
		$expire_time = date("Y-m-d H-i-s",mktime(date("H"),date("i"),date("s")+$expire,date("n"),date("j"),date("Y")));
		$user_ip = getRealIP();
		$user_ip = ip2long($user_ip);
		$this->open(5);
		$sql = "INSERT INTO db_api.blocked_access(sitti_id,api_key,block_time,expire_time,user_ip,n_time)
				VALUES('".$sittiID."','".$api_key."','".$block_time."','".$expire_time."',".$user_ip.",".$expire.")";
		//print $sql;
		$this->query($sql);
		//print mysql_error();
		$this->close();
	}
	function isBanned($methodName,$params){
		switch($methodName){
			case "GetDetailKampanye":
				$sitti_id = $params->sitti_id;
			break;
			case "GetDetailIklan":
				$sitti_id = $params->sitti_id;
			break;
			case "GetProfile":
				$sitti_id = $params->sitti_id;
			break;
			default:
				$sitti_id = $params->id;
			break;
		}
		$this->open(5);
		$api_key = $params->apikey;
		$user_ip = getRealIP();
		$user_ip = ip2long($user_ip);
		$sql = "SELECT * FROM db_api.blocked_access 
				WHERE sitti_id='".$sitti_id."' 
				AND api_key='".$api_key."'
				AND user_ip=".$user_ip." LIMIT 1";
		
		$rs = $this->fetch($sql);
		$this->close();
		if($rs['sitti_id']==NULL){
			return false;
		}
		$d = datediff('n',$rs['block_time'],date("Y-m-d H:i:s"));
		
		if(abs($d)>=$rs['n_time']){
			//remove from blocking
			//print "unblock";
			$this->unban($rs);
			return false;
		}else{
			return true;
		}
	} 
	function unban($d){
		$sitti_id = $d['sitti_id'];
		$api_key = $d['api_key'];
		$user_ip = $d['user_ip'];
		$this->open(5);
		$sql = "DELETE FROM db_api.blocked_access 
				WHERE sitti_id='".$sitti_id."' 
				AND api_key = '".$api_key."' 
				AND user_ip='".$user_ip."'";
		$this->query($sql);
		$sql = "DELETE FROM db_api.api_session WHERE api_key='".$api_key."' AND sitti_id='".$sitti_id."' 
					AND user_ip=".$user_ip;
		//print $sql;
		$this->query($sql);
		$this->close();
		//$this->clearSession($api_key, $sitti_id,$user_ip);
	}
	function run($methodName,$params,$session=null){
		if($this->isBanned($methodName,$params)){
			$status = array("code"=>355,"session"=>"-1");
			return $this->getResponse($status);
		}
		switch($methodName){
			case "GetListKampanye":
				//check if the user request for valid data based on her sittiId
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					$user_ip = getRealIP();
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetListKampanye($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>310,"session"=>$session);
				}
			break;
			case "GetListKataKunci":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetListKataKunci($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>313,"session"=>$session);
				}
			break;
			
			case "GetListIklan":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetListIklan($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>311,"session"=>$session);
				}
			break;
			case "GetListPenempatan":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetListPenempatan($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>314,"session"=>$session);
				}
			break;
			case "GetDetailKampanye":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->sitti_id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->sitti_id)){
							$status = $this->GetDetailKampanye($params->apikey, $params->sitti_id,$params->campaign_id);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>310,"session"=>$session);
				}
			break;
			case "GetDetailIklan":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->sitti_id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->sitti_id)){
							$status = $this->GetDetailIklan($params->apikey, $params->sitti_id,$params->iklan_id);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>311,"session"=>$session);
				}
			break;
			case "GetProfile":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->sitti_id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->sitti_id)){
							$status = $this->GetProfile($params->apikey, $params->sitti_id);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>312,"session"=>$session);
				}
			break; 
			case "GetBudget":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->sitti_id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->sitti_id)){
							$status = $this->GetBudget($params->apikey, $params->sitti_id);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>312,"session"=>$session);
				}
			break; 
			case "GetBudgetHistory":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetBudgetHistory($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>312,"session"=>$session);
				}
			break; 
			case "GetPerformaKampanye":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetPerformaKampanye($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>311,"session"=>$session);
				}
			break;
			case "GetPerformaIklan":
				$sess = $this->getSessionDetail($params->apikey,$session);
				if($sess['sitti_id']==$params->id){
					if(!$this->checkInterval($params->apikey,$params->id,$user_ip)){
						$status = array("code"=>355,"session"=>"-1");
					}else{
						if($this->isSessionActive($params->apikey, $session,$params->id)){
							$status = $this->GetPerformaIklan($params->apikey, $params->id,$params->list_number,$params->sort);
						}else{
							$status = array("code"=>399,"session"=>"-1");
						}
					}
				}else{
					$status = array("code"=>311,"session"=>$session);
				}
			break;
			case "GetUserAuth":
				$status = $this->GetUserAuth($params->apikey, $params->id, $params->username, $params->password);
			break;
			
			default:
				$status = array("code"=>300,"session"=>"-1");
			break;
		}
		return $this->getResponse($status);
		
	}
	function getResponse($status){
		
		$code = $status['code'];
		$session = $status['session'];
		$data = $status['data'];
		if($data==null){$data = "N/A";}
		settype($code,'integer');
		settype($session,'string');
		switch($status['code']){
			case 1:
				$response = array("response"=>array("status"=>$code,"message"=>"SUCCESS","data"=>$data,"session"=>$session));
			break;
			case 300:
				$response = array("response"=>array("status"=>$code,"message"=>"API CALL INVALID","data"=>$data,"session"=>$session));
			break;
			case 301:
				$response = array("response"=>array("status"=>$code,"message"=>"LOGIN ERROR","data"=>$data,"session"=>$session));
			break;
			case 302:
				$response = array("response"=>array("status"=>$code,"message"=>"API KEY INVALID","data"=>$data,"session"=>$session));
			break;
			case 310:
				$response = array("response"=>array("status"=>$code,"message"=>"data kampanye tidak ditemukan","data"=>$data,"session"=>$session));
			break;
			case 311:
				$response = array("response"=>array("status"=>$code,"message"=>"Data Iklan tidak ditemukan","data"=>$data,"session"=>$session));
			break;
			case 312:
				$response = array("response"=>array("status"=>$code,"message"=>"Profile tidak ditemukan","data"=>$data,"session"=>$session));
			break;
			case 313:
				$response = array("response"=>array("status"=>$code,"message"=>"Data Kata Kunci tidak ditemukan","data"=>$data,"session"=>$session));
			break;
			case 314:
				$response = array("response"=>array("status"=>$code,"message"=>"Data Penempatan iklan tidak ditemukan","data"=>$data,"session"=>$session));
			break;
			case 316:
				$response = array("response"=>array("status"=>$code,"message"=>"Informasi Budget tidak tersedia","data"=>$data,"session"=>$session));
			break;
			case 317:
				$response = array("response"=>array("status"=>$code,"message"=>"Jarak tanggal terlalu lama. maksimal hanya boleh 1 bulan.","data"=>$data,"session"=>$session));
			break;
			
			case 399:
				$response = array("response"=>array("status"=>$code,"message"=>"SESSION INVALID","data"=>$data,"session"=>$session));
			break;
			case 355:
				$response = array("response"=>array("status"=>$code,"message"=>"YOU GOT BANNED","data"=>$data,"session"=>"-1"));
			break;
			default:
				$response = array("response"=>array("status"=>$code,"message"=>"Unknown Error","session"=>$session));
			break;
		}
		return $response;
	}
	function ApiKeyValid($apikey){
		settype($apikey,"string");
		$sql = "SELECT * FROM db_api.api_user WHERE apikey='".$apikey."' LIMIT 1";
		$this->open(5);
		$rs = $this->fetch($sql);
		$this->close();
		if($rs['apikey']==$apikey){
			return true;
		}
	}
	function isSessionActive($apikey,$session,$sitti_id){
		
		$apikey = cleanString($apikey);
		$session = cleanString($session);
		$sitti_id = cleanString($sitti_id);
		
		if(strlen($apikey)>30&&strlen($session)>30&&strlen($sitti_id)>5){
			
			$rs = $this->getSessionDetail($apikey, $session);
			
			$user_ip = ip2long(getRealIP());
			//print $rs['api_key']."==".$apikey."|".$rs['sessionhash']."==".$session."|".$rs['sitti_id']."==".$sitti_id."|".$rs['user_ip']."==".$user_ip."|";
			if($rs['api_key']==$apikey
			   &&$session==$rs['sessionhash']
			   &&$sitti_id==$rs['sitti_id']
			   &&$user_ip==$rs['user_ip']){
				//print "CHECKING !";
				$d = datediff('s',$rs['start_time'],date("Y-m-d H:i:s"));
				//print "session exp ? ".$d."<".$this->expire()."<br/>";;
				if(abs($d)<$this->expire()){
					return true;
				}
			}
		}
	}
	function getSessionDetail($apikey,$session){
		$sql = "SELECT * FROM db_api.api_session 
					WHERE sessionhash='".$session."' 
					AND api_key='".$apikey."'
					LIMIT 1";
		
		$this->open(5);
		$rs = $this->fetch($sql);
		$this->close();
		
		return $rs;
	}
	
	function clearSession($apikey,$sitti_id,$user_ip){
		$apikey = cleanString($apikey);
		$sitti_id = cleanString($sitti_id);
		$user_ip = cleanString($user_ip);
		settype($user_ip,'integer');
		print $apikey."-".$sitti_id."-".$user_ip."<br/>";
		if(strlen($apikey)>30&&strlen($sitti_id)>10&&$user_ip>0){
			$sql = "DELETE FROM db_api.api_session WHERE api_key='".$apikey."' AND sitti_id='".$sitti_id."' 
					AND user_ip=".$user_ip;
			print $sql;
			$this->open(5);
			$rs = $this->query($sql);
			$this->close();
		}
	}
	function getRequest($data){
		//do some filtering here.
		
		//-->
		return $data;
	}
	function Foo(){
		return $response = array("response"=>array("status"=>"1","message"=>$methodName." --> SUCCESS"));;
	}
    function GetUserAuth ($apikey,$sitti_id, $username, $password, $attempt = 3){
    	
    	$apikey = cleanString($apikey);
    	$sitti_id = cleanString($sitti_id);
    	$username = cleanString($username);
    	$password = cleanString($password);

    	if($this->ApiKeyValid($apikey)&&strlen($sitti_id)>0&&strlen($username)>0&&strlen($password)>0){
    		
    		$this->Account->open(0);
    		$b_login = $this->Account->login($username,md5($password),1);
    		$this->Account->close();
    		
    		$this->open(0);
    		$sql = "SELECT *,b.email FROM db_web3.sitti_account a,db_web3.sitti_account_profile b 
    				WHERE a.sittiID='".$sitti_id."' AND b.user_id = a.id LIMIT 1";
    		$rs = $this->fetch($sql);
    		$this->close();
    		$login_ok = false;
    		if($b_login){
    			
    			if($rs['sittiID']==$sitti_id&&$rs['email']==$username&&$rs['password']==md5($password)){
	    			$login_ok = true;
    			}
    		}
    		if($login_ok){
    			$status['code'] = "1";
	    		$status['session']=$this->generateSession($apikey,$sitti_id,true);
    		}else{
    			$status['code'] = "301";
    			$status['session']="-1";
    		}
    	}else{
    		$status['code'] = "302";
    		$status['session']="-1";
    	}
    	
    	return $status;
    }
    function generateSession($apikey,$sitti_id,$skip_interval=false){
    	global $API_CONFIG;
    	
    	
    	
    	$session_key = md5("//sess//".date("YmdHisHisdmYmdHis").$apikey."//sess//".$API_CONFIG['secret_key'].rand(0,99999));
    	$user_ip = getRealIP();
    	
    	//$this->clearSession($apikey);
    	//check for old sessions
    	//if exists, update the session_key instead of create a new one
    	$this->open(5);
    	$sql = "SELECT * FROM db_api.api_session WHERE api_key='".$apikey."' AND sitti_id='".$sitti_id."' LIMIT 1";
    	$rs = $this->fetch($sql);
    	if($rs['api_key']==$apikey&&$sitti_id=$rs['sitti_id']){
    		//there's an exist session key for this sitti_id/api_key
    		//we update the session now.
    		$sql = "UPDATE db_api.api_session SET sessionhash='".$session_key."',
    				start_time=NOW(),user_ip=".ip2long($user_ip)." 
    				WHERE api_key='".$apikey."' AND sitti_id='".$sitti_id."'";
    		
    	}else{
    		//the session key never created before, so we create one.
    		$sql = "INSERT INTO db_api.api_session(api_key,sessionhash,start_time,attempt,user_ip,sitti_id) 
    			 VALUES('".$apikey."','".$session_key."',NOW(),0,".ip2long($user_ip).",'".$sitti_id."')";
    		
    	}
    	
    	//print $sql."<br/>-------<br/>";
    	$q = $this->query($sql);
    	//print mysql_error();
    	$this->close();
    	
    	if($q){
    		return $session_key;
    	}
    	return "-1";
    }
	function doFetch($sql,$flag=0){
    	$this->open(5);
    	$rs = $this->fetch($sql,$flag);
    	$this->close();
    	return $rs;
    }
    /**
     * 
     * Enter description here ...
     * @param $apikey
     * @param $sitti_id
     * @param $list_number
     * @param $sort_type
     */
    function GetPerformaKampanye($apikey,$sitti_id, $list_number=20, $sort_type=1){
    	//print $apikey."<br/>";
    	//print $sitti_id."<br/>";
    	if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		$sql = "SELECT kampanye_id AS id,kampanye AS name,jum_imp AS impression,jum_klik AS clicks,
					ctr,harga,posisi,budget_harian,budget_total,budget_sisa,last_update
				FROM db_report.tbl_performa_akun_kampanye_total 
				WHERE advertiser_id='".$sitti_id."' AND  tbl_performa_akun_kampanye_total.status = 0
				ORDER BY last_update ".$SORT." 
				LIMIT ".$nlimit;
    		
			$rs = $this->doFetch($sql,1);
			$n_len = sizeof($rs);
    		$status['code'] = "1";
    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
    	
    }
    
	function GetListKatakunci ($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		$sql = "SELECT keyword,jum_imp AS impression,jum_hit AS click,ctr,
					budget_harian,budget_total,budget_sisa,avg_cpc,avg_top5_cpc,last_update 
					FROM db_report.tbl_performa_kata_kunci_total 
					WHERE advertiser_id='".$sitti_id."' AND tbl_performa_kata_kunci_total.status = 0
					ORDER BY last_update ".$SORT." 
					LIMIT ".$nlimit;
    		
			$rs = $this->doFetch($sql,1);
			$n_len = sizeof($rs);
    		$status['code'] = "1";
    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetListPenempatan($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		$sql = "SELECT publisher_id AS pubID,web_name AS webpage,keywords AS keyword_iklan,user_keyword AS effective_keyword,ctr,last_update
					FROM db_report.tbl_performa_penempatan_iklan_total 
					WHERE advertiser_id='".$sitti_id."'
					ORDER BY last_update ".$SORT." 
					LIMIT ".$nlimit;
    		
			$rs = $this->doFetch($sql,1);
			$n_len = sizeof($rs);
    		$status['code'] = "1";
    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetPerformaIklan($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		$sql = "SELECT id_iklan as id,nama_iklan,keywords,jum_imp as impression,jum_klik as click,
					ctr,harga,posisi,budget_harian,budget_total,budget_sisa,last_update
					FROM db_report.tbl_performa_iklan_total 
    				WHERE advertiser_id = '".$sitti_id."' 
    				AND tbl_performa_iklan_total.status=0
					ORDER BY last_update ".$SORT." 
					LIMIT ".$nlimit;
    		
			$rs = $this->doFetch($sql,1);
			$n_len = sizeof($rs);
    		$status['code'] = "1";
    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetDetailKampanye($apikey,$sitti_id,$campaign_id){
		if($this->ApiKeyValid($apikey)){
			if(strlen($sitti_id)>0&&strlen($campaign_id)>0){
	    		$sitti_id = cleanString($sitti_id);
	    		$campaign_id = cleanString($campaign_id);
	    		settype($campaign_id, 'integer');
	    		
	    		$sql = "SELECT sittiID as sitti_id,ox_campaign_id as campaign_id,created_date,name,description 
	    				FROM db_web3.sitti_campaign 
	    				WHERE sittiID='".$sitti_id."'
	    				AND ox_campaign_id=".$campaign_id." LIMIT 1";
	    		$this->open(0);
				$rs = $this->fetch($sql);
				$this->close();
				if($rs['sitti_id']==$sitti_id&&$rs['campaign_id']==$campaign_id){
		    		$status['code'] = "1";
		    		$status['data'] = $rs;
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}else{
					$status['code'] = "310";
		    		$status['data'] = "N/A";
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}
			}else{
				$status['code'] = "310";
		    	$status['data'] = "N/A";
		    	$status['session']=$this->generateSession($apikey,$sitti_id);
			}
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetDetailIklan ($apikey,$sitti_id,$iklan_id){
		if($this->ApiKeyValid($apikey)){
			if(strlen($sitti_id)>0&&strlen($iklan_id)>0){
	    		$sitti_id = cleanString($sitti_id);
	    		$iklan_id = cleanString($iklan_id);
	    		settype($iklan_id, 'integer');
	    		
	    		$sql = "SELECT id AS iklan_id,advertiser_id AS sitti_id,nama,judul,baris1,urlName AS alamat_tampil,urlLink AS alamat_url,tglisidata AS created_date
						FROM db_web3.sitti_ad_inventory WHERE advertiser_id='".$sitti_id."' AND id=".$iklan_id." LIMIT 1";
	    		$this->open(0);
				$rs = $this->fetch($sql);
				$this->close();
				if($rs['sitti_id']==$sitti_id&&$rs['iklan_id']==$iklan_id){
		    		$status['code'] = "1";
		    		$status['data'] = $rs;
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}else{
					$status['code'] = "311";
		    		$status['data'] = "N/A";
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}
			}else{
				$status['code'] = "311";
		    	$status['data'] = "N/A";
		    	$status['session']=$this->generateSession($apikey,$sitti_id);
			}
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetProfile ($apikey,$sitti_id){
		if($this->ApiKeyValid($apikey)){
			if(strlen($sitti_id)>0){
	    		$sitti_id = cleanString($sitti_id);
	    		
	    		$sql = "SELECT sittiID AS sitti_id,NAME,email,address AS alamat,phone,mobile,komplek,blok,province,city FROM db_web3.sitti_account a,db_web3.sitti_account_profile b 
							WHERE a.sittiID = '".$sitti_id."' 
							AND a.id = b.user_id LIMIT 1
	    		";
	    		
	    		$this->open(0);
				$rs = $this->fetch($sql);
				$this->close();
				if($rs['sitti_id']==$sitti_id){
		    		$status['code'] = "1";
		    		$status['data'] = $rs;
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}else{
					$status['code'] = "312";
		    		$status['data'] = "N/A";
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}
			}else{
				$status['code'] = "312";
		    	$status['data'] = "N/A";
		    	$status['session']=$this->generateSession($apikey,$sitti_id);
			}
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetBudget($apikey,$sitti_id){
		if($this->ApiKeyValid($apikey)){
			if(strlen($sitti_id)>0){
	    		$sitti_id = cleanString($sitti_id);
	    		$sql = "SELECT sittiID as sitti_id,real_cash AS budget FROM db_billing.sitti_account_balance
						WHERE sittiID = '".$sitti_id."' 
						LIMIT 1";
	    		
	    		$this->open(4);
				$rs = $this->fetch($sql);
				$rs['budget'] = round($rs['budget'],0);
				$this->close();
				
				if($rs['sitti_id']==$sitti_id){
		    		$status['code'] = "1";
		    		$status['data'] = $rs;
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}else{
					$status['code'] = "316";
		    		$status['data'] = "-";
		    		$status['session']=$this->generateSession($apikey,$sitti_id);
				}
			}else{
				$status['code'] = "316";
		    	$status['data'] = "N/A";
		    	$status['session']=$this->generateSession($apikey,$sitti_id);
			}
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetBudgetHistory($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		$sql = "SELECT
				DATE_FORMAT(datee,'%d/%m/%Y') AS tgl,
				`sitti_id`,
				`transaction_code` AS kode_transaksi,
				`jenis` AS jenis_transaksi,
				`credit` AS kredit,
				`debit` AS debit,
				`saldo`
				FROM `db_billing_report`.`adv_detail`
				WHERE sitti_id = '".$sitti_id."' AND datee >= NOW() - INTERVAL ".$list_number." DAY 
				ORDER BY datee ".$SORT." 
				LIMIT ".$nlimit;
    		
			$rs = $this->doFetch($sql,1);
			$n_len = sizeof($rs);
    		$status['code'] = "1";
    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	/**
	 * 
	 * PENDING
	 * @param $apikey
	 * @param $sitti_id
	 * @param $date_start
	 * @param $date_end
	 * @param $list_type
	 */
	function GetListKampanye($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		//$date_end = cleanString($date_end);
    		//$date_start = cleanString($date_start);
    		//$list_type = cleanString($list_type);
			$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		
    		//$d = datediff('m', $date_start, $date_end);
    		//if(abs($d)<=1){
	    		$sql = "SELECT ox_campaign_id AS id,sitti_campaign.name AS kampanye,description AS deskripsi 
						FROM db_web3.sitti_campaign WHERE sittiID = '".$sitti_id."' 
						ORDER BY ctr ".$SORT." 
						LIMIT ".$nlimit;
	    		
	    		$this->open(0);
				$rs = $this->fetch($sql,1);
				$this->close();
				$n_len = sizeof($rs);
	    		$status['code'] = "1";
	    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
	    		$status['session']=$this->generateSession($apikey,$sitti_id);
    		//}else{
    		////	$status['code'] = "317";
    			//$status['data'] = "N/A";
    		//	$status['session']=$this->generateSession($apikey,$sitti_id);
    		//}
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
	function GetListIklan($apikey,$sitti_id, $list_number=20, $sort_type=1){
		if($this->ApiKeyValid($apikey)){
    		$sitti_id = cleanString($sitti_id);
    		
			$list_number = cleanString($list_number);
    		$sort_type = cleanString($sort_type);
    		settype($list_number,'integer');
    		settype($sort_type,'integer');
    		
    		if($list_number>100){
    			$list_number=100;
    		}
    		$nlimit = $list_number;
    		if($sort_type=='1'){
    			$SORT = 'DESC';
    		}else{
    			$SORT = 'ASC';
    		}
    		
    		
	    		$sql = "SELECT id,nama,judul,baris1 AS baris_iklan,created_date AS tgl_buat,urlName AS alamat_tampil,
						urlLink AS alamat_url,ox_campaign_id AS campaign_id  
						FROM db_web3.sitti_ad_inventory WHERE advertiser_id = '".$sitti_id."' AND ad_flag=0 
						ORDER BY ctr ".$SORT." 
						LIMIT ".$nlimit;
	    		
	    		$this->open(0);
				$rs = $this->fetch($sql,1);
				$this->close();
				$n_len = sizeof($rs);
	    		$status['code'] = "1";
	    		$status['data'] = array("rows"=>$rs,"length"=>$n_len);
	    		$status['session']=$this->generateSession($apikey,$sitti_id);
    		
    	}else{
    		$status['code'] = "302";
    		$status['data'] = "N/A";
    		$status['session']=$this->generateSession($apikey,$sitti_id);
    	}
    	return $status;
	}
}
?>