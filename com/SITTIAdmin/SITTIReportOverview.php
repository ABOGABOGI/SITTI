<?php
include_once $APP_PATH."SITTI/SITTIInventory.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $ENGINE_PATH."Utility/Paginate.php";

class SITTIReportOverview extends SQLData{
	var $strHTML;
	var $View;
	var $Inventory;
	function SITTIReportOverview($req){
		parent::SQLData();
		$this->autoconnect = false; //we set manual connection to db. because we use more than 1 database source.
		$this->View = new BasicView();
		$this->Request = $req;
		$this->Inventory = new SITTIInventory();
	}
	
	/****** End-User FUNCTIONS ********************************************/

	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		$req = $this->Request;
		if($req->getParam("r")=="keyword_ctr"){
			return $this->TOPKeywordCTR();
		}else if($req->getParam("r")=="top100_page_click"){
			return $this->Top100Pages();
		}else if($req->getParam("r")=="top100_advertisers"){
			return $this->Top10Advertisers();
		}else if($req->getParam("r")=="ad_daily"){
			return $this->AdPerformanceDaily();
		}else if($req->getParam("r")=="top100_publishers"){
			return $this->Top10Publishers();
		}else{
			return $this->GeneralSummary();
		}
	}
	function getGeneralReport(){
		$this->open(2);
		$sql = "SELECT * FROM db_report.tbl_rekap_admin";
		$general = $this->fetch($sql);
		$this->close();
		return $general;
	}
	
	function Dashboard(){
		$general = $this->getGeneralReport();
		$this->View->assign("general",$general);
		return $this->View->toString("SITTIAdmin/reporting/sitti_overview.html");
	}
	
	function GeneralSummary(){
		$req = $this->Request;
		$general = $this->getGeneralReport();
    //$n_days = 
    // Get current time 
    $n_days = datediff('d',"2010-08-10",date("Y-m-d"),false)/(60*60*24);
       
    $general['avg_imp_per_day'] = $general['imp_count'] / $n_days;
		$this->View->assign("general",$general);
		return $this->View->toString("SITTIAdmin/reporting/sitti_overview.html");
	}
	function TOPKeywordCTR($total=50){
		$req = $this->Request;
		$start = $req->getParam("st");
		if($start==NULL){
			$start=0;
		}
		$sql = "SELECT keyword, impression, click, 
				(( click / impression)*100) AS ctrz, 
				last_update
				FROM db_adlogs_adm.adm_gen_keyw
				ORDER BY ctrz DESC LIMIT ".$start.",".$total;
		$sql2 = "SELECT COUNT(*) as total
				FROM db_adlogs_adm.adm_gen_keyw 
				LIMIT 1";
		$this->open(2);
		$list = $this->fetch($sql,1);
		$foo = $this->fetch($sql2);
		
		$this->close();
		$this->View->assign("list",$list);
		$this->View->assign("total_keywords",$foo['total']);
		//paging
		$this->Paging = new Paginate();
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $total, $foo['total'], "?s=report2&r=keyword_ctr","common/admin/paging.html"));
		
		return $this->View->toString("SITTIAdmin/reporting/sitti_keyword_top_ctr.html");
	}
	function Top100Pages($total=100){
		$req = $this->Request;
		$start = $req->getParam("st");
		$the_date = $req->getParam("d");
		//-----------------------------------
		if($start==NULL){
			$start=0;
		}
    if($the_date!=null&&$the_date!="0"){
		  $sql = "SELECT capture_date, web_name, jum_klik, jum_imp, ctr , last_update
				FROM db_adlogs_adm.adm_pages_top100
				WHERE (capture_date = '".$the_date."')
				ORDER BY ctr DESC LIMIT 100";
		}else{
		  /*$sql = "SELECT web_name, jum_klik, jum_imp, ( jum_klik / jum_imp ) * 100 AS ctr
              FROM ( SELECT web_name, SUM(jum_klik) AS jum_klik, SUM(jum_imp) AS jum_imp
            FROM db_adlogs_adm.adm_pages_top100
            GROUP BY web_name
            ) a ORDER BY ctr DESC 
            LIMIT 100";*/
			$sql = "SELECT a.web_name, SUM(a.jum_imp) as jum_imp, SUM(a.jum_klik) as jum_klik
					FROM db_adslogs.sitti_top_pages a
    				GROUP BY web_name
    				ORDER BY ctr DESC
    				LIMIT 100";
    }
   // print $sql;
		//dropdown tanggal
		$date_list = array();
		$n=0;
		for($i=6;$i>=0;$i--){
			$date_list[$n] = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
			$n++;
		}
		
		$this->View->assign("date_list",$date_list);
		//-->
		
		//daftar website
		$this->open(2);
		$list = $this->fetch($sql,1);
		
		//print mysql_error();
		for($i=0;$i<sizeof($list);$i++){
			$list[$i]['ctr'] =  round(($list[$i]['jum_klik']/$list[$i]['jum_imp'])*100,2);
		}
		$this->close();
		$this->View->assign("list",$list);
		//-->

		return $this->View->toString("SITTIAdmin/reporting/sitti_top100_pages_clicks.html");
	}
	function Top10Advertisers($total=100){
		$req = $this->Request;
		$start = $req->getParam("st");
		$the_date = $req->getParam("d");
		//-----------------------------------
		if($start==NULL){
			$start=0;
		}
		if($the_date=='0'||$the_date == null){
		  /*$sql = "SELECT username, sittiID, jum_imp, jum_klik, ( jum_klik / jum_imp ) * 100 AS ctr
              FROM ( SELECT sittiID, username, SUM(jum_imp) AS jum_imp
              , SUM(jum_klik) AS jum_klik
              FROM db_adlogs_adm.adm_advertisers_top10
              GROUP BY sittiID, username
              ) a ORDER BY ctr DESC
              LIMIT 100";
              */
			$sql = "SELECT sittiID, username, jum_imp, jum_klik, ctr, last_update
					FROM db_adlogs_adm.adm_advertisers_total ORDER BY ctr DESC LIMIT 100";
		}else{
		  $sql = "SELECT capture_date, sittiID, username, jum_imp, jum_klik, ctr, last_update
				FROM db_adlogs_adm.adm_advertisers_top10
				WHERE (capture_date = '".$the_date."')
				ORDER BY ctr DESC LIMIT 10";
    }
		//dropdown tanggal
		$date_list = array();
		$n=0;
		for($i=6;$i>=0;$i--){
			$date_list[$n] = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
			$n++;
		}
		
		$this->View->assign("date_list",$date_list);
		//-->
		
		//daftar website
		$this->open(2);
		$list = $this->fetch($sql,1);
		for($i=0;$i<sizeof($list);$i++){
			$list[$i]['ctr'] =  round(($list[$i]['jum_klik']/$list[$i]['jum_imp'])*100,2);
		}
		$this->close();
		$this->View->assign("list",$list);
		//-->

		return $this->View->toString("SITTIAdmin/reporting/sitti_top100_advertiser.html");
	}
	function Top10Publishers($total=100){
		$req = $this->Request;
		$start = $req->getParam("st");
		$the_date = $req->getParam("d");
		//-----------------------------------
		if($start==NULL){
			$start=0;
		}
		
	 if($the_date!=null&&$the_date!='0'){
		$sql = "SELECT capture_date, sittiID,username, jum_klik, jum_impression, ctr, last_update
				FROM db_adlogs_adm.adm_publishers_top10
				WHERE (capture_date = '".$the_date."')
				ORDER BY ctr DESC LIMIT 10";
   }else{
     $sql = "SELECT sittiID, username, jum_klik, jum_imp as jum_impression, ( jum_klik / jum_imp ) * 100 AS ctr
            FROM ( SELECT sittiid, username
            , SUM(jum_klik) AS jum_klik
            , SUM(jum_impression) AS jum_imp
            FROM db_adlogs_adm.adm_publishers_top10
            GROUP BY sittiid, username
            ) a ORDER BY ctr DESC LIMIT 10";
   }
		//dropdown tanggal
		$date_list = array();
		$n=0;
		for($i=6;$i>=0;$i--){
			$date_list[$n] = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
			$n++;
		}
		
		$this->View->assign("date_list",$date_list);
		//-->
		
		//daftar website
		$this->open(2);
		$list = $this->fetch($sql,1);
		for($i=0;$i<sizeof($list);$i++){
			$list[$i]['ctr'] = round(($list[$i]['jum_klik']/$list[$i]['jum_impression'])*100,2);
		}
		$this->close();
		$this->View->assign("list",$list);
		//-->

		return $this->View->toString("SITTIAdmin/reporting/sitti_top100_publisher.html");
	}
	function AdPerformanceDaily($total=100){
		$req = $this->Request;
		$start = $req->getParam("st");
		$the_date = $req->getParam("d");
		//-----------------------------------
		if($start==NULL){
			$start=0;
		}
		 if($the_date!=null&&$the_date!='0'){
	
				$sql = "SELECT *,b.advertiser_id as sittiID,(t_click/t_view*100) as ctr 
				FROM db_adslogs.ps_report_daily a,db_web3.sitti_ad_inventory b 
				WHERE a.capture_date = '".$the_date."'
				AND a.iklan_id = b.id ORDER BY ctr DESC
				LIMIT ".$start.",".$total;
		
				$sql2 = "SELECT COUNT(*) as total 
				FROM db_adslogs.ps_report_daily a,
				db_web3.sitti_ad_inventory b 
				WHERE a.capture_date = '".$the_date."'
				AND a.iklan_id = b.id 
				LIMIT 1";
		 }else{
		 		$sql = "SELECT
        		a.iklan_id
       			 , b.advertiser_id as sittiID
       			 ,b.judul
        		, a.t_view 
        		, a.t_click
        		, a.ctr
    			FROM
        		db_adslogs.ps_report AS a
    			INNER JOIN db_web3.sitti_ad_inventory AS b
   				ON (a.iklan_id = b.id)
    			ORDER BY ctr DESC LIMIT ".$start.",".$total;
		
				$sql2 = "SELECT
        				COUNT(a.iklan_id) as total
    					FROM
        				db_adslogs.ps_report AS a
    					INNER JOIN db_web3.sitti_ad_inventory AS b
   	 					ON (a.iklan_id = b.id)";
		 }
		//dropdown tanggal
		$date_list = array();
		$n=0;
		for($i=6;$i>=0;$i--){
			$date_list[$n] = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
			$n++;
		}
		
		$this->View->assign("date_list",$date_list);
		//-->
		
		//daftar website
		$this->open(2);
		$list = $this->fetch($sql,1);
		$cnt = $this->fetch($sql2);
		$this->close();
		
		for($i=0;$i<sizeof($list);$i++){
			//$list[$i]['ctr'] =  round(($list[$i]['t_click']/$list[$i]['t_view'])*100,2);
		}
		
		$this->View->assign("list",$list);
		//-->
		//paging
		$this->Paging = new Paginate();
		//print $this->Inventory->found_rows;
		
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $total, $cnt['total'], "?s=report2&r=ad_daily&d=".$the_date));
		

		return $this->View->toString("SITTIAdmin/reporting/sitti_ad_daily.html");
	}
}
?>