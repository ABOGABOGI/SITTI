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
include_once "SITTIActionLog.php";

include_once $ENGINE_PATH."Utility/Paginate.php";

class SITTIEditKeyword extends SITTIApp{
   var $error_status=0; 
    function SITTIEditKeyword($req,$account){
       parent::SITTIApp($req,$account);
	   $this->ActionLog = new SITTIActionLog();
    }
    
    function FormEditKeyword($msg=null){
    	global $LOCALE;
    	$iklan_id = $this->Request->getParam("id");
    	//$this->resetSession();
    	$inventory = new SITTIInventory();
   		$campaign = new SITTICampaign();
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	$iklan = $inventory->getAdDetail($iklan_id, $profile['sittiID']);
    	$keywords = $inventory->getUnflaggedKeywords($iklan['id'],true);
    	$this->View->assign("msg",$msg);
    	$this->View->assign("list",$keywords);
    	$this->View->assign("iklan_id",$iklan_id);
    	return $this->View->toString("SITTI/form_edit_iklan/edit_keyword.html");
    }
    
    function HapusKeyword(){
    	global $LOCALE;
    	$iklan_id = $this->Request->getParam("id");
    	$keyword = $this->Request->getParam("k");
    	//$this->resetSession();
    	$inventory = new SITTIInventory();
   		$campaign = new SITTICampaign();
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	$iklan = $inventory->getAdDetail($iklan_id, $profile['sittiID']);
    	//remove the keyword
    	$inventory->open(0);
    	$rs = $inventory->flagKeywordForRemoval($iklan_id, $keyword);
    	$inventory->close();
    	if($rs){
			// action log delete keyword from ad (133)
			$this->ActionLog->actionLog(133, $iklan_id, $keyword);
    		$msg = $LOCALE['HAPUS_KEYWORD_SUCCESS'];
    		$inventory->flagAdForUpdate($iklan_id);
    	}else{
    		$msg = $LOCALE['HAPUS_KEYWORD_FAILURE'];
    	}
    	return $this->FormEditKeyword($msg);
    }
	
	function HapusKeywordByCampaign(){
    	$c_id = $this->Request->getParam("c_id");
    	$keyword = $this->Request->getParam("k");
    	if ($c_id!='none'){
			$c_id = urldecode64($c_id);
		}
    	$inventory = new SITTIInventory();
   		$campaign = new SITTICampaign();
		$reporting = new SITTIReporting($inventory);
		
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
		
		if ($c_id=='none'){
			$c_ids = $reporting->getAllAdvertiserCampaign($profile['sittiID']);
			foreach ($c_ids as $c_id){
				$ads = $reporting->getAllAdvertiserAdInCampaign($profile['sittiID'], $c_id['campaign_id']);
				//remove the keyword ad by ad
				foreach ($ads as $ad){
					$iklan_id = $ad['id'];
					$inventory->open(0);
					$sql = "SELECT * FROM db_web3.sitti_ad_keywords WHERE iklan_id=".mysql_escape_string($iklan_id)." 
							AND keyword='".mysql_escape_string($keyword)."' LIMIT 1";
					$rs = $this->fetch($sql);
					$inventory->close();
					if($rs){
						$inventory->open(0);
						$rs = $inventory->flagKeywordForRemoval($iklan_id, $keyword);
						$inventory->close();
						if($rs){
							// action log delete keyword from ad (133)
							//echo $iklan_id."<br/>";
							$this->ActionLog->actionLog(133, $iklan_id, $keyword);
							$inventory->flagAdForUpdate($iklan_id);
						}
					}
				}
			}
		}else{
			$ads = $reporting->getAllAdvertiserAdInCampaign($profile['sittiID'], $c_id);
			
			//remove the keyword ad by ad
			foreach ($ads as $ad){
				$iklan_id = $ad['id'];
				$inventory->open(0);
				$rs = $inventory->flagKeywordForRemoval($iklan_id, $keyword);
				$inventory->close();
				if($rs){
					// action log delete keyword from ad (133)
					$this->ActionLog->actionLog(133, $iklan_id, $keyword);
					$inventory->flagAdForUpdate($iklan_id);
				}
			}
		}
    }
}
?>