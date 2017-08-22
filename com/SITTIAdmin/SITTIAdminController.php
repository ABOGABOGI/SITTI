<?php
include_once $APP_PATH."SITTI/SITTIInventory.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $ENGINE_PATH."Utility/Paginate.php";
class SITTIAdminController extends SQLData{
	var $strHTML;
	var $View;
	var $Inventory;
	function SITTIAdminController($req){
		parent::SQLData();
		$this->View = new BasicView();
		$this->Request = $req;
		$this->Inventory = new SITTIInventory();
	}
	
	/****** End-User FUNCTIONS ********************************************/

	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		$req = $this->Request;
		if($req->getRequest("r")=="list"){
			return $this->AdManagement();
		}else{
			return $this->AdApproval();
		}
		
	}
	
	/* Business Processes */
	function AdManagement(){
		$req = $this->Request;
		
		if($req->getPost("do")=="update"){
			return $this->UpdateIklan();
		}else if($req->getParam("do")=="hapus1"){
			return $this->HapusIklan(1);
		}else if($req->getParam("do")=="hapus2"){
			return $this->HapusIklan(2);
		}else if($req->getParam("do")=="hapus3"){
			return $this->HapusIklan(3);
		}else if($req->getParam("do")=="hapus4"){
			return $this->HapusIklan(4);
		}else if($req->getParam("do")=="edit"){
			return $this->EditIklan();
		}else if($req->getRequest("do")=="keyword_list"){
			$this->DaftarKeyword();
		}else if($req->getParam("do")=="search"){ //SEARCH IKLAN TAYANG
            //pencarian iklan tayang berdasarkan advertiser no akun (sittiID)
            //dibuat : 02-09-2010
            //modifikasi : --------
            $txt_search = $req->getParam("txtsearch");
            return $this->searchIklan($txt_search);

        }else{
			return $this->ListingIklan();
		}
	}
	function AdApproval(){
		$req = $this->Request;
		
		if($req->getParam("do")=="delete"){
			//return $this->HapusIklan();
		}else if($req->getParam("do")=="approve"){
			return $this->ApproveAd();
		}else if($req->getParam("do")=="deny1"){
			return $this->DenyAd(1);
		}else if($req->getParam("do")=="deny2"){
			return $this->DenyAd(2);
		}else if($req->getParam("do")=="deny3"){
			return $this->DenyAd(3);
		}else if($req->getParam("do")=="deny4"){
			return $this->DenyAd(4);
		}else if($req->getParam("do")=="deny5"){
			return $this->DenyAd(5);
		}else if($req->getParam("do")=="deny6"){
			return $this->DenyAd(6);
		}else if($req->getParam("do")=="deny7"){
			return $this->DenyAd(7);
		}else if($req->getParam("do")=="deny8"){
			return $this->DenyAd(8);
		}else if($req->getParam("do")=="deny9"){
			return $this->DenyAdFlag(9,0);
		}else if($req->getParam("do")=="search"){ //SEARCH IKLAN PENDING
            //pencarian iklan pending berdasarkan advertiser no akun (sittiID)
            //dibuat : 02-09-2010
            //modifikasi : --------
            $txt_search = $req->getParam("txtsearch");
            return $this->searchPendingIklan($txt_search);
            
        }else{
			return $this->DaftarIklan();
		}
	}
	/**
	 * 
	 * fungsi untuk mengedit isi iklan yang masih pending
	 */
	function EditPendingAd(){
		$req = $this->Request;
		$id = $req->getParam("id");
		$iklan = $this->Inventory->getAdFromInventory($id);
		$this->View->assign("rs",$iklan);
		$this->View->assign("start",$req->getParam("st"));
		return $this->View->toString("SITTIAdmin/iklan/edit_iklan_pending.html");
	}
	function UpdateIklan(){
		global $LOCALE;
		$req = $this->Request;
		$iklan_id = $req->getPost("id");
		$nama = $req->getPost("nama");
		$judul = $req->getPost("judul");
		$baris1 = $req->getPost("baris1");
		$baris2 = $req->getPost("baris2");
		$urlLink = $req->getPost("urlLink");
		$urlName = $req->getPost("urlName");
		$category = $req->getPost("category");
		
		if($this->Inventory->updateAd($iklan_id, $nama, $judul, $category, $baris1, $baris2, $urlName, $urlLink)){
			$msg = $LOCALE['ADMIN_UPDATE_IKLAN_SUCCESS'];
		}else{
			$msg = $LOCALE['ADMIN_UPDATE_IKLAN_GAGAL'];
		}
		return $this->View->showMessage($msg,"?s=sitti&r=list&st=".$req->getPost("st"));
	}
	function EditIklan(){
		$req = $this->Request;
		$id = $req->getParam("id");
		$iklan = $this->Inventory->getAdFromInventory($id);
		$this->View->assign("rs",$iklan);
		$this->View->assign("start",$req->getParam("st"));
		return $this->View->toString("SITTIAdmin/iklan/edit_iklan.html");
	}
	/**
	 * 
	 * Listing iklan yang sudah di approve
	 * @param $total_per_page
	 * @return <<html>>
	 */
	function ListingIklan($total_per_page=50){
		$req = $this->Request;
		$start = $req->getParam("st");
	
		if($start==null){
			$start=0;
		}
		
		$list = $this->Inventory->getInventory($start, 50);
		$total_rows = $this->Inventory->found_rows;
		$this->View->assign("list",$list);
		$this->View->assign("start",$start);
		//paging
		$this->Paging = new Paginate();
		//print $this->Inventory->found_rows;
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $total_per_page, $this->Inventory->found_rows, "?s=sitti&r=list"));
		return $this->View->toString("SITTIAdmin/iklan/listing_iklan.html");
	}
	function DenyAd($reason=1){
		global $LOCALE,$CONFIG;
		$req = $this->Request;
		$iklan_id = $req->getParam("id");
		$iklan = $this->Inventory->getAdFromQueue($iklan_id);
		
		$this->Account = new SITTIAccount(&$req);
		$profile = $this->Account->getProfileBySittiID($iklan['advertiser_id']);
		
		if($this->Inventory->deleteAdFromQueue($iklan_id)){
			$this->Inventory->deleteKeywordFromQueue($iklan_id);
			//kirim email
			//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan['nama'].") Di tolak");
    		$smtp->setRecipient($profile['email']);
    		$this->View->assign("REASON",$LOCALE['AD_REJECTED'][$reason]);
    		
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_ditolak.html"));
    		$smtp->send();
    		//print $smtp->status."--->".$smtp->error;
			//-->
			$msg = "Iklan telah di tolak";
		}else{
			$msg = "Gagal mengupdate iklan. Silahkan coba kembali.";
		}
		return $this->View->showMessage($msg,"?s=sitti&st=".$req->getParam("st"));
	}
	/**
	 * 
	 * fungsi untuk ad rejection tanpa perlu menghapus ad yg bersangkutan. cukup di flag di status saja.
	 * @param $reason
	 */
	function DenyAdFlag($reason=1,$status=0){
		global $LOCALE,$CONFIG;
		$req = $this->Request;
		$iklan_id = $req->getParam("id");
		$iklan = $this->Inventory->getAdFromQueue($iklan_id);
		
		$this->Account = new SITTIAccount(&$req);
		$profile = $this->Account->getProfileBySittiID($iklan['advertiser_id']);
		
		if($this->Inventory->setAdStatusInQueue($iklan_id,$status)){
			
			//kirim email
			//kirim email notifikasi
    		$smtp = new SITTIMailer();
    		$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan['nama'].") Di tolak");
    		$smtp->setRecipient($profile['email']);
    		$this->View->assign("REASON",$LOCALE['AD_REJECTED'][$reason]);
    		
    		$smtp->setMessage($this->View->toString("SITTI/email/iklan_ditolak.html"));
    		$smtp->send();
    		//print $smtp->status."--->".$smtp->error;
			//-->
			$msg = "Iklan telah di tolak";
		}else{
			$msg = "Gagal mengupdate iklan. Silahkan coba kembali.";
		}
		return $this->View->showMessage($msg,"?s=sitti&st=".$req->getParam("st"));
	}
	function ApproveAd(){
		$req = $this->Request;
		$iklan_id = $req->getParam("id");
		//print $iklan_id;
		if($this->Inventory->moveAdFromQueue($iklan_id)){
			$msg = "Iklan telah di approve";
		}else{
			$msg = "Gagal mengupdate iklan. Silahkan coba kembali.";
		}
		return $this->View->showMessage($msg,"?s=sitti");
	}
	function HapusIklan($reason){
		global $LOCALE;
		$req = $this->Request;
		$id = $req->getParam("id");
		//detil iklan.
		$iklan = $this->Inventory->getAdFromInventory($id);
		if($req->getParam("confirm")=="1"){
		
			
			//butuh profilenya user untuk mendapatkan alamat emailnya
			$this->Account = new SITTIAccount($this->Request);
			
			$profile = $this->Account->getProfileBySittiID($iklan['advertiser_id']);
			
			
			if($this->Inventory->deleteAd($id)){
				//kirim email notifikasi
				$smtp = new SITTIMailer();
    			$smtp->setSubject("[SITTI] Pendaftaran Iklan Anda (".$iklan['nama'].") Di tolak");
    			$smtp->setRecipient($profile['email']);
    			$this->View->assign("REASON",$LOCALE['AD_REJECTED'][$reason]);
    		
    			$smtp->setMessage($this->View->toString("SITTI/email/iklan_ditolak.html"));
    			$smtp->send();
				$msg = "Iklan telah dihapus !";
			}else{
				$msg = "Tidak berhasil menghapus iklan.";
			}
			return $this->View->showMessage($msg,"index.php?s=sitti&r=list");
		}else{
			
			//pastikan di konfirmasi dulu.
			$iklan = $this->Inventory->getAdFromInventory($id);
			$msg = "<b>Nama Iklan : </b>&nbsp;&nbsp;".$iklan['nama'] ."<sup>".$iklan['id']."</sup><br/><br/>";
			$msg.= $LOCALE['ADMIN_KONFIRM_DELETE_IKLAN'];
			
			$strAction = "hapus".$reason;
			$onYes = array("label"=>"Ya","url"=>"?s=sitti&r=list&do=".$strAction."&id=".$id."&confirm=1");
			$onNo = array("label"=>"Tidak","url"=>"?s=sitti&r=list");
			return $this->View->confirm($msg, $onYes, $onNo);
		}
	}
	function DaftarKeyword(){
		$req = $this->Request;
		$id = $req->getRequest("id");
		
		//hapus keyword bila ada
		if(strlen($req->getParam("remove"))>0){
			$this->Inventory->removeKeyword($id, $req->getParam("remove"));
		}
		
		//tambah keyword bila ada.
		if($req->getPost("do")=="keyword_list"){
			for($i=0;$i<sizeof($_POST['keywords']);$i++){
				$this->Inventory->addKeyword($id, trim(stripslashes($_POST['keywords'][$i])));
			}
		}
		$iklan = $this->Inventory->getAdFromInventory($id);
		
		$keyword = explode(", ",$iklan['keywords']);
		$this->View->assign("keyword",$keyword);
		$this->View->assign("rs",$iklan);
		print $this->View->toString("SITTIAdmin/iklan/daftar_keyword.html");
		die();
	}
	function DaftarIklan($total_per_page=50){
		$req = $this->Request;
		
		$start = $req->getParam("st");
		if($start==null){
			$start=0;
		}
		$list = $this->Inventory->getPendingAdsQueue($start,$total_per_page);
		$this->View->assign("list",$list);
		//paging
		$this->Paging = new Paginate();
		//print $this->Inventory->found_rows;
		
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $total_per_page, $this->Inventory->found_rows, "?s=sitti"));
		return $this->View->toString("SITTIAdmin/iklan/daftar_iklan.html");
	}


    /*
     * PENCARIAN IKLAN PENDING BERDASARKAN ADVERTISER NO AKUN (sittiID)
     * dibuat : 02-09-2010
     * modifikasi : ---------
     */
    function searchPendingIklan($txt_search){
        $req = $this->Request;
        $start = $req->getParam("st");
        $limit = 50;
		if($start==null){
			$start=0;
		}
        if($txt_search){
            $list = $this->fetch("SELECT * FROM sitti_ad_inventory_temp
                                    WHERE advertiser_id='".$txt_search."'
                                    LIMIT ".$start.",".$limit,1);
            $this->View->assign("list",$list);
            //print_r($list);
            $view_paging = $this->fetch("SELECT COUNT(*) AS total FROM sitti_ad_inventory_temp
                                        WHERE advertiser_id='".$txt_search."' LIMIT 1");
            $search_total = $view_paging['total'];
            $base_url = "?s=sitti&do=search&txtsearch=".$txt_search."&act=GO";
            $this->Paging = new Paginate();
            $generate_paging = $this->Paging->getAdminPaging($start, $limit, $search_total, $base_url);
            $this->View->assign("paging", $generate_paging);
            $this->View->assign("hasil", "Total pencarian : ".$search_total);
            return $this->View->toString("SITTIAdmin/iklan/daftar_iklan.html");
            
        }else{
            return $this->DaftarIklan();
        }
    }



    /*
     * PENCARIAN IKLAN TAYANG BERDASARKAN ADVERTISER NO AKUN (sittiID)
     * dibuat : 02-09-2010
     * modifikasi : ---------
     */
    function searchIklan($txt_search){
        $req = $this->Request;
        $start = $req->getParam("st");
        $limit = 50;
		if($start==null){
			$start=0;
		}
        if($txt_search){
            $list = $this->fetch("SELECT * FROM sitti_ad_inventory
                                    WHERE advertiser_id='".$txt_search."'
                                    LIMIT ".$start.",".$limit,1);
            $this->View->assign("list",$list);
            //print_r($list);
            $view_paging = $this->fetch("SELECT COUNT(*) AS total FROM sitti_ad_inventory
                                        WHERE advertiser_id='".$txt_search."' LIMIT 1");
            $search_total = $view_paging['total'];
            $base_url = "?s=sitti&r=list&do=search&txtsearch=".$txt_search."&act=GO";
            $this->Paging = new Paginate();
            $generate_paging = $this->Paging->getAdminPaging($start, $limit, $search_total, $base_url);
            $this->View->assign("paging", $generate_paging);
            $this->View->assign("hasil", "Total pencarian : ".$search_total);
            return $this->View->toString("SITTIAdmin/iklan/listing_iklan.html");

        }else{
            return $this->ListingIklan();
        }
    }

    

	
}
?>