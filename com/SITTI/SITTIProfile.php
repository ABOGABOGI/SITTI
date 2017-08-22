<?php 
/**
 * SITTIProfile
 * class untuk halaman User Profile.
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIApp.php";
include_once "SITTIActionLog.php";

class SITTIProfile extends StaticPage{
	var $Account;
    function SITTIProfile($req,$account){
        parent::StaticPage(&$req);
        $this->Account = $account;
		$this->ActionLog = new SITTIActionLog();
    }
    /**
     * show advertiser pages.
     */
    function showPage(){
    	global $LOCALE;
    	$req = $this->Request;
    	if($req->getPost("save")=="1"){
    		//validasi token
    		$token = $this->Request->getPost('token');
    		if(!is_token_valid($token)){
    			$msg = $LOCALE['TRANSACTION_EXPIRED'];;
    			return $this->View->showMessage($msg,"beranda.php");
    		}
    		
    		//kita butuh user id
    		$this->Account->open(0);
    		$info = $this->Account->getProfile();
    		$this->Account->close();
    		//-->
    		$nama = $req->getPost("name");
    		$email = $req->getPost("email");
    		$alamat = $req->getPost("alamat");
    		$telepon = $req->getPost("telepon");
    		$handphone = $req->getPost("handphone");
    		//do some input validation here
    		//cek apakah ada tanda '' di alamat 
    		//print stripslashes($alamat);
    		
    		$isValid = true;
            if(preg_match_all('/\\d+/', $nama, $result, PREG_PATTERN_ORDER)){
    			$isValid = false;
    			$msg = $LOCALE['PROFILE_NAME_ALLOWEDCHARS'];
    		}
            
    		if(preg_match_all('/\\x5C\\x27|\\x22/', stripslashes($alamat), $result, PREG_PATTERN_ORDER)){
    			$isValid = false;
    			$msg = $LOCALE['PROFILE_ADDRESS_ALLOWEDCHARS'];
    		}

            if(preg_match_all('/[^0-9\\-\\s]/', ($telepon), $result, PREG_PATTERN_ORDER)){
                $isValid = false;
                $msg = $LOCALE['PROFILE_PHONE_ALLOWEDCHARS'];
            }

            if(preg_match_all('/[^0-9\\-\\s]/', ($handphone), $result, PREG_PATTERN_ORDER)){
                $isValid = false;
                $msg = $LOCALE['PROFILE_HP_ALLOWEDCHARS'];
            }
    		
    		if($isValid){
    			//--->
    			$this->Account->open(0);
    			if($this->Account->updateProfile($info['user_id'], $nama, $email, $alamat, $telepon, $handphone)){
					// action log edit advertiser profile (102)
					$this->ActionLog->actionLog(102,$info['sittiID'],'profile');
    				$msg = $LOCALE['USER_UPDATE_PROFILE_SUCCESS'];
    			}else{
    				$msg = $LOCALE['USER_UPDATE_PROFILE_ERROR'];
    			}
    			$this->Account->close();
    			$strHTML = $this->View->showMessage($msg,"beranda.php");
    		}else{
    			$this->View->assign("msg",$msg);
    			$this->Account->open(0);
    			$params = $this->Account->getProfile();
    			//$params = $_POST;
    			//$params['mobile'] = $params['handphone'];
    			//$params['phone'] = $params['telepon'];
    			//$params['address'] = $params['alamat'];
    			$this->View->assign("rs",$params);
    			$this->Account->close();
    			return $this->View->toString("SITTI/profile/edit_profile.html");
    		}
    		//$this->Account->close();
     		return $strHTML;
    	}else if($req->getPost("save_password")){
    	//validasi token
    		$token = $this->Request->getPost('token');
    		
    		if(!is_token_valid($token)){
    			$msg = $LOCALE['TRANSACTION_EXPIRED'];
    			return $this->View->showMessage($msg,"beranda.php");
    		}
    		//kita butuh user id
    		$this->Account->open(0);
    		$info = $this->Account->getProfile();
    		$this->Account->close();
    		
    		$old_pass = $req->getPost('old_pass');
    		$new_pass = $req->getPost('new_pass');
    		$new_pass2 = $req->getPost('new_pass2');
    		
    		if(strlen($old_pass)>=6&&strlen($new_pass)>=6&&strlen($new_pass2)>=6){
    			//periksa apakah password lama benar
    			$this->Account->open(0);
    			$rs = $this->Account->login($info['email'],md5($old_pass),1);
    			$this->Account->close();
    			if($rs['status']==1){
    				if(strlen($new_pass)==strlen($new_pass2)&&
    					eregi("([0-9A-Za-z]+)",$new_pass)&&
    					eregi("([0-9A-Za-z]+)",$new_pass2)&&$new_pass==$new_pass2){
    					
    					$new_pass = mysql_escape_string($new_pass);	
    					$user_id = $info['user_id'];
    					$this->open(0);
    					$q = $this->Account->updateCredential($user_id,$info['email'],$new_pass);
    					$this->close();
    					if($q){
							// action log edit advertiser password (102)
							$this->ActionLog->actionLog(102,$info['sittiID'],'password');
    						$msg = $LOCALE['PROFILE_NEWPASSWD_SAVED'];
    					}else{
    						$msg = $LOCALE['PROFILE_NEWPASSWD_FAILED'];
    					}
    				}else{
    					$msg = $LOCALE['PROFILE_PASSWD_NOT_MATCHED'];
    				}
    				
    			}else{
    				$msg = $LOCALE['PROFILE_OLDPASSWD_NOT_MATCHED'];
    			}
    		}else{
    			
    			$msg = $LOCALE['PROFILE_PASSWD_MIN_CHARS'];
    		}
    		return $this->View->showMessage($msg,"beranda.php");
    	}else if($req->getParam("refer")){
    		return $this->View->toString("SITTI/profile/refer_teman.html");
    	}else if($req->getParam("password")){
    		return $this->View->toString("SITTI/profile/edit_password.html");
    	}else{
    		$this->Account->open(0);
    		$info = $this->Account->getProfile();
    		$this->Account->close();
    		$this->View->assign("rs",$info);
    		return $this->View->toString("SITTI/profile/edit_profile.html");
    	}
    }
   

}
?>