<?php
include_once $APP_PATH."SITTI/Model/SITTITestQuery.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIReporting.php";
include_once "SITTIMailer.php";
include_once "Custom_Template/Custom_Template.php"; //angling:02-09-2010
include_once 'SITTISimulation.php';
include_once "SITTIActionLog.php";

include_once $ENGINE_PATH."Utility/Paginate.php";

define("HOME", "SITTI/index.html");
define("CONTACT_FORM", "SITTI/kontak.html");
define("LOGIN_FORM", "SITTI/login.html");
define("REGISTRATION_FORM", "SITTI/daftar.html");
define("CONTENT_PAGE", "SITTI/content.html");
class SITTICreateAd extends SQLData{
	var $View;
	var $stmt;
	var $Account;
	var $error_status=0;
	function SITTICreateAd($req,$account){
		parent::SQLData();
		$this->Request = $req;
		$this->Account = $account;
		$this->View = new BasicView();
		$this->stmt = new SITTITestQuery();
		$this->ActionLog = new SITTIActionLog();
	}
	/**
	 * helper untuk mengintip isi session
	 * hanya boleh digunakan di development.
	 */
	function peek_session(){
		
		$j = json_decode(base64_decode($_SESSION['adbids']));
		$j2 = json_decode(base64_decode($_SESSION['ad_create_step1']));
		$j3 = json_decode(base64_decode($_SESSION['adsetskeys']));
		$j4 = json_decode(base64_decode($_SESSION['keypicks']));
		
		print "adbids:<br/>";
		print_r($j);
		
		print "ad_create_step1:<br/>";
		print_r($j2);
		
		print "adsetskeys:<br/>";
		print_r($j3);
		
		print "keypicks:<br/>";
		print_r($j4);
	}
  	function run(){
  		global $CONFIG;
  		
  		//$this->peek_session();
  	
  		
  		$req = $this->Request;
  		
  		if($req->getPost("simulation")){
  			if($this->isTokenValid()){
  				return $this->SimulationPage();
  			}else{
  				return $this->show(true);
  			}
  		}else if($req->getPost("purchase")){
  			if($this->isTokenValid()){
  				return $this->Purchase();
  			}else{
  				return $this->show(true);
  			}
  		}else if($req->getPost("wizard")){
			return $this->Wizard($req->getPost("step"));
  		}else if($req->getPost("detil")){
  			return $this->Detil();
  		}else if($req->getPost("do")=="confirm"){
  			return $this->Confirm();
  		}else if($req->getPost("save")){
  			if($req->getPost("agree")=="1"){
  				if($this->isTokenValid()){
  					return $this->SaveAdvertisement();
  				}else{
  					$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    				return $this->View->showMessage($msg,"beranda.php");
  				}
  			}else{
  				return $this->Confirm(false);
  			}
  		}else if($req->getParam("edit")=="1"){
  			//edit ulang pilihan keyword
  			return $this->show(true);
  		}else if($req->getParam("edit")=="2"){
  			//edit ulang detail iklan
  			return $this->FormIklanBaru2(null,$_SESSION['ad_create_step1']);
  		}else if($req->getParam("edit")=="3"){
  			//edit ulang bidding
  			return $this->EditPurchase();
  		} else if ($req->getParam('show_upload_form')) {
			return $this->upload_form($req->getParam('jenis_iklan'));
		} else {
  			$this->resetSession();
  			//bypass dulu buat testing form iklan yang baru
  			//return $this->FormIklanBaru2(null,null);
  			return $this->show();	
  		}
  		
  	}
	function run_cpcbanner(){
		$req = $this->Request;
		if($req->getPost("detil")){
  			return $this->Detil_cpcbanner();
  		}else if($req->getPost("do")=="confirm"){
  			return $this->Confirm_cpcbanner();
  		}else if($req->getPost("save")){
  			if($req->getPost("agree")=="1"){
  				if($this->isTokenValid()){
  					return $this->SaveAdvertisement();
  				}else{
  					$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    				return $this->View->showMessage($msg,"beranda.php");
  				}
  			}else{
  				return $this->Confirm_cpcbanner(false);
  			}
  		}else{
			$this->resetSession();
			return $this->show_cpcbanner();
		}
	}
  	function isTokenValid(){
  		//validasi token
    	$token = $this->Request->getPost('token');
    	if(is_token_valid($token)){
    		return true;
    	}
  	}
/**
     * 
     * Save advertisement
     */
	function SaveAdvertisement($wizard=false){
		global $CONFIG, $LOCALE;
    	$inventory = new SITTIInventory();
    	$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
      //tambahkan data ke inventory
      if ($iklan->jenis_iklan == 'image')
      {
        $serve_type = 2;
      }
      elseif ($iklan->jenis_iklan == 'flash')
      {
        $serve_type = 3;
      }
      else
      {
        $serve_type = 0;
      }

      $inventory->open();
      $rs = $inventory->createAd($_SESSION['sittiID'],
                    mysql_escape_string($iklan->namaIklan),
                    mysql_escape_string($iklan->judul),
                    mysql_escape_string($iklan->category),
                    mysql_escape_string($iklan->baris1),
                    mysql_escape_string($iklan->baris2),
                    mysql_escape_string($iklan->urlName),
                    mysql_escape_string($iklan->urlLink),
                    mysql_escape_string(urldecode64($iklan->campaign)),
                    mysql_escape_string($iklan->target_market),
                    $serve_type
              );

    	$iklan_id = $inventory->last_insert_id;

      // simpan aset
      if ($iklan->jenis_iklan == 'image' || $iklan->jenis_iklan == 'flash')
      {
        $assets = $iklan->asset_file;
		// print_r($assets);
        foreach ($assets as $asset)
        {
          if ($asset->asset_file == '') continue;
          $inventory->add_ad_asset($iklan_id, $asset->asset_type);
          $inventory->add_ad_file_asset($iklan_id, substr($asset->asset_filename, 0, strpos($asset->asset_filename, '_')));
        }
      }
    	
    	//Geo location restrictions
    	if($iklan->allcity=="1"){
			$locations = array(array("kota"=>"ALL","priority"=>"0"));
		}else{
			$locs = $iklan->tcity;
			$locations = array();
			for($i=0;$i<sizeof($locs);$i++){
				array_push($locations,array("kota"=>$locs[$i],"priority"=>"5"));
			}
		}
		$inventory->addLocation($iklan_id,$locations);
		//---->
    	$inventory->close();
      //print_r($_SESSION['ad_create_step3']);
		
		$keywords="";
    	if($rs){
    		//kalau berhasil, tambahkan daftar keyword iklan
    		
    		//$list = $_SESSION['ad_create_step3'];
   			$j = json_decode(base64_decode($_SESSION['adbids']));
  			$list = $this->keywordJsonToArray($j);
  			$n = sizeof($list);
  			
    		for($i=0;$i<$n;$i++){
    		
          		if($list[$i]['bid']<$CONFIG['MINIMUM_BID']){
          	 		$list[$i]['bid'] = $CONFIG['MINIMUM_BID'];
          		}
         		if(strlen($list[$i]['name'])>0){
         			//if($iklan->ad_type!="1"){
         			if($serve_type!=0){
						// banner and flash
         				$inventory->open();	
					    $inventory->addKeyword($iklan_id,mysql_escape_string($list[$i]['name']),
    			                                      mysql_escape_string($list[$i]['bid']),
    			                                      mysql_escape_string($list[$i]['budget']),
                                                	  mysql_escape_string($list[$i]['total'])); 
						$inventory->close();
         			}else{
         				$inventory->open();	
    					$inventory->addKeyword($iklan_id,mysql_escape_string($list[$i]['name']),
    			                                      mysql_escape_string($list[$i]['bid']),
    			                                      mysql_escape_string($list[$i]['budget']),
                                                mysql_escape_string($list[$i]['total']));
						$inventory->close();
         			}
					if ($keywords!=""){
						$keywords.=",";
					}
					$keywords.=$list[$i]['name'];
					// action log add keyword to ad (131)
					// $this->ActionLog->actionLog(131, $iklan_id, mysql_escape_string($list[$i]['name']),mysql_escape_string($list[$i]['bid']));
					// replaced with 3 action log (134,135,136)
					$this->ActionLog->actionLog(134, $iklan_id, mysql_escape_string($list[$i]['name']),mysql_escape_string($list[$i]['bid']));
					$this->ActionLog->actionLog(135, $iklan_id, mysql_escape_string($list[$i]['name']),mysql_escape_string($list[$i]['budget']));
					$this->ActionLog->actionLog(136, $iklan_id, mysql_escape_string($list[$i]['name']),mysql_escape_string($list[$i]['total']));
				}
    		}
    		$msg = $LOCALE['ADS_SAVE_SUCCESS'];
    		
    		$this->Account->open(0);
    		$profile = $this->Account->getProfile();
    		$this->Account->close();
			// action log create ad (121)
			$this->ActionLog->actionLog(121,$profile['sittiID'],$iklan_id);
			if (!$wizard){
				//kirim email notifikasi
				$smtp = new SITTIMailer();
				$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan->namaIklan.") Berhasil");
				$smtp->setRecipient($profile['email']);
				$this->View->assign("nama", $profile['name']);
				$this->View->assign("judul_iklan", $iklan->judul);
				$this->View->assign("site_url", $CONFIG['WebsiteURL']);
				$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
				$smtp->send();
			}
    		//-->
     		$this->resetSession();
    	}else{
    		$msg = "Iklan anda tidak berhasil disimpan.";
    	}
    	//$this->resetSession();
    	
