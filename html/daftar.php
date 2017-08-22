<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIHomepage.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
//captcha
$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
//include_once $APP_PATH."MOP/MOPClient.php";

$view = new BasicView();
$sittiHomepage = new SITTIHomepage(&$req);

$account = new SITTIAccount(&$req);

$nama = $req->getPost("nama");
$email = $req->getPost("email");
$alamat = $req->getPost("alamat");
$blok = $req->getPost("blok");
$komplek = $req->getPost("komplek");
$telepon = $req->getPost("telepon");
$handphone = $req->getPost("handphone");
$username = $req->getPost("username");
$password = $req->getPost("password");
$retry_pass = $req->getPost("re-password");
$twitter = $req->getPost("twitter");
$referral = $req->getPost("referral");

//$role = $req->getPost("role");
$role = 1; //1 --> advertiser

$provinceID = $req->getPost("home_province");
$cityID = $req->getPost("home_city");

$account->open(0);
//$isLogin = $account->isLogin();
//$isFirstTimer = $account->isFirstTimer();
//$info = $account->getProfile();
$is_email_exist = $account->isEmailExist("", $email);
$account->close();

$sittiHomepage->open();
$qprovince = $sittiHomepage->getProvinceById($provinceID);
$province = $qprovince['provinceName'];
//citynya di query berdasarkan id, yg kita simpan ke database soalnya nama kota, bukan idnya
$qcity = $sittiHomepage->getCityById($cityID);
$city = $qcity['cityName'];
$sittiHomepage->close();

//informasi usaha

//start comment: prefixCompany(PT atau CV) dirubah jadi companyType, field database nya akhirnya di pisah
$prefixCompany = $req->getPost("companyType"); //company type
$getcompany = $req->getPost("company");//company
//$company = $prefixCompany." ".$getcompany;
//end comment

$brand = $req->getPost("brand");
$city_industry = $req->getPost("kotaUsaha");
//end informasi usaha

$aggrement = $req->getPost("aggrement");
$sittiHomepage->View->assign("referral",$referral);
$sittiHomepage->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
if($isLogin){
    $sittiHomepage->View->assign("isLogin",$isLogin);
}

