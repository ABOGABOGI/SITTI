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
include_once "SITTIActionLog.php";


include_once $ENGINE_PATH."Utility/Paginate.php";

class SITTIAddKeyword extends SITTIApp{
   var $error_status=0; 
    function SITTIAddKeyword($req,$account){
       parent::SITTIApp($req,$account);
	   $this->ActionLog = new SITTIActionLog();
    }
    
    function FormAddKeyword($msg=null){
    	global $LOCALE;
    	$iklan_id = $this->Request->getParam("id");
    	//$this->resetSession();
    	$inventory = new SITTIInventory();
   		
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	$iklan = $inventory->getAdDetail($iklan_id, $profile['sittiID']);
    	$this->View->assign("iklan_id",$iklan['id']);
    	$this->View->assign("total_keyword_selected",0); //ini sementara tidak akan terpakai.
    	return $this->View->toString("SITTI/form_edit_iklan/add_keyword.html");
    }
    function SaveKeyword(){
    	$req = $this->Request;
    	$iklan_id = $req->getPost("id");
    	
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	$inventory = new SITTIInventory();
    	$iklan = $inventory->getAdDetail($iklan_id, $profile['sittiID']);
    	$curr_keys = $this->toSerializedString($inventory->getUnflaggedKeywords($iklan['id'],true));
    	//$qKey =trim(strip_tags(stripslashes(mysql_escape_string($req->getPost("q")))));
    	//$candidate = $qKey;
    	//if(!eregi($qKey,$curr_keys)){
    		//$keys[0]['keyword']  = $qKey;
    	//}
    	
    	$n = sizeof($_POST['keywords']);
    	
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	
    	
    	
    	for($i=0;$i<$n;$i++){
    		$candidate = trim(strip_tags(stripslashes(mysql_escape_string($_POST['keywords'][$i]))));
    		if(trim($candidate)!=trim($curr_keys)){
    			$keys[sizeof($keys)]['keyword'] = $candidate;
    		}
    	}
    	$n_keys = sizeof($keys);
    	/*$inventory->open(0);
    	for($i=0;$i<$n_keys;$i++){
    		$inventory->addKeyword($iklan_id,trim($keys[$i]),
								0,
								0,
								0);
    	}
    	$inventory->close();*/
    	//return $this->View->showMessage("Keyword telah ditambahkan","beranda.php?edit_iklan=1&id=".$iklan_id);
    	$this->View->assign("iklan_id",$iklan_id);
    	
    	//populate to table
    	for($i=0;$i<$n_keys;$i++){
    		//print $keys[$i]['keyword']."<br/>";
    		$max_cpc = $inventory->getMaxCPC($keys[$i]['keyword']);
    		//print "Max CPC : ".$max_cpc['bids']."<br/>";
    		if($max_cpc['bids']<$CONFIG['MINIMUM_BID']){
    			$max_cpc['bids'] = $CONFIG['MINIMUM_BID'];
    		}
    		$keys[$i]['bid'] = $max_cpc['bids'];
    		$keys[$i]['max_cpc'] = $max_cpc['bids'];
    	}
    	$this->View->assign("list",$keys);
    	return $this->View->toString("SITTI/form_edit_iklan/set_budget.html");
    }
    function SaveBudget(){
    	global $LOCALE,$CONFIG;
    	$req = $this->Request;
    	$iklan_id = $req->getPost("id");
    	
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$special_user = $this->Account->isSpecialUser($_SESSION['sittiID'],false);
    	$this->Account->close();
    	
    	//--> Keyword Items
    	$keys = $_POST['keyword'];
    	$bids = $_POST['bid'];
    	$budget = $_POST['budget'];
    	$totals = $_POST['total'];
    	//--->
    	$inventory = new SITTIInventory();
    	$iklan = $inventory->getAdDetail($iklan_id, $profile['sittiID']);
    	if($iklan['id']==$iklan_id&&$profile['sittiID']==$iklan['advertiser_id']){
    		$n_keys = sizeof($keys);
    		
    		for($i=0;$i<$n_keys;$i++){
    			$bid = $bids[$i];
    			$budget_daily = $budget[$i];
    			$budget_total = $totals[$i];
    			$rs_cpc = $inventory->getMaxCPC($keys[$i]);
    			$max_cpc = $rs_cpc['bids'];
    			//print "Max CPC :".$max_cpc."<br/>";
    			if($max_cpc<$CONFIG['MINIMUM_BID']){
    				$max_cpc = $CONFIG['MINIMUM_BID'];
    			}
    			/*
    			if($bid<$max_cpc){
    				//$bid = $max_cpc;
    			}else if($bid>($max_cpc+$CONFIG['MAXIMUM_BID_CAP'])){
    				$bid = ($max_cpc+$CONFIG['MAXIMUM_BID_CAP']);
    			}*/
    			/*
    			if(!$special_user){
    				if($bid>($max_cpc+$CONFIG['MAXIMUM_BID_CAP'])){
    					//print $max_cpc." --> ".$CONFIG['MAXIMUM_BID_CAP']."<br/>";
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
        		}*/
        		//print $bid."<".$CONFIG['MINIMUM_BID']."<br/>";
    			if($bid<$CONFIG['MINIMUM_BID']){
    				$bid = $CONFIG['MINIMUM_BID'];
    			}
    			
    			
    			$inventory->open(0);
    			$flag = $inventory->addKeyword($iklan_id,trim($keys[$i]),
								$bid,
								$budget_daily,
								$budget_total);
				$inventory->close();
				// action log add keyword to ad (131)
				// $this->ActionLog->actionLog(131, $iklan_id, trim($keys[$i]),$bid);
				// replaced with 3 action log (134,135,136)
				if ($flag){
					$this->ActionLog->actionLog(134, $iklan_id, trim($keys[$i]),$bid);
					$this->ActionLog->actionLog(135, $iklan_id, trim($keys[$i]),$budget_daily);
					$this->ActionLog->actionLog(136, $iklan_id, trim($keys[$i]),$budget_total);
				}
				
    		}
    		$inventory->flagAdForUpdate($iklan_id);
			// action log add keyword to ad (131)
			// $this->ActionLog->actionLog(131, $iklan_id, $i);
    		$msg = $LOCALE['NEW_KEYWORD_BUYING_SUCCESS'];
    	}else{
    		$msg = $LOCALE['NEW_KEYWORD_BUYING_FAILURE'];
    	}
    	return $this->View->showMessage($msg,"beranda.php?edit_iklan=1&id=".$iklan_id);
    }
    function toSerializedString($arr){
    	$n = sizeof($arr);
    	
    	$str = "";
    	for($i=0;$i<$n;$i++){
    		$str.=",".$arr[$i]['keyword'];
    	}
    	return $str;
    }
    
    
}
?>