<?php 
/**
 * SITTIBeranda
 * class untuk halaman Beranda.
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIApp.php";
include_once "SITTIAdvertiser.php";
include_once "SITTIPublisher.php";
include_once "SITTIAdCreation.php";
include_once "SITTIEditKeyword.php";
include_once "SITTIAddKeyword.php";
include_once "SITTIBilling.php";
class SITTIBeranda extends StaticPage{
	var $Account;
    function SITTIBeranda($req,$account){
        parent::StaticPage(&$req);
        $this->Account = $account;
        $this->Advertiser = new SITTIAdvertiser($req, $account);
        $this->Publisher = new SITTIPublisher($req,$account);
        $this->CreateAd = new SITTIAdCreation($req,$account);
        $this->EditKeyword = new SITTIEditKeyword($req, $account);
        $this->AddKeyword = new SITTIAddKeyword($req, $account);
    }
    /**
     * serialized the keywords, and then saved it into temporary session.
     */
    function SerializedKeywords1(){
    	//serialized the keywords, and then saved it into temporary session.
    		for($i=0;$i<sizeof($_POST['keywords']);$i++){
    		 // print trim("key -->".$_POST['keywords'][$i])."<br/>";
    		  if(!eregi(trim($_POST['keywords'][$i]),$_SESSION['ad_keyword_list'])){
    				if(strlen($_SESSION['ad_keyword_list'])>0){
    		  			$_SESSION['ad_keyword_list'].=",";
    		  		}
    		  		$_SESSION['ad_keyword_list'].=$_POST['keywords'][$i];
          		}
    		}
			$q = $_POST['q'];
			$arr = explode(" ",trim($q));
			for($j=0;$j<sizeof($arr);$j++){
			   if(!eregi(trim($arr[$j]),$_SESSION['ad_keyword_list'])){
				    if(strlen($_SESSION['ad_keyword_list'])>0){
    				  $_SESSION['ad_keyword_list'].=",";
    			 	}
					$_SESSION['ad_keyword_list'].=$arr[$j];
         		}
			}
    		session_write_close();
    }
    function SerializedKeywords2(){
    	$phrase = $_POST['phrase'];
    	$n = sizeof($phrase);
    	$keywords = array();
    	$k=0;
    	for($i=0;$i<$n;$i++){
    		$arr = explode("_",urldecode64($phrase[$i]));
    		for($j=0;$j<sizeof($arr);$j++){
    			if($arr[$j]!=null){
    				$keywords[$k] = strtolower(trim($arr[$j]));
    				$k++;
    			}
    		}
    	}
    	
    	//serialized the keywords, and then saved it into temporary session.
    		for($i=0;$i<sizeof($keywords);$i++){
    		 // print trim("key -->".$_POST['keywords'][$i])."<br/>";
    		  if(!eregi(trim($keywords[$i]),$_SESSION['ad_keyword_list'])){
    				if(strlen($_SESSION['ad_keyword_list'])>0){
    		  			$_SESSION['ad_keyword_list'].=",";
    		  		}
    		  		$_SESSION['ad_keyword_list'].=$keywords[$i];
          		}
    		}
			$q = $_POST['q'];
			$arr = explode(" ",trim($q));
			for($j=0;$j<sizeof($arr);$j++){
			   if(!eregi(trim($arr[$j]),$_SESSION['ad_keyword_list'])){
				    if(strlen($_SESSION['ad_keyword_list'])>0){
    				  $_SESSION['ad_keyword_list'].=",";
    			 	}
					$_SESSION['ad_keyword_list'].=$arr[$j];
         		}
			}
    		session_write_close();
    }
    /**
     * show advertiser pages.
     */
    function showPage(){
      global $LOCALE;
    	$req = $this->Request;
  		$billing = new SITTIBilling($req, $account);
    	if($req->getPost("do")=="step2"){
    		$_SESSION['ad_create_step1'] = $_POST;
    		return $this->CreateAd->FormIklanBaru3();
    	}else if($req->getPost("do")=="step3"){
    		//serialized keywords from keyword list
    		$keywords = $_POST['keywords'];
    		$phrase = $_POST['phrase'];
    		if(sizeof($keywords)>0){
    			// get it from keyword list
    			$this->SerializedKeywords1();
    		}else if(sizeof($phrase)>0){
    			//get it from phrases list
    			$this->SerializedKeywords2();
    		}else{
    			//just do nothing
    		}
        	if($_SESSION['done']=="1"){
            	return $this->CreateAd->FormIklanBaru3(false); 
        	}else{
    		    return $this->CreateAd->FormIklanBaru2();
        	}
    	}else if($req->getPost("do")=="step4"){
    	  $_SESSION['ad_create_step3'] = $_POST;
    	  if($_SESSION['ad_create_step1']['target_web']=="sitti"&&$_SESSION['layoutID']==null){
    		  //return $this->CreateAd->CreateLandingPage1();
    		 // print_r($_SESSION['ad_create_step1']);
    		 // die();
    		 
    		  sendRedirect("modif_template_petunjuk.php?n=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['nama'])).
    		              "&j=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['judul'])).
    		              "&b1=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['baris1'])).
    		              "&b2=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['baris2'])).
    		              "&campaign=".urlencode(mysql_escape_string($_SESSION['ad_create_step1']['campaign'])).
    		              "&landing=1");
    		  die();
        }else{
          // print_r($_SESSION);
          $_SESSION['done']="1";
          //print "woorah";
    		  return $this->CreateAd->FormIklanBaru4();
        }
    	}else if($req->getPost("do")=="update_detail"){
    	   if($this->CreateAd->isForm2Valid()){
    	     $_SESSION['ad_create_step1'] = $_POST;
            return $this->CreateAd->FormIklanBaru4($_SESSION['layoutID']);
         }else{
            switch($this->CreateAd->error_status){
             case 1:
               $msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                return $this->CreateAd->FormIklanBaru2($msg, $_POST);
             break;
             case 2:
               $msg = $LOCALE['BUAT_IKLAN_FIELD_EMPTY'];
                return $this->CreateAd->FormIklanBaru2($msg, $_POST);
             break;
             case 3:
              //check apakah alamat urlLink exists
              $msg = $LOCALE['URL_INVALID'];
              return $this->CreateAd->FormIklanBaru2($msg, $_POST);
             break;
           }
         }
    	}else if($req->getPost("do")=="finish"){
    		//save advertisement
    		if($req->getPost("agree")=="1"){
    			$result = $this->CreateAd->SaveAdvertisement();
          $this->CreateAd->resetSessionLandingPage();
                //end
    			return $result;
    		}else{
    			return $this->CreateAd->FormIklanBaru4();
    		}
    	}else if($req->getPost("do")=="create_campaign"){
			//Save Campaign Baru
			return $this->Advertiser->CreateCampaign();
    	}else if($req->getPost("add_new_keyword")==1){
    		if($req->getPost("save")==1){
    			return $this->AddKeyword->SaveBudget();
    		}else{
    			return $this->AddKeyword->SaveKeyword();
    		}
    	}else if($req->getPost("do")=="update_campaign"){
    		//Save Campaign Baru
    		return $this->Advertiser->UpdateCampaign();
    	}else if($req->getRequest("buat_iklan")){
			sendRedirect("buat.php");
			die();
    		// if($req->getParam("step")=="2"){
    			// return $this->CreateAd->FormIklanBaru2();
    		// }else if($req->getRequest("step")=="4"){
				  // if($req->getParam("finish")=="1"){
					// $_SESSION['done']="1";
					// return $this->CreateAd->FormIklanBaru4($req->getParam("l"));
				  // }else{
					// return $this->CreateAd->CreateLandingPage1();
				// }
			// }else if($req->getParam("advanced")=="1"){
					// return $this->CreateAd->FormIklanBaru2b();
			// }else{
			  // if($req->getParam('step')!="1"){
					   // $this->CreateAd->resetSession();
			  // }
    			// //halaman buat iklan baru

    			// //return $this->Advertiser->FormIklanBaru();
    			// return $this->CreateAd->FormIklanBaru();
    		// }
    	}else if($req->getParam("selected_keywords")=="1"){
    		print $this->Advertiser->showSelectedKey();
    		die();
    	}else if($req->getParam("selected_keywords")=="2"){
    		print $this->Advertiser->showEditPendingSelectedKey();
    		die();
    	}else if($req->getParam("selected_keywords")=="3"){
    		print $this->Advertiser->showEditSelectedKey();
    		die();
    	}else if($req->getParam("delete_selected_key")){
    	 
    		$this->Advertiser->removeSelectedKey($req->getParam("k"));
    		print $this->Advertiser->showSelectedKey();
    		die();
    	}else if($req->getParam("detail")=="1"){
    		//tampilkan halaman summary
    		return $this->Advertiser->showDetail($req->getParam("id"));
    	}else if($req->getParam("buat_campaign")=="1"){
    		//tampilkan halaman summary
    		return $this->Advertiser->FormCampaignBaru();
    	}else if($req->getParam("laporan")=="1"){
    		//tampilkan halaman summary
    		return $this->Advertiser->ShowReport();
    	}else if($req->getParam("edit_kampanye")=="1"){
    		//tampilkan form edit kampanye
    		return $this->Advertiser->FormEditCampaign();
    	}else if($req->getParam("delete_kampanye")=="1"){
    		return $this->Advertiser->delete_kampanye();
    	}else if($req->getParam("m_delete_kampanye")=="1"){
    		$this->Advertiser->m_delete_kampanye();
    	}else if($req->getRequest("edit_iklan")=="1"){
    		return $this->Advertiser->EditIklan();
    	}else if($req->getParam("delete_iklan")=="1"){
    		return $this->Advertiser->delete_iklan();
    	}else if($req->getParam("m_delete_iklan")=="1"){
    		$this->Advertiser->m_delete_iklan();
    	}else if($req->getRequest("edit_pending")=="1"){
    		return $this->Advertiser->EditIklanPending();
    	}else if($req->getParam("hapus_pending")=="1"){
    		return $this->Advertiser->HapusIklanPending();
    	}else if($req->getParam("edit_keyword")=="1"){
    		return $this->EditKeyword->FormEditKeyword();
    	}else if($req->getParam("hapus_keyword")=="1"){
			if ($req->getParam("id")){
				return $this->EditKeyword->HapusKeyword();
			}else if ($req->getParam("c_id")){
				return $this->EditKeyword->HapusKeywordByCampaign();
			}
    	}else if($req->getParam("tambah_keyword")=="1"){
    		return $this->AddKeyword->FormAddKeyword();
    	}else if($req->getParam("enable")=="1"){
    		include_once "SITTIAdStatus.php";
    		$AdStatus = new SITTIAdStatus($this->Request, $this->Account);
    		return $AdStatus->enable();
    	}else if($req->getParam("disable")=="1"){
    		include_once "SITTIAdStatus.php";
    		$AdStatus = new SITTIAdStatus($this->Request, $this->Account);
    		return $AdStatus->disable();
    	}else if($req->getParam("m_enable")=="1"){
    		include_once "SITTIAdStatus.php";
    		$AdStatus = new SITTIAdStatus($this->Request, $this->Account);
    		$AdStatus->m_enable();
    	}else if($req->getParam("m_disable")=="1"){
    		include_once "SITTIAdStatus.php";
    		$AdStatus = new SITTIAdStatus($this->Request, $this->Account);
    		$AdStatus->m_disable();
    	}else if($req->getParam("m_enable_c")=="1"){
    		$this->Advertiser->m_enable_kampanye();
    	}else if($req->getParam("m_disable_c")=="1"){
    		$this->Advertiser->m_disable_kampanye();
    	}
        elseif ($req->getParam("notifikasi"))
        {
            return $this->Advertiser->showNotifications();
        }
        else
        {
        	if ($req->getParam("PerformaIklan")){
				return $this->Advertiser->showSummary(null,$req->getParam("PerformaIklan"));
			}
    		return $this->Advertiser->showSummary();
    	}
    }
    /**
     * showing publisher pages
     */
    function showPage2(){
    	$req = $this->Request;
    	if($req->getPost("do")=="create"){
    		$_SESSION['pub_web_reg1'] = $_POST;
    		return $this->Publisher->SaveSlot();
    	}else if($req->getPost("do")=="update"){
    		return $this->Publisher->UpdateSlot();
    	}else if($req->getParam("daftar")=="1"){
    		return $this->Publisher->FormDaftarWebsite();
    	}else if($req->getParam("snippet")=="1"){
    		return $this->Publisher->SnippetPage();
    	}else if($req->getParam("edit")=="1"){
    		return $this->Publisher->EditPage();
    	}else if($req->getParam("delete")=="1"){
    		return $this->Publisher->DeleteSlot();
    	}else if($req->getParam("notifikasi")=="1"){
    		return $this->Publisher->Notifikasi();
    	}else{
    		return $this->Publisher->showSummary();
    	}
    }

}
?>
