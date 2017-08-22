<?php 
/**
 * SITTIProfile
 * class untuk halaman User Profile.
 */

include_once "SITTIApp.php";
include_once "SITTIActionLog.php";

class SITTIPublisherProfile extends SITTIApp{
	
    function SITTIPublisherProfile($req,$account){
        parent::SITTIApp(&$req,$account);
		$this->ActionLog = new SITTIActionLog();
    }
   /*
    function showPage(){
    	global $LOCALE;
    	$req = $this->Request;
    	if($req->getPost("save")=="1"){
    		//kita butuh user id
    		$this->Account->open(0);
    		$info = $this->Account->getProfile();
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
    			$msg = "Nama hanya boleh diisi dengan karakter huruf saja.";
    		}
            
    		if(preg_match_all('/\\x5C\\x27|\\x22/', stripslashes($alamat), $result, PREG_PATTERN_ORDER)){
    			$isValid = false;
    			$msg = "Alamat hanya boleh diisi dengan karakter huruf dan angka saja.";
    		}

            if(preg_match_all('/[^0-9\\-\\s]/', ($telepon), $result, PREG_PATTERN_ORDER)){
                $isValid = false;
                $msg = "No telepon hanya boleh diisi dengan angka, karakter strip dan spasi saja.";
            }

            if(preg_match_all('/[^0-9\\-\\s]/', ($handphone), $result, PREG_PATTERN_ORDER)){
                $isValid = false;
                $msg = "Handphone hanya boleh diisi dengan angka, karakter strip dan spasi saja.";
            }
    		
    		if($isValid){
    			//--->
    			if($this->Account->updateProfile($info['user_id'], $nama, $email, $alamat, $telepon, $handphone)){
    				$msg = $LOCALE['USER_UPDATE_PROFILE_SUCCESS'];
    			}else{
    				$msg = $LOCALE['USER_UPDATE_PROFILE_ERROR'];
    			}
    			$this->Account->close();
    			$strHTML = $this->View->showMessage($msg,"beranda.php");
    		}else{
    			$this->View->assign("msg",$msg);
    			$params = $_POST;
    			$params['mobile'] = $params['handphone'];
    			$params['phone'] = $params['telepon'];
    			$params['address'] = $params['alamat'];
    			$this->View->assign("rs",$params);
    			return $this->View->toString("SITTI/profile/edit_profile.html");
    		}
    		$this->Account->close();
    		return $strHTML;
    	}else if($req->getParam("refer")){
    		return $this->View->toString("SITTI/profile/refer_teman.html");
    	}else{
    		$this->Account->open(0);
    		$info = $this->Account->getProfile();
    		$this->Account->close();
    		$this->View->assign("rs",$info);
    		return $this->View->toString("SITTI/profile/edit_profile.html");
    	}
    }
    */
    function save($profile){
    	$req = $this->Request;
    	$nama_lengkap = $req->getPost("nama_lengkap");
    	$alamat = $req->getPost("alamat");
    	$no_telp = $req->getPost("phone");
    	$payment_type = $req->getPost("payment_type");
    	$bank = $req->getPost("bank");
    	$cabang = $req->getPost("cabang");
    	$no_rekening = $req->getPost("no_rekening");
    	$nama_rekening = $req->getPost("nama_rekening");
    	$jenis_akun = $req->getPost("jenis_akun");
    	$npwp = $req->getPost("npwp");
    	
    	
    	//validasi token
    	$token = $this->Request->getPost('token');
    	if(!is_token_valid($token)){
    		$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    		return $this->View->showMessage($msg,"beranda.php");
    	}
    	$isValid = true;
	    if(preg_match_all('/\\d+/', $nama_lengkap, $result, PREG_PATTERN_ORDER)){
		    $isValid = false;
		    $msg = "Nama pemegang akun hanya boleh diisi dengan karakter huruf saja.";
	    }
    	if(preg_match_all('/\\d+/', $nama_rekening, $result, PREG_PATTERN_ORDER)){
		    $isValid = false;
		    $msg = "Nama Rekening hanya boleh diisi dengan karakter huruf saja.";
	    } 
	    if(preg_match_all('/\\d+/', $cabang, $result, PREG_PATTERN_ORDER)){
		    $isValid = false;
		    $msg = "Nama Cabang hanya boleh diisi dengan karakter huruf saja.";
	    } 
	    if(preg_match_all('/\\x5C\\x27|\\x22/', stripslashes($alamat), $result, PREG_PATTERN_ORDER)){
		    $isValid = false;
		    $msg = "Alamat lengkap hanya boleh diisi dengan karakter huruf dan angka saja.";
	    }

		if(preg_match_all('/[^0-9\\-\\s]/', ($no_telp), $result, PREG_PATTERN_ORDER)){
			$isValid = false;
			$msg = "No telepon hanya boleh diisi dengan angka, karakter strip dan spasi saja.";
		}

		if(preg_match_all('/[^0-9\\-\\s]/', ($no_rekening), $result, PREG_PATTERN_ORDER)){
			$isValid = false;
			$msg = "Nomor Rekening hanya boleh diisi dengan angka, karakter strip dan spasi saja.";
		}
		$pubId = $profile['id'];
    	settype($pubId,'integer');
    	$this->View->assign("info",$profile);
    	$this->View->assign("isLogin",1);
		if($isValid){
			$sql = "UPDATE db_web3.sitti_publisher_profile
					SET jenis_akun='".$jenis_akun."',
					nama_panjang='".$nama_lengkap."',
					alamat='".$alamat."',
					payment_type='".$payment_type."',
					bank='".$bank."',no_rekening='".$no_rekening."',
					nama_rekening='".$nama_rekening."',
					no_telp='".$no_telp."',
					cabang='".mysql_escape_string($cabang)."',
					npwp='".$npwp."'
					WHERE publisher_id=".$pubId;
			
			$this->open(0);
			$q = $this->query($sql);
			$this->close();
			$rs = $this->getData($profile['id']);
    		$this->View->assign("rs",$rs);
    		$pubId = $profile['id'];
    		settype($pubId,'integer');
			if($q){
				// action log edit publisher profile (202)
				$this->ActionLog->actionLog(202,$profile['sittiID'],'profile');
				$_SESSION['pflag'] = "0" ;	
				$this->View->assign("msgClass","suksesMessage");
				$this->View->assign("msg","Profile anda berhasil disimpan.");
			}else{
				$this->View->assign("msgClass","errorMessage");
				$this->View->assign("msg","Mohon Maaf, Profile anda tidak berhasil disimpan, silahkan coba kembali !");
			}
			
			return $this->View->toString("SITTIZEN/profile.html");
		}else{
			$rs = $this->getData($profile['id']);
	    	$this->View->assign("rs",$rs);
	    	$pubId = $profile['id'];
	    	settype($pubId,'integer');
			$this->View->assign("msgClass","errorMessage");
			$this->View->assign("msg",$msg);
    		$params = $rs;
    		$this->View->assign("rs",$params);
    		return $this->View->toString("SITTIZEN/profile.html");
		}
    }
    function hasProfile($pubId){
    	$sql = "SELECT COUNT(publisher_id) as total FROM db_web3.sitti_publisher_profile 
    			WHERE publisher_id=".$pubId." LIMIT 1";
    	$this->open(0);
    	$rs = $this->fetch($sql);
    	$this->close();
    	if($rs['total']==0){
    		//no profile. 
    		// so we create an empty one.
    		$sql = "INSERT INTO db_web3.sitti_publisher_profile(publisher_id)
    				VALUES(".$pubId.")";
    		$this->open(0);
    		$this->query($sql);
    		$this->close();
    	}
    	
    }
    function getData($pubId){
    	$this->hasProfile($pubId);
    	$sql = "SELECT a.*,b.username as email FROM db_web3.sitti_publisher_profile a,
    			db_web3.sitti_account_publisher b 
    			WHERE a.publisher_id=".$pubId." AND b.id = a.publisher_id 
    			LIMIT 1";
    	$this->open(0);
    	$rs = $this->fetch($sql);
    	$this->close();
    	return $rs;
    }
    function ProfilePage($profile){
    	$pubId = $profile['id'];
    	settype($pubId,'integer');
    	$rs = $this->getData($profile['id']);
    	$this->View->assign("rs",$rs);
    	$this->View->assign("info",$profile);
    	$this->View->assign("isLogin",1);
    	return $this->View->toString("SITTIZEN/profile.html");
    }
   	function ganti_password($profile){
   		return $this->View->toString("SITTIZEN/ganti_password.html");
   		
   	}
   	function save_password($profile){
   		$req = $this->Request;
   		$this->View->assign("isLogin","1");
   		$this->View->assign("info",$profile);
   		$token = $this->Request->getPost('token');
    		
    	if(!is_token_valid($token)){
    		$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    		$this->View->assign("mainContent",$this->View->showMessage($msg,"beranda.php"));
    		return $this->View->toString("SITTIZEN/content.html");
    	}
   		$old_pass = $req->getPost('old_pass');
    	$new_pass = $req->getPost('new_pass');
    	$new_pass2 = $req->getPost('new_pass2');
    	
    	if(strlen($old_pass)>=6&&strlen($new_pass)>=6&&strlen($new_pass2)>=6){
    		//periksa apakah password lama benar
    		$this->Account->open(0);
    		$rs = $this->Account->loginAsPublisher($profile['email'],$old_pass);
    		
    		$this->Account->close();
    		if($rs['status']==1){
    			if(strlen($new_pass)==strlen($new_pass2)&&
    				eregi("([0-9A-Za-z]+)",$new_pass)&&
    				eregi("([0-9A-Za-z]+)",$new_pass2)&&$new_pass==$new_pass2){
    				
    				$new_pass = mysql_escape_string($new_pass);	
    				$user_id = $profile['id'];
    				
    				$this->open(0);
    				$q = $this->Account->updatePublisherCredential($user_id,$profile['email'],$new_pass);
    				$this->close();
    				if($q){
						// action log edit publisher password (202)
						$this->ActionLog->actionLog(202,$profile['sittiID'],'password');
    					$msg = "Kata Sandi yang baru telah disimpan";
    				}else{
    					$msg = "Maaf, Kata Sandi yang baru tidak berhasil disimpan.";
    				}
    			}else{
    				$msg = "Maaf, Kata Sandi tidak sama.";
    			}
    		}else{
    			$msg = "Maaf, Kata Sandi lama yang anda masukkan salah.";
    		}
    	}else{
    		$msg = "Maaf, jumlah karakter untuk kata sandi minimal 8 karakter";
    	}
   		$this->View->assign("mainContent",$this->View->showMessage($msg,"beranda.php"));
   		return $this->View->toString("SITTIZEN/content.html");
   	}

}
?>