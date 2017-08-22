<?php 
/**
 * SITTIApp
 * base class untuk semua komponen2 SITTI
 * 
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIReporting.php";
include_once "SITTIMailer.php";
include_once "Custom_Template/Custom_Template.php"; //angling:02-09-2010

include_once $ENGINE_PATH."Utility/Paginate.php";


define("HOME", "SITTI/beranda.html");
define("HOME_BEGINNER", "SITTI/beranda2.html");
define("BUAT_IKLAN_BARU_STEP1", "SITTI/form_iklan/reg1.html");
define("BUAT_IKLAN_BARU_STEP2", "SITTI/form_iklan/reg2.html");
define("BUAT_IKLAN_BARU_STEP2B", "SITTI/form_iklan/reg2b.html");
define("BUAT_IKLAN_BARU_STEP3", "SITTI/form_iklan/reg3.html");
define("BUAT_IKLAN_BARU_STEP4", "SITTI/form_iklan/reg4.html");
define("DAFTAR_KEYWORD_PILIHAN", "SITTI/form_iklan/daftar_keyword.html");

class SITTIAdCreation extends SITTIApp{
   var $error_status=0; 
    function SITTIAdCreation($req,$account){
       parent::SITTIApp($req,$account);
    }
    
    function FormIklanBaru2($msg=null,$params=null){
    	global $LOCALE;
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
    	
    	$this->View->assign("adCategory",$adCategory);
    	$this->View->assign('adGenre',$adGenre);
    	$this->View->assign("campaign",$campaign_list);
      //print_r($_SESSION);
       if($params==null&&sizeof($_SESSION['ad_create_step1'])>1){
         $params = $_SESSION['ad_create_step1'];
       }
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
    	
    	//flag done aja.. kalo true langsung lompat ke halaman konfirmasi
    	$this->View->assign("done",$_SESSION['done']);
    	return $this->View->toString(BUAT_IKLAN_BARU_STEP1);
    }
    function resetSession(){
    	$_SESSION['ad_create_step1'] = "";
    	$_SESSION['ad_create_step2'] = "";
    	$_SESSION['ad_keyword_list'] = "";
      $_SESSION['layoutID'] = null;
      $_SESSION['done'] = null;
      $this->resetSessionLandingPage();
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
    function FormIklanBaru(){
        
    	global $APP_PATH,$LOCALE;
    	
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
        //jumlah keyword yg sudah dipilih
        $list = explode(",",$_SESSION['ad_keyword_list']);
        if(strlen($list[0])>0){
        	$this->View->assign("total_keyword_selected",sizeof($list));
        }else{
        	$this->View->assign("total_keyword_selected","0");
        }
        /*
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
        */
        if($this->Request->getParam("step")=="1"&&$_SESSION['done']=="1"){
          $this->View->assign("done","1");
        }
        return $this->View->toString(BUAT_IKLAN_BARU_STEP2);
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
    /**
     *  method untuk mengecek isian dari form detail iklan
     * @return boolean
     */
    
    function isForm2Valid(){
      $this->error_status=0;
      $namaIklan = $this->Request->getPost("nama");
      $judul = $this->Request->getPost("judul");
      $baris1 = $this->Request->getPost("baris1");
      $baris2 = $this->Request->getPost("baris2");
      $urlName = $this->Request->getPost("urlName");
      $urlLink = $this->Request->getPost("urlLink");
      $target_web = $this->Request->getPost("target_web");
      
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
      }else{
       
         return true;
      }
      
     
    }
    function FormIklanBaru3($flag=true,$msg=null){
      global $LOCALE;
      
      if($flag){
      	$namaIklan = $this->Request->getPost("nama");
      	$judul = $this->Request->getPost("judul");
      	$baris1 = $this->Request->getPost("baris1");
      	$baris2 = $this->Request->getPost("baris2");
      	$urlName = $this->Request->getPost("urlName");
      	$urlLink = $this->Request->getPost("urlLink");
      	$target_web = $this->Request->getPost("target_web");
       
         if(!$this->isForm2Valid()){
           switch($this->error_status){
             	case 1:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->FormIklanBaru2($msg, $_POST);
             	break;
             	case 2:
               		$msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                	return $this->FormIklanBaru2($msg, $_POST);
             	break;
             	case 3:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['URL_INVALID'];
             		 return $this->FormIklanBaru2($msg, $_POST);
             	break;
             	case 4:
              		//check apakah alamat urlLink exists
              		$msg = $LOCALE['TEXT_INVALID'];
             		 return $this->FormIklanBaru2($msg, $_POST);
             	break;
           	}
         }else{
            $this->View->assign("random",rand(1000000, 9999999));
           	 	return $this->View->toString(BUAT_IKLAN_BARU_STEP3);
         	}
        }else{
           	$this->View->assign("random",rand(1000000, 9999999));
           	$this->View->assign("err_msg",$msg);
            return $this->View->toString(BUAT_IKLAN_BARU_STEP3);
        }
      
    }
    /**
     * halaman konfirmasi pembuatan iklan.
     * @param $landingPageID  kalo nilainya bukan -1 berarti advertiser telah membuat landing page.
     */
    function FormIklanBaru4($landingPageID=-1){
    	global $CONFIG,$LOCALE;
    	//jika advertiser bikin landing page, maka landingpagenya di simpan dan url di modif.
      if($landingPageID!=-1&&$_SESSION['ad_create_step1']['target_web']=="sitti"){
        
        $urlLink = "http://www.sittibelajar.com/lp/?id=".$landingPageID;
        //print $urlLink;
        $urlName = substr($urlLink, 0,30)."...";
        $_SESSION['ad_create_step1']['urlLink'] = $urlLink;
        $_SESSION['ad_create_step1']['urlName'] = $urlName;
        $_SESSION['landingSelected'] = "1";
        $lp = new Custom_Template(&$req,&$account);
          $lp->open();
            $customPage=$lp->getLayoutByUser($_SESSION['sittiID'],$landingPageID);
          $lp->close();
          if($customPage['id']==null){
             sendRedirect("modif_template_petunjuk.php?n=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['nama'])).
                      "&j=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['judul'])).
                      "&b1=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['baris1'])).
                      "&b2=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['baris2'])).
                      "&campaign=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['campaign'])).
                      "&landing=1");
            die();
            //die();
          }
        
           $this->View->assign("rs2",$customPage);
      }else{
        $_SESSION['landingSelected'] = null;
        
      }
      $list = $_SESSION['ad_create_step3'];
      $isBudgetDailyZero = false;
      $isBudgetTotalZero=false;
      for($i=0;$i<sizeof($list['keyword']);$i++){
         //hapus ini kalau sudah tidak beta lagi.
        // if($list['cpc'][$i]<$CONFIG['MINIMUM_BID']){
         //	$list['cpc'][$i] = $CONFIG['MINIMUM_BID'];
        // }
         //if($list['budget'][$i]==0){
      	if($list['budget'][$i]<$list['bid'][$i]){
         	$isBudgetDailyZero = true;
         }
      	 if($list['total'][$i]<$list['bid'][$i]){
         	$isBudgetTotalZero = true;
         }
          /*if($list['bid'][$i]>$list['cpc'][$i]+200){
            $list['bid'][$i] = $list['cpc'][$i]+200;
          }
          */
          /*
          else if($list['bid'][$i]==0||$list['bid'][$i]<$list['cpc'][$i]){
          	 $list['bid'][$i] = $list['cpc'][$i];
          }
          if($list['cpc'][$i]<100&&$list['bid'][$i]==$list['cpc'][$i]){
          	$list['bid'][$i] = $CONFIG['MINIMUM_BID'];
          }
          */
          if($list['bid'][$i]<$CONFIG['MINIMUM_BID']){
          	 $list['bid'][$i] = $CONFIG['MINIMUM_BID'];
          }
          $keywords[$i]['name'] = $list['keyword'][$i];
           $keywords[$i]['max_cpc'] = $list['cpc'][$i];
          $keywords[$i]['bid'] = $list['bid'][$i];
          $keywords[$i]['budget'] = $list['budget'][$i];
          $keywords[$i]['total'] = $list['total'][$i];
      }
      
      if($isBudgetDailyZero||$isBudgetTotalZero){
      	return $this->FormIklanBaru3(false,$LOCALE['KEYWORD_BIDDING_BUDGET_ERROR']);
      }
      $this->View->assign("keywords",$keywords);
      $this->View->assign("rs1",$_SESSION['ad_create_step1']);
      $this->View->assign("landingSelected",$_SESSION['landingSelected']);
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
                          GROUP BY keyword ORDER BY keyword",1);
           */
       $rs = $this->fetch("SELECT * FROM db_web3.sitti_keywords_max_bid WHERE keyword IN (".$arr.")",1);
    // print_r($rs);
      $this->close();
       }
       //temporary session for storing the latest maximum cpc's value.
     
      for($i=0;$i<$n;$i++){
          $biddings[$i]['keyword'] = $list[$i];
          for($j=0;$j<sizeof($rs);$j++){
             $biddings[$i]['max_cpc'] = $CONFIG['MINIMUM_BID'];
             
             if($rs[$j]['keyword'] == trim($list[$i])){
                 $biddings[$i]['max_cpc'] = $rs[$j]['bids'];
               
                 break;
             }
             
          }
       }
     
      
    	$this->View->assign("list",$biddings);
    	$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
    	return $this->View->toString(DAFTAR_KEYWORD_PILIHAN);
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
     * Save advertisement
     */
	function SaveAdvertisement(){
		global $CONFIG, $LOCALE;
    	$inventory = new SITTIInventory();
    	$iklan = $_SESSION['ad_create_step1'];
    	
        $special_user = $this->Account->isSpecialUser($_SESSION['sittiID'],true);
        
    //	print_r($iklan);
    	//die();
    	//tambahkan data ke inventory
    	$inventory->open();
    	$rs = $inventory->createAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan['nama']),
    								mysql_escape_string($iklan['judul']),
    								mysql_escape_string($iklan['category']),
    								mysql_escape_string($iklan['baris1']),
    								mysql_escape_string($iklan['baris2']),
    								mysql_escape_string($iklan['urlName']),
    								mysql_escape_string($iklan['urlLink']),
    								mysql_escape_string($iklan['campaign']),
    								mysql_escape_string($iklan['target_market'])
    								);
    	$iklan_id = $inventory->last_insert_id;
      //print_r($_SESSION['ad_create_step3']);
      
    	if($rs){
    		//kalau berhasil, tambahkan daftar keyword iklan
    		//$list = $_SESSION['ad_create_step2'];
    		//$list = explode(",",$_SESSION['ad_keyword_list']);
    		$list = $_SESSION['ad_create_step3'];
   			
    		for($i=0;$i<sizeof($list['keyword']);$i++){
    		  //hapus ini kalau sudah tidak beta lagi.
    		 /* if($list['bid'][$i]>200){
    		    $list['bid'][$i] = 200;
    		  }*/
    		
    		/*
    		if($list['cpc'][$i]<$CONFIG['MINIMUM_BID']){
          		$list['cpc'][$i] = $CONFIG['MINIMUM_BID'];
          	}
          	*/
          	/*
    		if($list['bid'][$i]>$list['cpc'][$i]+200){
            	$list['bid'][$i] = $list['cpc'][$i]+200;
         	 }*/
         	 //possible bug here.. agak aneh aja.. jika bid kosong atau bid dibawah cpc, maka bid di set sama dengan cpc ?
         	// else if($list['bid'][$i]==0||$list['bid'][$i]<$list['cpc'][$i]){
          	// 	$list['bid'][$i] = $list['cpc'][$i];
          	//}
          	
          	/*
    		if(!$special_user){
    			if($list['bid'][$i]>400){
    				$list['bid'][$i] = 400;	
    			}
    			if($list['budget'][$i]>1500){
    				$list['budget'][$i] = 1500;	
    			}
    			if($list['total'][$i]>375000){
    				$list['total'][$i] = 375000;	
    			}
        	}
          	*/
          	if($list['bid'][$i]<$CONFIG['MINIMUM_BID']){
          	 $list['bid'][$i] = $CONFIG['MINIMUM_BID'];
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
    		//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan['nama'].") Berhasil");
    		$smtp->setRecipient($profile['email']);
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_baru.html"));
    		$smtp->send();
    		//-->
    	}else{
    		$msg = $LOCALE['ADS_SAVE_FAILED'];
    	}
    	$this->resetSession();
    	$inventory->close();
    	if($rs){
    		//insert data kosong ke table reporting
    		$sql = "INSERT INTO db_report.tbl_performa_iklan_total
    				(advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
    				jum_imp, jum_klik, ctr, harga, 
    				budget_harian, budget_total, last_update)
    				VALUES ('".$_SESSION['sittiID']."',".$iklan_id.",'".mysql_escape_string($iklan['nama'])."','',0,
    				0,0,'0.000','0','0','0',NOW())";
    		$this->open(2);
    		$this->query($sql);
    		$this->close();
    	}
    	return $this->View->showMessage($msg,"beranda.php");
    }
    /*
    function SaveAdvertisement(){
    	$inventory = new SITTIInventory();
    	$iklan = $_SESSION['ad_create_step1'];
      
    //	print_r($iklan);
    	//die();
    	//tambahkan data ke inventory
    	
    	$rs = $inventory->queueNewAd($_SESSION['sittiID'],
    								mysql_escape_string($iklan['nama']),
    								mysql_escape_string($iklan['judul']),
    								mysql_escape_string($iklan['category']),
    								mysql_escape_string($iklan['baris1']),
    								mysql_escape_string($iklan['baris2']),
    								mysql_escape_string($iklan['urlName']),
    								mysql_escape_string($iklan['urlLink']),
    								mysql_escape_string($iklan['campaign']),
    								mysql_escape_string($iklan['target_market'])
    								);
    	$iklan_id = $inventory->last_insert_id;
      //print_r($_SESSION['ad_create_step3']);
      
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
    }
    */
    function CreateLandingPage1(){
      $req = $this->Request;
      if($req->getParam('design')=="1"){
        return $this->View->toString("SITTI/form_iklan/custom_template/add_layout.html");
      }else if($req->getPost("action")=="preview"){
        return $this->PreviewLanding();
      }else{
        return $this->View->toString("SITTI/form_iklan/custom_template/petunjuk.html");
      }
    }
    
}
?>