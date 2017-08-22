<?php 
/**
 * SITTIApp
 * base class untuk semua komponen2 SITTI
 * 
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once $APP_PATH."SITTI/Model/SITTITestQuery.php";
include_once "SITTIApp.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIReporting.php";
include_once "SITTIMailer.php";
include_once "Custom_Template/Custom_Template.php"; //angling:02-09-2010
include_once "SITTIActionLog.php";

include_once $ENGINE_PATH."Utility/Paginate.php";


define("HOME", "SITTI/beranda.html");
define("HOME_BANNER", "SITTIBanner/beranda.html");
define("HOME_PPA", "SITTIPPA/beranda.html");
define("HOME_BEGINNER", "SITTI/beranda2.html");
define("BUAT_IKLAN_BARU_STEP1", "SITTI/form_iklan/reg1.html");
define("BUAT_IKLAN_BARU_STEP2", "SITTI/form_iklan/reg2.html");
define("BUAT_IKLAN_BARU_STEP2B", "SITTI/form_iklan/reg2b.html");
define("BUAT_IKLAN_BARU_STEP3", "SITTI/form_iklan/reg3.html");
define("BUAT_IKLAN_BARU_STEP4", "SITTI/form_iklan/reg4.html");
define("DAFTAR_KEYWORD_PILIHAN", "SITTI/form_iklan/daftar_keyword.html");
define("DETAIL_IKLAN", "SITTI/advertiser/detail_iklan.html");
define("FORM_CAMPAIGN_BARU", "SITTI/form_campaign/campaign_baru.html");
define("FORM_EDIT_CAMPAIGN","SITTI/form_campaign/edit_campaign.html");
define("FORM_EDIT_IKLAN1","SITTI/form_edit_iklan/reg1.html");
define("FORM_EDIT_IKLAN1b","SITTI/form_edit_iklan/reg1b.html");
define("FORM_EDIT_IKLAN2","SITTI/form_edit_iklan/reg2.html");
define("FORM_EDIT_IKLAN2_ADVANCED","SITTI/form_edit_iklan/reg2b.html");
define("FORM_EDIT_IKLAN2B","SITTI/form_edit_iklan/reg2c.html");
define("FORM_EDIT_IKLAN2_ADVANCED_B","SITTI/form_edit_iklan/reg2d.html");
define("DAFTAR_KEYWORD_PILIHAN2","SITTI/form_edit_iklan/daftar_keyword.html");
class SITTIAdvertiser extends SITTIApp{
	var $error_status = 0;
    function SITTIAdvertiser($req,$account){
       parent::SITTIApp($req,$account);
	   $this->ActionLog = new SITTIActionLog();
    }
    function getAdvertisers($start,$total){
    	
    	$list = $this->fetch("SELECT * FROM sitti_account a,sitti_account_profile b 
    						 WHERE a.id = b.user_id ORDER BY a.sittiID LIMIT ".$start.",".$total,1);
    	
    	$q = $this->fetch("SELECT COUNT(*) as total FROM sitti_account a,sitti_account_profile b 
    						 WHERE a.id = b.user_id ORDER BY a.sittiID LIMIT 1");
    	$rs['list'] = $list;
    	$rs['total_rows'] = $q['total'];
    	return $rs;
    }

    function showSummary($msg=null,$xcid=null){
    	global $LOCALE, $CONFIG;
    	//pastikan user sudah terdaftar ke openx
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
      	$last_login = $this->Account->advertiserLastLoginTime();
    	$this->Account->close();
    	
      	$this->View->assign("account_name", $profile['name']);
      	$this->View->assign("last_login", $last_login);

    	$billing = new SITTIBilling($req, $this->Account);
    	if($billing->hasBillingProfile($profile['sittiID'])){
    		$this->View->assign("BILLING_READY","1");
    	}
    	
    	$saldo = $this->Account->getSaldo($profile['sittiID']);
    	$user_budget = intval($saldo['budget']);
    	$min_bid = intval($CONFIG['MINIMUM_BID']);
    	
    	if ($user_budget > $min_bid)
    	{
			 $this->View->assign("BUDGET_BIGGER_THAN_MINIMUM_BID","1");
		  }
    	
    	//-->
    	//Inventory
    	//$inventory = new SITTIInventory();
    	//$iklan = $inventory->getAds($this->Account->getActiveID());
    	
		if ($_SESSION['is_ppa']){
			return $this->View->toString(HOME_PPA);
		}
    	//print "don't need it";
    	//Summary Performance Iklan
    	$report = new SITTIReporting($inventory);
    	$campaign = new SITTICampaign();
		if($user_budget==0&&!$_SESSION['is_cpm']){
			$this->View->assign("intro_popup","1");
		}
    	if($campaign->hasCampaign($profile['sittiID'])){
    		
    		//kalau belum ada iklan.. force redirect ke halaman buat iklan.
	    	if(!$this->hasAd($profile['sittiID'])){
	    		//print $user_budget."-->";
				//die();
				
	    		$msg = $LOCALE['MUST_CREATE_AN_AD'];
	    		$this->View->assign("msg",$msg);
    			return $this->View->toString(HOME_BEGINNER);
	    		//return $this->View->showMessage($msg,"buat.php");
	    	}
    	
    		
    		//untuk sementara dibatasi 20 campaign terbaru dulu.
    		
    		//$stats = $report->getCampaignSummary($profile['sittiID'],$campaign->getCampaignList($profile['sittiID'], 0, 20));

    		//$n = sizeof($stats);
    		//for($i=0;$i<$n;$i++){
    		//$stats[$i]['no'] = $i+1;
    		//$stats[$i]['ctr'] = round($stats[$i]['clicks']/$stats[$i]['impressions'],2);
    		//}
    	
    		//$this->View->assign("list",$stats);
    		$this->View->assign("msg",$msg);

			
			
    		//--->
			
			
	        //$this->View->assign("page",$this->Paging->generate($start,$total,$foo['total'],"beranda.php"));
	        if ($_SESSION['is_cpm']) 
	        {
	          return $this->View->toString(HOME_BANNER);
	        }
			if ($xcid!=null){
				$this->View->assign("xcid",$xcid);
			}
        	return $this->View->toString(HOME);       		
    		
    	}else{
    		$this->View->assign("msg",$msg);
    		return $this->View->toString(HOME_BEGINNER);
    	}
    }
    /**
     * 
     * Create OpenX Advertiser Account for current User
     * @param array $profile
     * @deprecated
     */
    function createOXAccount($profile){
    	global $APP_PATH,$OX_CONFIG;
    	//buat campaign id di openx
    	include_once $APP_PATH."kana/SITTI_OX_RPC.php";
    	//SOAP Request Parameters
    	$params['advertiserName'] = $profile['sittiID'];
		$params['contactName'] = $profile['name'];
		$params['emailAddress'] = $profile['email'];
		//-->
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
    	$ox->logon();
    	$ox_adv_id = $ox->registerAsAdvertiser(&$params);
    	
    	$ox->logout();
    	//die($ox_adv_id);
    	
    	return $ox_adv_id;
    }
    function FormIklanBaru($msg=null,$params=null){
    	global $LOCALE;
    	$this->resetSession();
    	$inventory = new SITTIInventory();
    	$campaign = new SITTICampaign();
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//untuk sementara daftar campaign untuk dropdown pilih campaign kita batasi 30 campaign dulu.
    	$campaign_list = $campaign->getCampaignList($profile['sittiID'],0,30);
    	if(sizeof($campaign_list)==0){
    		//kalau belum ada campaign, paksa user utk buat campaign baru dulu.
    		return $this->showSummary($LOCALE['USER_HAVE_NO_CAMPAIGN']);
    	}
    	//$inventory->open(0);
    	$adCategory = $inventory->getAdCategory();
    	//print mysql_error();
    	//$inventory->close();
    	
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign("campaign",$campaign_list);

    	//params sebelumnya (optional)
    	if($params!=null){
    		$this->View->assign("campaign_session", $params['campaign']);
    		$this->View->assign("nama_iklan", $params['nama']);
    		$this->View->assign("judul_iklan", $params['judul']);
    		$this->View->assign("baris1", $params['baris1']);
    		$this->View->assign("baris2", $params['baris2']);
    		$this->View->assign("urlName", $params['urlName']);
    		$this->View->assign("urlLink", $params['urlLink']);
    	}else{
    		//session yang sudah di isi sebelumnya jika SITTI landing page dipilih
    		$this->View->assign("campaign_session", $_SESSION['campaignSelected']);
    		$this->View->assign("nama_iklan", $_SESSION['nama_iklan']);
    		$this->View->assign("judul_iklan", $_SESSION['judulIklan']);
    		$this->View->assign("baris1", $_SESSION['baris1']);
    		$this->View->assign("baris2", $_SESSION['baris2']);
    		
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
    	$this->View->assign("campaign_id",$this->Request->getParam("campaign"));
    	return $this->View->toString(BUAT_IKLAN_BARU_STEP1);
    }
    function resetSession(){
    	$_SESSION['ad_create_step1'] = "";
    	$_SESSION['ad_create_step2'] = "";
    	$_SESSION['ad_keyword_list'] = "";
    }


    function resetSessionLandingPage(){
        //reset session create iklan
        $_SESSION['nama_iklan']="";
        $_SESSION['baris1']="";
        $_SESSION['baris2']="";
        $_SESSION['judulIklan']="";
        $_SESSION['campaignSelected']="";
        $_SESSION['landingSelected']="";
        //print_r($_SESSION);
    }
    /**
     * 
     * Form daftar iklan halaman 2
     */
    function FormIklanBaru2(){
        
    	global $APP_PATH,$LOCALE;
    	
        $namaIklan = $this->Request->getPost("nama");
        $judul = $this->Request->getPost("judul");
        $baris1 = $this->Request->getPost("baris1");
        $baris2 = $this->Request->getPost("baris2");
        $urlName = $this->Request->getPost("urlName");
        $urlLink = $this->Request->getPost("urlLink");
        $target_web = $this->Request->getPost("target_web");
        
        $this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
        if($target_web=="sitti"){
        	include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";
        	$lp = new Custom_Template(&$req,&$account);
        	$lp->open();
            $customPage=$lp->getLayoutLastID($profile['sittiID']);
            //$customPage['id'] = $_SESSION['layoutID'];
            //print_r($customPage['id'])."<br>";
            
        	$lp->close();
        	$urlLink = "http://www.sittibelajar.com/lp/?id=".$customPage['id'];
            //print $urlLink;
        	$urlName = substr($urlLink, 0,30)."...";
        	$_SESSION['ad_create_step1']['urlLink'] = $urlLink;
        	$_SESSION['ad_create_step1']['urlName'] = $urlName;
        }
       
        
        //jumlah keyword yg sudah dipilih
        $list = explode(",",$_SESSION['ad_keyword_list']);
        if(strlen($list[0])>0){
        	$this->View->assign("total_keyword_selected",sizeof($list));
        }else{
        	$this->View->assign("total_keyword_selected","0");
        }
        if(!$this->Request->getParam("step")=='2'){
        
            if(!$namaIklan || !$judul || !$baris1 || !$baris2 || (!$urlName ||
                    !$urlName  || !$urlLink || $urlLink=='http://')){
             
                $msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                return $this->FormIklanBaru($msg, $_POST);
               // return $this->View->showMessageError($er, "?buat_iklan=1");

            }else if(!@fopen($urlLink,"r")){
            	//check apakah alamat urlLink exists
            	$msg = $LOCALE['URL_INVALID'];
            
            	return $this->FormIklanBaru($msg, $_POST);
            	//-->
            }else{
                return $this->View->toString(BUAT_IKLAN_BARU_STEP2);
            }
        }else{
            return $this->View->toString(BUAT_IKLAN_BARU_STEP2);
        }
    }
    /**
     * 
     * form daftar iklan halaman 2, advanced keyword.
     */
    function FormIklanBaru2b(){
    	//jumlah keyword yg sudah dipilih
        $list = explode(",",$_SESSION['ad_keyword_list']);
        if(strlen($list[0])>0){
        	$this->View->assign("total_keyword_selected",sizeof($list));
        }else{
        	$this->View->assign("total_keyword_selected","0");
        }
       
    	return $this->View->toString(BUAT_IKLAN_BARU_STEP2B);
    }
    function FormIklanBaru3(){
    	$this->View->assign("random",rand(1000000, 9999999));
    	return $this->View->toString(BUAT_IKLAN_BARU_STEP3);
    }
    function FormIklanBaru4(){
    	$this->View->assign("rs1",$_SESSION['ad_create_step1']);
    	$list = explode(",",$_SESSION['ad_keyword_list']);
    	if(strlen($list[0])==0){
    		$list=null;
    	}
    	//$this->View->assign("keywords",$_SESSION['ad_create_step2']);
    	$this->View->assign("keywords",$list);
    	return $this->View->toString(BUAT_IKLAN_BARU_STEP4);
    }
    /**
     * 
     * menampilkan daftar key yang sedang ditampilkan
     */
    function showSelectedKey(){
      global $CONFIG;
    	$list = explode(",",$_SESSION['ad_keyword_list']);
     
      $n = sizeof($list);
      $arr = ""; 
      for($i=0;$i<$n;$i++){
        if($i>0){
          $arr.=",";
        }
       $arr .= "'".$list[$i]."'"; 
      }
     
      if($arr!=""){
       $this->open(0);
    	 /*$rs = $this->fetch("SELECT keyword, (SELECT A.bid
                          FROM db_web3.sitti_ad_keywords A, db_web3.sitti_ad_keywords B
                          WHERE A.keyword = B.keyword AND A.keyword = c.keyword ORDER BY bid DESC LIMIT 1) AS bids
                          FROM db_web3.sitti_ad_keywords c
                          WHERE (keyword IN (".$arr."))
                          GROUP BY keyword ORDER BY keyword",1);*/
       //$rs = $this->fetch("SELECT * FROM db_web3.sitti_keywords_max_bid WHERE keyword IN (".$arr.")",1);
       $rs = $this->fetch("SELECT keyword,avg_cpc as bids FROM db_web3.sitti_keywords_avg_cpc 
       WHERE keyword IN (".$arr.")",1);
     
      $this->close();
       }
      for($i=0;$i<$n;$i++){
          $biddings[$i]['keyword'] = $list[$i];
          for($j=0;$j<sizeof($rs);$j++){
             $biddings[$i]['max_cpc'] = $CONFIG['MINIMUM_BID'];
             if($rs[$j]['keyword'] == trim($list[$i])){
             	
                 $biddings[$i]['max_cpc'] = round(floor($rs[$j]['bids']/10),0)*10;
                
                 //$biddings[$i]['bid'] = $
                 break;
             }
             
          }
       }
     	$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
    	$this->View->assign("list",$biddings);
    	return $this->View->toString(DAFTAR_KEYWORD_PILIHAN);
    }
/**
     * 
     * menampilkan daftar keyword yang sudah dibeli untuk halaman edit iklan pending.
     */
    function showEditPendingSelectedKey(){
      global $CONFIG;
      $iklan_id = $this->Request->getParam("id");
      $this->Account->open(0);
      $profile = $this->Account->getProfile();
      $this->Account->close();
      $inventory = new SITTIInventory();
      $iklan = $inventory->getPendingAdDetail($iklan_id, $profile['sittiID']);
     
      $n = sizeof($iklan['biddings']);
      $arr = ""; 
      for($i=0;$i<$n;$i++){
        if($i>0){
          $arr.=",";
        }
       $arr .= "'".$iklan['biddings'][$i]['keyword']."'"; 
      }
      if($arr!=""){
       $this->open(0);
    	 $rs = $this->fetch("SELECT keyword, (SELECT A.bid
                          FROM db_web3.sitti_ad_keywords A, db_web3.sitti_ad_keywords B
                          WHERE A.keyword = B.keyword AND A.keyword = c.keyword ORDER BY bid DESC LIMIT 1) AS bids
                          FROM db_web3.sitti_ad_keywords c
                          WHERE (keyword IN (".$arr."))
                          GROUP BY keyword ORDER BY keyword",1);
     
      $this->close();
       }
      for($i=0;$i<$n;$i++){
          $biddings[$i]['keyword'] = $iklan['biddings'][$i]['keyword'];
          $biddings[$i]['bid'] = $iklan['biddings'][$i]['bid'];
          $biddings[$i]['budget'] = $iklan['biddings'][$i]['budget_daily'];
          $biddings[$i]['total'] = $iklan['biddings'][$i]['budget_total'];
          for($j=0;$j<sizeof($rs);$j++){
             $biddings[$i]['max_cpc'] = $CONFIG['MINIMUM_BID'];
             if($rs[$j]['keyword'] == trim($list[$i])){
                 $biddings[$i]['max_cpc'] = $rs[$j]['bids'];
                 
                 //$biddings[$i]['bid'] = $
                 break;
             }
             
          }
       }
     
    	$this->View->assign("list",$biddings);
    	return $this->View->toString(DAFTAR_KEYWORD_PILIHAN2);
    }
/**
     * 
     * menampilkan daftar keyword yang sudah dibeli untuk halaman edit iklan.
     */
    function showEditSelectedKey(){
      global $CONFIG;
      $iklan_id = $this->Request->getParam("id");
      $this->Account->open(0);
      $profile = $this->Account->getProfile();
      $this->Account->close();
      $inventory = new SITTIInventory();
      $iklan = $inventory->getAdDetailUnflagged($iklan_id, $profile['sittiID']);
     
      $n = sizeof($iklan['biddings']);
      $arr = ""; 
      for($i=0;$i<$n;$i++){
        if($i>0){
          $arr.=",";
        }
       $arr .= "'".$iklan['biddings'][$i]['keyword']."'"; 
      }
      if($arr!=""){
       $this->open(0);
  
       /*$rs = $this->fetch("SELECT keyword, (SELECT A.bid
                          FROM db_web3.sitti_ad_keywords A, db_web3.sitti_ad_keywords B
                          WHERE A.keyword = B.keyword AND A.keyword = c.keyword ORDER BY bid DESC LIMIT 1) AS bids
                          FROM db_web3.sitti_ad_keywords c
                          WHERE (keyword IN (".$arr."))
                          GROUP BY keyword ORDER BY keyword",1);*/
     	//$rs = $this->fetch("SELECT * FROM db_web3.sitti_keywords_max_bid WHERE keyword IN (".$arr.")",1);
     	$rs = $this->fetch("SELECT keyword,avg_cpc as bids FROM db_web3.sitti_keywords_avg_cpc WHERE keyword IN (".$arr.")",1);
      $this->close();
       }
      for($i=0;$i<$n;$i++){
          $biddings[$i]['keyword'] = $iklan['biddings'][$i]['keyword'];
          $biddings[$i]['bid'] = $iklan['biddings'][$i]['bid'];
          $biddings[$i]['budget'] = $iklan['biddings'][$i]['budget_daily'];
          $biddings[$i]['total'] = $iklan['biddings'][$i]['budget_total'];
          for($j=0;$j<sizeof($rs);$j++){
             $biddings[$i]['max_cpc'] = $CONFIG['MINIMUM_BID'];
             if($rs[$j]['keyword'] == trim($biddings[$i]['keyword'])){
                 $biddings[$i]['max_cpc'] = $rs[$j]['bids'];
                 //$biddings[$i]['bid'] = $
                 break;
             }
             
          }
       }
     
    	$this->View->assign("list",$biddings);
    	return $this->View->toString(DAFTAR_KEYWORD_PILIHAN2);
    }
    /**
     * 
     * remove 1 key dari cookie
     */
    function removeSelectedKey($keyword){
    	$keyword = mysql_escape_string(trim($keyword));
    	//$list = $_SESSION['ad_create_step2'];
    	$list = explode(",",$_SESSION['ad_keyword_list']);
    	$new_list = array();
    	$n=0;
    	for($i=0;$i<sizeof($list);$i++){
    		if($list[$i]!=$keyword){
    			$new_list[$n] = $list[$i];
    			$n++;
    		}
    		
    	}
    	$strList = "";
    	for($i=0;$i<sizeof($new_list);$i++){
    		if($i>0){
    			$strList .=",";
    		}
    		$strList.=$new_list[$i];
    	}
    	$_SESSION['ad_create_step2'] = $new_list;
    	$_SESSION['ad_keyword_list'] = $strList;
    }
    /**
     * 
     * Save advertisement - old version
     * semua iklan di simpan dulu ke tabel temporary (pending iklan)
     */
    /*function SaveAdvertisement(){
    	$inventory = new SITTIInventory();
    	$iklan = $_SESSION['ad_create_step1'];
    	//print_r($iklan);
    	//tambahkan data ke inventory
    	
    	$rs = $inventory->queueNewAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan['nama']),
    								mysql_escape_string($iklan['judul']),
    								mysql_escape_string($iklan['category']),
    								mysql_escape_string($iklan['baris1']),
    								mysql_escape_string($iklan['baris2']),
    								mysql_escape_string($iklan['urlName']),
    								mysql_escape_string($iklan['urlLink']),
    								mysql_escape_string($iklan['campaign']));
    	$iklan_id = $inventory->last_insert_id;
      
    	if($rs){
    		//kalau berhasil, tambahkan daftar keyword iklan
    		//$list = $_SESSION['ad_create_step2'];
    		//$list = explode(",",$_SESSION['ad_keyword_list']);
    		$list = $_SESSION['ad_create_step3'];
   
    		for($i=0;$i<sizeof($list['keyword']);$i++){
    		  //hapus ini kalau sudah tidak beta lagi.
    		  if($list['bid'][$i]>200){
    		    $list['bid'][$i] = 200;
    		  }
          //-->
    			$inventory->queueNewKeyword($iklan_id,mysql_escape_string($list['keyword'][$i]),
    			                                      mysql_escape_string($list['bid'][$i]),
    			                                      mysql_escape_string($list['budget'][$i]),
                                                mysql_escape_string($list['total'][$i]));
    		}
    		$msg = "Iklan anda berhasil disimpan.";
    		
    		$this->Account->open(0);
    		$profile = $this->Account->getProfile();
    		$this->Account->close();
    		//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan['nama'].") Berhasil");
    		$smtp->setRecipient($profile['email']);
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
    		$smtp->send();
    		//-->
    	}else{
    		$msg = "Iklan anda tidak berhasil disimpan.";
    	}
    	//$this->resetSession();
    	
    	return $this->View->showMessage($msg,"beranda.php");
    }*/
    /**
     * 
     * simpan data iklan ke database
     */
	function SaveAdvertisement(){
      global $LOCALE;
    	$inventory = new SITTIInventory();
    	$iklan = $_SESSION['ad_create_step1'];
    	//print_r($iklan);
    	//tambahkan data ke inventory
    	
    	$rs = $inventory->createAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan['nama']),
    								mysql_escape_string($iklan['judul']),
    								mysql_escape_string($iklan['category']),
    								mysql_escape_string($iklan['baris1']),
    								mysql_escape_string($iklan['baris2']),
    								mysql_escape_string($iklan['urlName']),
    								mysql_escape_string($iklan['urlLink']),
    								mysql_escape_string($iklan['campaign']),
    								mysql_escape_string($iklan['target_market']));
    	$iklan_id = $inventory->last_insert_id;
      
    	if($rs){
    		//kalau berhasil, tambahkan daftar keyword iklan
    		//$list = $_SESSION['ad_create_step2'];
    		//$list = explode(",",$_SESSION['ad_keyword_list']);
    		$list = $_SESSION['ad_create_step3'];
   
    		for($i=0;$i<sizeof($list['keyword']);$i++){
    		  //hapus ini kalau sudah tidak beta lagi.
    		  if($list['bid'][$i]>200){
    		    $list['bid'][$i] = 200;
    		  }
          //-->
    			$inventory->addKeyword($iklan_id,mysql_escape_string($list['keyword'][$i]),
    			                                      mysql_escape_string($list['bid'][$i]),
    			                                      mysql_escape_string($list['budget'][$i]),
                                                mysql_escape_string($list['total'][$i]));
    		}
    		$msg = $LOCALE['ADS_SAVE_SUCCESS'];
    		
    		$this->Account->open(0);
    		$profile = $this->Account->getProfile();
    		$this->Account->close();
			// action log create ad (121)
			$this->ActionLog->actionLog(121,$profile['sittiID'],$iklan_id);
    		//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Iklan Anda (".$iklan['nama'].") Sudah Terdaftar");
    		$smtp->setRecipient($profile['email']);
    		$this->View->assign("nama",$profile['nama']);
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
    		$smtp->send();
    		//-->
    	}else{
    		$msg = "Iklan anda tidak berhasil disimpan.";
    	}
    	//$this->resetSession();
    	
    	return $this->View->showMessage($msg,"beranda.php");
    }
    /**
     * 
     * Detil campaign
     * @param $ox_campaign_id
     */
    function showDetail($campaign_id,$total_per_page = 20){
    	$start = $this->Request->getParam("st");
    	$start2 = $this->Request->getParam("st2");
    	settype($campaign_id,'integer');
    	if($start==null){
    		$start=0;
    	}
    	if($start2==null){
    		$start2=0;
    	}
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	$this->View->assign("info",$profile);
    	
      $this->open(0);
      if($profile['sittiID']!=NULL){
        $campaign = $this->fetch("SELECT ox_campaign_id,name,camp_flag FROM sitti_campaign WHERE sittiID = '".$profile['sittiID']."' LIMIT 200",1);
      }
      $this->close();
      
      //campaign dropdown
      $this->View->assign("campaign",$campaign);

      $inventory = new SITTIInventory();
    	//$banners = $reporting->getCampaignBannerStats($ox_campaign_id);
    	//$foo = $reporting->getCampaignPublisherStatistic($ox_campaign_id);
    	$ads =  $inventory->getAdvertiserAdByCampaignID($campaign_id,$profile['sittiID'],$start,$total_per_page);
    	$found_rows = $inventory->found_rows;
    	$this->View->assign("list",$ads);
    	
    	//$pending = $inventory->getPendingAdByCampaignID($campaign_id,$profile['sittiID'],$start2,$total_per_page);
    	//$found_rows2 = $inventory->found_rows;
    	//$this->View->assign("pending",$pending);

      $banner =  $inventory->getBannerAdsByCampaignID($campaign_id,$profile['sittiID'],$start,$total_per_page);
    	$this->View->assign("banner",$banner);
    	
    	//paging
  		$this->Paging = new Paginate();
  		
  		$this->View->assign("paging",$this->Paging->generate($start, $total_per_page, $found_rows, "?detail=1&id=".$campaign_id));
  		//$this->View->assign("paging2",$this->Paging->generate($start2, $total_per_page, $found_rows2, "?detail=1&id=".$campaign_id,"st2"));
  		$this->View->assign("campaign_id",$campaign_id);
    	$this->View->assign("rs",$inventory->getAdDetail($iklan_id,$this->Account->getActiveID()));
    	return $this->View->toString(DETAIL_IKLAN);
    }
    /**
     * menampilkan form campaign baru
     * @return string HTML
     */
    function FormCampaignBaru(){
    	return $this->View->toString(FORM_CAMPAIGN_BARU);
    }
    /**
     * menampilkan form edit campaign
     * @reutrn string HTML
     */
    function FormEditCampaign($msg=null){
    	global $LOCALE;
    	$req = $this->Request;
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$campaign = new SITTICampaign();
    	$campaign->open(0);
    	$isOwner = $campaign->isCampaignOwner($profile['sittiID'],$req->getRequest("id"));
    	$campaign->close();
    	if($isOwner){
    		$campaign->open(0);
    		$rs = $campaign->getCampaignByOwner($profile['sittiID'], $req->getRequest("id"));
    		$campaign->close();
    		$this->View->assign("rs",$rs);
    		$this->View->assign("msg",$msg);
    		return $this->View->toString(FORM_EDIT_CAMPAIGN);	
    	}else{
    		$msg = $LOCALE['CAMPAIGN_OWNER_INVALID'];
    		return $this->View->showMessage($msg,"beranda.php");
    	}
    }
    /**
     * 
     * save campaign baru ke database
     */
    function CreateCampaign(){
    	global $LOCALE;
		
    	$token = $this->Request->getPost('ch');
    	if(!is_token_valid($token)){
    		$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    		return $this->View->showMessage($msg,"beranda.php");
    	}
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//-->
		$campaign_name = get_correct_utf8_mysql_string($this->Request->getPost("name"));
		$flag = false;
		if(preg_match("/[^\w|\s|\`|\~|\@|\/|\\|\+|\}|\{|\]|\[|\#|\$|\%|^|\&|\*|\(|\)|\"|\'|:|;|\?|>|<|\.|\,\!|\_|\-|\+|\=]/",$campaign_name)){
			$flag=true;
		}
		
        if(!$this->Request->getPost("name")){
            $er = "Semua Langkah 'Kampanye Program' Wajib Diisi.";
            return $this->View->showMessageError($er, "?buat_campaign=1");
            
        }else if($flag){
        	
        	$er = "Mohon maaf, nama campaign harus dalam huruf dan angka.";
            return $this->View->showMessageError($er, "?buat_campaign=1");
        }else{
			$req = $this->Request;
			$startFrom = $req->getPost("tanggalAwal");
    		$endTo = $req->getPost("tanggalAkhir");
    		//check the date input
    		$arr_start = explode("/",mysql_escape_string($startFrom));
			$tgl_mulai = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
			$arr_end = explode("/",mysql_escape_string($endTo));
			$tgl_berakhir = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
            $campaign = new SITTICampaign();
            if($campaign->addCampaign($profile['sittiID'],
                                      mysql_escape_string($campaign_name),
                                      $tgl_mulai,
                                      $tgl_berakhir,
                                      mysql_escape_string($this->Request->getPost("deskripsi")),
                                      $profile['ox_adv_id'])){
                //masukan data campaign baru ke database reporting
               $campaign->addCampaignToReporting($profile['sittiID'], $campaign->lastInsertId, mysql_escape_string($campaign_name));
			   // action log create advertiser campaign (111)
				$this->open(0);
				$rs = $this->fetch("SELECT ox_campaign_id FROM sitti_campaign ORDER BY ox_campaign_id DESC LIMIT 1");
				$this->close();
				$campaign_id = $rs['ox_campaign_id'];
				$this->ActionLog->actionLog(111,$profile['sittiID'],$campaign_id);
                $msg = $LOCALE['SITTI_CREATE_CAMPAIGN_SUCCESS'];
            }else{
                $msg = $LOCALE['SITTI_CREATE_CAMPAIGN_FAILED'];
            }
			if ($_SESSION['is_cpm']){
				return $this->View->showMessage($msg,"buat.php?ad_banner=true");
			}
			return $this->View->showMessage($msg,"buat.php");
        }
    }
	 /**
     * CreateCampaignWizard
     */
	function CreateCampaignWizard(){
		global $LOCALE;
		// validasi informasi kampanye dan menyimpan dalam session
		$req = $this->Request;
		$hari = "";
		$campaign_name = get_correct_utf8_mysql_string($this->Request->getPost("name"));
		$startFrom = $req->getPost("tanggalAwal");
		$endTo = $req->getPost("tanggalAkhir");
		
		$c_info = array();
		$c_info['campaign_name'] = $campaign_name;
		$c_info['tanggalAwal'] = $startFrom;
		$c_info['tanggalAkhir'] = $endTo;
		$c_info['product_name'] = $this->Request->getPost("product_name");
		$c_info['type'] = $this->Request->getPost("type");
		$c_info['category'] = $this->Request->getPost("category");
		$c_info['keyword'] = $this->Request->getPost("keyword");
		$c_info['keyword_usulan'] = $this->Request->getPost("keyword_usulan");
		$c_info['urlName'] = $this->Request->getPost("urlName");
		$c_info['urlLink'] = $this->Request->getPost("urlLink");
		
		$flag = false;
		if(preg_match("/[^\w|\s|\`|\~|\@|\/|\\|\+|\}|\{|\]|\[|\#|\$|\%|^|\&|\*|\(|\)|\"|\'|:|;|\?|>|<|\.|\,\!|\_|\-|\+|\=]/",$campaign_name)){
			$flag=true;
		}
		
		$this->Account->open(0);
		$info = $this->Account->getProfile();
		$this->Account->close();
		$this->View->assign("info",$info);
		$this->View->assign("isLogin","1");
		$this->View->assign("LOGIN_URL",$CONFIG['LOGIN_URL']);
		
        if(!$this->Request->getPost("name") || !$this->Request->getPost("urlName") || !$this->Request->getPost("urlLink") || !($this->Request->getPost("keyword") || $this->Request->getPost("keyword_usulan"))){
            $er = "Semua Langkah 'Kampanye Program' Wajib Diisi.";
			$_SESSION['campaign_info'] = base64_encode(json_encode($c_info));
			$this->View->assign("mainContent",$this->View->showMessageError($er, "kampanye_wizard.php?edit=1"));
			print $this->View->toString("SITTI/content.html");
			die();
        }else if($flag){
        	$er = "Mohon maaf, nama campaign harus dalam huruf dan angka.";
			$c_info['campaign_name'] = "";
			$_SESSION['campaign_info'] = base64_encode(json_encode($c_info));
            $this->View->assign("mainContent",$this->View->showMessageError($er, "kampanye_wizard.php?edit=1"));
			print $this->View->toString("SITTI/content.html");
			die();
        }else if(!@isValidUrl($c_info['urlLink'])){
			$er = $LOCALE['URL_INVALID'];
			$c_info['urlLink'] = "";
			$_SESSION['campaign_info'] = base64_encode(json_encode($c_info));
			$this->View->assign("mainContent",$this->View->showMessageError($er, "kampanye_wizard.php?edit=1"));
			print $this->View->toString("SITTI/content.html");
			die();
		}else{
    		$arr_start = explode("/",mysql_escape_string($startFrom));
			$tgl_mulai = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
			$arr_end = explode("/",mysql_escape_string($endTo));
			$tgl_berakhir = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
			$c_info['tgl_mulai'] = $tgl_mulai;
			$c_info['tgl_berakhir'] = $tgl_berakhir;
			// simpan di session
			$_SESSION['campaign_info'] = base64_encode(json_encode($c_info));
			// print_r(json_decode(base64_decode($_SESSION['campaign_info'])));
			// echo "<br/>";
			// hitung durasi campaign
			$interval = round((strtotime($tgl_berakhir) - strtotime($tgl_mulai))/(60*60*24)) + 1;
			$hari = "".$interval;
			// print_r($hari);
			// echo "<br/>";
		}
		
		// construct keyword (ditambah sejumlah relasi) dan menyimpan dalam session
		$keyword = strtolower($this->Request->getPost("keyword"));
		if ($this->Request->getPost("keyword_usulan")!=""){
			if ($keyword!= ""){
				$keyword .= ",";
			}
			$keyword .= strtolower($this->Request->getPost("keyword_usulan"));
		}
		$keyword = explode(",",$keyword);
		$keywords_arr = array();
		foreach ($keyword as $word){
			array_push($keywords_arr, trim($word));
		}
		$keywords_arr_temp = array_unique($keywords_arr);
		$keywords_arr = array();
		$keywords = "";
		foreach($keywords_arr_temp as $word){
			if($keywords!=""){
				$keywords.=",";
			}
			$keywords.="'".$word."'";
			array_push($keywords_arr, $word);
		}
		
		$json = json_encode(array($hari,$keywords_arr));
		$_SESSION['keypicks'] = base64_encode($json);
		// print_r(base64_decode($_SESSION['keypicks']));
		// echo "<br/>";
		
		// simpan informasi iklan dalam session
		if ($this->Request->getPost("urlLink")){
			$url_link = $this->Request->getPost("urlLink");
			if (strpos($url_link, 'http://') === false && strpos($url_link, 'https://') === false){
			  $url_link = 'http://' . $url_link;
			}
		}
		$p['jenis_iklan'] = "text";
		$p['namaIklan'] = "";
		$p['judul'] = "";
		$p['judul2'] = "";
		$p['judul3'] = "";
		$p['ad_type'] = "";
		$p['ads_size'] = "";
		$p['baris1'] = "";
		$p['baris2'] = "";
		$p['urlName'] = $this->Request->getPost("urlName");
		$p['urlLink'] = $url_link;
		$p['target_web'] = "";
		$p['category'] = $this->Request->getPost("category");
		$p['target_market'] = "";
		$p['campaign'] = "";
		$p['allcity'] = "1";
		$p['tcity'] = "";
		$_SESSION['ad_create_step1'] = base64_encode(json_encode($p));
		// print_r(json_decode(base64_decode($_SESSION['ad_create_step1'])));
		// echo "<br/>";
		
		$iklan = json_decode(base64_decode($_SESSION['ad_create_step1']));
		$c_info = json_decode(base64_decode($_SESSION['campaign_info']));
		
		$this->View->assign("urlName",$iklan->urlName);
		$this->View->assign("campaign_name",$c_info->campaign_name);
		print $this->View->toString("SITTI/kampanye_wizard2.html");
		// return $this->View->showMessage($msg, "?buat_campaign=1");
	}
    /**
     * Update Campaign
     */
    function updateCampaign(){
    	global $LOCALE;
    	$req = $this->Request;
    	
    	//validasi token
    	$token = $this->Request->getPost('token');
    	if(!is_token_valid($token)){
    		$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
    		return $this->View->showMessage($msg,"beranda.php");
    	}
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//-->
    	$campaign = new SITTICampaign();
    	$campaign->open(0);
    	$isOwner = $campaign->isCampaignOwner($profile['sittiID'],$req->getPost("id"));
    	$campaign->close();
    	if($isOwner){
    		$startFrom = $req->getPost("tanggalAwal");
    		$endTo = $req->getPost("tanggalAkhir");
    		//check the date input
    		$arr_start = explode("/",mysql_escape_string($startFrom));
			$startDate = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
			$arr_end = explode("/",mysql_escape_string($endTo));
			$endDate = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
			$tdiff = datediff('s',$startDate,$endDate);	
			if(($tdiff/(60*60*24))>=0){
				
				//update database
				$campaign->open(0);
				$rs = $campaign->updateCampaign($profile['sittiID'], 
												$req->getPost("id"), 
												$req->getPost("name"), 
												$startDate, $endDate, 
												$req->getPost("deskripsi"));
				$campaign->close();
				if($rs){
					// action log edit advertiser campaign (112)
					$this->ActionLog->actionLog(112,$profile['sittiID'],$req->getPost("id"));
					$msg = $LOCALE['EDIT_CAMPAIGN_SUCCESS'];
					return $this->View->showMessage($msg,"beranda.php?PerformaAkun");
				}else{
					$msg = $LOCALE['EDIT_CAMPAIGN_FAILURE'];
				}
			}else{
				$msg = $LOCALE['EDIT_CAMPAIGN_DATE_INVALID'];
			}
			return $this->FormEditCampaign($msg);
    	}else{
    		$msg = $LOCALE['CAMPAIGN_OWNER_INVALID'];
    		return $this->View->showMessage($msg,"beranda.php?edit_kampanye=1&id=".$req->getPost("id"));
    	}
    }
    /**
     * Menampilkan halaman report
     */
    function ShowReport($total_per_page=20){
    	//HTTPRequest
    	$start = $this->Request->getParam('st');
      if($start==null){
        $start=0;
      }
    	$campaign_id = $this->Request->getParam("c");
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	//-->
    	//daftar campaign untuk dimasukkan ke dalam dropdown
    	$campaign = new SITTICampaign();
    	$daftar_campaign = $campaign->getCampaignList($profile['sittiID'], 0, 20);
    	
    	//untuk sementara dibatasi 20 campaign dulu.
    	$this->View->assign("campaign",$daftar_campaign);
    	
    	if($campaign_id!=null){
    		//Inventory
    		$inventory = new SITTIInventory();
    	
    		//Summary Performance Iklan
    		$report = new SITTIReporting($inventory);
    		$iklan = $report->getBannerSummary2($profile['sittiID'], $campaign_id,$start,$total_per_page);
    	   
    		$this->View->assign("list",$iklan['list']);
        
        //paging
        $this->Paging = new Paginate();
        $this->View->assign("paging",$this->Paging->generate($start, $total_per_page, $iklan['total'], "?laporan=1&c=".$campaign_id));
    
        
    	}
    	return $this->View->toString("SITTI/reporting/summary_iklan.html");
    }
	
    function m_delete_iklan(){
    	$id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($id);$i++){
			$iklan_id = urldecode64($id[$i]);
			
			//user profile
			$this->Account->open(0);
			$profile = $this->Account->getProfile();
			$this->Account->close();
			
			$inventory = new SITTIInventory();
			$inventory->open();
			$iklan = $inventory->getAdDetail($iklan_id,$profile['sittiID']);
			$inventory->close();
			
			//check apakah iklannya legitimate.
			if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){
				//delete iklannya segera
				$inventory->open(0);
				//remove ad from inventory
				$rs = $inventory->deleteAd($iklan_id);
				//force re-rank
				if($rs){$inventory->onAdDeleted($iklan_id);}
				$inventory->close();
				if($rs){
					// action log delete advertiser ad (123)
					$this->ActionLog->actionLog(123,$profile['sittiID'],$iklan_id);
				}
			}
		}
	}
	
    function delete_iklan(){
    	global $LOCALE;
    	$req = $this->Request;
    	$iklan_id = $req->getParam("id");
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$inventory = new SITTIInventory();
    	$inventory->open();
    	$iklan = $inventory->getAdDetail($iklan_id,$profile['sittiID']);
    	$inventory->close();
    	
    	$campaign_id = $req->getParam('campaign_id');
    		//belum konfirm
    		//check apakah iklannya legitimate.
    		if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){
    			//apakah sudah di confirm utk delete iklan ini ?
    			if($req->getParam("confirm")=="1"){
    				//delete iklannya segera
    				$inventory->open(0);
    				//remove ad from inventory
    				$rs = $inventory->deleteAd($iklan_id);
    				//force re-rank
    				if($rs){$inventory->onAdDeleted($iklan_id);}
    				$inventory->close();
    				if($rs){
						// action log delete advertiser ad (123)
						$this->ActionLog->actionLog(123,$profile['sittiID'],$iklan_id);
    					$msg = $LOCALE['USER_DELETE_IKLAN_SUCCESS'];
    					return $this->View->showMessage($msg,"beranda.php?PerformaIklan=".$campaign_id);
    				}else{
    					$msg = $LOCALE['USER_DELETE_IKLAN_FAILED'];
    					return $this->View->showMessageError($msg,"beranda.php?PerformaIklan=".$campaign_id);
    				}
    			}else{
    				$msg = $LOCALE['DELETE_AD_CONFIRM'];
    				$onYes = array("label"=>"Ya","url"=>"beranda.php?delete_iklan=1&id=".$iklan_id."&campaign_id=".$campaign_id."&confirm=1");
    				$onNo = array("label"=>"Tidak","url"=>"beranda.php?PerformaIklan=".$campaign_id);
    				return $this->View->confirm($msg,$onYes,$onNo);
    			}
    		}else{
    			//tidak legitimate
    			return $this->View->showMessageError($LOCALE['DELETE_AD_UNLEGITIMATE_ERROR'],"beranda.php?PerformaIklan=".$campaign_id);
    		}
    }
	/**
     * 
     * Controller untuk edit iklan pending
     */
    function EditIklanPending(){
    	global $LOCALE;
    	$req = $this->Request;
    	$iklan_id = $req->getRequest("id");
    	$campaign_id = $req->getRequest("c");
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$inventory = new SITTIInventory();
    	//detail iklan
    	$inventory->open();
    	$iklan = $inventory->getPendingAdDetail($iklan_id,$profile['sittiID']);
    	$inventory->close();
    	
    	$this->View->assign("campaign_id",$campaign_id);
    	//cocokin apakah iklan ini milik si advertiser
    	if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){																																																																																																																																																																																																																																																																																																																																		
    		if($req->getParam('step')=="2"){
    			return $this->EditPendingKeywords();
    		}else if($req->getPost("do")=="update_pending"){
    			return $this->updateIklan2(&$inventory);
    		}else if($req->getPost("step")=="3"){
    			//serialized the keywords, and then saved it into temporary session.
    			for($i=0;$i<sizeof($_POST['keywords']);$i++){
    				if(strlen($_SESSION['ad_keyword_list'])>0){
    					$_SESSION['ad_keyword_list'].=",";
    				}
    				$_SESSION['ad_keyword_list'].=$_POST['keywords'][$i];
    			}
    			session_write_close();
    			return $this->EditIklan2(&$iklan_id,&$profile,&$inventory,&$iklan);
    		}else{
    			$this->resetSession();
    			return $this->EditIklan2(&$iklan_id,&$profile,&$inventory,&$iklan);
    		}
    	}else{
    		//tidak legitimate
    		return $this->View->showMessageError($LOCALE['DELETE_AD_UNLEGITIMATE_ERROR'],"beranda.php?detail=1&id=".$campaign_id);
    	}
    }
    /**
     * 
     * hapus iklan yang masih pending
     */
    function HapusIklanPending(){
    	global $LOCALE;
    	$req = $this->Request;
    	$iklan_id = $req->getParam("id");
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$inventory = new SITTIInventory();
    	$inventory->open();
    	$iklan = $inventory->getPendingAdDetail($iklan_id,$profile['sittiID']);
    	$inventory->close();
    	
    	
    		//belum konfirm
    		//check apakah iklannya legitimate.
    		if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){
    			//apakah sudah di confirm utk delete iklan ini ?
    			if($req->getParam("confirm")=="1"){
    				//delete iklannya segera
    				$inventory->open();
    				if($inventory->deleteAdFromQueue($iklan_id)){
						// action log delete advertiser pending ad (125)
						// sudah tidak dipakai
						// $this->ActionLog->actionLog(125,$profile['sittiID'],$iklan_id);
    					$msg = $LOCALE['USER_DELETE_IKLAN_SUCCESS'];
    					return $this->View->showMessage($msg,"beranda.php?detail=1&id=".$req->getParam("c"));
    				}else{
    					$msg = $LOCALE['USER_DELETE_IKLAN_FAILED'];
    					return $this->View->showMessageError($msg,"beranda.php?detail=1&id=".$req->getParam("c"));
    				}
    				$inventory->close();
    				
    			}else{
    				$msg = $LOCALE['DELETE_AD_CONFIRM'];
    				$onYes = array("label"=>"Ya","url"=>"beranda.php?hapus_pending=1&id=".$iklan_id."&c=".$req->getParam("c")."&confirm=1");
    				$onNo = array("label"=>"Tidak","url"=>"beranda.php?hapus_pending=1&id=".$req->getParam("c"));
    				return $this->View->confirm($msg,$onYes,$onNo);
    			}
    		}else{
    			//tidak legitimate
    			return $this->View->showMessageError($LOCALE['DELETE_AD_UNLEGITIMATE_ERROR'],"beranda.php?detail=1&id=".$req->getParam("c"));
    		}
    }
    /**
     * 
     * Controller untuk edit iklan
     */
    function EditIklan(){
    	
    	global $LOCALE;
    	$req = $this->Request;

    	$iklan_id = $req->getRequest("id");
    	$campaign_id = $req->getRequest("c");

    	if ($req->getParam('campaign_id'))
      {
        $_SESSION['campaign_id'] = urldecode64($req->getParam('campaign_id'));
      }
      //user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$inventory = new SITTIInventory();
    	//detail iklan
    	//$inventory->open();
    	$iklan = $inventory->getAdDetail($iklan_id,$profile['sittiID']);
    	$locations = $inventory->getLocation($iklan_id);
    	//var_dump($locations);
    	//$inventory->close();
    	
    	$this->View->assign("campaign_id",$campaign_id);
      $n_location = sizeof($locations);
   		for($i=0;$i<$n_location;$i++){
   			$this->View->assign(str_replace(" ","_",$locations[$i]['kota']),"checked='true'");
   		}
    	//cocokin apakah iklan ini milik si advertiser
    	if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){																																																																																																																																																																																																																																																																																																																																		
    		if($req->getParam('step')=="2"){
    			return $this->EditKeywords();
    		}else if($req->getPost("do")=="update"){
    			return $this->updateIklan(&$inventory);
    		}else if($req->getPost("step")=="3"){
    			//serialized the keywords, and then saved it into temporary session.
    			for($i=0;$i<sizeof($_POST['keywords']);$i++){
    				if(strlen($_SESSION['ad_keyword_list'])>0){
    					$_SESSION['ad_keyword_list'].=",";
    				}
    				$_SESSION['ad_keyword_list'].=$_POST['keywords'][$i];
    			}
    			session_write_close();
    			return $this->EditIklan1(&$iklan_id,&$profile,&$inventory,&$iklan);
    		}else{
    			$this->resetSession();
    			return $this->EditIklan1(&$iklan_id,&$profile,&$inventory,&$iklan);
    		}
    	}else{
    		//tidak legitimate
    		return $this->View->showMessageError($LOCALE['DELETE_AD_UNLEGITIMATE_ERROR'],"beranda.php?detail=1&id=".$campaign_id);
    	}
    }
	function isForm2Valid(){
      $this->error_status=0;
      $namaIklan = $this->Request->getPost("nama");
      $judul = $this->Request->getPost("judul");
      $baris1 = $this->Request->getPost("baris1");
      $baris2 = $this->Request->getPost("baris2");
      $urlName = $this->Request->getPost("urlName");
      $urlLink = $this->Request->getPost("urlLink");
      $target_web = $this->Request->getPost("target_web");
      
	  // pengecekan nilai daily budget and budget total
	  $isbudget_err = false;
	  $istotal_err = false;
	  $cek_jumlah_budget = false;
	  $cek_jumlah_total = false;
	  $cek_kelipatan_50 = false;
	  $keywords = $_POST['keyword'];
	  $bid = $_POST['bid'];
	  $budget_daily = $_POST['budget'];
	  $budget_total = $_POST['total'];
	  $n = sizeof($keywords);
	  for($i=0;$i<$n;$i++){
		  if((string)$budget_daily[$i]==""){
			  $isbudget_err = true;
		  }
		  if((string)$budget_total[$i]==""){
			  $istotal_err = true;
		  }
		  if ((string)$budget_daily[$i]!="" && $budget_daily[$i]<$bid[$i]){
			  $cek_jumlah_budget = true;
		  }
		  if ((string)$budget_daily[$i]!="" && $budget_total[$i]<$budget_daily[$i]){
			  $cek_jumlah_total = true;
		  }
		  if ((string)$bid[$i]!="" && (int)$bid[$i]%50!=0){
			  $cek_kelipatan_50 = true;
		  }
		  if ((string)$budget_daily[$i]!="" && (int)$budget_daily[$i]%50!=0){
			  $cek_kelipatan_50 = true;
		  }
		  if ((string)$budget_total[$i]!="" && (int)$budget_total[$i]%50!=0){
			  $cek_kelipatan_50 = true;
		  }
	  }
	  
      //a pattern to make sure no karakter aneh2...
      $pattern = "/([^A-Za-z0-9\"\'\!\@\#\$\%\^\&\*\(\)\.\,\_\-\=\+\?\:\;\"\'\/\"\ ]+)/";
      //take out baris2 ..
      if($target_web!="sitti"&&(!$namaIklan || !$judul || !$baris1 || (!$urlName ||
                    !$urlName  || !$urlLink || $urlLink=='http://'))){
            
        $this->error_status=1;
        return false;

      }else if($target_web=="sitti"&&(!$namaIklan || !$judul || !$baris1)){
        $this->error_status=2;
        return false;
     }else if($target_web!="sitti"&&!@isValidUrl($urlLink)){
          
        $this->error_status=3;
       return false;
              
      //-->
      }else if(preg_match_all($pattern,stripslashes($namaIklan),$m)>0){
      	$this->error_status=4;
      	
      	return false;
      }else if(preg_match_all($pattern,stripslashes($judul),$m)>0){
      	$this->error_status=4;
      	
      	return false;
      }else if(preg_match_all($pattern,stripslashes($baris1),$m)>0){
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
		include_once "SITTISimulation.php";
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
    function updateIklan($inventory){
    	global $LOCALE;
    	$req = $this->Request;
    	
    	//validasi token
    	$token = $this->Request->getPost('token');
      $campaign_id = $req->getRequest('campaign_id') == '' ? $_SESSION['campaign_id'] : urldecode64($req->getRequest('campaign_id'));
      
    	if(!is_token_valid($token)){
    		$msg = "Maaf, transaksi anda sudah kadaluarsa. Silahkan coba kembali!";
        if (isset($_SESSION['campaign_id'])) unset($_SESSION['campaign_id']);
		$campaign_id = urlencode64($campaign_id);
        return $this->View->showMessage($msg, "beranda.php?PerformaIklan=" . $campaign_id);
    	}
    	
    	//$iklan_id = $req->getPost("id");
    	
    	$iklan_id = $req->getPost("id");
    	$nama = $req->getPost("nama");
    	$baris1 = $req->getPost("baris1");
    	$baris2 = $req->getPost("baris2");
    	$judul = $req->getPost("judul");
    	$urlName = $req->getPost("urlName");
    	$urlLink = $req->getPost("urlLink");
    	$category = $req->getPost("category");
    	$target_market = $req->getPost("target_market");
    	$city = $_POST['tcity'];
    	$allcity = $req->getPost("allcity");
    	if($allcity==1){
    		$city = array("ALL");
    	}
    	//$n_city = sizeof($city);
    	if($this->isForm2Valid()){
	    	
	    	
	    	
	    	$inventory->open(0);
	    	$rs = $inventory->updateAd($iklan_id,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$target_market);
	    	$special_user = $this->Account->isSpecialUser($_SESSION['sittiID'],false);
	    	$inventory->close();
	    	
	    	
	    	
	    	//reset session sebelum lupa.
	    	$this->resetSession();
	    	
	    	if($rs){
	    		
	    		//reassign ad location
	    		$inventory->UpdateLocation($iklan_id,$city);
	    		
	    		//re-assign the keywords
	    		/* di non aktifkan dulu.  proses ini akan dipisahkan nanti.
	    		$inventory->open(0);
	    		$inventory->deleteKeywords($iklan_id);
	    		for($i=0;$i<sizeof($_POST['keyword']);$i++){
	    			$inventory->addKeyword($iklan_id,trim(strip_tags(stripslashes($_POST['keyword'][$i]))));
	    		}
	    		$inventory->close();
	    		*/
	    		$keywords = $_POST['keyword'];
	    		$bid = $_POST['bid'];
	    		$budget_daily = $_POST['budget'];
	    		$budget_total = $_POST['total'];
	    		//CHECK terlebih dahulu keyword apa saja yang budgetnya berubah
	    		
	    		$old_keys = $inventory->getKeywordsByIklanID($iklan_id,true);
	    		$n_keys = sizeof($old_keys);
	    		//print_r($old_keys);
	    		
	    		
	    		
	    		
	    		for($i=0;$i<sizeof($keywords);$i++){
	    			$keyword = $keywords[$i];
	    			for($j=0;$j<$n_keys;$j++){
	    				if(trim($keyword) == trim($old_keys[$j]['keyword'])){
	    					//Dapatkan max cpc terakhir
	    					$rs_cpc = $this->getMaxCPC($keyword);
	    					$toUpdate = false;
							$updateBid = false;
							$updateDaily = false;
							$updateTotal = false;
	    					if($bid[$i]!=$old_keys[$j]['bid']||$bid[$i]<$rs_cpc['bids']){
	    						$toUpdate = true;
								$updateBid = true;
	    					}
	    					if($budget_daily[$i]!=$old_keys[$j]['budget_daily']){
	    						$toUpdate = true;
								$updateDaily = true;
	    					}	
	    					if($budget_total[$i]!=$old_keys[$j]['budget_total']){
	    						$toUpdate = true;
								$updateTotal = true;
	    					}
	    					if($toUpdate){	
	    						$this->updateKeywordBudget($iklan_id, $keyword,$bid[$i],$budget_daily[$i],$budget_total[$i],$rs_cpc['bids'],$special_user);
								// action log edit advertiser ad (132)
								// $this->ActionLog->actionLog(132,$iklan_id,$keyword,$bid[$i]);
								// replaced with one or more action log (134,135,136)
								if ($updateBid){
									$this->ActionLog->actionLog(134,$iklan_id,$keyword,$bid[$i]);
								}
								if ($updateDaily){
									$this->ActionLog->actionLog(135,$iklan_id,$keyword,$budget_daily[$i]);
								}
								if ($updateTotal){
									$this->ActionLog->actionLog(136,$iklan_id,$keyword,$budget_total[$i]);
								}
	    					}	
	    					break;
	    				}
	    			}
	    		}
	    		
	    		//update keyword & update tabel rerank
	    		
	    		//-->
				// action log edit advertiser ad (122)
				$this->Account->open(0);
				$profile = $this->Account->getProfile();
				$this->Account->close();
				$this->ActionLog->actionLog(122,$profile['sittiID'],$iklan_id);
	    		$msg = $LOCALE['UPDATE_IKLAN_BERHASIL'];
	    		//flag iklan ini.
	    		$inventory->open(0);
	    		$inventory->flagAdForUpdate($iklan_id);
	    		$inventory->close();
          if (isset($_SESSION['campaign_id'])) unset($_SESSION['campaign_id']);
				$campaign_id = urlencode64($campaign_id);
	    		return $this->View->showMessage($msg, "beranda.php?PerformaIklan=" . $campaign_id);
	    		//return $this->View->showMessage($msg,"beranda.php?edit_iklan=1&id=".$iklan_id);
	    	}else{
	    		$msg = $LOCALE['UPDATE_IKLAN_GAGAL'];
	    		return $this->View->showMessage($msg,"beranda.php?edit_iklan=1&id=".$iklan_id);
	    	}
    	}else{
    		
    		switch($this->error_status){
             	case 1:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
             	break;
             	case 2:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
             	break;
             	case 3:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['URL_INVALID'];
             	break;
             	case 4:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['TEXT_INVALID'];
             	break;
				case 5:
              		$msg = "Teks Iklan mengandung Kata Yang Tidak Diperbolehkan Untuk Digunakan";
             	break;
				case 6:
              		$msg = "Anda Belum Memasukkan Nilai Anggaran Anda <br/>(Masih Ada Nilai Anggaran Yang Kosong)";
             	break;
				case 7:
              		$msg = "Nilai Anggaran Harian Tidak Boleh Lebih Kecil dari Nilai Tawaran";
             	break;
				case 8:
              		$msg = "Nilai Anggaran Total Tidak Boleh Lebih Kecil dari Nilai Anggaran Harian";
             	break;
				case 9:
              		$msg = "Nilai Tawaran, Anggaran Harian, dan Anggaran Total Harus Kelipatan 50";
             	break;
           	}
	    	return $this->View->showMessage($msg,"beranda.php?edit_iklan=1&id=".$iklan_id);
    	}
    }
    function getMaxCPC($keyword){
    	$this->open(0);
    	/*$rs_cpc = $this->fetch("SELECT keyword, (SELECT A.bid
                          FROM db_web3.sitti_ad_keywords A, db_web3.sitti_ad_keywords B
                          WHERE A.keyword = B.keyword AND A.keyword = c.keyword ORDER BY bid DESC LIMIT 1) AS bids
                          FROM db_web3.sitti_ad_keywords c
                          WHERE (keyword IN ('".$keyword."'))
                          GROUP BY keyword ORDER BY keyword");*/
    	//$rs_cpc = $this->fetch("SELECT * FROM db_web3.sitti_keywords_max_bid WHERE keyword='".$keyword."'");
    	$rs_cpc = $this->fetch("SELECT keyword,avg_cpc as bids 
    							FROM db_web3.sitti_keywords_avg_cpc
    							WHERE keyword='".$keyword."'");
    
    	$this->close();
    	
    	return $rs_cpc;
    }
    function updateKeywordBudget($iklan_id,$keyword,$bid,$budget_daily,$budget_total,$max_cpc=100,$special_user){
    	global $CONFIG;
    	
    	
    	/*if(!$special_user){
    			if($bid>($max_cpc+$CONFIG['MAXIMUM_BID_CAP'])){
    				$bid = ($max_cpc+$CONFIG['MAXIMUM_BID_CAP']);
    			}
    			if($bid>400){
    				$bid = 400;	
    			}
    			if($budget_daily>1500){
    				$budget_daily = 1500;	
    			}
    			if($budget_total>375000){
    				$budget_total = 375000;	
    			}
        	}
        */
    	if($bid<$CONFIG['MINIMUM_BID']){
    		$bid = $CONFIG['MINIMUM_BID'];
    	}
    	$this->open(0);
    	//print 'update budget';
    	$sql = "UPDATE sitti_ad_keywords 
    				 SET bid=".$bid.",budget_daily=".$budget_daily.",budget_total=".$budget_total.",
    				 last_update = NOW()
    				 WHERE iklan_id=".$iklan_id." AND keyword='".$keyword."'";
    	$this->query($sql);
    	//print $sql;
    	$this->close();
    }
    function updateIklan2($inventory){
    	global $LOCALE;
    	$req = $this->Request;
    	$iklan_id = $req->getPost("id");
    	
    	$iklan_id = $req->getPost("id");
    	$nama = $req->getPost("nama");
    	$baris1 = $req->getPost("baris1");
    	$baris2 = $req->getPost("baris2");
    	$judul = $req->getPost("judul");
    	$urlName = $req->getPost("urlName");
    	$urlLink = $req->getPost("urlLink");
    	$category = $req->getPost("category");
    	$genre = $req->getPost("target_market");
    	$target_market = $req->getPost("target_market");
    	
    	$inventory->open(0);
    	$rs = $inventory->updatePendingAd($iklan_id,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$genre);
    	$inventory->close();
    	
    	//reset session sebelum lupa.
    	$this->resetSession();
    	
    	if($rs){
    		/* dimatiin dulu sementara
    		 * karena nanti proses ganti keyword akan dipisah dalam halaman (proses) berbeda.
    		 */
    		//re-assign the keywords
    		/*
    		$inventory->open(0);
    		$inventory->deleteKeywordFromQueue($iklan_id);
    		for($i=0;$i<sizeof($_POST['keyword']);$i++){
    			$inventory->queueNewKeyword($iklan_id,trim(strip_tags(stripslashes($_POST['keyword'][$i]))));
    		}
    		$inventory->close();
    		*/
			// action log edit advertiser pending ad (124)
			// $this->Account->open(0);
			// $profile = $this->Account->getProfile();
			// $this->Account->close();
			// sudah tidak dipakai
			// $this->ActionLog->actionLog(124,$profile['sittiID'],$iklan_id);
    		$msg = $LOCALE['UPDATE_IKLAN_BERHASIL'];
    		return $this->View->showMessage($msg,"beranda.php?detail=1&id=".$req->getPost("c"));
    	}else{
    		$msg = $LOCALE['UPDATE_IKLAN_GAGAL'];
    		return $this->View->showMessageError($msg,"beranda.php?detail=1&id=".$req->getPost("c"));
    	}
    }
    function updateKeywords(){
    	
    }
    /**
     * 
     * Edit or add new keywords di form edit Iklan.
     */
    function EditKeywords(){
    	$iklan_id = $this->Request->getParam("id");
    	$this->View->assign("iklan_id",$iklan_id);
    //jumlah keyword yg sudah dipilih
        $list = explode(",",$_SESSION['ad_keyword_list']);
        if(strlen($list[0])>0){
        	$this->View->assign("total_keyword_selected",sizeof($list));
        }else{
        	$this->View->assign("total_keyword_selected","0");
        }
    	if($this->Request->getParam("advanced")=="1"){
    		return $this->View->toString(FORM_EDIT_IKLAN2_ADVANCED);
    	}else{
    		return $this->View->toString(FORM_EDIT_IKLAN2);
    	}
    }
	/**
     * 
     * Edit or add new keywords di form edit Iklan yang masih pending.
     */
    function EditPendingKeywords(){
    	$iklan_id = $this->Request->getParam("id");
    	$this->View->assign("iklan_id",$iklan_id);
    	//jumlah keyword yg sudah dipilih
        $list = explode(",",$_SESSION['ad_keyword_list']);
        if(strlen($list[0])>0){
        	$this->View->assign("total_keyword_selected",sizeof($list));
        }else{
        	$this->View->assign("total_keyword_selected","0");
        }
    	
    	if($this->Request->getParam("advanced")=="1"){
    		return $this->View->toString(FORM_EDIT_IKLAN2_ADVANCED_B);
    	}else{
    		return $this->View->toString(FORM_EDIT_IKLAN2B);
    	}
    }
    /**
     * 
     * Form edit iklan
     * @param $iklan_id
     * @param $profile SITTIAccount Object
     * @param $inventory  SITTIInventory Object
     * @param $iklan Array
     */
    function EditIklan1($iklan_id,$profile,$inventory,$iklan){
    	global $LOCALE;
    	//category iklan
    	$adCategory = $inventory->getAdCategory();
    	$adGenre = $inventory->getAdGenre();
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign("adGenre",$adGenre);
    	$keywords = str_replace(", ",",",$iklan['keywords']);
    	if(strlen($_SESSION['ad_keyword_list'])==0){
    		$_SESSION['ad_keyword_list']=$keywords;
    	}
    	//detail iklan
    	$this->View->assign("rs",$iklan);
    	//daftar keywords
		include_once "SITTISimulation.php";
    	$simulation = new SITTISimulation();
		$this->View->assign('restricted_words', $simulation->getRestrictedWords());
    	return $this->View->toString(FORM_EDIT_IKLAN1);
    }
    /**
     * 
     * form edit pending iklan.
     * @param $iklan_id
     * @param $profile
     * @param $inventory
     * @param $iklan
     */
    function EditIklan2($iklan_id,$profile,$inventory,$iklan){
    	global $LOCALE;
    	
    	//category iklan
    	$adCategory = $inventory->getAdCategory();
    	$adGenre = $inventory->getAdGenre();
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign("adGenre",$adGenre);
    	$keywords = str_replace(", ",",",$iklan['keywords']);
    	if(strlen($_SESSION['ad_keyword_list'])==0){
    		$_SESSION['ad_keyword_list']=$keywords;
    	}


        /* START
         * INI UNTUK CEK LANDING PAGE, JIKA ADVERTISER PUNYA LANDING PAGE,
         * MAKA AKAN TAMPIL URL LANDING PAGE NYA
         * angling:02-09-2010
         */
         $landing_page = new Custom_Template(&$req);
         
         $userID = $landing_page->Account->getActiveID();
         $landing_page->open(0);

         $url_link = $iklan['urlLink'];
         
         if(preg_match('/http:\\x2F{2}\\x77{3}\\x2Esittibelajar\\x2Ecom\\x2Flp\\x2F\\x3Fid=\\d+/', $url_link)){
             $iklan['landing_page'] = $landing_page->getLayoutLastID($userID);
         }

         
         $landing_page->close();
         $this->View->assign("landing_page", $landing_page);
        //END CEK LANDING PAGE

    	//detail iklan
    	$this->View->assign("rs",$iklan);
    	//daftar keywords
    	
    	return $this->View->toString(FORM_EDIT_IKLAN1b);
    }
    /**
     * Method untuk menghapus kampanye
     * @return string html
     */
    function delete_kampanye(){
      global $LOCALE;
      $req = $this->Request;
      $campaignID = mysql_escape_string(strip_tags(stripslashes($req->getParam("id"))));
      $campaign = new SITTICampaign();
      $this->Account->open(0);
      $profile = $this->Account->getProfile();
      $sittiID = $profile['sittiID'];
      $this->Account->close();
      $campaign->open(0);
      $isOwner = $campaign->isCampaignOwner($sittiID, $campaignID);
      $campaign->close();
      //periksa berapa iklan yang dimiliki campaign ini.
      if($isOwner){
      	$campaign->open(0);
        $rs = $campaign->getTotalAds($sittiID, $campaignID);
        $campaign->close();
        if($rs['active_ads']>0){
          $msg = $LOCALE['PUBLISHER_HAPUS_CAMPAIGN_ERROR2'];
           $strHTML = $this->View->showMessageError($msg, "beranda.php?PerformaAkun");
        }else{
        	$campaign->open(0);
        	$rs = $campaign->deleteCampaign($sittiID, $campaignID);
        	$campaign->close();
          if($rs){
          	//force update in reporting db
          	$campaign->flagCampaignInReport($sittiID,$campaignID);
			// action log delete advertiser campaign (113)
			$this->ActionLog->actionLog(113,$profile['sittiID'],$campaignID);
            $msg = $LOCALE['PUBLISHER_HAPUS_CAMPAIGN_SUCCESS1'];
            $strHTML = $this->View->showMessage($msg, "beranda.php?PerformaAkun");
          }else{
             $msg = $LOCALE['PUBLISHER_HAPUS_CAMPAIGN_ERROR3'];
           $strHTML = $this->View->showMessageError($msg, "beranda.php?PerformaAkun");
          }
         
        }
      }else{
        $msg = $LOCALE['PUBLISHER_HAPUS_CAMPAIGN_ERROR1'];
        $strHTML = $this->View->showMessageError($msg, "beranda.php?PerformaAkun");
      }
     
      return $strHTML;
    }
	function m_delete_kampanye(){
    	$id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($id);$i++){
			$campaignID = urldecode64($id[$i]);
			
			$campaign = new SITTICampaign();
			$this->Account->open(0);
			$profile = $this->Account->getProfile();
			$sittiID = $profile['sittiID'];
			$this->Account->close();
			$campaign->open(0);
			$isOwner = $campaign->isCampaignOwner($sittiID, $campaignID);
			$campaign->close();
			//periksa berapa iklan yang dimiliki campaign ini.
			if($isOwner){
				$campaign->open(0);
				$rs = $campaign->getTotalAds($sittiID, $campaignID);
				$campaign->close();
				if($rs['active_ads']==0){
					$campaign->open(0);
					$rs = $campaign->deleteCampaign($sittiID, $campaignID);
					$campaign->close();
					if($rs){
						//force update in reporting db
						$campaign->flagCampaignInReport($sittiID,$campaignID);
						// action log delete advertiser campaign (113)
						$this->ActionLog->actionLog(113,$profile['sittiID'],$campaignID);
					}
				}
			}
		}
	}
    function hasAd($sittiID){
    	$this->open(0);
    	
      if ($_SESSION['is_cpm']) 
      {
        $sql = "SELECT COUNT(*) as total FROM sitti_banner_inventory 
          WHERE advertiser_id='".$sittiID."' LIMIT 1";
      }
      else
      {
        $sql = "SELECT COUNT(*) as total FROM sitti_ad_inventory 
          WHERE advertiser_id='".$sittiID."' LIMIT 1"; 
      }
    	$rs = $this->fetch($sql);
    	$this->close();
    	if($rs['total']>0){
    		return true;
    	}
    }
    
    function showNotifications($sitti_id = false)
    {
		include_once "SITTINotification.php";
      	$sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
      	$notification = new SITTINotification();
      	$data=$notification->getAlertsAndNotificationsByAdvertiserId($sitti_id,$this->Request->getParam('page'));
      	$this->View->assign('prev',$data['prev']);
      	$this->View->assign('next',$data['next']);
      	$this->View->assign('list',$data['list']);
      	return $this->View->toString("SITTI/advertiser/daftar_notifikasi.html"); 
    }

	function m_enable_kampanye(){
    	$id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($id);$i++){
			$campaignID = urldecode64($id[$i]);
			
			$campaign = new SITTICampaign();
			$this->Account->open(0);
			$profile = $this->Account->getProfile();
			$sittiID = $profile['sittiID'];
			$this->Account->close();
			$campaign->open(0);
			$isOwner = $campaign->isCampaignOwner($sittiID, $campaignID);
			$campaign->close();
			//periksa berapa iklan yang dimiliki campaign ini.
			if($isOwner){
				$campaign->open(0);
				$rs = $campaign->Enable($sittiID, $campaignID);
				$campaign->close();
			}
		}
	}
	
	function m_disable_kampanye(){
    	$id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($id);$i++){
			$campaignID = urldecode64($id[$i]);
			
			$campaign = new SITTICampaign();
			$this->Account->open(0);
			$profile = $this->Account->getProfile();
			$sittiID = $profile['sittiID'];
			$this->Account->close();
			$campaign->open(0);
			$isOwner = $campaign->isCampaignOwner($sittiID, $campaignID);
			$campaign->close();
			//periksa berapa iklan yang dimiliki campaign ini.
			if($isOwner){
				$campaign->open(0);
				$rs = $campaign->Disable($sittiID, $campaignID);
				$campaign->close();
			}
		}
	}
}
?>