    	if($rs){
    		//insert data kosong ke table reporting
    		$sql = "INSERT INTO db_report.tbl_performa_iklan_total
    				(advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
    				jum_imp, jum_klik, ctr, harga, 
    				budget_harian, budget_total, last_update)
    				VALUES ('".$_SESSION['sittiID']."',".$iklan_id.",'".mysql_escape_string($iklan->namaIklan)."','".$keywords."',0,
    				0,0,'0.000','0','0','0',NOW())";
    		//print $sql;
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
			$sql = "INSERT INTO db_report.tbl_performa_iklan
    				(datee,advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
    				jum_imp, jum_klik, ctr, avg_cpm, harga, posisi,
    				budget_harian, budget_total, budget_sisa, last_update)
    				VALUES (DATE(NOW()),'".$_SESSION['sittiID']."',".$iklan_id.",'".mysql_escape_string($iklan->namaIklan)."','".$keywords."',0,
    				0,0,'0.0000','0.0000','0','0.00','0','0','0',NOW())";
    		//print $sql;
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
    	}
    	return $this->View->showMessage($msg,"beranda.php");
    }
    function resetSession(){
		$_SESSION['campaign_info'] = null;
    	$_SESSION['adbids'] = null;
    	$_SESSION['ad_create_step1'] = null;
    	$_SESSION['adsetskeys'] = null;
    	$_SESSION['keypicks'] = null;
    }
  	function keywordJsonToArray($json){
  		$n = sizeof($json);
  		for($i=0;$i<$n;$i++){
  			$keyword = mysql_escape_string($json[$i]->keyword);
  			$bid = $json[$i]->bid;
  			$budget = $json[$i]->budget;
  			$total = $json[$i]->total;
  			$max_cpc = $json[$i]->max_cpc;
  			
  			settype($bid,"integer");
  			settype($budget,"integer");
  			settype($total,"integer");
  			settype($max_cpc,"integer");
  			
  			$params[$i]['keyword'] = $keyword;
			$params[$i]['name'] = $keyword;
  			$params[$i]['bid'] = $bid;
  			$params[$i]['budget'] = $budget;
  			$params[$i]['total'] = $total;
  			$params[$i]['max_cpc'] = $max_cpc;
  		}
  		return $params;
  	}
	function Wizard($step){
		global $LOCALE;
		//if ($step=="1"){
		$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
		$p['jenis_iklan'] = $iklan->jenis_iklan;
		$p['namaIklan'] = $_POST["nama_iklan"];
		$p['judul'] = $_POST["judul"];
		$p['judul2'] = "";
		$p['judul3'] = "";
		$p['ad_type'] = "";
		$p['ads_size'] = "";
		$p['baris1'] = $_POST["baris1"];
		$p['baris2'] = "";
		$p['urlName'] = $iklan->urlName;
		$p['urlLink'] = $iklan->urlLink;
		$p['target_web'] = "";
		$p['category'] = $iklan->category;
		$p['target_market'] = "";
		$p['campaign'] = "";
		$p['allcity'] = "1";
		$p['tcity'] = "";
		
		//validasi input
		$flag = false;
		$n = sizeof($p['judul']);
		for ($i=0;$i<$n;$i++){
			if (strlen($p['namaIklan'][$i])==0){
				$flag = true;
				break;
			}
			if (strlen($p['judul'][$i])==0){
				$flag = true;
				break;
			}
			if (strlen($p['baris1'][$i])==0){
				$flag = true;
				break;
			}
		}
		if ($flag){
			$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
			$this->Account->open(0);
			$isLogin = $this->Account->isLogin();
			$info = $this->Account->getProfile();
			$this->Account->close();
			$this->View->assign("namaIklan",$p['namaIklan']);
			$this->View->assign("judul",$p['judul']);
			$this->View->assign("baris1",$p['baris1']);
			$this->View->assign("urlName",$p['urlName']);
			$this->View->assign("msg",$msg);
			$this->View->assign("info",$info);
			$this->View->assign("isLogin","1");
			$this->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
			print $this->View->toString("SITTI/kampanye_wizard2.html");
			die();
		}else{
			$pattern = "/([^A-Za-z0-9\"\'\!\@\#\$\%\^\&\*\(\)\.\,\_\-\=\+\?\:\;\"\'\/\"\ ]+)/";
			for ($i=0;$i<$n;$i++){
				if(preg_match_all($pattern,stripslashes($p['namaIklan'][$i]),$m)>0){
					$flag = true;
					$p['namaIklan'][$i] = "";
					break;
				}
				if(preg_match_all($pattern,stripslashes($p['judul'][$i]),$m)>0){
					$flag = true;
					$p['judul'][$i] = "";
					break;
				}
				if(preg_match_all($pattern,stripslashes($p['baris1'][$i]),$m)>0){
					$flag = true;
					$p['baris1'][$i] = "";
					break;
				}
			}
			if ($flag){
				$msg = $LOCALE['TEXT_INVALID'];
				$this->Account->open(0);
				$isLogin = $this->Account->isLogin();
				$info = $this->Account->getProfile();
				$this->Account->close();
				$this->View->assign("namaIklan",$p['namaIklan']);
				$this->View->assign("judul",$p['judul']);
				$this->View->assign("baris1",$p['baris1']);
				$this->View->assign("urlName",$p['urlName']);
				$this->View->assign("msg",$msg);
				$this->View->assign("info",$info);
				$this->View->assign("isLogin","1");
				$this->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
				print $this->View->toString("SITTI/kampanye_wizard2.html");
				die();
			}else{
				$_SESSION['ad_create_step1'] = base64_encode(json_encode($p));
				// print_r(base64_decode($_SESSION['keypicks']));
				// echo "<br/>";
				// print_r(json_decode(base64_decode($_SESSION['ad_create_step1'])));
				// echo "<br/>";
				// $this->View->assign("wizard","1");
				// return $this->show();
				
				// from step 2
				// create campaign
				$this->Account->open(0);
				$profile = $this->Account->getProfile();
				$this->Account->close();
				$campaign_info = json_decode(base64_decode($_SESSION['campaign_info']));
				// print_r($campaign_info);
				// echo "<br/>";
				$campaign = new SITTICampaign();
				if($campaign->addCampaign($profile['sittiID'],
										  mysql_escape_string($campaign_info->campaign_name),
										  $campaign_info->tgl_mulai,
										  $campaign_info->tgl_berakhir,
										  "",
										  $profile['ox_adv_id'])){
					//masukan data campaign baru ke database reporting
					$campaign->addCampaignToReporting($profile['sittiID'], $campaign->lastInsertId, mysql_escape_string($campaign_info->campaign_name));
					// action log create advertiser campaign (111)
					$this->open(0);
					$rs = $this->fetch("SELECT ox_campaign_id FROM sitti_campaign ORDER BY ox_campaign_id DESC LIMIT 1");
					$this->close();
					$campaign_id = $rs['ox_campaign_id'];
					$this->ActionLog->actionLog(111,$profile['sittiID'],$campaign_id);
					$campaign_id = urlencode64($campaign_id);
					
					// create iklan
					$saldo = $this->Account->getSaldo($profile['sittiID']);
					$saldo = $saldo['budget'];
					$keypicks = json_decode(base64_decode($_SESSION['keypicks']));
					$hari = $keypicks[0];
					$keywords_temp = $keypicks[1];
					$keywords_temp = array_unique($keywords_temp);
					$keywords = array();
					foreach($keywords_temp as $word){
						array_push($keywords, $word);
					}
					// print_r($keywords);
					// echo "<br/>";
					
					$budget = 10000;
					if($saldo/($hari*2)>10000){
						$budget = floor($saldo/($hari*2));
					}
					$total = 100000;
					if($saldo>100000){
						$total = $saldo;
					}
					$slots = sizeof($keywords);
					$picks = array();
					$simulation = new SITTISimulation();
					for($i=0;$i<$slots;$i++){
						$keyword[$i]['keyword'] = $keywords[$i];
						$keyword[$i]['bid'] = $CONFIG['MINIMUM_BID'];
						$keyword[$i]['budget'] = $budget;
						$keyword[$i]['total'] = $total;
						$keydata = json_decode($simulation->getKeywordData($keyword[$i]['keyword']));
						$keyword[$i]['max_cpc'] = $keydata->avg_cpc;
						array_push($picks,$keyword[$i]['keyword']);
					}
					$_SESSION['keypicks'] = base64_encode(json_encode(array($hari,$picks)));
					// print_r(array($hari,$picks));
					// echo "<br/>";
					// print_r($keyword);
					// echo "<br/>";
					$bids = json_encode($keyword);
					for($i=0;$i<$slots;$i++){
						$keyword[$i]['hari'] = $hari;
					}
					// print_r($keyword);
					// echo "<br/>";
					$son = json_encode($keyword);
					
					$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
					$copy_ad_namaIklan = $iklan->namaIklan;
					$copy_ad_judul = $iklan->judul;
					$copy_ad_baris = $iklan->baris1;
					$judul_iklan = "";
					for($i=0;$i<sizeof($copy_ad_judul);$i++){
						// set session adbids dan adsetskeys
						$_SESSION['adsetskeys'] = base64_encode($bids);
						$_SESSION['adbids'] = base64_encode($son);
						// insert campaign id to ad_create_step1
						$p['jenis_iklan'] = $iklan->jenis_iklan;
						$p['namaIklan'] = $copy_ad_namaIklan[$i];
						$p['judul'] = $copy_ad_judul[$i];
						$p['judul2'] = "";
						$p['judul3'] = "";
						$p['ad_type'] = "";
						$p['ads_size'] = "";
						$p['baris1'] = $copy_ad_baris[$i];
						$p['baris2'] = "";
						$p['urlName'] = $iklan->urlName;
						$p['urlLink'] = $iklan->urlLink;
						$p['target_web'] = "";
						$p['category'] = $iklan->category;
						$p['target_market'] = "";
						$p['campaign'] = $campaign_id;
						$p['allcity'] = "1";
						$p['tcity'] = "";
						$_SESSION['ad_create_step1'] = base64_encode(json_encode($p));
						
						// print_r(json_decode(base64_decode($_SESSION['keypicks'])));
						// echo "<br/>";
						// print_r(json_decode(base64_decode($_SESSION['adsetskeys'])));
						// echo "<br/>";
						// print_r(json_decode(base64_decode($_SESSION['adbids'])));
						// echo "<br/>";
						// print_r(json_decode(base64_decode($_SESSION['ad_create_step1'])));
						// echo "<br/>";
						
						$this->SaveAdvertisement(true);
						if ($i!=0){
							$judul_iklan .= ", ";
						}
						$judul_iklan .= $copy_ad_judul[$i];
					}
					// send email
					$smtp = new SITTIMailer();
					$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda Pada Kampanye ".$campaign_info->campaign_name." Berhasil");
					$smtp->setRecipient($profile['email']);
					$this->View->assign("nama", $profile['name']);
					$this->View->assign("judul_iklan", $judul_iklan);
					$this->View->assign("site_url", $CONFIG['WebsiteURL']);
					$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
					$smtp->send();
				}
				$msg = $LOCALE['ADS_SAVE_SUCCESS'];
				return $this->View->showMessage($msg,"beranda.php");
			}
		}
		//}else if ($step=="2"){
		
		//}
	}
  	function Confirm($flag=TRUE){
  		global $LOCALE,$CONFIG;
  		
  		$jenis_iklan = $this->Request->getPost('jenis_iklan');
      $p['jenis_iklan'] = $jenis_iklan;
      $this->View->assign("jenis_iklan", $jenis_iklan);
      if ($jenis_iklan == 'image' || $jenis_iklan == 'flash')
      {
        $banner = array(
          null,
          array("popupName"=>"popup300x250","width"=>300,"height"=>250),
          array("popupName"=>"popup336x280","width"=>336,"height"=>280),
          array("popupName"=>"popup728x90","width"=>728,"height"=>90),
          array("popupName"=>"popup160x600","width"=>160,"height"=>600),
          array("popupName"=>"popup610x60","width"=>610,"height"=>60),
          array("popupName"=>"popup300x160","width"=>300,"height"=>160),
          array("popupName"=>"popup940x70","width"=>940,"height"=>70),
          array("popupName"=>"popup520x70","width"=>520,"height"=>70),
          array("popupName"=>"popup468x60","width"=>468,"height"=>60),
          array("popupName"=>"popup250x250","width"=>250,"height"=>250)
        );

        $fileID = $this->Request->getPost("fileID");
        $_SESSION['file_token'] = urlencode64($fileID);
        $file_ext = $jenis_iklan == 'flash' ? '.swf' : '.'.$this->Request->getPost('image_ext');
        
        $banners = array();
        for($i=1;$i<=10;$i++)
        {
            $asset_file="";
            if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$file_ext))
            {
                $asset_file=$CONFIG['BANNER_URL'].$fileID."_".$i.$file_ext;
                $asset_file_name = $fileID."_".$i;
            }
            array_push($banners,array("banner"=>$banner[$i],"asset_file"=>$asset_file, "asset_type" => $i, "asset_filename" => $asset_file_name));
        }
        $p['asset_file'] = $banners;
      }
  		
      if ($jenis_iklan == 'image')
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_image");
        $judul_iklan = $this->Request->getPost("judul_iklan_image");
      } 
      elseif ($jenis_iklan == 'flash')
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_flash");
        $judul_iklan = $this->Request->getPost("judul_iklan_flash");
      }
      else
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_text");
        $judul_iklan = $this->Request->getPost("judul_iklan_text"); 
      }

      if ($this->Request->getPost("urlLink"))
      {
        $url_link = $this->Request->getPost("urlLink");
        if (strpos($url_link, 'http://') === false && strpos($url_link, 'https://') === false)
        {
          $url_link = 'http://' . $url_link;
        }
      }
		
  		if($flag){
			// update session adbids
			$req = $this->Request;
			$keys = $_POST['keyword'];
			$bids = $_POST['bid'];
			$hari = $_POST['hari'];
			$budget = $_POST['budget'];
			$total = $_POST['total'];
			$cpc = $_POST['cpc'];
			$n = sizeof($keys);
			for($i=0;$i<$n;$i++){
				$n_bid = $bids[$i];//bid
				if($n_bid<$CONFIG['MINIMUM_BID']){
					$n_bid = $CONFIG['MINIMUM_BID'];
				}
				$n_cpc = $cpc[$i]; //max cpc
				$n_total = $total[$i];
				$n_budget = $budget[$i];
				if ($n_budget!=""){
					settype($n_budget, 'integer');
				}
				settype($n_bid,'integer');
				settype($n_cpc,'integer');
				if ($n_total!=""){
					settype($n_total,'integer');
				}
				$keywords[$i]['keyword'] = $keys[$i];
				$keywords[$i]['bid'] = $n_bid;
				$keywords[$i]['budget'] = $n_budget;
				$keywords[$i]['total'] = $n_total;
				$keywords[$i]['max_cpc'] = $n_cpc;
				$keywords[$i]['hari'] = $hari;
			}
			$son = json_encode($keywords);
			
			$_SESSION['adbids'] = base64_encode($son);
			//-->
			
  			$p['namaIklan'] = $nama_iklan;
      		$p['judul'] = $judul_iklan;
      		$p['judul2'] = $this->Request->getPost("judul2");
      		$p['judul3'] = $this->Request->getPost("judul3");
      		$p['ad_type'] = $this->Request->getPost("ad_type");
      		$p['ads_size'] = $this->Request->getPost("ads_size");
      		$p['baris1'] = $this->Request->getPost("baris1");
      		$p['baris2'] = $this->Request->getPost("baris2");
      		$p['urlName'] = $this->Request->getPost("urlName");
      		$p['urlLink'] = $url_link;
      		$p['target_web'] = $this->Request->getPost("target_web");
      		$p['category'] = $this->Request->getPost("category");
      		$p['target_market'] = $this->Request->getPost("target_market");
      		$p['campaign'] = $this->Request->getPost("campaign");
      		$p['allcity'] = $this->Request->getPost("allcity");
 			$p['tcity'] = $_POST['tcity'];
 			//untuk sementara pasang disini dulu
 			/*if($p['ad_type']=="2"){
  				$asset_file = date("YmdHis").$salt.".jpg";
 			}else if($p['ad_type']=="3"){
 				$asset_file = date("YmdHis").$salt.".jpg";
 			}
 			$p['asset_file'] = $asset_file;*/
      		$json = json_encode($p);
      		$_SESSION['ad_create_step1'] = base64_encode($json);
  		}else{
  			//print base64_decode($_SESSION['ad_create_step1']);
  			$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
  			$p['namaIklan'] = $iklan->namaIklan;
  			$p['judul'] = $iklan->judul;
  			$p['judul2'] = $this->Request->getPost("judul2");
      		$p['judul3'] = $this->Request->getPost("judul3");
      		$p['ad_type'] = $this->Request->getPost("ad_type");
      		$p['ads_size'] = $this->Request->getPost("ads_size");
      		$p['baris1'] =$iklan->baris1;
      		$p['baris2'] = $iklan->baris2;
      		$p['urlName'] = $iklan->urlName;
      		$p['urlLink'] = $url_link;
      		$p['target_web'] = $iklan->target_web;
      		$p['category'] = $iklan->category;
      		$p['target_market'] = $iklan->target_market;
      		$p['campaign'] = $iklan->campaign;
  			/*if($p['ad_type']=="2"){
  				$asset_file = date("YmdHis").$salt.".jpg";
 			}else if($p['ad_type']=="3"){
 				$asset_file = date("YmdHis").$salt.".jpg";
 			}
 			$p['asset_file'] = $asset_file;*/
      		$this->View->assign("msg","Anda belum menyetujui Syarat Ketentuan SITTI");
      		
  		}

		//selected campaign
    	$this->View->assign("campaign_id",$this->Request->getPost("c_id"));
  		//var_dump($_POST);
  		//die();
      $is_form_valid = $this->isForm2Valid();
      //$is_form_valid = true;
      if($is_form_valid || !$flag){
  			/*if($p['ad_type']=="2"){
  				$this->uploadImageBanner($asset_file);
  			}else if($p['ad_type']=="3"){
  				$this->uploadFlashBanner($asset_file);
  			}*/
  			
  			$j = json_decode(base64_decode($_SESSION['adbids']));
  			$keywords = $this->keywordJsonToArray($j);
  			$n = sizeof($keywords);
  			$n_budget = 0;
  			$n_total = 0;
  			for($i=0;$i<$n;$i++){
  		 		if($keywords[$i]['bid']<$CONFIG['MINIMUM_BID']){
          	 		$keywords[$i]['bid'] = $CONFIG['MINIMUM_BID'];
          		}
          		$n_budget+=$keywords[$i]['budget'];
          		$n_total+=$keywords[$i]['total'];
  			}
  			$this->View->assign("n_budget",$n_budget);
  			$this->View->assign("n_total",$n_total);
  			$this->View->assign("keywords",$keywords);
  			$this->View->assign("rs1",$p);
  		}else{
        switch($this->error_status){
             	case 1:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 2:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 3:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['URL_INVALID'];
             		 return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 4:
              		//check text
              		$msg = $LOCALE['TEXT_INVALID'];
             		 return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
				case 5:
              		$msg = "Teks Iklan mengandung Kata Yang Tidak Diperbolehkan Untuk Digunakan";
					return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
				case 6:
              		$msg = "Anda Belum Memasukkan Nilai Anggaran Anda <br/>(Masih Ada Nilai Anggaran Yang Kosong)";
					return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
				case 7:
              		$msg = "Nilai Anggaran Harian Tidak Boleh Lebih Kecil dari Nilai Tawaran";
					return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
				case 8:
              		$msg = "Nilai Anggaran Total Tidak Boleh Lebih Kecil dari Nilai Anggaran Harian";
					return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
				case 9:
              		$msg = "Nilai Tawaran, Anggaran Harian, dan Anggaran Total Harus Kelipatan 50";
					return $this->FormIklanBaru2($msg, $_SESSION['ad_create_step1']);
             	break;
           	}
  			return $this->FormIklanBaru2($msg,$_SESSION['ad_create_step1']);
  			
  		}
  		
  		return $this->View->toString("SITTI/form_iklan_baru/reg4.html");
  	}
	function Confirm_cpcbanner($flag=TRUE){
  		global $LOCALE,$CONFIG;
  		
  		$jenis_iklan = $this->Request->getPost('jenis_iklan');
      $p['jenis_iklan'] = $jenis_iklan;
      $this->View->assign("jenis_iklan", $jenis_iklan);
      if ($jenis_iklan == 'image' || $jenis_iklan == 'flash')
      {
        $banner = array(
          null,
          array("popupName"=>"popup300x250","width"=>300,"height"=>250),
          array("popupName"=>"popup336x280","width"=>336,"height"=>280),
          array("popupName"=>"popup728x90","width"=>728,"height"=>90),
          array("popupName"=>"popup160x600","width"=>160,"height"=>600),
          array("popupName"=>"popup610x60","width"=>610,"height"=>60),
          array("popupName"=>"popup300x160","width"=>300,"height"=>160),
          array("popupName"=>"popup940x70","width"=>940,"height"=>70),
          array("popupName"=>"popup520x70","width"=>520,"height"=>70),
          array("popupName"=>"popup468x60","width"=>468,"height"=>60),
          array("popupName"=>"popup250x250","width"=>250,"height"=>250)
        );

        $fileID = $this->Request->getPost("fileID");
        $_SESSION['file_token'] = urlencode64($fileID);
        $file_ext = $jenis_iklan == 'flash' ? '.swf' : '.'.$this->Request->getPost('image_ext');
        
        $banners = array();
        for($i=1;$i<=10;$i++)
        {
            $asset_file="";
            if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$file_ext))
            {
                $asset_file=$CONFIG['BANNER_URL'].$fileID."_".$i.$file_ext;
                $asset_file_name = $fileID."_".$i;
            }
            array_push($banners,array("banner"=>$banner[$i],"asset_file"=>$asset_file, "asset_type" => $i, "asset_filename" => $asset_file_name));
        }
        $p['asset_file'] = $banners;
      }
  		
      if ($jenis_iklan == 'image')
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_image");
        $judul_iklan = $this->Request->getPost("judul_iklan_image");
      } 
      elseif ($jenis_iklan == 'flash')
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_flash");
        $judul_iklan = $this->Request->getPost("judul_iklan_flash");
      }
      else
      {
        $nama_iklan = $this->Request->getPost("nama_iklan_text");
        $judul_iklan = $this->Request->getPost("judul_iklan_text"); 
      }

      if ($this->Request->getPost("urlLink"))
      {
        $url_link = $this->Request->getPost("urlLink");
        if (strpos($url_link, 'http://') === false && strpos($url_link, 'https://') === false)
        {
          $url_link = 'http://' . $url_link;
        }
      }
		
  		if($flag){
			// update session adbids
			$req = $this->Request;
			$keys = $_POST['keyword'];
			$bids = $_POST['bid'];
			$hari = $_POST['hari'];
			$budget = $_POST['budget'];
			$total = $_POST['total'];
			$cpc = $_POST['cpc'];
			$n = sizeof($keys);
			for($i=0;$i<$n;$i++){
				$n_bid = $bids[$i];//bid
				if($n_bid<$CONFIG['MINIMUM_BID']){
					$n_bid = $CONFIG['MINIMUM_BID'];
				}
				$n_cpc = $cpc[$i]; //max cpc
				$n_total = $total[$i];
				$n_budget = $budget[$i];
				if ($n_budget!=""){
					settype($n_budget, 'integer');
				}
				settype($n_bid,'integer');
				settype($n_cpc,'integer');
				if ($n_total!=""){
					settype($n_total,'integer');
				}
				$keywords[$i]['keyword'] = $keys[$i];
				$keywords[$i]['bid'] = $n_bid;
				$keywords[$i]['budget'] = $n_budget;
				$keywords[$i]['total'] = $n_total;
				$keywords[$i]['max_cpc'] = $n_cpc;
				$keywords[$i]['hari'] = $hari;
			}
			$son = json_encode($keywords);
			
			$_SESSION['adbids'] = base64_encode($son);
			//-->
			
  			$p['namaIklan'] = $nama_iklan;
      		$p['judul'] = $judul_iklan;
      		$p['judul2'] = $this->Request->getPost("judul2");
      		$p['judul3'] = $this->Request->getPost("judul3");
      		$p['ad_type'] = $this->Request->getPost("ad_type");
      		$p['ads_size'] = $this->Request->getPost("ads_size");
      		$p['baris1'] = $this->Request->getPost("baris1");
      		$p['baris2'] = $this->Request->getPost("baris2");
      		$p['urlName'] = $this->Request->getPost("urlName");
      		$p['urlLink'] = $url_link;
      		$p['target_web'] = $this->Request->getPost("target_web");
      		$p['category'] = $this->Request->getPost("category");
      		$p['target_market'] = $this->Request->getPost("target_market");
      		$p['campaign'] = $this->Request->getPost("campaign");
      		$p['allcity'] = $this->Request->getPost("allcity");
 			$p['tcity'] = $_POST['tcity'];
 			//untuk sementara pasang disini dulu
 			/*if($p['ad_type']=="2"){
  				$asset_file = date("YmdHis").$salt.".jpg";
 			}else if($p['ad_type']=="3"){
 				$asset_file = date("YmdHis").$salt.".jpg";
 			}
 			$p['asset_file'] = $asset_file;*/
      		$json = json_encode($p);
			// print_r($p);
      		$_SESSION['ad_create_step1'] = base64_encode($json);
  		}else{
  			//print base64_decode($_SESSION['ad_create_step1']);
  			$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
  			$p['namaIklan'] = $iklan->namaIklan;
  			$p['judul'] = $iklan->judul;
  			$p['judul2'] = $this->Request->getPost("judul2");
      		$p['judul3'] = $this->Request->getPost("judul3");
      		$p['ad_type'] = $this->Request->getPost("ad_type");
      		$p['ads_size'] = $this->Request->getPost("ads_size");
      		$p['baris1'] =$iklan->baris1;
      		$p['baris2'] = $iklan->baris2;
      		$p['urlName'] = $iklan->urlName;
      		$p['urlLink'] = $url_link;
      		$p['target_web'] = $iklan->target_web;
      		$p['category'] = $iklan->category;
      		$p['target_market'] = $iklan->target_market;
      		$p['campaign'] = $iklan->campaign;
  			/*if($p['ad_type']=="2"){
  				$asset_file = date("YmdHis").$salt.".jpg";
 			}else if($p['ad_type']=="3"){
 				$asset_file = date("YmdHis").$salt.".jpg";
 			}
 			$p['asset_file'] = $asset_file;*/
      		$this->View->assign("msg","Anda belum menyetujui Syarat Ketentuan SITTI");
      		
  		}

		//selected campaign
    	$this->View->assign("campaign_id",$this->Request->getPost("c_id"));
  		//var_dump($_POST);
  		//die();
      $is_form_valid = $this->isForm2Valid();
      //$is_form_valid = true;
      if($is_form_valid || !$flag){
  			/*if($p['ad_type']=="2"){
  				$this->uploadImageBanner($asset_file);
  			}else if($p['ad_type']=="3"){
  				$this->uploadFlashBanner($asset_file);
  			}*/
  			
  			$j = json_decode(base64_decode($_SESSION['adbids']));
  			$keywords = $this->keywordJsonToArray($j);
  			$n = sizeof($keywords);
  			$n_budget = 0;
  			$n_total = 0;
  			for($i=0;$i<$n;$i++){
  		 		if($keywords[$i]['bid']<$CONFIG['MINIMUM_BID']){
          	 		$keywords[$i]['bid'] = $CONFIG['MINIMUM_BID'];
          		}
          		$n_budget+=$keywords[$i]['budget'];
          		$n_total+=$keywords[$i]['total'];
  			}
  			$this->View->assign("n_budget",$n_budget);
  			$this->View->assign("n_total",$n_total);
  			$this->View->assign("keywords",$keywords);
  			$this->View->assign("rs1",$p);
  		}
  		
  		return $this->View->toString("SITTI/form_iklan_baru/reg4_cpcbanner.html");
  	}
  	function uploadImageBanner($name){
  		global $CONFIG;
  		$salt = rand(1000,9999);
  		//$name = date("YmdHis").$salt.".jpg";
  		if(move_uploaded_file($_FILES['img']['tmp_name'],$CONFIG['BANNER_ASSET_PATH'].$name)){
  			return $name;
  		}else{
  			//do something here
  			return null;	
  		}
  		
  	}
  	function uploadFlashBanner($name){
  		$salt = rand(1000,9999);
  		//$name = date("YmdHis").$salt.".swf";
  		if(move_uploaded_file($_FILES['fla']['tmp_name'],$CONFIG['BANNER_ASSET_PATH'].$name)){
  			return $name;
  		}else{
  			//do something here
  			return null;	
  		}
  	}
