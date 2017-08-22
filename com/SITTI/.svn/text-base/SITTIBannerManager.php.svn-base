<?php

include_once "SITTIActionLog.php";

class SITTIBannerManager extends SQLData
{
	private $request;
	private $view;
	private $Account;
	function SITTIBannerManager($req,$account)
	{
		$this->Account = $account;
		$this->request = $req;
		$this->Request = $this->request;
		$this->view = new BasicView();
		$this->View = $this->view;
		$this->ActionLog = new SITTIActionLog();
	}

	public function addBannerPage()
	{
		if ($this->request->getPost('next') == '2' || ($this->request->getPost('back') && $this->request->getPost('prev') == '2'))
		{
			if ($this->request->getPost('hitungBiayaKampanye')) 
			{
				$post_categories = $_POST['categories'];
				
				$category_keyword = new CategoryKeywordModel();
				$all_categories = $category_keyword->getCategories();
				
				$categories = array();
				$total_impresi_harian = $total_budget_harian = $total_impresi = $total_biaya = 0;
				
				foreach ($post_categories as $index => $category) 
				{
					$categories[$index]['category'] = $category;
					$category_keyword->setCategory($category);
					$categories[$index]['data'] = $category_keyword->getData();
					
					//$categories[$index]['data']['impresi'] = intval(0.25 * ( intval($categories[$index]['data']['jum_imp']) * intval($categories[$index]['data']['jum_keyword']) ));
					$categories[$index]['data']['impresi'] = intval($categories[$index]['data']['jum_imp']);
					$categories[$index]['data']['budget'] = intval($categories[$index]['data']['impresi']) * (intval($categories[$index]['data']['cpm']) / 1000);
					 
					$total_impresi += intval($categories[$index]['data']['impresi']);
					$total_biaya += intval($categories[$index]['data']['budget']);
				}
				
				$create_banner_data['total_impresi'] = $total_impresi;
				$this->view->assign('total_impresi', $total_impresi);
				$create_banner_data['total_biaya'] = $total_biaya;
				$this->view->assign('total_biaya', $total_biaya);

				//$create_banner_data['all_categories'] = $all_categories;
				//$this->view->assign('all_categories', $all_categories);
				$create_banner_data['categories'] = $categories;
				$this->view->assign('categories', $categories);

				$create_banner_data['cities'] = $_POST['tcity'];

        $this->Account->open(0);
				$saldo = $this->Account->getSaldo($_SESSION['sittiID']);
				$user_saldo = $saldo['budget'];
				$this->Account->close();

				$create_banner_data['user_saldo'] = $user_saldo;
				$this->view->assign('user_saldo', $user_saldo);
        
        $this->view->assign('current_date', date('Y-m-d'));
				
				$_SESSION['create_banner_data'] = serialize($create_banner_data);
			}

			return $this->view->toString("SITTI/ads/create_banner_step2.html");
		}
		elseif ($this->request->getPost('next') == '3')
		{
			return $this->step3();
		}
		elseif ($this->request->getPost('do') == 'upload_assets')
		{
			return $this->upload_assets(true);
		}
		elseif ($this->request->getPost('do') == 'confirm')
		{
			return $this->confirm(true);
		}
		elseif($this->request->getPost('do')=="save")
		{
			return $this->SaveAdvertisement();
		}
		else
		{
			$category_keyword = new CategoryKeywordModel();
      $categories = $category_keyword->getCategories();
      $this->view->assign('categories', $categories);
      $this->view->assign('categories_half', intval(count($categories) / 2));
      return $this->view->toString("SITTI/ads/create_banner_step1.html");
		}
	}
	
