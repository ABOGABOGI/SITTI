<?php
include_once $APP_PATH."SITTI/SITTIApp.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIPublisher.php";
include_once $ENGINE_PATH."Utility/Paginate.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
class SITTIPublisherManager extends SQLData{
	var $strHTML;
	var $View;
	var $Publisher;
	
	function SITTIPublisherManager($req){
		parent::SQLData();
		$this->View = new BasicView();
		$this->Request = $req;
		$this->Publisher = new SITTIPublisher($req,new SITTIAccount($req));
	}
	
	/****** End-User FUNCTIONS ********************************************/

	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		$req = $this->Request;
		if($req->getParam("pending")=="1"){
			return $this->PendingPublisher($req);
		}else if($req->getParam("approve")=="1"){
			return $this->ApprovePublisher($req);
		}else if($req->getParam("approve")=="0"){
			return $this->DenyPublisher($req);
		}else{
			return $this->PublisherList($req);
		}
	}
	function PublisherList($req,$total=100){
		$start = $req->getParam("st");
		if($start==NULL){$start = 0;}
		$rs = $this->Publisher->getPublishers($start,$total);
		$this->View->assign("list",$rs['list']);
		$this->View->assign("start",$st);
     //paging
    $this->Paging = new Paginate();
    $this->View->assign("paging",$this->Paging->getAdminPaging($start, $total, $rs['total_rows'], "?s=publisher&list=1"));
 
		return $this->View->toString("SITTIAdmin/publisher/list.html");
	}
	function PendingPublisher($req,$total=100){
		$start = $req->getParam("st");
		if($start==NULL){$start = 0;}
		$rs = $this->Publisher->getPendingPublishers($start,$total);
		$this->View->assign("list",$rs['list']);
		$this->View->assign("start",$st);
    //paging
    $this->Paging = new Paginate();
    $this->View->assign("paging",$this->Paging->getAdminPaging($start, $total, $rs['total_rows'], "?s=publisher&pending=1"));
    
		return $this->View->toString("SITTIAdmin/publisher/pending.html");
	}
	function ApprovePublisher($req){
		global $LOCALE;
		$id = $req->getParam("id");
		if($this->Publisher->approve($id)){
			
			$profile = $this->Publisher->getPublisherProfileByID($id);
			
			 //email notifikasi approval
             $smtp = new SITTIMailer();
             $smtp->setSubject("[SITTI] Pendaftaran Anda Berhasil");
             $smtp->setRecipient($profile['email']);
             
             $this->View->assign("username",$profile['username']);
    		$this->View->assign("website",$profile['website']);
    		$smtp->setMessage($this->View->toString("SITTI/email/publisher_registration.html"));
    		$smtp->send();
    		$msg = $LOCALE['PUBLISHER_APPROVED'];
		}else{
			$msg = $LOCALE['PUBLISHER_APPROVAL_ERROR'];
		}
		
		$this->View->assign("msg",$msg);
		return $this->PendingPublisher($req);
	}
	function DenyPublisher($req){
		global $LOCALE;
		$id = $req->getParam("id");
		if($this->Publisher->reject($id)){
			$msg = $LOCALE['PUBLISHER_REJECTED'];
			//email user
		}else{
			$msg = $LOCALE['PUBLISHER_REJECTED_ERROR'];
		}
		$this->View->assign("msg",$msg);
		return $this->PendingPublisher($req);
	}
}
?>