/**
     *  method untuk mengecek isian dari form detail iklan
     * @return boolean
     */
    
    function isForm2Valid(){
      $this->error_status=0;
      $namaIklan = mysql_escape_string(stripslashes($this->Request->getPost("nama_iklan_text")));
      $judul = $this->Request->getPost("judul_iklan_text");
      $judul2 = $this->Request->getPost("judul2");
      $judul3 = $this->Request->getPost("judul3");
      $ads_size = $this->Request->getPost('ads_size');
      $ad_type = $this->Request->getPost('ad_type');
      
      $baris1 = $this->Request->getPost("baris1");
      $baris2 = $this->Request->getPost("baris2");
      $urlName = $this->Request->getPost("urlName");
      $urlLink = $this->Request->getPost("urlLink");
      $target_web = $this->Request->getPost("target_web");

      $jenis_iklan = $this->Request->getPost('jenis_iklan');
	  
	  // pengecekan nilai daily budget and budget total
	  $isbudget_err = false;
	  $istotal_err = false;
	  $cek_jumlah_budget = false;
	  $cek_jumlah_total = false;
	  $cek_kelipatan_50 = false;
	  $keywords = json_decode(base64_decode($_SESSION['adbids']));
	  $n = sizeof($keywords);
	  $adbids = array();
	  for($i=0;$i<$n;$i++){
		  if((string)$keywords[$i]->budget==""){
			  $isbudget_err = true;
		  }
		  if((string)$keywords[$i]->total==""){
			  $istotal_err = true;
		  }
		  if ((string)$keywords[$i]->budget!="" && $keywords[$i]->budget<$keywords[$i]->bid){
			  $cek_jumlah_budget = true;
			  $keywords[$i]->budget = "";
			  $keywords[$i]->total = "";
		  }
		  if ((string)$keywords[$i]->budget!="" && $keywords[$i]->total<$keywords[$i]->budget){
			  $cek_jumlah_total = true;
			  $keywords[$i]->total = "";
		  }
		  if ((string)$keywords[$i]->bid!="" && (int)$keywords[$i]->bid%50!=0){
			  $cek_kelipatan_50 = true;
			  $keywords[$i]->bid = "600";
		  }
		  if ((string)$keywords[$i]->budget!="" && (int)$keywords[$i]->budget%50!=0){
			  $cek_kelipatan_50 = true;
			  $keywords[$i]->budget = "";
		  }
		  if ((string)$keywords[$i]->total!="" && (int)$keywords[$i]->total%50!=0){
			  $cek_kelipatan_50 = true;
			  $keywords[$i]->total = "";
		  }
		  $adbids[$i]['keyword'] = $keywords[$i]->keyword;
		  $adbids[$i]['bid'] = $keywords[$i]->bid;
		  $adbids[$i]['budget'] = $keywords[$i]->budget;
		  $adbids[$i]['total'] = $keywords[$i]->total;
		  $adbids[$i]['max_cpc'] = $keywords[$i]->max_cpc;
		  $adbids[$i]['hari'] = $keywords[$i]->hari;
	  }
	  // update session
	$son = json_encode($adbids);
	
	$_SESSION['adbids'] = base64_encode($son);
      
      //a pattern to make sure no karakter aneh2...
      $pattern = "/([^A-Za-z0-9\"\'\!\@\#\$\%\^\&\*\(\)\.\,\_\-\=\+\?\:\;\"\'\/\"\ ]+)/";
      
      //take out baris2 ..
      if($jenis_iklan=="text"&&(strlen($namaIklan)==0 || strlen($judul)==0 || strlen($baris1)==0 || (strlen($urlName)==0   
      						|| strlen($urlLink)==0 || $urlLink=='http://'))){
        $this->error_status=1;
        return false;

      }else if($jenis_iklan=="text"&&(strlen($namaIklan)==0 || strlen($judul)==0 || strlen($baris1)==0)){
        $this->error_status=2;
        return false;
     }else if(!@isValidUrl($urlLink)){
        $this->error_status=3;
       return false;
      //-->
      }else if(preg_match_all($pattern,stripslashes($namaIklan),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($jenis_iklan=="text"&&preg_match_all($pattern,stripslashes($judul),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($ad_type=="2"&&preg_match_all($pattern,stripslashes($judul2),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($ad_type=="3"&&preg_match_all($pattern,stripslashes($judul3),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($jenis_iklan=="text"&&preg_match_all($pattern,stripslashes($baris1),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($isbudget_err || $istotal_err){
      	$this->error_status=6;
      	return false;
      }else if($cek_jumlah_budget){
      	$this->error_status=7;
      	return false;
      }else if($cek_jumlah_total){
      	$this->error_status=8;
      	return false;
      }else if($cek_kelipatan_50){
      	$this->error_status=9;
      	return false;
      }else{
    	$simulation = new SITTISimulation();
		$restricted_words = $simulation->getRestrictedWords();
		$words = $namaIklan . " " .$judul. " " . $baris1;
		$words = strtolower($words);
		$restricted_words = str_replace("\"","",$restricted_words);
		$restricted_words = str_replace("[","",$restricted_words);
		$restricted_words = str_replace("]","",$restricted_words);
		$restricted_words = explode(",",$restricted_words);
		for ($i=0;$i<count($restricted_words);$i++){
			$pattern = "/\b".$restricted_words[$i]."\b/";
			if(preg_match($pattern,stripslashes($words))>0){
				$this->error_status=5;
				return false;
			}
		}
         return true;
      }
      
     
    }
	function Purchase(){
  		global $CONFIG;
  		$req = $this->Request;
  		$slots = $req->getPost("slots");
  		$picks = array();
  		for($i=0;$i<$slots;$i++){
  			$budget = $req->getPost("budget_".$i);
  			$hari = $req->getPost("hari");
  			$bid = $req->getPost("avg_cpc_".$i);//bid
  			$cpc = $req->getPost("cpc_".$i); //max cpc
  			settype($budget, 'integer');
  			settype($hari, 'integer');
  			settype($bid,'integer');
  			settype($cpc,'integer');
  			$keywords[$i]['keyword'] = $req->getPost("daily_".$i);
  			$keywords[$i]['bid'] = $bid;
  			$keywords[$i]['budget'] = $budget;
  			$keywords[$i]['total'] = $budget*$hari;
  			$keywords[$i]['max_cpc'] = $cpc;
  			array_push($picks,$keywords[$i]['keyword']);
  		}
  		
  		$oldpicks = json_decode(base64_decode($_SESSION['keypicks']));
  		$this->View->assign("n_slots",sizeof($keywords));
  		$bids = json_encode($keywords);
  		
  		$_SESSION['adsetskeys'] = base64_encode($bids);
  		$_SESSION['keypicks'] = base64_encode(json_encode(array($oldpicks[0],$picks)));
      $this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
  		$this->View->assign("list",$keywords);
  		return $this->View->toString("SITTI/form_iklan_baru/reg3.html");
  	}
  	function EditPurchase(){
  		global $CONFIG;
  		$req = $this->Request;
  		 //print base64_decode($_SESSION['adbids']);
  		$obj = json_decode(stripslashes(base64_decode($_SESSION['adbids'])));
  		$obj2 = json_decode(base64_decode($_SESSION['keypicks']));
  		$hari = $obj2[0];
  		$n = sizeof($obj);
  		
  		for($i=0;$i<$n;$i++){
  			$keyword = mysql_escape_string($obj[$i]->keyword);
  			$budget = mysql_escape_string($obj[$i]->budget);
  			//$hari = mysql_escape_string($hari);
  			$hari = mysql_escape_string($obj[$i]->hari);
  			$bid = mysql_escape_string($obj[$i]->bid);
  			$cpc = mysql_escape_string($obj[$i]->max_cpc);
			$total = mysql_escape_string($obj[$i]->total);
  			settype($budget, 'integer');
  			settype($hari, 'integer');
        if ($bid < $CONFIG['MINIMUM_BID']) {
          $bid = $CONFIG['MINIMUM_BID'];
        }
  			settype($bid,'integer');
  			settype($cpc,'integer');
  			$keywords[$i]['keyword'] = $keyword;
  			$keywords[$i]['bid'] = $bid;
  			$keywords[$i]['budget'] = $budget;
  			$keywords[$i]['total'] = $total;
  			$keywords[$i]['max_cpc'] = $cpc;
  		}
  		$this->View->assign("n_slots",sizeof($keywords));
  		$this->View->assign("list",$keywords);
      $this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
  		return $this->View->toString("SITTI/form_iklan_baru/reg3.html");
  	}
  	function Detil(){
  		global $CONFIG;
		
		$req = $this->Request;
    	$q = preg_replace("/(\r\n)+|(\n|\r)+/", ";", $_POST['q']);
    	$c = $req->getPost("c");
    	$hari = $req->getPost("hari");
    	
    	$main_key = $_POST['keywords'];
    	$json = json_encode(array($hari,$main_key));
    	$_SESSION['keypicks']=base64_encode($json);
    	$n_len = sizeof($main_key);
    	if($n_len>100){
    		$n_len = 100;
    	}
    	for($i=0;$i<$n_len;$i++){
    		$list[$i]['no'] = $i+1;
    		$list[$i]['list'][0] = strip_tags(mysql_escape_string($main_key[$i]));
    		
    			$this->open(0);
    			$sql = $this->stmt->getSuggestion2("'".$list[$i]['list'][0]."'",0,40);
    			
    			$foo = $this->fetch($sql,1);
    			$this->close();
    			for($j=1;$j<=40;$j++){
    				if(strlen($foo[$j]['kata2'])>1){
    					$list[$i]['list'][$j] = $foo[$j]['kata2'];
    				}  
    			}
    	}
		
		$slots = $n_len;
  		$picks = array();
		$simulation = new SITTISimulation();
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['keyword'] = $list[$i]['list'][0];
  			$keywords[$i]['bid'] = $CONFIG['MINIMUM_BID'];
  			$keywords[$i]['budget'] = "";
  			$keywords[$i]['total'] = "";
  			$keydata = json_decode($simulation->getKeywordData($keywords[$i]['keyword']));
			$keywords[$i]['max_cpc'] = $keydata->avg_cpc;
  			array_push($picks,$keywords[$i]['keyword']);
  		}
		
		// old detil
		// $req = $this->Request;
		// $slots = $req->getPost("slots");
  		// $picks = array();
  		// for($i=0;$i<$slots;$i++){
  			// $budget = $req->getPost("budget_".$i);
  			// $hari = $req->getPost("hari");
  			// $bid = $req->getPost("avg_cpc_".$i);//bid
  			// $cpc = $req->getPost("cpc_".$i); //max cpc
  			// settype($budget, 'integer');
  			// settype($hari, 'integer');
  			// settype($bid,'integer');
  			// settype($cpc,'integer');
  			// $keywords[$i]['keyword'] = $req->getPost("daily_".$i);
			// if($bid<$CONFIG['MINIMUM_BID']){
          	 	// $bid = $CONFIG['MINIMUM_BID'];
          	// }
  			// $keywords[$i]['bid'] = $bid;
  			// $keywords[$i]['budget'] = $budget;
  			// $keywords[$i]['total'] = $budget*$hari;
  			// $keywords[$i]['max_cpc'] = $cpc;
  			// array_push($picks,$keywords[$i]['keyword']);
  		// }
  		//old detil
		
  		$oldpicks = json_decode(base64_decode($_SESSION['keypicks']));
  		
  		$bids = json_encode($keywords);
  		$_SESSION['adsetskeys'] = base64_encode($bids);
  		$_SESSION['keypicks'] = base64_encode(json_encode(array($oldpicks[0],$picks)));
		
  		$hari = $_POST['hari'];
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['hari'] = $hari;
  		}
  		$son = json_encode($keywords);
		//print_r($son);
  		
  		$_SESSION['adbids'] = base64_encode($son);
  		//print_r($_SESSION);
  		return $this->FormIklanBaru2(null,$_SESSION['ad_create_step1']);
  		//return $this->View->toString("SITTI/form_iklan_baru/reg1.html");
  	}
	function Detil_cpcbanner(){
  		global $CONFIG;
		
		$req = $this->Request;
    	$q = preg_replace("/(\r\n)+|(\n|\r)+/", ";", $_POST['q']);
    	$c = $req->getPost("c");
    	$hari = $req->getPost("hari");
    	
    	$main_key = $_POST['keywords'];
    	$json = json_encode(array($hari,$main_key));
    	$_SESSION['keypicks']=base64_encode($json);
    	$n_len = sizeof($main_key);
    	if($n_len>100){
    		$n_len = 100;
    	}
    	for($i=0;$i<$n_len;$i++){
    		$list[$i]['no'] = $i+1;
    		$list[$i]['list'][0] = strip_tags(mysql_escape_string($main_key[$i]));
    		
    			$this->open(0);
    			$sql = $this->stmt->getSuggestion2("'".$list[$i]['list'][0]."'",0,40);
    			
    			$foo = $this->fetch($sql,1);
    			$this->close();
    			for($j=1;$j<=40;$j++){
    				if(strlen($foo[$j]['kata2'])>1){
    					$list[$i]['list'][$j] = $foo[$j]['kata2'];
    				}  
    			}
    	}
		
		$slots = $n_len;
  		$picks = array();
		$simulation = new SITTISimulation();
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['keyword'] = $list[$i]['list'][0];
  			$keywords[$i]['bid'] = $CONFIG['MINIMUM_BID'];
  			$keywords[$i]['budget'] = "";
  			$keywords[$i]['total'] = "";
  			$keydata = json_decode($simulation->getKeywordData($keywords[$i]['keyword']));
			$keywords[$i]['max_cpc'] = $keydata->avg_cpc;
  			array_push($picks,$keywords[$i]['keyword']);
  		}
		
		// old detil
		// $req = $this->Request;
		// $slots = $req->getPost("slots");
  		// $picks = array();
  		// for($i=0;$i<$slots;$i++){
  			// $budget = $req->getPost("budget_".$i);
  			// $hari = $req->getPost("hari");
  			// $bid = $req->getPost("avg_cpc_".$i);//bid
  			// $cpc = $req->getPost("cpc_".$i); //max cpc
  			// settype($budget, 'integer');
  			// settype($hari, 'integer');
  			// settype($bid,'integer');
  			// settype($cpc,'integer');
  			// $keywords[$i]['keyword'] = $req->getPost("daily_".$i);
			// if($bid<$CONFIG['MINIMUM_BID']){
          	 	// $bid = $CONFIG['MINIMUM_BID'];
          	// }
  			// $keywords[$i]['bid'] = $bid;
  			// $keywords[$i]['budget'] = $budget;
  			// $keywords[$i]['total'] = $budget*$hari;
  			// $keywords[$i]['max_cpc'] = $cpc;
  			// array_push($picks,$keywords[$i]['keyword']);
  		// }
  		//old detil
		
  		$oldpicks = json_decode(base64_decode($_SESSION['keypicks']));
  		
  		$bids = json_encode($keywords);
  		$_SESSION['adsetskeys'] = base64_encode($bids);
  		$_SESSION['keypicks'] = base64_encode(json_encode(array($oldpicks[0],$picks)));
		
  		$hari = $_POST['hari'];
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['hari'] = $hari;
  		}
  		$son = json_encode($keywords);
		//print_r($son);
  		
  		$_SESSION['adbids'] = base64_encode($son);
  		//print_r($_SESSION);
  		return $this->FormIklanBaru2_cpcbanner(null,$_SESSION['ad_create_step1']);
  		//return $this->View->toString("SITTI/form_iklan_baru/reg1.html");
  	}
	function FormIklanBaru2($msg=null,$params=null){
    	global $LOCALE,$CONFIG,$APP_PATH;
    	//$this->resetSession();
    	$inventory = new SITTIInventory();
   		$campaign = new SITTICampaign();
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//untuk sementara daftar campaign untuk dropdown pilih campaign kita batasi 30 campaign dulu.
    	$campaign_list = $campaign->getCampaignList($profile['sittiID'],0,30);
    	if(sizeof($campaign_list)==0){
    		//kalau belum ada campaign, paksa user utk buat campaign baru dulu.
    		return $this->View->showMessageError($LOCALE['USER_HAVE_NO_CAMPAIGN'],"beranda.php");
    	}
		for($i=0;$i<sizeof($campaign_list);$i++){
			$campaign_list[$i]['enc_campaign_id'] = urlencode64($campaign_list[$i]['ox_campaign_id']);
    	}
    	//$inventory->open(0);
    	$adCategory = $inventory->getAdCategory();
    	$adGenre = $inventory->getAdGenre();
    	//print mysql_error();
    	//$inventory->close();
    	
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign('adGenre',$adGenre);
    	$this->View->assign("campaign",$campaign_list);
      
      $simulation = new SITTISimulation();
      $this->View->assign('restricted_words', $simulation->getRestrictedWords());
       
    	//params sebelumnya (optional)
    	if($params!=null){
    		//$p = json_decode(base64_decode($_SESSION['ad_create_step1']));
    		$p = json_decode(base64_decode($params));
    		//print_r($params);
    		
    		$this->View->assign("campaign_session", $p->campaign);
    		$this->View->assign("nama_iklan", $p->namaIklan);
    		$this->View->assign("judul_iklan", $p->judul);
    		
    		$this->View->assign("judul_iklan2", $p->judul2);
    		$this->View->assign("judul_iklan3", $p->judul3);
    		
    		$this->View->assign("ad_type", $p->ad_type);
    		$this->View->assign("ads_size",$p->ads_size);
    		$this->View->assign("baris1", $p->baris1);
    		
    		$this->View->assign("urlName", $p->urlName);
			if ($this->error_status!=3){
				$this->View->assign("urlLink", $p->urlLink);
			}
    		$this->View->assign("category", $p->category);
    		$this->View->assign("target_market", $p->target_market);
    		for($i=0;$i<sizeof($p->tcity);$i++){
    			$this->View->assign(str_replace(" ","_",$p->tcity[$i]),"checked='true'");
    		}
    		$this->View->assign("allcity",$p->allcity);
    	}else{
    		//session yang sudah di isi sebelumnya jika SITTI landing page dipilih
    		$this->View->assign("campaign_session", $_POST['campaignSelected']);
    		$this->View->assign("nama_iklan", $_POST['nama']);
    		$this->View->assign("judul_iklan", $_POST['judul']);
    		$this->View->assign("baris1", $_POST['baris1']);
    		//$this->View->assign("baris2", $_POST['baris2']);
    		$this->View->assign("urlName", $_POST['urlName']);
    		$this->View->assign("urlLink", $_POST['urlLink']);
    		$this->View->assign("category", $_POST['category']);
    		$this->View->assign("target_market", $_POST['target_market']);
    		
    	}
        //end session
    	$this->View->assign("landing_selected", $_SESSION['landingSelected']);

        //ambil paget title yang akan ditampilkan sebagai url link iklan
        $userID = $this->Account->getActiveID();
        $this->open();
        $select = $this->fetch("SELECT * FROM layout WHERE id='".$_SESSION['layoutID']."'
                                AND user_id='".$userID."' LIMIT 1");
        $this->close();
        $page_title = $select['page_title'];
        $id = $select['id'];
        $this->View->assign("page_title", $page_title);
        $this->View->assign("id", $id);
        //end
        
        //Error Message - if any
        $this->View->assign("msg",$msg);
     
    	//selected campaign
		if ($this->Request->getPost("c_id")){
			$c_id = $this->Request->getPost("c_id");
		}
		if ($this->Request->getParam("c_id")){
			$c_id = $this->Request->getParam("c_id");
		}
    	$this->View->assign("campaign_id",$c_id);
    	
    	//flag done aja.. kalo true langsung lompat ke halaman konfirmasi
    	$this->View->assign("done",$_SESSION['done']);
		
		// from create iklan template reg3
		
		$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
		$keywords = json_decode(base64_decode($_SESSION['adbids']));
		//print_r($keywords);
		$this->View->assign("n_slots",sizeof($keywords));
		$this->View->assign("list",$keywords);
    	return $this->View->toString("SITTI/form_iklan_baru/reg1.html");
    }
	function FormIklanBaru2_cpcbanner($msg=null,$params=null){
    	global $LOCALE,$CONFIG,$APP_PATH;
    	//$this->resetSession();
    	$inventory = new SITTIInventory();
   		$campaign = new SITTICampaign();
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//untuk sementara daftar campaign untuk dropdown pilih campaign kita batasi 30 campaign dulu.
    	$campaign_list = $campaign->getCampaignList($profile['sittiID'],0,30);
    	if(sizeof($campaign_list)==0){
    		//kalau belum ada campaign, paksa user utk buat campaign baru dulu.
    		return $this->View->showMessageError($LOCALE['USER_HAVE_NO_CAMPAIGN'],"beranda.php");
    	}
		for($i=0;$i<sizeof($campaign_list);$i++){
			$campaign_list[$i]['enc_campaign_id'] = urlencode64($campaign_list[$i]['ox_campaign_id']);
    	}
    	//$inventory->open(0);
    	$adCategory = $inventory->getAdCategory();
    	$adGenre = $inventory->getAdGenre();
    	//print mysql_error();
    	//$inventory->close();
    	
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign('adGenre',$adGenre);
    	$this->View->assign("campaign",$campaign_list);
      
      $simulation = new SITTISimulation();
      $this->View->assign('restricted_words', $simulation->getRestrictedWords());
       
    	//params sebelumnya (optional)
    	if($params!=null){
    		//$p = json_decode(base64_decode($_SESSION['ad_create_step1']));
    		$p = json_decode(base64_decode($params));
    		//print_r($params);
    		
    		$this->View->assign("campaign_session", $p->campaign);
    		$this->View->assign("nama_iklan", $p->namaIklan);
    		$this->View->assign("judul_iklan", $p->judul);
    		
    		$this->View->assign("judul_iklan2", $p->judul2);
    		$this->View->assign("judul_iklan3", $p->judul3);
    		
    		$this->View->assign("ad_type", $p->ad_type);
    		$this->View->assign("ads_size",$p->ads_size);
    		$this->View->assign("baris1", $p->baris1);
    		
    		$this->View->assign("urlName", $p->urlName);
			if ($this->error_status!=3){
				$this->View->assign("urlLink", $p->urlLink);
			}
    		$this->View->assign("category", $p->category);
    		$this->View->assign("target_market", $p->target_market);
    		for($i=0;$i<sizeof($p->tcity);$i++){
    			$this->View->assign(str_replace(" ","_",$p->tcity[$i]),"checked='true'");
    		}
    		$this->View->assign("allcity",$p->allcity);
    	}else{
    		//session yang sudah di isi sebelumnya jika SITTI landing page dipilih
    		$this->View->assign("campaign_session", $_POST['campaignSelected']);
    		$this->View->assign("nama_iklan", $_POST['nama']);
    		$this->View->assign("judul_iklan", $_POST['judul']);
    		$this->View->assign("baris1", $_POST['baris1']);
    		//$this->View->assign("baris2", $_POST['baris2']);
    		$this->View->assign("urlName", $_POST['urlName']);
    		$this->View->assign("urlLink", $_POST['urlLink']);
    		$this->View->assign("category", $_POST['category']);
    		$this->View->assign("target_market", $_POST['target_market']);
    		
    	}
        //end session
    	$this->View->assign("landing_selected", $_SESSION['landingSelected']);

        //ambil paget title yang akan ditampilkan sebagai url link iklan
        $userID = $this->Account->getActiveID();
        $this->open();
        $select = $this->fetch("SELECT * FROM layout WHERE id='".$_SESSION['layoutID']."'
                                AND user_id='".$userID."' LIMIT 1");
        $this->close();
        $page_title = $select['page_title'];
        $id = $select['id'];
        $this->View->assign("page_title", $page_title);
        $this->View->assign("id", $id);
        //end
        
        //Error Message - if any
        $this->View->assign("msg",$msg);
     
    	//selected campaign
		if ($this->Request->getPost("c_id")){
			$c_id = $this->Request->getPost("c_id");
		}
		if ($this->Request->getParam("c_id")){
			$c_id = $this->Request->getParam("c_id");
		}
    	$this->View->assign("campaign_id",$c_id);
    	
    	//flag done aja.. kalo true langsung lompat ke halaman konfirmasi
    	$this->View->assign("done",$_SESSION['done']);
		
		// from create iklan template reg3
		
		$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
		$keywords = json_decode(base64_decode($_SESSION['adbids']));
		//print_r($keywords);
		$this->View->assign("n_slots",sizeof($keywords));
		$this->View->assign("list",$keywords);
    	return $this->View->toString("SITTI/form_iklan_baru/reg1_cpcbanner.html");
    }
  /**
   * fungsi cari kata baru untuk halaman uji sitti
   * @param $txt
   */
	function search2($txt){
		global $CONFIG,$LOCALE;
		$max_n = 100;
		$txt = strtolower($txt);
		$c = $this->Request->getParam("c");
		if(preg_match("/([0-9\'\-\"\_])+/",$txt)){
			return $LOCALE['KEYWORD_SUGGESTION_NUMERIC_ERROR'];
		}
		$arr = explode(" ",trim($txt));
		$strKey="";
		$n_main = sizeof($arr);
		for($i=0;$i<$n_main;$i++){
			if($i!=0){
				$strKey.=",";
			}
			$strKey.="'".$arr[$i]."'";
		}
		$start = $this->Request->getParam('start');
		if($start==null){
			$start = 0;
		}
		if($start<$n_main){
			$total = $max_n-$start-$n_main;
		}else if($start>$n_main){
			$total = $max_n;
		}else{
			$total = 0 ;
		}
		//print $total;
		//$total = $max_n;
		
		$sql1 = $this->stmt->getSuggestion2($strKey,$start,$total);
	
		$this->open(1);
		$suggested = $this->fetch($sql1,1);
		
		//print mysql_error();
		$n_suggest = sizeof($suggested);
		$suggested_keys = "";
		for($i=0;$i<$n_suggest;$i++){
			if($i!=0){
				$suggested_keys.=",";
			}
			$suggested_keys.="'".$suggested[$i]['kata2']."'";
			
			
		}
		$sql3 = "SELECT keyword, avg_cpc, jum_imp, jum_hit, ctr, jum_guna, 1 as is_main 
    FROM db_web3.sitti_keywords_simulasi
    WHERE keyword IN (".$strKey.")
    ORDER BY ctr DESC";
		
		if($start<$n_main){
			$main_keys = $this->fetch($sql3,1);
		}
		//$suggested_keys = $strKey.",".$suggested_keys;
		//query data keyword summary berdasarkan list dari suggested keywords
		$sql3 = "SELECT keyword, avg_cpc, jum_imp, jum_hit, ctr, jum_guna 
    FROM db_web3.sitti_keywords_simulasi
    WHERE keyword IN (".$suggested_keys.")
    ORDER BY ctr DESC";
		//print $sql3;
		$list = $this->fetch($sql3,1);
		if(is_array($main_keys)){
			$list = array_merge($main_keys,$list);
		}
    //save new phrases from user input
		$txt = mysql_escape_string(stripslashes(strip_tags($txt)));
    	$ip = getRealIP();
    	//$this->query($this->stmt->savePhraseQuery($txt, $ip,''));
    	$sql = "SELECT jum_guna FROM db_web3.sitti_keywords_simulasi ORDER BY jum_guna DESC LIMIT 1";
    	$top = $this->fetch($sql);
		$this->close();
		$n_list = sizeof($list);
		$empty_keys = array();
		for($i=0;$i<$n_suggest;$i++){
			$flag = false;
			for($j=0;$j<$n_list;$j++){
				if($suggested[$i]['kata2']==$list[$j]['keyword']){
					$flag = true;
					break;
				}
			}
			if($flag==false){
				$empty_keys[sizeof($empty_keys)] = array("keyword"=>$suggested[$i]['kata2'],
														 "jum_imp"=>0,"avg_cpc"=>0,"jum_guna"=>0);
			}	
		}
		$list = array_merge($list,$empty_keys);
		$n_list = sizeof($list);
		for($i=0;$i<$n_list;$i++){
				$list[$i]['index'] = ($c*40)+$i;
				$list[$i]['jum_imp'] = round(floor($list[$i]['jum_imp'] / 100)*100,0);
				$list[$i]['avg_cpc'] = round(floor($list[$i]['avg_cpc'] / 10)*10,0);
				$list[$i]['popularity'] = ceil(($list[$i]['jum_guna']/$top['jum_guna'])*100);
				///print $list[$i]['popularity']."<br/>";
				//$list[$i]['keyword'] = $rs[$i]['kata2'];
		}
		$this->View->assign("list",$list);
        $this->View->assign("PAGE",$start);
		return $this->View->toString("SITTI/uji_keywords_sim.html");
	}
  /**
   * fungsi mencari saran frase.
   */
  function searchPhrase($txt){
    global $CONFIG;
    $arr = explode(" ",trim($txt));
    $strKey="";
    for($i=0;$i<sizeof($arr);$i++){
      if($i!=0){
        $strKey.=",";
      }
      $strKey.="'".$arr[$i]."'";
      
    }
    $sql1 = $this->stmt->getPhraseSuggestion($strKey);
    $this->open(1);
    $list = $this->fetch($sql1,1);
    $this->close();
    $n = sizeof($list);
   	for($i=0;$i<$n;$i++){
   		$list[$i]['combo_encrypted'] = urlencode64($list[$i]['kata1']."_".$list[$i]['kata2']);
   	}
    $this->View->assign("list",$list);
    return $this->View->toString("SITTI/form_iklan/daftar_phrase.html");
  }
	function search($txt,$type=1){
		global $CONFIG;
		//query semua
		
		if(sizeof(explode(" ",$txt))>1){
			$arr = explode(" ",$txt);
			$txt = trim($arr[0]." ".$arr[1]);
			//lebih dari 1 kata
			$sql1 = $this->stmt->getRelatedKeywordOverall2($txt);
			//print_r($sql1);
			$sql2 = $this->stmt->getRelatedWebKeyword2($txt);
		}else{
			//1 kata
			$sql1 = $this->stmt->getRelatedKeywordOverall($txt);
			$sql2 = $this->stmt->getRelatedWebKeyword($txt);
		}
		
		  $this->open(1);
		if($type==1){
			$rs = $this->fetch($sql1,1);
			
		}else{
			$rs = $this->fetch($sql2,1);
			print mysql_error();
		}
		$this->close();
		//print_r($rs);
		for($i=0;$i<sizeof($rs);$i++){
				$list[$i] = $rs[$i]['kata1'];
			}
			
		if(sizeof($list)>0){
			$merge[0] = $txt;
			$list = array_merge($merge,$list);
		}
		$this->View->assign("list",$list);
		
		//return $this->View->toString("SITTI/uji_keywords.html");
	}
	
	function searchAdvanced($txt,$type=1){
		global $CONFIG;
		  $this->open(1);
		  //2 kata
		  if(sizeof(explode(" ",$txt))>1){
		  	$arr = explode(" ",$txt);
		  	$txt = trim($arr[0]." ".$arr[1]);
		  	if($this->Request->getParam("n")=="1"){
		  		$this->SearchSITTIDataAdvanced2($txt,$type);
		  	}else{
		  		$this->SearchSocialAdvanced2($txt,$type);
		  	}
		  	
		  }else{
		  	//1 kata
		  	if($this->Request->getParam("n")=="1"){
		  		$this->SearchSITTIDataAdvanced($txt,$type);
		  	}else{
		  		$this->SearchSocialAdvanced($txt,$type);
		  	}
		  }
		return $this->View->toString("SITTI/uji_keywords.html");
	}
	function SearchSITTIDataAdvanced($txt,$type){
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	function SearchSocialAdvanced($txt,$type){
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	
	function SearchSITTIDataAdvanced2($txt,$type){
		//kita batasi jadi 2 keyword dulu.
		$arr = explode(" ",$txt);
		$txt = trim($arr[0]." ".$arr[1]);
		
		//-->
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		//print_r($rs);
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	function SearchSocialAdvanced2($txt,$type){
		$arr = explode(" ",$txt);
		$txt = trim($arr[0]." ".$arr[1]);
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
    function show($editmode=false){
		if($editmode){
			$json = base64_decode($_SESSION['keypicks']);
    		$this->View->assign("jsondata",$json);
    	}
		if ($_SESSION['keypicks']){
			$obj = json_decode(base64_decode($_SESSION['keypicks']));
			$n = sizeof($obj[1]);
			$q = "";
			for($i=0;$i<$n;$i++){
				if ($i!=0){
					$q .= "\n";
				}
				$q .= $obj[1][$i];
			}
			$this->View->assign("q",$q);
		}
		$c_id = "";
		if ($this->Request->getParam("c_id")){
			$c_id = $this->Request->getParam("c_id");
		}
		$this->View->assign("c_id",$c_id);
    	return $this->View->toString("SITTI/form_iklan_baru/uji.html");
    }
	function show_cpcbanner(){
		return $this->View->toString("SITTI/form_iklan_baru/uji_cpcbanner.html");
	}
    function showAdvanced(){
    	return $this->View->toString("SITTI/uji_advanced.html");
    }	
    function SimulationPage(){
    	global $CONFIG;
    	$req = $this->Request;
    	$q = preg_replace("/(\r\n)+|(\n|\r)+/", ";", $_POST['q']);
    	$c = $req->getPost("c");
    	$hari = $req->getPost("hari");
    	
    	$main_key = $_POST['keywords'];
    	$json = json_encode(array($hari,$main_key));
    	$_SESSION['keypicks']=base64_encode($json);
    	$n_len = sizeof($main_key);
    	if($n_len>100){
    		$n_len = 100;
    	}
    	for($i=0;$i<$n_len;$i++){
    		$list[$i]['no'] = $i+1;
    		$list[$i]['list'][0] = strip_tags(mysql_escape_string($main_key[$i]));
    		
    			$this->open(0);
    			$sql = $this->stmt->getSuggestion2("'".$list[$i]['list'][0]."'",0,40);
    			
    			$foo = $this->fetch($sql,1);
    			$this->close();
    			for($j=1;$j<=40;$j++){
    				if(strlen($foo[$j]['kata2'])>1){
    					$list[$i]['list'][$j] = $foo[$j]['kata2'];
    				}  
    			}
    	}
    	$this->View->assign("main_key",$list);
    	$this->View->assign("hari",$hari);
    	$this->View->assign("n_slots",$n_len);
    	$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
    	return $this->View->toString("SITTI/form_iklan_baru/simulasi.html");
    }	
    function getAvgCPC($keyword){
    	$param['avg_cpc'] = 200;
    	return json_encode($param);
    }
    function getKeywordData($keyword){
    	$this->open(0);
    	
    	$sql = "SELECT keyword, avg_cpc, jum_imp as imp, jum_hit as hit, ctr, jum_guna, max_bid,min_bid 
				FROM db_web3.sitti_keywords_simulasi 
				WHERE keyword IN ('".mysql_escape_string($keyword)."')";
    	
    	$rs2 = $this->fetch($sql);
    	//print mysql_error();
    	$this->close();
    	if($rs2['avg_cpc']==null){
    		$rs2['avg_cpc'] = 0;
    	}
    	if($rs2['imp']==null){
    		$rs2['imp'] = 0;
    	}
    	if($rs2['hit']==null){
    		$rs2['hit'] = 0;
    	}  
    	if($rs2['ctr']==null){
    		$rs2['ctr'] = 0;
    	}
    	if($rs2['max_bid']==null){
    		$rs2['max_bid'] = 0;
    	}
    	if($rs2['min_bid']==null){
    		$rs2['min_bid'] = 0;
    	}
    	$param['avg_cpc'] = round(floor($rs2['avg_cpc']/10)*10,0);
    	$param['imp'] = floor($rs2['imp']/100)*100;
    	$param['click'] = $rs2['hit'];
    	$param['ctr'] = round($rs2['ctr'],2);
    	$param['max_bid'] = $rs2['max_bid'];
    	$param['min_bid'] = $rs2['min_bid'];
    	
    	if($param['ctr']==0){
    		$param['ctr'] = "0";
    	}else{
    		$param['ctr'] = $param['ctr'];
    	}
    	//print_r($param);
    	return json_encode($param);
    }

    function upload_form($banner_type)
    {
      global $LOCALE,$CONFIG,$APP_PATH;
      if (isset($banner_type))
      {
        $ad_type = $banner_type == 'flash' ? 3 : 2;
        // Form upload Banner Image / Flash
        include_once $APP_PATH."FileUploader/FileUploader.php";
        $fileID = date("YmdHis").floor(round(rand(0,999)));
        $total_uploader = 10;

        $uploader = new FileUploader($fileID,$total_uploader,'SITTI/form_iklan_baru/uploader.html');
        $uploader->setLabels(array('Banner Ukuran 300 X 250',
                      'Banner Ukuran 336 X 280',
                      'Banner Ukuran 728 X 90',
                      'Banner Ukuran 160 X 600',
                      'Banner Ukuran 610 X 60',
                      'Banner Ukuran 300 X 160',
                      'Banner Ukuran 940 X 70',
                      'Banner Ukuran 520 X 70',
                      'Banner Ukuran 468 X 60',
                      'Banner Ukuran 250 X 250'
                      ));
        
        $uploader->assign("ad_type", $ad_type);
        $uploader->assign("banner_url",$CONFIG['BANNER_URL']);
        $uploader->assign("fileID",$fileID);
        $uploader->assign("total_assets",$total_uploader);
        echo $uploader;
        die();
      }
    }
}
?>