	function step3($msg = false)
	{
		if ($this->request->getPost('uploadMateri')) 
		{
			global $LOCALE;
			$create_banner_data = unserialize($_SESSION['create_banner_data']);
		
			$total_biaya = $this->request->getPost('total_biaya');
			$tgl_awal = $this->request->getPost('tglAwalKampanye');
			$tgl_akhir = $this->request->getPost('tglAkhirKampanye');
			$interval = $this->request->getPost('interval');
			
			$categories = $create_banner_data['categories'];
			foreach ($categories as $idx => $category)
			{
				$categories[$idx]['data']['impresi'] = $this->request->getPost('imp_total_' . $idx);
				$categories[$idx]['data']['cpm'] = $this->request->getPost('cpm_' . $idx);
			}
			
			$create_banner_data['total_biaya'] = intval($total_biaya);
			$create_banner_data['tgl_awal'] = $tgl_awal;
			$create_banner_data['tgl_akhir'] = $tgl_akhir;
			$create_banner_data['interval'] = $interval;
			$create_banner_data['categories'] = $categories;
			
			$_SESSION['create_banner_data'] = serialize($create_banner_data);	
		}
		elseif ($msg)
		{
			$this->View->assign("msg",$msg);
		}
			
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
		//$inventory->open(0);
		$adCategory = $inventory->getAdCategory();
		$adGenre = $inventory->getAdGenre();
		//print mysql_error();
		//$inventory->close();

		/*$create_banner_data = unserialize($_SESSION['create_banner_data']);
		$city = $create_banner_data['city'];

		if (is_array($city))
		{
		  $this->View->assign("cities",json_encode($city));
		}
		else
		{
		  $this->View->assign("allcity",$city);  
		}*/  

		$this->View->assign("adCategory",$adCategory);
		$this->View->assign("adGenre",$adGenre);
		$this->View->assign("campaign",$campaign_list);

	    return $this->view->toString("SITTI/ads/create_banner_step3.html");
		 
    }
	function upload_assets($flag=TRUE){
		
  		global $LOCALE,$CONFIG,$APP_PATH;
  		include_once $APP_PATH."FileUploader/FileUploader.php";
  		$fileID = date("YmdHis").floor(round(rand(0,999)));
  		$total_uploader = 10;
  		
  		$uploader = new FileUploader($fileID,$total_uploader,'SITTI/ads/uploader.html');
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
  		//--> banner setup
  		
  		//-->
  		if($flag){
        	$p['namaIklan'] = $this->Request->getPost("nama");
      		$p['judul'] = $this->Request->getPost("judul");
      		
      	
      		$p['ad_type'] = $this->Request->getPost("ad_type");
      		if ($p['ad_type'] == '2')
      		{
      			$p['img_ext'] = $this->Request->getPost("image_ext");
      		}
      		
      		
      		$p['urlLink'] = $this->Request->getPost("urlLink");
      		$p['target_web'] = $this->Request->getPost("target_web");
      		$p['category'] = $this->Request->getPost("category");
      		$p['target_market'] = $this->Request->getPost("target_market");
      		$p['campaign'] = $this->Request->getPost("campaign");
      		$p['allcity'] = $this->Request->getPost("allcity");
 			
 			
  		}else{
  			
  			$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
  			$p['namaIklan'] = $iklan->namaIklan;
  			$p['judul'] = $iklan->judul;
      		$p['ad_type'] = $iklan->ad_type;

      		if ($p['ad_type'] == '2')
      		{
      			$p['img_ext'] = $this->Request->getPost("image_ext");
      		}

      		$p['urlLink'] = $iklan->urlLink;
      		$p['target_web'] = $iklan->target_web;
      		$p['category'] = $iklan->category;
      		$p['target_market'] = $iklan->target_market;
      		$p['campaign'] = $iklan->campaign;
  			$p['allcity'] = $iklan->allcity;
  			
      		$this->View->assign("msg",$msg);
      		
  		}
  		$json = json_encode($p);
        $_SESSION['ad_create_step1'] = base64_encode($json);
  		if($this->isForm2Valid()||!$flag){
  			$this->View->assign("fileID",$fileID);
  			$this->View->assign("ad_type",$p['ad_type']);
  			if ($p['ad_type'] == '2')
      		{
      			$this->View->assign("img_ext",$p['img_ext']);
      		}

	  		$this->View->assign("total_assets",$total_uploader);
	  		
	  		$uploader->assign("ad_type",$p['ad_type']);

	  		
	  		$this->View->assign("uploader",$uploader);
	  		$this->View->assign("banner_url",$CONFIG['BANNER_URL']);
	  		//var_dump( base64_decode(($_SESSION['ad_create_step1'])));
	  		return $this->view->toString("SITTI/ads/create_banner_step_upload.html");
  		}else{
  			switch($this->error_status){
             	case 1:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->step3($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 2:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->step3($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 3:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['URL_INVALID'];
             		 return $this->step3($msg, $_SESSION['ad_create_step1']);
             	break;
             	case 4:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['TEXT_INVALID'];
             		 return $this->step3($msg, $_SESSION['ad_create_step1']);
             	break;
           	}
  			return $this->step3($msg,$_SESSION['ad_create_step1']);
  			
  		}
  		
  	}
	function Confirm($flag=TRUE){
		
  		global $LOCALE,$CONFIG;
  		
  		//--> banner setup
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
  		//-->
  		
			$fileID = $this->Request->getPost("fileID");
  			$_SESSION['file_token'] = urlencode64($fileID);
  		
  			$adstep1 = json_decode(base64_decode( $_SESSION['ad_create_step1']));
  			$p['judul'] = strip_tags($adstep1->judul);
  			$p['ad_type'] = strip_tags($adstep1->ad_type);
  			$p['urlLink'] = strip_tags($adstep1->urlLink);
  			$p['target_web'] = strip_tags($adstep1->target_web);
  			$p['category'] = strip_tags($adstep1->category);
  			$p['target_market'] = strip_tags($adstep1->target_market);
  			$p['campaign'] = strip_tags($adstep1->campaign);
  			$p['allcity'] = strip_tags($adstep1->allcity);
  			$p['BANNER_URL'] = $CONFIG['BANNER_URL'].$fileID;
	        $create_banner_data = unserialize($_SESSION['create_banner_data']);
	        $p['img_ext'] = $this->Request->getPost("img_ext");
			$campaign = new SITTICampaign();
			
			$campaign->open(0);
			$cmp = $campaign->getCampaignByOwner($_SESSION['sittiID'],intval($p['campaign']));
			$campaign->close();
			
	        //$tgl_awal = $create_banner_data['tgl_awal']->format("j F Y");
	        //$tgl_akhir = $create_banner_data['tgl_akhir']->format('j F Y');
	        $tgl_awal = date('j F Y', strtotime($create_banner_data['tgl_awal']));
	        $tgl_akhir = date('j F Y', strtotime($create_banner_data['tgl_akhir']));
	        $interval = $create_banner_data['interval'];
	        $total_impresi = $create_banner_data['total_impresi'];
	        $total_biaya = $create_banner_data['total_biaya'];
  			
	        //$create_banner_data['city'] = $this->request->getPost('allcity') ? $this->request->getPost('allcity') : $_POST['tcity'];
	        //$_SESSION['create_banner_data'] = serialize($create_banner_data);
			
	        //load all banner previews by checking for asset files existance
	        if($p['ad_type']==3){
	        	$ext = ".swf";
	        }else{
	        	$ext = ".jpg";
	        }
	        $banners = array();
	        for($i=1;$i<=10;$i++){
	        	//print $CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$ext."<br/>";
	        	$asset_file="";
	        	if ($ext == '.swf')
	        	{
	        		if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$ext)){
	        			$asset_file=$CONFIG['BANNER_URL'].$fileID."_".$i.$ext;
	        		}	
	        	}
	        	elseif ($ext == '.jpg')
	        	{
	        		if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$ext)){
	        			$asset_file=$CONFIG['BANNER_URL'].$fileID."_".$i.$ext;
	        		} else {
	        			$ext = '.gif';
	        			if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$ext)){
	        				$asset_file=$CONFIG['BANNER_URL'].$fileID."_".$i.$ext;
	        			}		
	        		}
	        	}
	        	
	        	array_push($banners,array("banner"=>$banner[$i],"asset_file"=>$asset_file));
	        }
	      //  var_dump($banners);
	        //-->
	        $this->View->assign("tgl_awal",$tgl_awal);
	        $this->View->assign("tgl_akhir",$tgl_akhir);
	        $this->View->assign("interval",$interval);
	        $this->View->assign("total_impresi",intval($total_impresi) * intval($interval));
	        $this->View->assign("total_biaya",$total_biaya);
			//$this->View->assign("banner",$banner[$p['ads_size']]);
	        $this->View->assign("n_budget",$n_budget);
			$this->View->assign("n_total",$n_total);
			$this->View->assign("keywords",$keywords);
			$this->View->assign("rs1",$p);
			$this->View->assign("campaign",$cmp);
			$this->View->assign("banners",$banners);
			$this->View->assign("img_ext", $this->Request->getPost("img_ext"));
			//var_dump($_SESSION);
  
  			
  		return $this->view->toString("SITTI/ads/create_banner_step4.html");
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
  		global $CONFIG;
  		$salt = rand(1000,9999);
  		//$name = date("YmdHis").$salt.".swf";
  		if(move_uploaded_file($_FILES['fla']['tmp_name'],$CONFIG['BANNER_ASSET_PATH'].$name)){
  			return $name;
  		}else{
  			//do something here
  			return null;	
  		}
  	}
	function SaveAdvertisement(){
		global $CONFIG, $LOCALE;
		$inventory = new SITTIInventory();
    	$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
    	$create_banner_data = unserialize($_SESSION['create_banner_data']);
    	$fileID = urldecode64($_SESSION['file_token']);
    	
    	//tambahkan data ke inventory
		$inventory->open();
    	if($iklan->ad_type=="2"){
    		$rs = $inventory->createImageAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan->namaIklan),
    								mysql_escape_string($iklan->judul),
    								mysql_escape_string($iklan->category),
    								mysql_escape_string($iklan->ads_size),
    								mysql_escape_string($fileID),
    								mysql_escape_string($iklan->urlLink),
    								mysql_escape_string($iklan->campaign),
    								mysql_escape_string($iklan->target_market),
                    mysql_escape_string($create_banner_data['tgl_awal']),
                    mysql_escape_string($create_banner_data['tgl_akhir'])
    								);
    		if($this->Request->getPost("img_ext")){
    			$ext = ".". $this->Request->getPost("img_ext");
    		}else{
    			$ext=".jpg";
    		}
    		
    		//print mysql_error();
    	}else if($iklan->ad_type=="3"){
    		$rs = $inventory->createFlashAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan->namaIklan),
    								mysql_escape_string($iklan->judul),
    								mysql_escape_string($iklan->category),
    								mysql_escape_string($iklan->ads_size),
    								mysql_escape_string($fileID),
    								mysql_escape_string($iklan->urlLink),
    								mysql_escape_string($iklan->campaign),
    								mysql_escape_string($iklan->target_market),
                    mysql_escape_string($create_banner_data['tgl_awal']),
                    mysql_escape_string($create_banner_data['tgl_akhir'])
    								);
    		$ext=".swf";
    	}else{}
    	
    	$iklan_id = intval($inventory->last_insert_id);
    	
    	
    	//setup asset lookup.
    	$inventory->setup_asset_lookup($iklan_id,$fileID,$ext);
    	//-->
      //Geo location restrictions
      $create_banner_data = unserialize($_SESSION['create_banner_data']);
      $cities = $create_banner_data['cities'];

      $locations = array();
      if( ! is_array($cities)) {
  			$locations = array(array("kota"=>"ALL","priority"=>"0"));
  		} else {
  			$locs = $cities;
  			foreach ($locs as $city) {
           array_push($locations,array("kota"=>$city,"priority"=>"5"));
        }
  		}
      
    //$locations = array(array("kota"=>"ALL","priority"=>"0"));

		$inventory->addBannerLocation($iklan_id,$locations);
		//---->
    	$inventory->close();
    	
		//print_r($_SESSION['ad_create_step3']);
      
    	if($rs){
    		//kalau berhasil, tambahkan daftar keyword iklan
    		
    		//$list = $_SESSION['ad_create_step3'];
   			//$j = json_decode(base64_decode($_SESSION['adbids']));
  			//$list = $this->keywordJsonToArray($j);
  			//$n = sizeof($list);
    		
    		$create_banner_data = unserialize($_SESSION['create_banner_data']);
    		$categories = $create_banner_data['categories'];
    		
        // mencatat banner kategori
        $strCategory = "";
        $ncat=0;
       
    		foreach ($categories as $idx => $row)
        {
          $inventory->open();
          $bid = $row['data']['cpm'];
          $imp = $row['data']['impresi'];
          if(strlen($row['category'])>0){
          	if($ncat>0){
          		$strCategory.=", ";
          	}
          	$strCategory.=strip_tags($row['category']);
          	$ncat++;
          }
          $inventory->addBannerCategory($iklan_id, mysql_escape_string($row['category']), mysql_escape_string($bid), mysql_escape_string($imp));
          $inventory->close();

          /*$category_keyword = new CategoryKeywordModel($row['category']);
        	$keywords = $category_keyword->getKeywords();
        	$category_keyword_data = $category_keyword->getData();
        	
        	$cpm = $categories[$idx]['data']['cpm'];
        	$imp = floatval($categories[$idx]['data']['impresi']) / intval($category_keyword_data['jum_keyword']);
        	
        	foreach ($keywords as $index => $row)
        	{
        		$keyword = $row['keyword'];
        		
        		$inventory->open();	
           	$inventory->addBannerKeyword($iklan_id, mysql_escape_string($keyword), mysql_escape_string($cpm), mysql_escape_string($imp));     				
            $inventory->close();
        	}*/
        }
    		//exit();
    		
    		/*for($i=0;$i<$n;$i++){
    		
          		if($list[$i]['bid']<$CONFIG['MINIMUM_BID']){
          	 		$list[$i]['bid'] = $CONFIG['MINIMUM_BID'];
          		}
         		if(strlen($list[$i]['name'])>0){
         			$inventory->open();	
         			if($iklan->ad_type!="1"){
					    $inventory->addBannerKeyword($iklan_id,mysql_escape_string($list[$i]['name']),
    			                                      mysql_escape_string($list[$i]['bid']),
    			                                      mysql_escape_string($list[$i]['budget']),
                                                	  mysql_escape_string($list[$i]['total']));     				
         			}else{
    					$inventory->addKeyword($iklan_id,mysql_escape_string($list[$i]['name']),
    			                                      mysql_escape_string($list[$i]['bid']),
    			                                      mysql_escape_string($list[$i]['budget']),
                                                mysql_escape_string($list[$i]['total']));
         			}
					
                    $inventory->close();
         		}
    		}*/
    		
    		$msg = $LOCALE['ADS_SAVE_SUCCESS'];
    		
    		$this->Account->open(0);
    		$profile = $this->Account->getProfile();
    		$this->Account->close();
			// action log create banner (141)
			$this->ActionLog->actionLog(141,$profile['sittiID'],$iklan_id);
    		//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan->namaIklan.") Berhasil");
    		$smtp->setRecipient($profile['email']);
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
    		$smtp->send();
    		//-->
     		//$this->resetSession();
    	}else{
    		$msg = "Iklan anda tidak berhasil disimpan.";
    	}
    	//$this->resetSession();
    	
    	if($rs){
    		//insert data kosong ke table reporting
    		$sql  ="INSERT INTO db_report.tbl_performa_banner_total
				    (advertiser_id, banner_id, kategori, STATUS, last_update)
				    VALUES ('".$_SESSION['sittiID']."', ".$iklan_id.", 
				    '".mysql_escape_string($strCategory)."', '0', NOW())";
			
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
    	}
    	return $this->View->showMessage($msg,"beranda.php");
    }
