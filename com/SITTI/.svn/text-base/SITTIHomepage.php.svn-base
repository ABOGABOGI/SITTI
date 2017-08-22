<?php 
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once $APP_PATH."SITTIAdmin/SITTIReportOverview.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
define("HOME", "SITTI/index.html");
define("CONTACT_FORM", "SITTI/kontak.html");
define("LOGIN_FORM", "SITTI/login.html");
define("LOGIN_FORM_CPM", "SITTIBanner/login.html");
define("REGISTRATION_FORM", "SITTI/daftarv2.html");
define("CONTENT_PAGE", "SITTI/content.html");
define("CONTENT_PAGE_PPA", "SITTIPPA/content.html");
class SITTIHomepage extends StaticPage{
	var $broadcast;
    var $type;
    function SITTIHomepage($req,$broadcast=null,$type = false){
        parent::StaticPage(&$req);
        $this->broadcast = $broadcast;
        if ($type) $this->type = $type;
    }

    function showIndexPage(){
    	if ($this->type == 'ppa')
        {
            $this->View->assign("ppa", true);
            return $this->View->toString(LOGIN_FORM);
        }
        elseif ($this->type == 'ppm')
        {
            $this->View->assign("ppm", true);
            return $this->View->toString(LOGIN_FORM_CPM);
        }
        else
        {
            $this->View->assign("broadcast",$this->broadcast);
            $overview = new SITTIReportOverview(null);
            $stats = $overview->getGeneralReport();
            $this->View->assign("PAGE_SERVED",$stats['pg_served']);
            return $this->View->toString(HOME);    
        }
    }

    function showPage($id){
    	$this->View->assign("broadcast",$this->broadcast);
        return $this->getPage($id);
    }

    function showContactForm(){
        return $this->View->toString(CONTACT_FORM);
    }

    function showLoginForm($type = false){
    	$this->View->assign("broadcast",$this->broadcast);
        if ($type == 'cpm')
        {
            return $this->View->toString(LOGIN_FORM_CPM);
        }
        else
        {
            return $this->View->toString(LOGIN_FORM);   
        }
    }