$isreferralvalid = true;
if ($referral!=""){
	$isreferralvalid = $account->isReferralMember($referral);
}
if (!$isreferralvalid){
	$flag = "0";
	$var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
	$sittiHomepage->View->assign("valid",$flag);
	$sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   	print $sittiHomepage->View->toString("SITTI/content5.html");
}else if(!$nama || !$email || !$password ){
	
    //filter form
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    //end filter
    
    $er = $LOCALE['REGISTER_REQUIRED_FIELDS'];
    $sittiHomepage->View->assign("error_msg", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   	print $sittiHomepage->View->toString("SITTI/content5.html");

}else if(preg_match_all('/[^a-zA-Z\\s{1}]/', $nama, $result, PREG_PATTERN_ORDER)){//validasi nama
    
    //$isValid = false;
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = "Nama hanya boleh diisi dengan karakter huruf dan spasi saja";
    $sittiHomepage->View->assign("isNotValidNama", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");

}else if(!preg_match_all('/^[A-Z0-9._-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i', $email, $result, PREG_PATTERN_ORDER)){ //validasi email
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = "Format email anda tidak valid. Contoh : sample@domain.com ";
    $sittiHomepage->View->assign("isNotValidEmail", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");

}
/*
 else if(preg_match_all('/[^a-z0-9]/i', ($username), $result, PREG_PATTERN_ORDER)){ //validasi username
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = "Username hanya boleh diisi karakter huruf dan angka";
    $sittiHomepage->View->assign("isNotValidUsername", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
    print $sittiHomepage->showContentPage();

}
*/else if(preg_match_all('/[^a-z0-9]/i', $password, $result, PREG_PATTERN_ORDER)){ //validasi password
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = "Password hanya boleh diisi dengan karakter huruf dan angka saja";
    $sittiHomepage->View->assign("isNotValidPassword", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");

}else if(strlen($password)<8){ //password dibawah 8 karakter
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = $LOCALE['REGISTER_PASSWD_FIELDS_LESSTHAN_8CHARS'];
   
    $sittiHomepage->View->assign("isNotValidPassword", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");

}else if(strcmp($password,$retry_pass)!=0){ //jika password dengan retrynya tidak cocok
    $var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    //$er = $LOCALE['REGISTER_PASSWD_FIELDS_NOT_MATCH'];
   	$er = "Password anda tidak sama !";
   
    $sittiHomepage->View->assign("isNotValidPassword", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");

}else if($is_email_exist){
	$var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $er = $LOCALE['REGISTER_EMAIL_ALREADY_REGISTERED'];
    $sittiHomepage->View->assign("isNotValidEmail", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
   print $sittiHomepage->View->toString("SITTI/content5.html");
}else if(!$captcha->verify()){

	$er = $LOCALE['CAPTCHA_ERROR'];
	//$view->assign("mainContent",$view->showMessageError($msg,"daftar.php"));
	$var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                        $city, $telepon, $handphone, $username, $prefixCompany,
                                        $getcompany, $brand, $city_industry,$twitter);
    $sittiHomepage->View->assign("var", $var);
    $sittiHomepage->View->assign("error_msg", $er);
    $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());
    print $sittiHomepage->View->toString("SITTI/content5.html");
		
}else{
    if($aggrement>0){
		
		$_SESSION['referral'] = $referral;
        $account->open();
        $fix_pass = md5($password);
        $enc = md5($email.$fix_pass);

        $insert = $account->create('1',$email, $fix_pass, $enc);
        //print mysql_error();
        if($insert){
            $user_id = mysql_insert_id();
            if($user_id){
                $insertToProfile = $account->createProfile($user_id, $nama, $email, $alamat,
                                                    $komplek, $blok, $province, $city, $telepon, $handphone,$twitter);
                
                //print mysql_error();                                    
				$insertPPCUser = $account->createCPCProfile($account->getSITTIID($user_id));
               

                if($insertToProfile && $insertPPCUser){
                    $account->createBusinessProfile($user_id, $prefixCompany, $getcompany, $brand, $city_industry);
                    
					
					$sittiID = $account->getSITTIID($user_id);
					// echo "sittiID=".$sittiID;
					if ($referral!=""){
						$referral_profile = $account->getProfileBySittiID($referral);
					}
					// echo "referral:";print_r ($referral_profile);
					
                    $insertToBilling = $account->createBillingProfile($user_id, $alamat, '99', '99');
                    if($insertToBilling){
                    	
                   
                   		if($_SESSION['ref']!=null){
                        	$ref = unserialize(urldecode64($_SESSION['ref']));
                        	$webs = $ref['webs'];
                        	$websid = $ref['websid'];
                        	
                        	if(strlen($ref['webs'])>5&&eregi('([0-9]+)',$ref['websid'])){
                        		$acc = $account->fetch("SELECT * FROM db_web3.sitti_account WHERE id=".$user_id." LIMIT 1");
                        		$sittiID = $acc['sittiID'];
                        		$sql = "INSERT INTO db_web3.register_referral(websid,webname,tipe,sittiID,tglisidata)
                        				VALUES(".$websid.",'".$webs."',0,'".$sittiID."',NOW())";
                        		
                        		$account->query($sql);
                        	}
                        }
                    	
                    	/*
                    	//auto login procedure
                    	if($account->login($email, $fix_pass, 1)){
                    		//print "sukses";
                    	}else{
                    		//print "gagal";
                    	}
                    	*/
                        //$sendingEmail = $account->sendEmail();
                        $msg = $LOCALE['REGISTRATION_SUCCESS'];
						$_SESSION['referral'] = "";
                        $url = $sittiHomepage->View->showMessage($msg,"http://www.sitti.co.id");
                        //$sittiHomepage->View->assign("msg", $msg);
                        $sittiHomepage->View->assign("mainContent",$url);
                        
                        //email notifikasi pendaftaran
                        $smtp = new SITTIMailer();
                        $smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
    					$smtp->setRecipient($email);
    					$view->assign("nama",$nama);
    					$view->assign("confirm_url",$CONFIG['WebsiteURL']."konfirmasi.php?token=".$account->getConfirmKey($user_id));
    					$smtp->setMessage($view->toString("SITTI/email/notifikasi_pendaftaran.html"));
						// print $view->toString("SITTI/email/notifikasi_pendaftaran.html");
						// die();
    					$smtp->send();
						
                        $account->close();
						
						//add referrer if exists
						if($referral!=""){
							$account->insertAdvertiserReferral($sittiID, $referral);
							$account->insertAdvertiserReferralReport($referral, $sittiID, $nama);
							
							//email notifikasi downline
							$smtp2 = new SITTIMailer();
							$smtp2->setSubject("[SITTI] Pendaftaran Referral");
							$smtp2->setRecipient($referral_profile['email']);
							$view->assign("nama",$nama);
							$view->assign("sittiID",$sittiID);
							$smtp2->setMessage($view->toString("SITTI/email/notifikasi_pendaftaran_downline.html"));
							$smtp2->send();
						}
						
						print $sittiHomepage->View->toString("SITTI/content5.html");
                       // sendRedirect("index.php?login=1");
                        die();


                    }else{
                        //print mysql_error();
                        $er = "Proses registrasi user tidak berhasil.";
                        $url = $sittiHomepage->View->showMessageError($er, "index.php?registration=1");
                        //$sittiHomepage->View->assign("er", $er);
                    }


                }else{
                    //print mysql_error();
                    $er = "Proses registrasi user tidak berhasil.";
                    $url = $sittiHomepage->View->showMessageError($er, "index.php?registration=1");
                    //$sittiHomepage->View->assign("er", $er);

                }

            }else{//error handling

                $er = "Proses registrasi user tidak berhasil.";
                $url = $sittiHomepage->View->showMessageError($er, "index.php?registration=1");
                //$sittiHomepage->View->assign("er", $er);
                //print mysql_error();
            }

        }else{//error handling
            $er = "Proses registrasi user tidak berhasil. Nama Akun yang anda inginkan tidak tersedia, silahkan coba kembali.";
            $url = $sittiHomepage->View->showMessageError($er, "index.php?registration=1");
            //$sittiHomepage->View->assign("er", $er);
            //print mysql_error();
        }
        $sittiHomepage->View->assign("mainContent", $url);

        //print $sittiHomepage->showContentPage();
        
        $account->close();
		print $sittiHomepage->View->toString("SITTI/content5.html");

    }else{
		$er = $LOCALE['REGISTER_REQUIRED_TERM'];
		$sittiHomepage->View->assign("error_msg", $er);

		//filter form
		$var = $sittiHomepage->filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
										$city, $telepon, $handphone, $username,
										$prefixCompany, $getcompany, $brand, $city_industry,$twitter);
		$sittiHomepage->View->assign("var", $var);
		//end filter
        $sittiHomepage->View->assign("mainContent",$sittiHomepage->showRegistrationForm());

        //print $sittiHomepage->showContentPage();
         print $sittiHomepage->View->toString("SITTI/content5.html");

    }


}





?>