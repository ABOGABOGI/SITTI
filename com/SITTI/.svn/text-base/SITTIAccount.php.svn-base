<?php 
/**
 * 
 * Enter description here ...
 * @author Hapsoro Renaldy N
 *
 */
 
 include_once "SITTIActionLog.php";

class SITTIAccount extends SQLData{
	var $View;
	function SITTIAccount($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
		$this->ActionLog = new SITTIActionLog();
	}
	function getActiveID(){
		return $_SESSION['sittiID'];
	}
    function create($role, $username, $password, $enc, $type = 2,$industry=0){
    	$roleCode = array(null,"A","B","N","O");
    	$businessType = array(null,"P","C");
    	
    	//$rs = $this->fetch("SELECT COUNT(*) as total FROM sitti_account LIMIT 1");
    	$rs = $this->fetch("SELECT adv_counter as total FROM account_counter");
    	$queue = $rs['total']+1;
    	$no = str_pad($queue, 6,'000000',STR_PAD_LEFT);
    	$sittiID = $roleCode[$role].$businessType[$type].$industry.$no;
    	//increement counter
    	$rs = $this->query("UPDATE account_counter SET adv_counter = adv_counter+1");
    	
    	//$cek = $this->fetch("SELECT sittiID FROM sitti_account WHERE sittiID='".$sittiID."' LIMIT 1");
    	/*
    	//retry attempts are limited to 20
    	$retry_attempts = 999;
    	$retry=0;
    	while(strlen($cek['sittiID'])!=0){
    		
    		$queue+=1;
    		$no = str_pad($queue, 6,'000000',STR_PAD_LEFT);
    		$sittiID = $roleCode[$role].$businessType[$type].$industry.$no;
    		$cek = $this->fetch("SELECT sittiID FROM sitti_account WHERE sittiID='".$sittiID."' LIMIT 1");
    		if($cek['sittiID']==""){
    			//recek ke table sitti_account yang telah di hapus
    			//kita pastikan account id yang baru tidak ada didalam daftar account yang di hapus
    			$cek2 = $this->fetch("SELECT sittiID FROM sitti_account WHERE sittiID='".$sittiID."' LIMIT 1");
    		}
    		$retry++;
    		//retry attempt exceeded.
    		if($retry==$retry_attempts){
    			break;
    		}
    	}
    	*/
    	$password = md5(sha1($password.$enc));
    	//add cpc user to lookup
        /*$sql = "INSERT IGNORE INTO sitti_account_lookup(advertiser_id,status_cpc,status_ppm,status_ppa,last_update)
        			VALUES('".$sittiID."',1,0,0,NOW())";
        $this->query($sql);*/
		if ($role>2){ // advertiser dari API
			$role=1;
		}
        return $this->query("INSERT INTO sitti_account(role, username, password, enc_key,sittiID,n_status,tgldaftar)
                            VALUES('".$role."', '".$username."', '".$password."', '".$enc."','".$sittiID."',0,NOW())");
        
        
    }
	function autoConfirmAdvertiser($sittiID,$username){
		return $this->query("UPDATE sitti_account SET n_status = 1 WHERE username='".$username."'");
		
	}
    function createProfile($user_id, $nama, $email, $alamat, $komplek, $blok,
                                $province, $city, $telepon, $handphone,$twitter){
        return $this->query("INSERT INTO sitti_account_profile(user_id, name, email, address, komplek, blok,
                            province, city, phone, mobile,twitter)
                            VALUES('".$user_id."', '".$nama."', '".$email."', '".$alamat."', '".$komplek."',
                            '".$blok."', '".$province."', '".$city."', '".$telepon."', '".$handphone."','".$twitter."')");
    }

    function createBillingProfile($user_id, $alamat, $card_no, $bank_acc){
        return $this->query("INSERT INTO sitti_billing_profile(user_id, address, card_no, bank_acc)
                            VALUES('".$user_id."', '".$alamat."', '".$card_no."', '".$bank_acc."')");
    }
    /**
     * 
     * membuat business profile untuk user bersangkutan
     * @param $user_id
     */

    /*
    function createBusinessProfile($user_id,$company,$industry,$brand,$city,$business_type){
    	
    }
     *
     */

    function createBusinessProfile($userID, $companyType, $company, $brand, $city_industry){
        return $this->query("INSERT INTO sitti_business_profile(user_id, tipe_perusahaan, perusahaan, brand, kota)
                                VALUES('".$userID."', '".$companyType."', '".$company."', '".$brand."',
                                '".$city_industry."')");
    }

    function createCPCProfile($userID)
    {
        $query = "INSERT INTO db_web3.sitti_account_lookup (advertiser_id, status_cpc)
                    VALUES ('". $userID ."', 1)";
    
        return $this->query($query);
    }

    function createBannerProfile($userID)
    {
        $query = "INSERT INTO db_web3.sitti_account_lookup (advertiser_id, status_ppm)
                    VALUES ('". $userID ."', 1)";
    
        return $this->query($query);
    }

    function createPPAProfile($userID)
    {
        $query = "INSERT INTO db_web3.sitti_account_lookup (advertiser_id, status_ppa)
                    VALUES ('". $userID ."', 1)";
    
        return $this->query($query);
    }
	
	function insertAdvertiserReferral($advertiser_id, $referral_id)
    {
		$this->open(0);
        $query = "INSERT INTO db_web3.sitti_advertiser_referral (advertiser_id, referral_id)
                    VALUES ('". $advertiser_id ."', '". $referral_id ."')";
        $q = $this->query($query);
		$this->close();
		return $q;
    }
	
	function insertAdvertiserReferralReport($advertiser_id, $referred_id, $referred_name)
    {
		$this->open(2);
        $query = "INSERT IGNORE INTO db_report.advertiser_referral_topup
(advertiser_id,referred_id,referred_name,income)
                    VALUES ('". $advertiser_id ."', '". $referred_id ."', '". $referred_name ."', '0')";
        $q = $this->query($query);
		$this->close();
		return $q;
    }
    
    function sendEmail(){
        return true;
    }
    
    
    // AUTHENTICATION STUFF !
    
    /**
     * 
     * Login method
     * @param $username
     * @param $password
     * @param $enc_pass
     */
    function login($email, $password, $role, $type = false){
    	//$this->logout();
        /*return $this->fetch("SELECT * FROM sitti_account WHERE role='99' AND username='".$username."'
                             AND password='".$password."' AND enc_key='".$enc_pass."' LIMIT 1");*/
    	
    	/** retrieve username dan password **/
    	//$username = mysql_escape_string($username);
    	$email = mysql_escape_string($email);
    	$role = mysql_escape_string($role);
    	
    	// pemeriksaan user yang login, apakah user ppa, banner, atau iklan biasa
        $sql_for_type = '';
        if ($type == 'ppa') // user ppa
        {
            $sql_for_type = ' INNER JOIN db_web3.sitti_account_lookup c
                                ON ( a.sittiID = c.advertiser_id AND c.status_ppa = 1 ) ';
        }
        elseif ($type == 'ppm') // user ppm
        {
            $sql_for_type = ' INNER JOIN db_web3.sitti_account_lookup c
                                ON ( a.sittiID = c.advertiser_id AND c.status_ppm = 1 ) ';
        }
        else // user cpc
        {
            $sql_for_type = ' INNER JOIN db_web3.sitti_account_lookup c
                                ON ( a.sittiID = c.advertiser_id AND c.status_cpc = 1 ) ';
        }

        //sekarang orang login menggunakan email
    	$sql = "SELECT a.id,a.sittiID,a.username,b.name,b.email,a.password,a.enc_key,a.n_status
    				FROM db_web3.sitti_account a
                    INNER JOIN db_web3.sitti_account_profile b 
    				ON ( a.id = b.user_id )
                    $sql_for_type
                    WHERE a.role='".$role."' 
                    AND b.email='".$email."'  
    				AND a.enc_key <> ''
    				ORDER BY id DESC 
    				LIMIT 1";
		// echo $sql."<br/>";
    	
        $rs = $this->fetch($sql);
    
    	if($rs['id']<=1857){
    		
    		$enc_key = md5($rs['username'].$password);
    	}else{
    		
    		$enc_key = md5($email.$password);
    	}
		// print_r($rs);
		// echo "<br/>".$enc_key;
		// echo "<br/>".$email;
		// echo "<br/>".md5(sha1($password.$enc_key));
		// die();
    	
    	if($rs['enc_key']==$enc_key&&$rs['email']==$email&&$rs['password']==md5(sha1($password.$enc_key))){
    		
    		if($rs['n_status']=="0"){
    			$info['name'] = $rs['name'];
    			$info['email'] = $rs['email'];
    			return array("status"=>"2","confirmKey"=>$this->getConfirmKey($rs['id']),"info"=>$info);
    		} elseif ($rs['n_status']=="99") {
                return array("status"=>"99");      
            }else{
    			session_regenerate_id();
	    		//save sittiID into session
	    		$_SESSION['sittiID'] = $rs['sittiID'];
	    		
	    		$this->updateLoginTime($rs['sittiID']);
	    		//update session status
	    		$this->updateSession();
    			return array("status"=>"1");
    		}
    	}
    	return array("status"=>"0");
    	
    }
	
	function login_via_onigi($sittiID){
		session_regenerate_id();
		$_SESSION['sittiID'] = $sittiID;
		$this->updateLoginTime($sittiID);
		$this->updateSession();
		return true;
	}
    /**
     * 
     * check if the user is a firsttimer
     * @param $flag 0 for advertiser, 1 for publisher
     */
    function isFirstTimer($flag=0){
    	if($flag==1){
    		$rs = $this->fetch("SELECT login_number FROM sitti_account_publisher
    							WHERE sittiID='".mysql_escape_string($_SESSION['sittiID'])."'
    							LIMIT 1");
    		if($rs['login_number']==1){
    			return true;
    		}
    	}else{
    		$rs = $this->fetch("SELECT login_number FROM sitti_account
    							WHERE sittiID='".mysql_escape_string($_SESSION['sittiID'])."'
    							LIMIT 1");
    		if($rs['login_number']==1){
    			return true;
    		}
    	}
    }
    function updateLoginTime($sittiID,$flag = 0){
    	if($flag==1){
    		return $this->query("UPDATE sitti_account_publisher SET login_number = login_number+1, last_login = NOW() WHERE sittiID='".$sittiID."'");
    	}else{
    		return $this->query("UPDATE sitti_account SET login_number = login_number+1, last_login = NOW() WHERE sittiID='".$sittiID."'");
    	}
    }
    function logout(){
    	$this->query("UPDATE sitti_account SET session_id='' WHERE sittiID='".$_SESSION['sittiID']."'");
    	$_SESSION['sittiID']=NULL;
    	$_SESSION[base64_encode("sitti_login_session")] = null;
    	session_destroy();
    	return true;
    }
    /**
     * 
     * Update user's session
     * @param $flag  0 --> advertiser, 1 --> publisher
     */
    function updateSession($flag=0){
    	//$sessionID = md5(date("YmdHis"));
    	
    	if($_SESSION['sittiID']!=NULL){
    		$sesstime = mktime(date("H"),date("i")+60,date("s"),date("n"),date("j"),date("Y"));
    		$sess_key = urlencode64(json_encode(array("sess_time"=>$sesstime,"sittiID"=>$_SESSION['sittiID'])));
    		$sessionID = urlencode64($sesstime);
    		if($flag==1){
    			$this->query("UPDATE sitti_account_publisher SET session_id='".$sessionID."' WHERE sittiID='".$_SESSION['sittiID']."'");
    		}else{
    			$this->query("UPDATE sitti_account SET session_id='".$sessionID."' WHERE sittiID='".$_SESSION['sittiID']."'");
    		}
    		$_SESSION[base64_encode("sitti_login_session")] = $sess_key;
    	}
    }
    /**
     * 
     * check login session
     * @todo
     * implementasi session time cap
     */
    function isLogin($flag=0){
    	if($flag==1){
    		
    		$rs = $this->fetch("SELECT session_id,sittiID FROM sitti_account_publisher WHERE sittiID='".$_SESSION['sittiID']."' LIMIT 1");
    		
    	}else{
    		$rs = $this->fetch("SELECT session_id,sittiID FROM sitti_account WHERE sittiID='".$_SESSION['sittiID']."' LIMIT 1");
    	}
    	if(strlen($rs['session_id'])>0){
    		/*if($_SESSION[base64_encode("sitti_login_session")]==$rs['session_id']){
    			$this->updateSession($flag);
    			return true;
    		}
    		*/
    		$sessparam = urldecode64($_SESSION[base64_encode("sitti_login_session")]);
    		$ss = json_decode($sessparam);
    		//print $ss->sittiID;
    		//var_dump($ss);
    		if($ss->sittiID==$rs['sittiID']){
//    			/var_dump($rs);
    			$sesstime = urldecode64($rs['session_id']);
				// echo $sesstime.",";
				// echo $ss->sess_time;
    			if($sesstime==$ss->sess_time){
    				if(time()<$sesstime){
    					//print time()." --> ".$sesstime." vs ".$ss->sess_time;
    					//die();
    					//if($ss->sess_time>$rs['s'])
    					$this->updateSession($flag);
    					return true;
    				}
    			}
    		}
    	}
    }
    function getProfile(){
    	//print_r($_SESSION)
		if(strlen($_SESSION['sittiID'])>0&&strlen($_SESSION['sittiID'])<=10){
			$rs = $this->fetch("SELECT a.id, a.sittiID,a.ox_pub_id,a.ox_adv_id,a.login_number,b.* FROM sitti_account a,sitti_account_profile b
								WHERE a.sittiID='".mysql_escape_string($_SESSION['sittiID'])."' AND a.id = b.user_id 
								LIMIT 1");
			//print_r($rs);
			return $rs;
		}
    }
	function getSaldo($sittiID){
		include_once "SITTIBilling.php";
		$sittiID = mysql_escape_string($sittiID);
		$sittiID = cleanString($sittiID);
		$billing = new SITTIBilling($this->Request,null);
		$budget = $billing->getTodaySaldo($sittiID);
		$rs['budget'] = $budget;
		return $rs;
		//this is become obselete .. sorry.
    	//print_r($_SESSION)
    	/**if(strlen($_SESSION['sittiID'])>0&&strlen($_SESSION['sittiID'])<=10){
    		$sittiID = mysql_escape_string($_SESSION['sittiID']);
    		$rs = $this->fetch("SELECT budget FROM
    							db_billing.sitti_account_balance WHERE sittiID='".$sittiID."'
    							LIMIT 1");
    		
    		//print_r($rs);
    		return $rs;
    	}**/
    }

    function advertiserLastLoginTime($sitti_id = false)
    {
        $sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
        
        $query = "SELECT last_login FROM db_web3.sitti_account WHERE sittiID ='".$sitti_id."' LIMIT 1";
        $result = $this->fetch($query);
        
        return (bool) $result ? $result['last_login'] : false;
    }

    function publisherLastLoginTime($sitti_id = false)
    {
        $sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
        
        $query = "SELECT last_login FROM db_web3.sitti_account_publisher WHERE sittiID ='".$sitti_id."' LIMIT 1";
        $result = $this->fetch($query);
        
        return (bool) $result ? $result['last_login'] : false;
    }

	function isFreePromo($sittiID){
		include_once "SITTIBilling.php";
		$sittiID = mysql_escape_string($sittiID);
		$sittiID = cleanString($sittiID);
		$billing = new SITTIBilling($this->Request,null);
		$free = $billing->isFreePromo($sittiID);
		return $free;
    }
    function getProfileBySittiID($sittiID){
    	$rs = $this->fetch("SELECT a.sittiID,a.ox_pub_id,a.ox_adv_id,a.login_number,b.* FROM sitti_account a,sitti_account_profile b
    							WHERE a.sittiID='".mysql_escape_string($sittiID)."' AND a.id = b.user_id 
    							LIMIT 1");
    	return $rs;
    }
    function updateCredential($user_id,$email,$password){
    	$fix_pass = md5($password);
    	$email = mysql_escape_string(urldecode($email));
        $enc = md5($email.$fix_pass);
        //for backward compatibility-->
        $rs = $this->fetch("SELECT id,username FROM sitti_account WHERE id=".$user_id);
        if($user_id<=1857){
        	$enc = md5($rs['username'].$fix_pass);
        }else{
        	$enc = md5($email.$fix_pass);
        }
        $fix_pass = md5(sha1($fix_pass.$enc));
        return $this->query("UPDATE sitti_account SET password='".$fix_pass."',enc_key='".$enc."' WHERE id='".$user_id."'");
    }
    /**
     * 
     * update publisher's credential
     * @param $user_id
     * @param $username
     * @param $password
     */
	function updatePublisherCredential($user_id,$username,$password){
		//print $username."-".$password;
    	$fix_pass = md5($password);
        $enc = md5($username.$fix_pass);
         $fix_pass = md5(sha1($fix_pass.$enc));
       // print "UPDATE sitti_account_publisher SET password='".$fix_pass."',enc_key='".$enc."' WHERE id='".$user_id."'";
        return $this->query("UPDATE sitti_account_publisher SET password='".$fix_pass."',enc_key='".$enc."' WHERE id='".$user_id."'");
    }
    function updateProfile($user_id, $nama, $email, $alamat, $telepon, $handphone){
    
    	return $this->query("UPDATE sitti_account_profile 
    						SET name='".$nama."',address='".$alamat."',
    						phone='".$telepon."',mobile='".$handphone."'
    						WHERE user_id='".$user_id."'");
    }
    /**
     * 
     * set openx advertiser id ke user ini.
     * @param $sittiID
     * @param $ox_adv_id
     * @return boolean
     */
    function setOXAdvertiserID($sittiID,$ox_adv_id){
    	return $this->query("UPDATE sitti_account SET ox_adv_id='".$ox_adv_id."' WHERE sittiID='".$sittiID."'");
    }
    /**
     * 
     * set openx publisher id ke user ini.
     * @param $sittiID
     * @param $ox_pub_id
     * @return boolean
     */
    function setOXPublisherID($sittiID,$ox_pub_id){
    	return $this->query("UPDATE sitti_account SET ox_pub_id='".$ox_pub_id."' WHERE sittiID='".$sittiID."'");
    }
    /**
     * 
     * check if an email is exist within database.
     * @param $email
     * @return boolean
     */
    function isEmailExist($username,$email){
    	/*$rs = $this->fetch("SELECT a.username,b.email FROM sitti_account a,sitti_account_profile b
    						WHERE a.id = b.user_id AND b.email='".$email."' AND a.username='".$username."' LIMIT 1");*/
    	
    	$rs = $this->fetch("SELECT a.username,b.email FROM sitti_account a,sitti_account_profile b
    						WHERE a.id = b.user_id AND b.email='".$email."'
    						AND a.enc_key <> '' AND n_status < 2
    						GROUP BY a.id DESC 
    						LIMIT 1");
    	
    	/*if($username==$rs['username']&&$email==$rs['email']){
    		return true;
    	}*/
    	if($email==$rs['email']){
    		
    		return true;
    	}
    }
	/**
     * 
     * check if publisher with the email is exist within database.
     * @param $email
     * @return boolean
     */
    function isPublisherEmailExist($email){
    	$rs = $this->fetch("SELECT a.username,a.email FROM sitti_account_publisher a
    						WHERE a.email='".$email."' AND a.username='".$email."' LIMIT 1");
    	
    	
    	if($email==$rs['username']&&$email==$rs['email']){
    		return true;
    	}
    }
    /**
     * 
     * retrieve credential by user's email
     * @param $email
     * @return array
     */
    function getCredentialByEmail($email){
    	/*$rs = $this->fetch("SELECT * FROM sitti_account a,sitti_account_profile b 
    						WHERE a.id = b.user_id AND a.username='".$username."' 
    						AND b.email='".$email."' LIMIT 1");*/
    	$rs = $this->fetch("SELECT * FROM sitti_account a,sitti_account_profile b 
    						WHERE a.id = b.user_id
    						AND b.email='".$email."'
    						AND a.enc_key <> '' 
    						ORDER BY a.id DESC 
    						LIMIT 1");
    	return $rs;
    }
	/**
     * 
     * retrieve publisher's credential by user's email
     * @param $email
     * @return array
     */
    function getPublisherCredentialByEmail($email){
    	$rs = $this->fetch("SELECT * FROM sitti_account_publisher a
    						WHERE a.username='".$email."' 
    						AND a.email='".$email."' LIMIT 1");
    	return $rs;
    }
    function registerRequestKey($key,$username,$email){
    	if(strlen($key)>0&&strlen($username)>0&&strlen($email)>0){
    		
    		if($this->query("INSERT INTO sitti_change_password_queue(request_key,username,email)
    					 VALUES('".$key."','".$username."','".$email."')")){
    			return true;
    		}
    	}
    	
    }
    /**
     * 
     * changed password request for publisher
     * @param $key
     * @param $username
     * @param $email
     */
	function registerPublisherRequestKey($key,$email){
    	if(strlen($key)>0&&strlen($email)>0){
    		if($this->query("INSERT INTO db_web3.sitti_change_password_queue(request_key,username,email)
    					 VALUES('".$key."','".$email."','".$email."')")){
    			return true;
    		}
    	}
    }
    function isRequestValid($key,$username,$email){
    	$rs = $this->fetch("SELECT * FROM sitti_change_password_queue WHERE request_key ='".$key."' LIMIT 1");
    	if($email==$rs['email'] && $key = $rs['request_key']){
    		
    		return true;
    	}
    }
    function removeRequestKey($key,$email){
    	return $this->query("DELETE FROM sitti_change_password_queue WHERE request_key ='".$key."' AND email='".$email."'");
    }
    // PUBLISHER STUFF HERE -->
    function createPublisher($username, $password, $nama,$email,$website,$jenis_web,$jenis_app,$jum_pengunjung,$kota,$telp){
    	$nama = mysql_escape_string($nama);
    	$kota = mysql_escape_string($kota);
    	$telp = mysql_escape_string($telp);
    	
    	$password = md5($password);
    	$enc = md5($username.$password);
    	$roleCode = "B";
    	$businessType = "C"; //nanti ini bisa dinamik
    	$industry = "0"; // untuk sementara 0 dulu. blm di define soalnya.
    	//$rs = $this->fetch("SELECT COUNT(*) as total FROM sitti_account_publisher LIMIT 1");
    	$rs = $this->fetch("SELECT pub_counter as total FROM account_counter");
    	$queue = $rs['total']+1;
    	$no = str_pad($queue, 6,'000000',STR_PAD_LEFT);
    	$sittiID = $roleCode.$businessType.$industry.$no;
    	//increement counter
    	$rs = $this->query("UPDATE account_counter SET pub_counter = pub_counter+1");
    	
    	/*
    	$cek = $this->fetch("SELECT sittiID FROM sitti_account_publisher WHERE sittiID='".$sittiID."' LIMIT 1");
    	
    	//retry attempts are limited to 20
    	$retry_attempts = 20;
    	$retry=0;
    	while(strlen($cek['sittiID'])!=0){
    		$queue+=1;
    		$no = str_pad($queue, 6,'000000',STR_PAD_LEFT);
    		$sittiID = $roleCode.$businessType.$industry.$no;
    		$cek = $this->fetch("SELECT sittiID FROM sitti_account_publisher WHERE sittiID='".$sittiID."' LIMIT 1");
    		$retry++;
    		
    		//retry attempt exceeded.
    		if($retry==$retry_attempts){
    			break;
    		}
    	}
    	*/
    	 $password = md5(sha1($password.$enc));
        return $this->query("INSERT INTO sitti_account_publisher
        					(username, password, enc_key,sittiID,email,website,jenis_situs,jenis_aplikasi,jumlah_visitor,status,tanggal_daftar,kota,telp,nama)
                            VALUES('".$username."', '".$password."', '".$enc."', '".$sittiID."','".$email."','".$website."','".$jenis_web."',
                            '".$jenis_app."','".$jum_pengunjung."','0',NOW(),'".$kota."','".$telp."','".$nama."')");
    }
    function loginAsPublisher($username, $password){
    	
    	/** retrieve username dan password **/
    	$username = mysql_escape_string($username);
    	$password = md5($password);
    	//print $username."-".$password;
    	$rs = $this->fetch("SELECT * FROM sitti_account_publisher WHERE username='".$username."' 
    						 LIMIT 1");
    	$enc_key = md5($username.$password);
    	//print "<br/>".$enc_key;
    	//print_r($rs);
    	if($rs['enc_key']==$enc_key&&$rs['username']==$username&&$rs['password']==md5(sha1($password.$enc_key))){
    		if($rs['status']=="1"){
	    		session_regenerate_id();
	    		//save sittiID into session
	    		$_SESSION['sittiID'] = $rs['sittiID'];
	    		$this->updateLoginTime($rs['sittiID'],1);
	    		//update session status
	    		$this->updateSession(1);
	    		$arr['status'] = "1";
	    		$arr['id'] = $rs['id'];
	    	
    		} elseif ($rs['status']=="99") {
                $arr['status'] = "99";
            }else{
    		 	$arr['status'] = "2";
    		 	$arr['confirmKey'] = $this->getPubConfirmKey($rs['id']);
    		 	$arr['info'] = array("email"=>$rs['email'],"website"=>$rs['website']);
    		}
    		
    	}else{
    		$arr['status'] = "0";
    	}
    	return $arr;
    }
    function logoutAsPublisher(){
    	$this->query("UPDATE sitti_account_publisher SET session_id='' WHERE sittiID='".$_SESSION['sittiID']."'");
    	$_SESSION['sittiID']=NULL;
    	$_SESSION[base64_encode("sitti_login_session")] = null;
    	session_destroy();
    	return true;
    }
    function getPublisherProfile($user_id = false){
    	
    	if (empty($user_id))
        {
            if(strlen($_SESSION['sittiID'])>0&&strlen($_SESSION['sittiID'])<=10){
        		$rs = $this->fetch("SELECT * FROM sitti_account_publisher
        							WHERE sittiID='".mysql_escape_string($_SESSION['sittiID'])."'
        							LIMIT 1");
        	}
        }
        else
        {
            $rs = $this->fetch("SELECT * FROM sitti_account_publisher
                                    WHERE id='".mysql_escape_string($user_id)."'
                                    LIMIT 1");
        }

        return $rs;
    }
    /**
     * check if the user is special-cased user.
     * @param $sittiID
     */
    function isSpecialUser($sittiID,$auto=false){
    	if($auto)$this->open(0);
    	$rs = $this->fetch("SELECT sittiID FROM sitti_user_special WHERE sittiID='".$sittiID."' LIMIT 1");
    	if($auto)$this->close();
    	if($rs['sittiID']==$sittiID){
    		return true;
    	}
    }
    
    /**
     * konfirmasi pendaftaran advertiser
     */
    function confirmAdvertiser($key){
    	//$user_id,$email,$sitti_id,$enc_key
    	if(strlen($key)>10){
	    	$p = json_decode(urldecode64($key));
	    	$user_id = $p->user_id;
	    	$email = $p->email;
	    	$enc_key = $p->enc_key;
	    	$sittiID = $p->sittiID;
	    	settype($user_id,'integer');
	    	$sql = "SELECT * FROM sitti_account WHERE id=".$user_id." AND username='".$email."' LIMIT 1";
	    	
	    	$this->open(0);
	    	$rs = $this->fetch($sql);
	    	
	    	if($rs['username']==$email&&$rs['enc_key']==$enc_key&&$rs['sittiID']==$sittiID){
	    		$sql = "UPDATE sitti_account SET n_status=1 WHERE id=".$user_id;
	    		
	    		$q = $this->query($sql);
	    		// action log advertiser account create (101)
				if ($q){
					$this->ActionLog->actionLog(101,$sittiID,$email);
				}
	    	}
	    	$this->close();
	    	return $q;
    	}
	}
	function getConfirmKey($user_id){
		$sql = "SELECT * FROM sitti_account WHERE id=".$user_id." LIMIT 1";
		$this->open(0);
    	$rs = $this->fetch($sql);
    	$this->close();
    	if($rs['id']==$user_id){
    		$p = array('email'=>$rs['username'],
    					'user_id'=>$rs['id'],
    					'enc_key'=>$rs['enc_key'],
    					"sittiID"=>$rs['sittiID']);
    		$s = json_encode($p);
    		return urlencode64($s);
    	}
    	return 0;
	}
	//untuk publisher --> 
 /**
     * konfirmasi pendaftaran advertiser
     */
    function confirmPublisher($key){
    	//$user_id,$email,$sitti_id,$enc_key
    	if(strlen($key)>10){
	    	$p = json_decode(urldecode64($key));
	    	$user_id = $p->user_id;
	    	$email = $p->email;
	    	$enc_key = $p->enc_key;
	    	$sittiID = $p->sittiID;
	    	settype($user_id,'integer');
	    	$sql = "SELECT * FROM sitti_account_publisher WHERE id=".$user_id." AND username='".$email."' LIMIT 1";
	    	
	    	$this->open(0);
	    	$rs = $this->fetch($sql);
	    	
	    	if($rs['username']==$email&&$rs['enc_key']==$enc_key&&$rs['sittiID']==$sittiID){
	    		$sql = "UPDATE sitti_account_publisher a SET a.status=1 WHERE a.id=".$user_id;
	    		
	    		$q = $this->query($sql);
	    		// action log publisher account create (201)
				if ($q){
					$this->ActionLog->actionLog(201,$sittiID,$email);
				}
	    	}
	    	$this->close();
	    	return $q;
    	}
	}
	function getPubConfirmKey($user_id){
		$sql = "SELECT * FROM sitti_account_publisher WHERE id=".$user_id." LIMIT 1";
		$this->open(0);
    	$rs = $this->fetch($sql);
    	$this->close();
    	if($rs['id']==$user_id){
    		$p = array('email'=>$rs['username'],
    					'user_id'=>$rs['id'],
    					'enc_key'=>$rs['enc_key'],
    					"sittiID"=>$rs['sittiID']);
    		$s = json_encode($p);
    		return urlencode64($s);
    	}
    	return 0;
	}

    function getSITTIID($user_id)
    {
        $query = "SELECT sittiID FROM db_web3.sitti_account WHERE id = '". $user_id ."'";
        $result = $this->fetch($query);
        
        return (bool) $result ? $result['sittiID'] : false;
    }
	
	function isReferralMember($advertiser_id)
    {
		$this->open(0);
        $query = "SELECT * FROM db_web3.sitti_advertiser_referral_member WHERE advertiser_id ='".$advertiser_id."' LIMIT 1";
        $result = $this->fetch($query);
		$this->close();
        
        if ($result){
			return true;
		}else{
			return false;
		}
    }
	
	function insertReferralMember($advertiser_id)
    {
		$this->open(0);
        $sql = "INSERT IGNORE INTO db_web3.sitti_advertiser_referral_member (advertiser_id) VALUES ('".$advertiser_id."')";
        $result = $this->query($sql);
		$this->close();
        
        return $result;
    }
}
?>