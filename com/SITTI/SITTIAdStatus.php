<?php 
/**
 * handle activated / inactivated ad.
 */
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIReporting.php";
include_once "SITTIMailer.php";

class SITTIAdStatus extends SITTIApp{
   var $error_status=0; 
    function SITTIAdStatus($req,$account){
       parent::SITTIApp($req,$account);
    }
    
   	function enable(){
   		$iklan_id = $this->Request->getParam("id");
   		//make sure the ownership of this ad
   		if($this->isOwner($iklan_id)){
   			return $this->activate($iklan_id);	
   		}else{
   			return $this->OwnershipError();
   		}
   	}
   	function activate($iklan_id){
   		global $LOCALE;
   		$req  = $this->Request;
         $campaign_id = $req->getParam('campaign_id');
   		$isConfirm = $req->getParam('confirm');
   		if($isConfirm){
   			$q = $this->doEnable($iklan_id);
   			if($q){
   				$msg = $LOCALE['AD_ACTIVATE_SUCCESS'];
   			}else{
   				$msg = $LOCALE['AD_ACTIVATE_ERROR'];
   			}
			//$campaign_id = urlencode64($campaign_id);
			return $this->View->showMessage($msg,"beranda.php?PerformaIklan=".$campaign_id);
   		}else{
   			$msg = $LOCALE['AD_ACTIVATE_CONFIRM'];
   			$onYes = array("label"=>$LOCALE['YES'],"url"=>"beranda.php?enable=1&id=".$iklan_id."&confirm=1&campaign_id=".$campaign_id);
			//$campaign_id = urlencode64($campaign_id);
    		   $onNo = array("label"=>$LOCALE['NO'],"url"=>"beranda.php?PerformaIklan=".$campaign_id);
    		   return $this->View->confirm($msg,$onYes,$onNo);
   		}
   	}
   	function doEnable($iklan_id){
   		$iklan_id = mysql_escape_string($iklan_id);
   		#1. set flag iklan
		$sql = "UPDATE db_web3.sitti_ad_inventory AS ad
				SET ad.ad_flag = 0, ad.action_flag = 1
				WHERE ad.id IN (".$iklan_id.")";
		
		$this->open(0);
		$q = $this->query($sql);
		$sql = "UPDATE db_web3.sitti_ad_keywords
				SET action_flag = 1
				WHERE iklan_id IN (".$iklan_id.") AND keyword_flag IN (0,1)";
		$q = $this->query($sql);
		$sql2 = "SELECT ad_flag,action_flag FROM db_web3.sitti_ad_inventory WHERE id = ".$iklan_id." LIMIT 1";
		$rs = $this->fetch($sql2);
   		$this->close();
 		
   		return true;
   		//proses dibawah ini sudah di handle oleh bot bid_manager
   		/*
   		if($rs['ad_flag']!='0'){
   			
   			return false;
   		}else{
			$this->open(0);
			#2. masukkan keyword2 dari iklan tersebut ke imp-hit
			$sql ="INSERT IGNORE INTO db_publisher.sitti_imp_hit_100 (iklan_id, keyword, budget_daily_last, budget_total_last, bid)
				SELECT iklan_id, keyword, budget_daily, budget_total, bid 
				FROM db_web3.sitti_ad_keywords where iklan_id IN (".$iklan_id.")
				AND keyword_flag = 0";
			$q1 = $this->query($sql);
			
			#3. rerank ???
			$sql = "UPDATE db_publisher.tb_counter SET jum_imp = 1000 
					WHERE keyword IN (SELECT keyword FROM db_web3.sitti_ad_keywords 
					WHERE iklan_id IN (".$iklan_id.")
					AND keyword_flag = 0)";
			$q2 = $this->query($sql);
			$this->close();
   			if($q1){
				return true;
			}
   		}
   		*/
		
   	}
	function m_enable(){
   		$iklan_id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($iklan_id);$i++){
			$id = urldecode64($iklan_id[$i]);
			//make sure the ownership of this ad
			if($this->isOwner($id)){
				$this->doEnable($id);
			}
		}
   	}
	
   	function disable(){
   		$iklan_id = $this->Request->getParam("id");
   		//make sure the ownership of this ad
   		if($this->isOwner($iklan_id)){
   			return $this->deactivate($iklan_id);	
   		}else{
   			return $this->OwnershipError();
   		}
   	}
   	function deactivate($iklan_id){
   		global $LOCALE;
   		$req  = $this->Request;
         $campaign_id = $req->getParam('campaign_id');
   		$isConfirm = $req->getParam('confirm');
   		if($isConfirm){
   			$q = $this->doDisable($iklan_id);
   			if($q){
   				$msg = $LOCALE['AD_NONACTIVE_SUCCESS'];
   			}else{
   				$msg = $LOCALE['AD_NONACTIVE_ERROR'];
   			}
			//$campaign_id = urlencode64($campaign_id);
   			return $this->View->showMessage($msg,"beranda.php?PerformaIklan=".$campaign_id);
   		}else{
   			$msg = $LOCALE['AD_DISACTIVATE_CONFIRM'];
    		$onYes = array("label"=>$LOCALE['YES'],"url"=>"beranda.php?disable=1&id=".$iklan_id."&confirm=1&campaign_id=".$campaign_id);
			//$campaign_id = urlencode64($campaign_id);
    		$onNo = array("label"=>$LOCALE['NO'],"url"=>"beranda.php?PerformaIklan=".$campaign_id);
    		return $this->View->confirm($msg,$onYes,$onNo);
   		}
   	}
   	function doDisable($iklan_id){
   		$iklan_id = mysql_escape_string($iklan_id);
   		#1. set flag iklan
   		$sql = "UPDATE db_web3.sitti_ad_inventory AS ad
				SET ad.ad_flag = 1, ad.action_flag = 1
				WHERE ad.id IN (".$iklan_id.")";
   		
   		$this->open(0);
   		$q = $this->query($sql);
		$sql = "UPDATE db_web3.sitti_ad_keywords
				SET action_flag = 1
				WHERE iklan_id IN (".$iklan_id.") AND keyword_flag IN (0,1)";
		$q = $this->query($sql);
   		$rs = $this->fetch("SELECT ad_flag,action_flag FROM db_web3.sitti_ad_inventory WHERE id = ".$iklan_id." LIMIT 1");
   		$this->close();
   		//print_r($rs);
   		if($rs['ad_flag']!='1'&&$rs['action_flag']!='1'){
   			
   			return false;
   		}else{
   			
   			#2. apabila keyword2 dari iklan(2) itu masuk ke dalam list impressi, update impressi di imp_hit dan tb_counter
   			//untuk sementara, assume query dibawah ini jalan semua.
   			// nanti di kembangin failover mechanismnya
   			$sql = "UPDATE db_publisher.sitti_imp_hit_100 AS ih
				INNER JOIN db_publisher.tb_counter AS cnt
				ON ih.keyword = cnt.keyword
				SET cnt.jum_imp = cnt.jum_imp + ih.maxcount - ih.jum_imp
				WHERE ih.iklan_id IN (".$iklan_id.")
				AND ih.maxcount > 0
				AND ih.jum_imp < ih.maxcount";
   			$this->open(0);
   			$q1 = $this->query2($sql);
   			//print $q1;
   			#3. delete iklan-keyword from imp_hit
			$sql = "DELETE FROM db_publisher.sitti_imp_hit_100
				WHERE iklan_id IN (".$iklan_id.")";
			$q2 = $this->query2($sql);
			//print $q2;
			$this->close();
			if($q2){
				return true;
			}
   		}
   	}
	function m_disable(){
   		$iklan_id = explode(",",$this->Request->getParam("id"));
		for($i=0;$i<sizeof($iklan_id);$i++){
			$id = urldecode64($iklan_id[$i]);
			//make sure the ownership of this ad
			if($this->isOwner($id)){
				$this->doDisable($id);
			}
		}
   	}
	
   	function OwnershipError(){
   		global $LOCALE;
   		$msg = $LOCALE['DELETE_AD_UNLEGITIMATE_ERROR'];
   		return $this->View->showMessageError($msg,"beranda.php?PerformaIklan");
   	}
   	function isOwner($iklan_id){
   		//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getProfile();
    	$this->Account->close();
    	$inventory = new SITTIInventory();
    	//detail iklan
    	$inventory->open(0);
    	$iklan = $inventory->getAdDetail($iklan_id,$profile['sittiID']);
    	$inventory->close();
    	if($iklan['id']==$iklan_id&&$iklan['advertiser_id']==$profile['sittiID']){
   			return true;
    	}
   	}
}
?>