<?php
include_once $APP_PATH."SITTI/SITTIInventory.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $ENGINE_PATH."Utility/Paginate.php";
class SITTIAdPerformance extends SQLData{
	var $strHTML;
	var $View;
	var $Inventory;
	function SITTIAdPerformance($req){
		parent::SQLData();
		$this->View = new BasicView();
		$this->Request = $req;
		$this->Inventory = new SITTIInventory();
	}
	
	/****** End-User FUNCTIONS ********************************************/

	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		$req = $this->Request;
		$approved = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory LIMIT 1");
		$pending = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory_temp LIMIT 1");
		$stats['approved'] = $approved['total'];
		$stats['pending'] = $pending['total'];
		$this->View->assign("stats",$stats);
		return $this->View->toString("SITTIAdmin/reporting/ad_registration_summary.html");
	}
	function Dashboard(){
		
		$req = $this->Request;
		$this->open(0);
		$approved = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory LIMIT 1");
		$pending = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory_temp LIMIT 1");
		$this->close();
		$stats['approved'] = $approved['total'];
		$stats['pending'] = $pending['total'];
		$this->View->assign("stats",$stats);
		$html = $this->View->toString("SITTIAdmin/dashboard/ad_registrations.html");
		
		return $html;
	}
}
?>