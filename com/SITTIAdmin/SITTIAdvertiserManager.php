<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIAdvertiser.php";
include_once $ENGINE_PATH."Utility/Paginate.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
class SITTIAdvertiserManager extends SQLData{
	var $strHTML;
	var $View;
	var $Account;
	
	function SITTIAdvertiserManager($req){
		parent::SQLData();
		$this->View = new BasicView();
		$this->Request = $req;
		$this->Advertiser = new SITTIAdvertiser($req,new SITTIAccount($req));
	}
	
	/****** End-User FUNCTIONS ********************************************/

	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		$req = $this->Request;
		return $this->AdvertiserList($req);
	}
	function AdvertiserList($req,$total_per_page=100){
		$start = $req->getParam("st");
		if($start==NULL){$start = 0;}
		$rs = $this->Advertiser->getAdvertisers($start,$total_per_page);
		
		$this->View->assign("list",$rs['list']);
		$this->View->assign("start",$st);
		//print_r($rs);
		//paging
		$this->Paging = new Paginate();
		//print $this->Inventory->found_rows;
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $total_per_page, $rs['total_rows'], "?s=advertiser&r=list"));
		return $this->View->toString("SITTIAdmin/advertiser/list.html");
	}
	
}
?>