    function showRegistrationForm(){
    	global $CONFIG, $LOCALE;
    	//captcha
		$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
    	$this->View->assign("broadcast",$this->broadcast);
        //province n city dropdown
        $this->open();

        $country = array('0'=>array('id'=>'1','countryName'=>'indonesia'));
        //$country = $this->fetch("SELECT * FROM country",1);
        //print_r($country);
        for($i=0;$i<sizeof($country);$i++){
            $country[$i]['province'] = $this->fetch("SELECT * FROM province
                                        WHERE countryID='1'
                                        ORDER BY provinceName ASC",1);
            $n = sizeof($country[$i]['province']);
            for($j=0;$j<$n;$j++){
                $country[$i]['province'][$j]['city'] = $this->fetch("SELECT * FROM city
                                                            WHERE provinceID='".$country[$i]['province'][$j]['id']."'
                                                            ORDER BY cityName ASC",1);
            }
        }


        $city_industry = $this->getCity();

        $cityJakarta_default = $country[0]['province'][4]['city'];
        $this->View->assign("cityJakarta",$cityJakarta_default);
        $this->close();
        $this->View->assign("country",$country);
        $this->View->assign("province",$country[0]['province']);
        $this->View->assign("city",$country[0]['province'][0]['city']);
        $this->View->assign("city_industry", $city_industry);
        //end
		
        //captcha
        //-->render captcha
		$this->View->assign("CAPTCHA",$captcha->getHtml());
        
        $this->View->assign("passwd_short", $LOCALE['REGISTER_PASSWD_SHORT']);
        $this->View->assign("passwd_weak", $LOCALE['REGISTER_PASSWD_WEAK']);
        $this->View->assign("passwd_strong", $LOCALE['REGISTER_PASSWD_STRONG']);
        $this->View->assign("passwd_very_strong", $LOCALE['REGISTER_PASSWD_VERY_STRONG']);
        $this->View->assign("passwd_sameas_username", $LOCALE['REGISTER_PASSWD_SAMEAS_USERNAME']);

        return $this->View->toString(REGISTRATION_FORM);
    }
    function showContentPage(){
        $this->View->assign("broadcast",$this->broadcast);
        if ($this->type == 'ppa')
        {
            $this->View->assign("ppa", true);
            return $this->View->toString(CONTENT_PAGE_PPA);
        }
        else
        {
            return $this->View->toString(CONTENT_PAGE);   
        }
    }

    function filterRegistrationForm(){

    }

    function filterAllFields($nama, $email, $alamat, $komplek, $blok, $province,
                                $city, $telepon, $handphone, $username, 
                                $prefixCompany, $getcompany, $brand, $city_industry,$twitter){

        $var['nama'] = $nama;
        $var['email'] = $email;
        $var['alamat'] = $alamat;
        $var['komplek'] = $komplek;
        $var['blok'] = $blok;
        $var['propinsi'] = $province;
        $var['city'] = $city;
        $var['telp'] = $telepon;
        $var['hp'] = $handphone;
        $var['username'] = $username;
        $var['prefixCompany'] = $prefixCompany;
        $var['company'] = $getcompany;
        $var['brand'] = $brand;
        $var['city_industry'] = $city_industry;
        $var['twitter'] = $twitter;

        $var['required'] = "<font color=red>*</font>";
        
        return $var;
    }

    

    function getProvinceById($provinceID){
        return $this->fetch("SELECT provinceName FROM province WHERE id='".$provinceID."' LIMIT 1");
    }

    function getCityById($cityID){
        return $this->fetch("SELECT cityName FROM city WHERE id='".$cityID."' LIMIT 1");
    }

    function getCity(){
        return $this->fetch("SELECT * FROM city ORDER BY cityName ASC",1);
    }
    
    //SITTIZEN Specific content here --->
    function PublisherHomepage(){
    	global $CONFIG;
	    if($_SESSION[urlencode64('block'.date("Ymd"))]==NULL){
				$_SESSION[urlencode64('block'.date("Ymd"))]=0;
		}
    	//captcha
		$captcha = new CaptchaUtil($CONFIG['BELAJARSITTI_CAPTCHA_PUBLIC'], $CONFIG['BELAJARSITTI_CAPTCHA_PRIVATE']);
 	   	if($_SESSION[urlencode64('block'.date("Ymd"))]>=3){
			$this->View->assign("CAPTCHA",$captcha->getHtml());
    	}
		//-->
		$this->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL_2']);
    	$overview = new SITTIReportOverview(null);
    	$stats = $overview->getGeneralReport();
    	$this->View->assign("PAGE_SERVED",$stats['pg_served']);
    	return $this->View->toString("SITTIZEN/index.html");
    }
    function PublisherLogin($account){
    	global $CONFIG,$LOCALE;
    	if($_SESSION[urlencode64('block'.date("Ymd"))]==NULL){
			$_SESSION[urlencode64('block'.date("Ymd"))]=0;
		}
    	//captcha
		$captcha = new CaptchaUtil($CONFIG['BELAJARSITTI_CAPTCHA_PUBLIC'], $CONFIG['BELAJARSITTI_CAPTCHA_PRIVATE']);
		//-->
    	$req = $this->Request;
    	$username = $req->getPost("username");
    	$password = $req->getPost("password");
    	if($_SESSION[urlencode64('block'.date("Ymd"))]>=3&&$captcha->verify()){
    		$_SESSION[urlencode64('block'.date("Ymd"))] = 0;
    	}
    	if(strlen($username)==0 || strlen($password)==0){
    		$er = "Semua field wajib diisi.";
    		return $this->View->showMessageError($er,"index.php");
    		
    	}else if($_SESSION[urlencode64('block'.date("Ymd"))]>=3&&!$captcha->verify()){
    		return $this->View->showMessageError($LOCALE['CAPTCHA_ERROR'],"index.php");
    	}else{
    		$account->open();
    		$check = $account->loginAsPublisher($username, $password);
    		if($check['status']=="1"){
    			
    			// prosedur utk memeriksa kelengkapan data publisher
    			$pub_profile = $account->fetch("SELECT * FROM sitti_publisher_profile 
    										WHERE publisher_id=".intval($check['id'])." 
    										LIMIT 1");
    			$profile_ok = true;
    			if(is_array($pub_profile)){
	    			foreach($pub_profile as $nm=>$val){
	    				if($nm!="npwp"){
		    				if(strlen($val)==0){
		    					$profile_ok = false;
		    				}
	    				}
	    			}
    			}else{
    				$profile_ok = false;
    			}
    			//end of procedure
    			if($profile_ok){
	    			$msg = "Anda berhasil masuk.";
	    			$this->View->assign("msg", $msg);
	    			$strHTML = $this->View->showMessage($msg,"beranda.php");
    			}else{
    				$_SESSION['pflag'] = "1" ;
    				$msg = "Segera Lengkapi Data Anda di bagian Profile untuk mendapatkan Pembayaran sebagai Publisher Sitti";
	    			$this->View->assign("msg", $msg);
	    			$strHTML = $this->View->showMessage($msg,"profile.php");
    			}
    		}else if($check['status']=="2"){
    			$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
    			$confirm_url = $CONFIG['WebsiteURL2']."konfirmasi.php?token=".$check['confirmKey'];
        		//var_dump($check['info']);
       		 	$msg = "Maaf, akun Anda belum aktif. <br/>Apabila Anda belum menerima email konfirmasi, 
       				 periksa spam folder Anda <br/>atau <a href='login.php?resend=1&token=".urlencode64(serialize(array($confirm_url,urlencode64(serialize($check['info'])))))."'>klik disini</a> untuk mengirim ulang.";
        		$strHTML = $this->View->showMessageError($msg, "http://www.sitti.co.id");
    		} elseif ($check['status']=="99") {
                $_SESSION[urlencode64('block'.date("Ymd"))]+=1;
                $msg = "Maaf, akun Anda telah disuspend.";
                $strHTML = $this->View->showMessageError($msg, "http://www.sitti.co.id");
            } else {
    			$_SESSION[urlencode64('block'.date("Ymd"))]+=1;
    			$er = "Anda tidak berhasil masuk. Periksa nama akun dan kata sandi anda.";
    			$this->View->assign("er", $er);
    			$strHTML =  $this->View->showMessageError($er,"http://www.sitti.co.id");
    		}
    		$account->close();
    		return $strHTML;
    	}
    }

}
?>