/**
     *  method untuk mengecek isian dari form detail iklan
     * @return boolean
     */
    
    function isForm2Valid(){
      $this->error_status=0;
      $namaIklan = mysql_escape_string(stripslashes($this->Request->getPost("nama")));
      $judul = $this->Request->getPost("judul");
      $judul2 = $this->Request->getPost("judul2");
      $judul3 = $this->Request->getPost("judul3");
      $ads_size = $this->Request->getPost('ads_size');
      $ad_type = $this->Request->getPost('ad_type');
      
      $baris1 = $this->Request->getPost("baris1");
      $baris2 = $this->Request->getPost("baris2");
      //$urlName = $this->Request->getPost("urlName");
      $urlLink = $this->Request->getPost("urlLink");
      $target_web = $this->Request->getPost("target_web");
      
      //a pattern to make sure no karakter aneh2...
      $pattern = "/([^A-Za-z0-9\"\'\!\@\#\$\%\^\&\*\(\)\.\,\_\-\=\+\?\:\;\"\'\/\"\ ]+)/";
      
      //take out baris2 ..
      if($ad_type=="1"&&$target_web!="sitti"&&(!$namaIklan || strlen($judul)==0 || strlen($baris1)==0 || (/*strlen($urlName)==0   
      						||*/ strlen($urlLink)==0 || $urlLink=='http://'))){
        $this->error_status=1;
        return false;

      }else if($ad_type=="1"&&$target_web=="sitti"&&(strlen($namaIklan)==0 || strlen($judul)==0 || strlen($baris1)==0)){
        $this->error_status=2;
        return false;
     }/*else if($target_web!="sitti"&&!@isValidUrl($urlLink)){
        $this->error_status=3;
       return false;
      //-->
      }*/else if(preg_match_all($pattern,stripslashes($namaIklan),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($ad_type=="1"&&preg_match_all($pattern,stripslashes($judul),$m)>0){
      	$this->error_status=4;
      	return false;
      }else if($ad_type=="2" && (strlen($judul)==0 || preg_match_all($pattern,stripslashes($judul),$m)>0)){
      	$this->error_status=1;
      	return false;
      }else if($ad_type=="3" && (strlen($judul)==0 || preg_match_all($pattern,stripslashes($judul),$m)>0)){
      	$this->error_status=1;
      	return false;
      }else if($ad_type=="1"&&preg_match_all($pattern,stripslashes($baris1),$m)>0){
      	$this->error_status=4;
      	return false;
      } elseif ($ad_type == '') {
      	$this->error_status=1;
      	return false;
      }
      else{
         return true;
      }
      
     
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
  			
  			$params[$i]['name'] = $keyword;
  			$params[$i]['bid'] = $bid;
  			$params[$i]['budget'] = $budget;
  			$params[$i]['total'] = $total;
  			$params[$i]['max_cpc'] = $max_cpc;
  		}
  		return $params;
  	}
	function resetSession(){
    	$_SESSION['adbids'] = null;
    	$_SESSION['ad_create_step1'] = null;
    	$_SESSION['adsetskeys'] = null;
    	$_SESSION['keypicks'] = null;
    }

}

?>
