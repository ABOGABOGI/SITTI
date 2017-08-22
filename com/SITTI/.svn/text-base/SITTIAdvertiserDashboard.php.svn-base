<?php 
/**
 * SITTIBeranda
 * class untuk halaman Beranda.
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIApp.php";
include_once "SITTIAdvertiser.php";
include_once "SITTIReporting.php";
include_once "SITTIInventory.php";
include_once "SITTICampaign.php";
include_once "SITTIBilling.php";
include_once "Model/SITTIPPAModel.php";

class SITTIAdvertiserDashboard extends StaticPage{
	var $Account;
	var $Paging;
    function SITTIAdvertiserDashboard($req,$account){
        parent::StaticPage(&$req);
        $this->Account = $account;
        
    }
    
	function Reporting1($user){
        $inventory = new SITTIInventory();
    	$reporting = new SITTIReporting($inventory);
    	$campaign = new SITTICampaign();
    	
    	if ($_SESSION['is_cpm'])
        {
            //Account Summary
            $summary = $reporting->getAdvertiserBannerAccountSummary($user['sittiID']);
            
            if ($summary)
            {
                 $this->View->assign("summary",$summary);    
            }

            return $this->View->toString("SITTIBanner/reporting/beranda_tab1.html");        
        }
        elseif ($_SESSION['is_ppa'])
        {
            $summary = $reporting->getAdvertiserPPAAccountSummary($user['sittiID']);
            
            if ($summary)
            {
                 $this->View->assign("summary",$summary);    
            }

            $campaign = new SITTICampaign();
            $campaign_list = $campaign->getCampaigns($user['sittiID']);
            $this->View->assign("list_campaign", $campaign_list);
            $this->View->assign("selected_campaign", $campaign_list[0]['ox_campaign_id']);
            
            return $this->View->toString("SITTIPPA/reporting/beranda_tab1.html");
        }
        else
        { 
            //Account Summary
            $summary = $reporting->getAdvertiserAccountSummary($user['sittiID']);
            
            if ($summary)
            {
                 $this->View->assign("summary",$summary);    
            }
            //$arr_campaign = $campaign->getCampaignList($user['sittiID'],0,50);
            $topCampaign = $reporting->getActiveAdvertiserCampaignSummary($user['sittiID'],0,5);
            
            $this->View->assign("list",$topCampaign);
            
            //performa kata kunci
            $top_key = $reporting->getAdvertiserKeywordSummary($user['sittiID'],0,5);
            $this->View->assign("top_key",$top_key);
                
            //performa Iklan
            $top_iklan = $reporting->getAdvertiserAdSummary($user['sittiID'],0,5);
            $this->View->assign("top_iklan",$top_iklan);
            //print_r($top_iklan);
            
            //performa penempatan iklan
            $placement = $reporting->getAdvertiserPlacementSummary($user['sittiID'],0,5);
            $this->View->assign("placement",$placement);
        
            return $this->View->toString("SITTI/reporting/beranda_tab1.html");
        }
        
    }
	function Reporting1New(){
        global $ENGINE_PATH, $CONFIG;
        include_once $ENGINE_PATH."Utility/rss_php.php";
    	include_once "SITTINotification.php";
      	$sitti_id = $_SESSION['sittiID'];
      	
        $notification = new SITTINotification();
      	$data = $notification->dashboardGetAlertsAndNotificationsByAdvertiserId($sitti_id);
      	
        $reporting = new SITTIReporting($inventory);
        $campaign = $reporting->getHistoricalAdvertiserCampaignSummary($sitti_id, 5);
        $this->View->assign('campaign_list', $campaign);
        $daftar_kampanye = $this->View->toString("SITTI/reporting/beranda_tab1_new_kampanyeaktif.html");
        
        $rss = new rss_php();
        $rss->load($CONFIG['SITTI_FORUM_RSSURL']);
        $items = $rss->getItems();
        $rss_items = array();
        if (is_array($items))
        {
            for ($i = 0; $i < 6; $i++)
            {
                $rss_items[$i]['title'] = $items[$i]['title'];
                $rss_items[$i]['link'] = $items[$i]['link'];
                $rss_items[$i]['description'] = $items[$i]['description'];
            }
        }

        $this->View->assign('daftar_kampanye',$daftar_kampanye);
        $this->View->assign('notif',$data);
        $this->View->assign('rss',$rss_items);

        return $this->View->toString("SITTI/reporting/beranda_tab1_new.html");
    }
	function Reporting1New_HistoricalCampaign($count){
        $sitti_id = $_SESSION['sittiID'];
        $reporting = new SITTIReporting($inventory);
        $campaign = $reporting->getHistoricalAdvertiserCampaignSummary($sitti_id, $count);
        $this->View->assign('campaign_list', $campaign);

        return $this->View->toString("SITTI/reporting/beranda_tab1_new_kampanyeaktif.html");
    }

    function Reporting2($user,$total=40){
    	global $ENGINE_PATH, $LOCALE;
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	if ($_SESSION['is_cpm'])
        {
            $rs = $reporting->getBannerCampaignPerformanceOverall($user['sittiID']);
        }
        else
        {
            $rs = $reporting->getCampaignPerformanceOverall($user['sittiID']);   
        }
    	$this->View->assign("report",$rs);
    	
    	//detailed
    	//untuk sementara di hardcode 50 rows dulu. nanti baru di pasang paging. 
    	//hapsoro.renaldy@kamisitti.com
    	$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	$this->View->assign("start",$start);
    	
        if ($_SESSION['is_cpm'])
        {
            $list = $reporting->getAdvertiserBannerCampaignSummary($user['sittiID'],$start,$total);
        }
        else
        {
            $list = $reporting->getAdvertiserCampaignSummaryDaily($user['sittiID'],$start,$total);    
        }
        
    	$total_rows = $reporting->found_rows;
    	//print $total_rows;
    	//enkrip campaign_idnya
    	for($i=0;$i<sizeof($list);$i++){
			$list[$i]['enc_campaign_id'] = urlencode64($list[$i]['campaign_id']);
    	}
		//ambil list semua kampanye dari advertiser ini
		$campaign_id_list = array();
		$list_campaign = $reporting->getAllAdvertiserCampaign($user['sittiID']);
		for($i=0;$i<sizeof($list_campaign);$i++){
			array_push($campaign_id_list,urlencode64($list_campaign[$i]['campaign_id']));
			$list_campaign[$i]['enc_campaign_id'] = urlencode64($list_campaign[$i]['campaign_id']);
    	}
		$this->View->assign("campaign_id_list",json_encode($campaign_id_list));
		$this->View->assign("campaign_list",$list_campaign);
    	$this->View->assign("list",$list);
    	//paging
    	include_once $ENGINE_PATH."Utility/Paginate.php";
    	//paging
		$this->Paging = new Paginate();
		
		$this->View->assign("paging",$this->Paging->getJsPaging($start, $total, $total_rows, "report.php?t=2","tab02"));
    	
        // Chart Messages
        $this->View->assign("chart_no_latest_30days_data", $LOCALE['CHART_NO_LATEST_30DAYS_DATA']);
        $this->View->assign("chart_no_chosen_dates_data", $LOCALE['CHART_NO_CHOSEN_DATES_DATA']);
        $this->View->assign("chart_start_date_biggerthan_end_date", $LOCALE['CHART_START_DATE_BIGGERTHAN_END_DATE']);

        if ($_SESSION['is_cpm'])
        {
            return  $this->View->toString("SITTIBanner/reporting/beranda_tab2.html");
        }
        else
        {
            $today = date("Y-m-d");
            $last_month = date("Y-m-d", strtotime("-30 days"));
            $this->View->assign("tglAwalChart",$last_month);
            $this->View->assign("tglAkhirChart",$today);
            return  $this->View->toString("SITTI/reporting/beranda_tab2.html");    
        }
        
    }
	function Reporting201($user,$tgl_awal,$tgl_akhir,$total=40){
		$reporting = new SITTIReporting($inventory);
		$start=0;
        if ($_SESSION['is_cpm']){
            $list = $reporting->getAdvertiserBannerCampaignSummary($user['sittiID'],$start,$total);
        }
        else{
            $list = $reporting->getAdvertiserCampaignSummaryDaily($user['sittiID'],$start,$total,$tgl_awal,$tgl_akhir);
        }
    	for($i=0;$i<sizeof($list);$i++){
    		$list[$i]['enc_campaign_id'] = urlencode64($list[$i]['campaign_id']);
    	}
    	return json_encode($list);
	}
    function Reporting201CSV($user,$tgl_awal,$tgl_akhir,$total=40){
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/CSVPrinter.php";
    	$reporting = new SITTIReporting($inventory);
    	$csv = new CSVPrinter("102");
    	$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	$list = $reporting->getAdvertiserCampaignSummaryDaily($user['sittiID'],$start,$total,$tgl_awal,$tgl_akhir);
    	$csv->setLabels("'Campaign Name'","impression","click","ctr","bids","'budget harian'","'budget total'");
    	$csv->setFields("campaign_name","imp","click","ctr","bids","budget_dailies","budget_totals");
    	$csv->setData($list);
    	$csv->output();
    }
	function Reporting201XLS($user,$tgl_awal,$tgl_akhir,$total=40){
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/XLSPrinter.php";
    	$reporting = new SITTIReporting($inventory);
    	$xls = new XLSPrinter("102");
    	$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	$list = $reporting->getAdvertiserCampaignSummaryDaily($user['sittiID'],$start,$total,$tgl_awal,$tgl_akhir);
    	$xls->setLabels("No","Campaign Name","Impression","Click","CTR","Bids","Budget Harian","Budget Total");
    	$xls->setFields("campaign_name","imp","click","ctr","bids","budget_dailies","budget_totals");
    	$xls->setData($list);
    	$xls->output();
    }
	
	function Reporting3($user){
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	$rs = $reporting->getCampaignPerformanceOverall($user['sittiID']);
    	$this->View->assign("report",$rs);
    	
    	//detailed
    	//untuk sementara di hardcode 50 rows dulu. nanti baru di pasang paging. 
    	//hapsoro.renaldy@kamisitti.com
    	$list = $reporting->getAdvertiserCampaignSummary($user['sittiID'],0,50);
    	$this->View->assign("list",$list);
    	
    	return  $this->View->toString("SITTI/reporting/beranda_tab3.html");
    }
	
    function Reporting4($user,$c_id,$total=50){
    	global $ENGINE_PATH;
    	$reporting = new SITTIReporting($inventory);
    	//detailed
    	$start = $this->Request->getParam("st");
		if($start==null){$start=0;}
		$this->View->assign("start",$start);
		$this->View->assign("c_id",$c_id);
    	//summary
    	$rs = $reporting->getKeywordPerformanceOverall($user['sittiID']);
    	$this->View->assign("report",$rs);
    	$inventory = new SITTIInventory();
		$flagged_keywords = $inventory->getFlaggedKeywords($user['sittiID']);
    	
    	$len_flag = sizeof($flagged_keywords);
    	$flagged_word = array();
    	for($i=0;$i<$len_flag;$i++){
			array_push($flagged_word,trim($flagged_keywords[$i]['keyword']));
    	}
    	
    	if($len_flag>0){
    		$this->View->assign("UpdateInProgress","1");
    	}
		
		//list campaign
		$campaign = new SITTICampaign();
		$campaign_list = $campaign->getCampaignList($user['sittiID'],0,30);
		for($i=0;$i<sizeof($campaign_list);$i++){
			$campaign_list[$i]['enc_campaign_id'] = urlencode64($campaign_list[$i]['ox_campaign_id']);
    	}
		$this->View->assign("list_campaign",$campaign_list);
		
    	//detailed
    	//untuk sementara di hardcode 50 rows dulu. nanti baru di pasang paging. 
    	//hapsoro.renaldy@kamisitti.com
    	if($c_id=='none'){
			$top_key = $reporting->getAdvertiserKeywordSummary($user['sittiID'],$start,$total);
		}else{
			$top_key = $reporting->getAdvertiserKeywordSummaryByCampaign($user['sittiID'],$start,$total,urldecode64($c_id));
		}
		$len_key = sizeof($top_key);
    	for($i=0;$i<$len_key;$i++){
    		if(in_array($top_key[$i]['keyword'],$flagged_word)){
    			$top_key[$i]['in_progress'] = 1;
    		}
    	}
        $total_rows = $reporting->found_rows;
    	//paging
    	include_once $ENGINE_PATH."Utility/Paginate.php";
    	//paging
		$this->Paging = new Paginate();
		
		if($c_id=='none'){
			$this->View->assign("paging",$this->Paging->getJsPaging($start, $total, $total_rows, "report.php?t=4","tab04"));
		}else{
			$this->View->assign("paging",$this->Paging->getJsPaging($start, $total, $total_rows, "report.php?t=4&c_id=".$c_id,"tab04"));
		}
		
    	$this->View->assign("top_key",$top_key);
    	
    	return  $this->View->toString("SITTI/reporting/beranda_tab4.html");
    }
	function Reporting4Status($user,$c_id){
    	global $ENGINE_PATH;
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	$rs = $reporting->getKeywordPerformanceOverall($user['sittiID']);
    	$this->View->assign("report",$rs);
		$inventory = new SITTIInventory();
    	$flagged_keywords = $inventory->getFlaggedKeywords($user['sittiID']);
    	$len_flag = sizeof($flagged_keywords);
    	
    	if($len_flag>0){
    		print '1';
    	}else{
    		print '0';
    	}
    	
    }
 	function Reporting4CSV($user,$c_id,$total=50){
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/CSVPrinter.php";
    	$reporting = new SITTIReporting($inventory);

    	$csv = new CSVPrinter("104");
 		$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
		if($c_id=='none'){
			$list = $reporting->getAdvertiserKeywordSummary($user['sittiID'],$start,$total,urldecode64($c_id));
		}else{
			$list = $reporting->getAdvertiserKeywordSummaryByCampaign($user['sittiID'],$start,$total,urldecode64($c_id));
		}
    	$csv->setLabels("keyword","imp","click","ctr");
    	$csv->setFields("keyword","imp","click","ctr");
    	$csv->setData($list);
    	$csv->output();
    }
	
	function Reporting6($user,$campaign_id = false,$total=100){
		
		global $ENGINE_PATH, $LOCALE;
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	if ($_SESSION['is_cpm'])
        {
            $rs = $reporting->getBannerPerformanceOverall($user['sittiID']);    
        }
        else
        {
            $rs = $reporting->getAdPerformanceOverall($user['sittiID'], $campaign_id);    
        }
        
    	$this->View->assign("report",$rs);
		$inventory = new SITTIInventory();
    	$flagged_ads = $inventory->getFlaggedAds($user['sittiID'], $campaign_id);
    	
    	$len_flag = sizeof($flagged_ads);
    	$flagged_id = "";
    	for($i=0;$i<$len_flag;$i++){
    			$flagged_id.=",";
    			$flagged_id.=trim($flagged_ads[$i]['id']);
    	}
    	
    	if($len_flag>0){
    		$this->View->assign("UpdateInProgress","1");
    	}
    	//detailed
    	$start = $this->Request->getParam("st");
		if($start==null){$start=0;}
		$this->View->assign("start",$start);
    	
        if ($_SESSION['is_cpm'])
        {
            $top_iklan = $reporting->getAdvertiserBannerAdSummary($user['sittiID'],$start,$total);
            foreach ($top_iklan as $idx => $iklan)
            {
                $top_iklan[$idx]['categories'] = ($iklan['kategori'] != '') ? explode(",", $iklan['kategori']) : array();
            }
        }
        else
        {    
            $top_iklan = $reporting->getAdvertiserAdSummaryDaily($user['sittiID'],$campaign_id,$start,$total,$tgl_awal,$tgl_akhir);
            if(sizeof($top_iklan)>0){
	            foreach ($top_iklan as $idx => $iklan)
	            {
	                $top_iklan[$idx]['keywords'] = ($iklan['keywords'] != '') ? explode(",", $iklan['keywords']) : array();
	            }
            }
        }

    	$len_iklan = sizeof($top_iklan);
    	$n_progress = 0;
    	for($i=0;$i<$len_iklan;$i++){
    		if(eregi("\,".$top_iklan[$i]['id'],$flagged_id)){
    			$top_iklan[$i]['in_progress'] = 1;
    		}
			$top_iklan[$i]['enc_id'] = urlencode64($top_iklan[$i]['id']);
    	}
		//ambil list semua iklan dari advertiser pada sebuah kampanye
		$ad_id_list = array();
		$list_ad = $reporting->getAllAdvertiserAdInCampaign($user['sittiID'],$campaign_id);
		if(sizeof($list_ad)>0){
			foreach ($list_ad as $idx => $iklan){
				$list_ad[$idx]['keywords'] = ($iklan['keywords'] != '') ? explode(",", $iklan['keywords']) : array();
			}
		}
		for($i=0;$i<sizeof($list_ad);$i++){
			array_push($ad_id_list,urlencode64($list_ad[$i]['id']));
			if(eregi("\,".$list_ad[$i]['id'],$flagged_id)){
    			$list_ad[$i]['in_progress'] = 1;
    		}
			$list_ad[$i]['enc_id'] = urlencode64($list_ad[$i]['id']);
    	}
		$this->View->assign("ad_id_list",json_encode($ad_id_list));
		$this->View->assign("ad_list",$list_ad);
    	$total_rows = $reporting->found_rows;
    	//paging
    	include_once $ENGINE_PATH."Utility/Paginate.php";
    	//paging
		$this->Paging = new Paginate();
		
		$this->View->assign("paging",$this->Paging->getJsPaging($start, $total, $total_rows, "report.php?t=6","tab06"));
    	$this->View->assign("top_iklan",$top_iklan);
    	$this->View->assign("sittiID",$user['sittiID']);
    	
        // Chart Messages
        $this->View->assign("chart_no_latest_30days_data", $LOCALE['CHART_NO_LATEST_30DAYS_DATA']);
        $this->View->assign("chart_no_chosen_dates_data", $LOCALE['CHART_NO_CHOSEN_DATES_DATA']);
        $this->View->assign("chart_start_date_biggerthan_end_date", $LOCALE['CHART_START_DATE_BIGGERTHAN_END_DATE']);

        if ($_SESSION['is_cpm'])
        {
            return  $this->View->toString("SITTIBanner/reporting/beranda_tab6.html");
        }
        else
        {
            $today = date("Y-m-d");
            $last_month = date("Y-m-d", strtotime("-30 days"));
            $campaign = new SITTICampaign();
            $campaign_list = $campaign->getCampaigns($user['sittiID']);
            foreach($campaign_list as $idx=>$val){
            	$campaign_list[$idx]['enc_campaign_id'] = urlencode64($val['ox_campaign_id']);
            }
            if ($campaign_id) 
            {
                $campaign_info = $campaign->getCampaignInfo($campaign_id);
                $this->View->assign("selected_campaign",mysql_escape_string(urlencode64($campaign_id)));
                $this->View->assign("campaign_name",$campaign_info['name']);
            }
            $this->View->assign("tglAwalChart",$last_month);
            $this->View->assign("tglAkhirChart",$today);
            $this->View->assign("list_campaign",$campaign_list);
            return  $this->View->toString("SITTI/reporting/beranda_tab6.html"); 
        }
    }
	function Reporting602($user,$campaign_id,$tgl_awal,$tgl_akhir,$total=40){
		$reporting = new SITTIReporting($inventory);
		$inventory = new SITTIInventory();
		
		$flagged_ads = $inventory->getFlaggedAds($user['sittiID'], $campaign_id);
    	
    	$len_flag = sizeof($flagged_ads);
    	$flagged_id = "";
    	for($i=0;$i<$len_flag;$i++){
    			$flagged_id.=",";
    			$flagged_id.=trim($flagged_ads[$i]['id']);
    	}
    	
    	if($len_flag>0){
    		$this->View->assign("UpdateInProgress","1");
    	}
		$start=0;
    	
        if ($_SESSION['is_cpm'])
        {
            $top_iklan = $reporting->getAdvertiserBannerAdSummary($user['sittiID'],$start,$total);
            foreach ($top_iklan as $idx => $iklan)
            {
                $top_iklan[$idx]['categories'] = ($iklan['kategori'] != '') ? explode(",", $iklan['kategori']) : array();
            }
        }
        else
        {    
            $top_iklan = $reporting->getAdvertiserAdSummaryDaily($user['sittiID'],$campaign_id,$start,$total,$tgl_awal,$tgl_akhir);
            if(sizeof($top_iklan)>0){
	            foreach ($top_iklan as $idx => $iklan)
	            {
	                $top_iklan[$idx]['keywords'] = ($iklan['keywords'] != '') ? explode(",", $iklan['keywords']) : array();
	            }
            }
        }

    	$len_iklan = sizeof($top_iklan);
    	$n_progress = 0;
    	for($i=0;$i<$len_iklan;$i++){
    		if(eregi("\,".$top_iklan[$i]['id'],$flagged_id)){
    			$top_iklan[$i]['in_progress'] = 1;
    		}
			$top_iklan[$i]['enc_id'] = urlencode64($top_iklan[$i]['id']);
    	}
    	return json_encode($top_iklan);
	}
    /**
     * method untuk mendapatkan status update untuk Reporting performa iklan.
     * @param $user
     */
    function Reporting6Status($user,$campaign_id){
    	global $ENGINE_PATH;
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	$rs = $reporting->getAdPerformanceOverall($user['sittiID']);
    	$this->View->assign("report",$rs);
		$inventory = new SITTIInventory();
    	$flagged_ads = $inventory->getFlaggedAds($user['sittiID'], $campaign_id);
    	$len_flag = sizeof($flagged_ads);
    	
    	
    	if($len_flag>0){
    		print '1';
    	}else{
    		print '0';
    	}
    	
    }
    function Reporting6CSV($user,$campaign_id = false,$total=100){ 
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/CSVPrinter.php";
    	$reporting = new SITTIReporting($inventory);
    	$csv = new CSVPrinter("106");
		$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	
    	$list = $reporting->getAdvertiserAdSummary($user['sittiID'],$campaign_id,$start,$total);
    	
    	for ($i=0;$i<count($list);$i++){
 		  	//$list_nama=$list[$i][nama];
            //$list[$i][nama] = str_replace('"','"', $list[$i][nama]);
    		$list[$i][keywords] = str_replace(",",";", $list[$i][keywords]);
    	}
    	$csv->setLabels("nama","keywords","imp","click","ctr","bids","budget harian","budget total");
    	$csv->setFields("nama","keywords","imp","click","ctr","bids","budget_dailies","budget_totals"); 
		$csv->setData($list);
	   	$csv->output();
    	
    }
	function Reporting602CSV($user,$campaign_id = false, $tgl_awal, $tgl_akhir, $total=100){ 
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/CSVPrinter.php";
    	$reporting = new SITTIReporting($inventory);
    	$csv = new CSVPrinter("106");
		$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	
    	$list = $reporting->getAdvertiserAdSummaryDaily($user['sittiID'],$campaign_id,$start,$total,$tgl_awal,$tgl_akhir);
    	
    	for ($i=0;$i<count($list);$i++){
 		  	//$list_nama=$list[$i][nama];
            //$list[$i][nama] = str_replace('"','"', $list[$i][nama]);
    		$list[$i]['keywords'] = str_replace(",",";", $list[$i]['keywords']);
    	}
    	$csv->setLabels("nama","keywords","imp","click","ctr","bids","budget harian","budget total");
    	$csv->setFields("nama","keywords","imp","click","ctr","bids","budget_dailies","budget_totals"); 
		$csv->setData($list);
	   	$csv->output();
    	
    }
	function Reporting602XLS($user,$campaign_id = false, $tgl_awal, $tgl_akhir, $total=100){ 
    	global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/XLSPrinter.php";
    	$reporting = new SITTIReporting($inventory);
    	$xls = new XLSPrinter("106");
		$start = $this->Request->getParam("st");
    	if($start==null){$start=0;}
    	
    	$list = $reporting->getAdvertiserAdSummaryDaily($user['sittiID'],$campaign_id,$start,$total,$tgl_awal,$tgl_akhir);
    	
    	for ($i=0;$i<count($list);$i++){
 		  	//$list_nama=$list[$i][nama];
            //$list[$i][nama] = str_replace('"','"', $list[$i][nama]);
    		$list[$i]['keywords'] = str_replace(",",";", $list[$i]['keywords']);
    	}
    	$xls->setLabels("No","nama","keywords","imp","click","ctr","bids","budget harian","budget total");
    	$xls->setFields("nama","keywords","imp","click","ctr","bids","budget_dailies","budget_totals"); 
		$xls->setData($list);
	   	$xls->output();
    	
    }
	function Reporting6_ByCampaign($campaign_id = false){
        global $ENGINE_PATH;
        $reporting = new SITTIReporting($inventory);

        $inventory = new SITTIInventory();
        $flagged_ads = $inventory->getFlaggedAds($user['sittiID'], $campaign_id);
        
        $len_flag = sizeof($flagged_ads);
        $flagged_id = "";
        for($i=0;$i<$len_flag;$i++){
                $flagged_id.=",";
                $flagged_id.=trim($flagged_ads[$i]['id']);
        }

        $top_iklan = $reporting->getAdvertiserAdSummary($_SESSION['sittiID'], $campaign_id);
        
        foreach ($top_iklan as $idx => $iklan)
        {
            $top_iklan[$idx]['keywords'] = ($iklan['keywords'] != '') ? explode(",", $iklan['keywords']) : array();
        }

        $len_iklan = sizeof($top_iklan);
        $n_progress = 0;
        for($i=0;$i<$len_iklan;$i++){
            if(eregi("\,".$top_iklan[$i]['id'],$flagged_id)){
                $top_iklan[$i]['in_progress'] = 1;
            }
        }

        $this->View->assign("top_iklan",$top_iklan);
        return  $this->View->toString("SITTI/reporting/beranda_tab6_bycampaign.html");
    }

    function getAdDailyReport($user,$iklan_id,$type=0){
    	$iklan_id = mysql_escape_string($iklan_id);
    	$type = mysql_escape_string($type);
    	$inventory = new SITTIInventory();
    	$iklan = $inventory->getAdDetail($iklan_id, $user['sittiID']);
    	
        $startFrom = $this->Request->getParam("startFrom");
    	$endTo = $this->Request->getParam("endTo");
    	$this->View->assign("iklan",$iklan);
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	
    	$rs = $reporting->getDailyAdStatistic($user['sittiID'], $iklan_id,$type,$this->Request->getParam('startFrom'),$this->Request->getParam('endTo'));	
    	
    	$this->View->assign("list",$rs);
    	$this->View->assign("iklan_id",$iklan_id);
        $this->View->assign("campaign_id",$this->Request->getParam("campaign_id"));
    	$this->View->assign("sittiID",$user['sittiID']);
    	return $this->View->toString("SITTI/reporting/ad_daily.html");
    }
	
	function getAdDailyReportCSV($user,$iklan_id,$type=0){
		global $ENGINE_PATH;
    	include_once $ENGINE_PATH."Utility/CSVPrinter.php";
    	$iklan_id = mysql_escape_string($iklan_id);
    	$type = mysql_escape_string($type);
    	$inventory = new SITTIInventory();
		$csv = new CSVPrinter("106b");
    	$reporting = new SITTIReporting($inventory);
    	//summary
    	$rs = $reporting->getDailyAdStatistic($user['sittiID'], $iklan_id, $type,$this->Request->getParam('startFrom'),$this->Request->getParam('endTo'));
    	
    	$csv->setLabels("tanggal","impresi","klik","ctr");
    	$csv->setFields("tanggal","imp","click","ctr");
    	$csv->setData($rs);
    	$csv->output();
    	
    }
    
	function getCampaignDailyChartData($user, $tgl_awal = false, $tgl_akhir = false){
    	$reporting = new SITTIReporting($inventory);
    	if ($_SESSION['is_cpm'])
        {
            $list = $reporting->getBannerCampaignDailyTotal($user['sittiID']);
        }
        else
        {
            $list = $reporting->getCampaignDailyTotal($user['sittiID'], $tgl_awal, $tgl_akhir);    
        }
        
    	#$this->View->assign("list",$list);
    	#return $this->View->toString("SITTI/reporting/xml_daily_campaign.xml");
    	return json_encode($list);
    }
    
	function getDailyAdChart($user,$iklan_id,$n=0, $campaign_id = false, $tgl_awal = false, $tgl_akhir = false){
    	$iklan_id = mysql_escape_string($iklan_id);
    	$reporting = new SITTIReporting($inventory);
    	if($n==0){
    		$n=5;
    	}else if($n>10){
    		$n=10;
    	}
    	//summary
    	if ($_SESSION['is_cpm'])
        {
            $list = $reporting->getTop5BannerAdsDaily($user['sittiID'],$n);
        }
        else
        {
            $list = $reporting->getTop5AdsDaily($user['sittiID'],$n, $campaign_id, $tgl_awal, $tgl_akhir);
    	}
        //$this->View->assign("list",$list);
    	//return $this->View->toString("SITTI/reporting/xml_ad_daily.xml");
    	return json_encode($list);
    
    }
	
	function getKeywordChart($user,$n=null,$c_id){
    	
    	$reporting = new SITTIReporting($inventory);
    	if($n!=null&&eregi('([0-9]+)',$n)){
    		$total = $n;	
    	}else{
    		$total=5;
    	}
    	//summary
		if($c_id=='none'){
			$list = $reporting->getTopKeywordChartData($user['sittiID'],$total);
		}else{
			$list = $reporting->getTopKeywordChartDataByCampaign($user['sittiID'],$total,urldecode64($c_id));
		}
    	
    	return json_encode($list);
    
    }
	
	function getGeoChart($user,$iklan_id){
    	
    	$reporting = new SITTIReporting($inventory);
    	/*if($n!=null&&eregi('([0-9]+)',$n)){
    		$total = $n;	
    	}else{
    		$total=5;
    	}*/
    	//summary
    	$list = $reporting->getGeoChartData($user['sittiID'],$iklan_id);
    	return json_encode($list);
    
    }
    
	function infoTopup($user){
    	return $this->View->toString("SITTI/reporting/beranda_tab9.html");
    }
    
	/**
     * 
     * Tab informasi penagihan
     * @param $user
     */
	function Reporting8($user){
		$billing  = new SITTIBilling($this->Request, $user);
    	return $billing->Summary();
    }

    /**
    *
    * Data untuk tab konversi
    *
    */
    function ReportKonversiCampaigns($user){
        $inventory = new SITTIInventory();
        $reporting = new SITTIReporting($inventory);

        $data = $reporting->getPPACampaign($user['sittiID']);
        if ($data)
        {
            $this->View->assign("list",$data);
        }

        global $CONFIG;
            
        $this->View->assign("ppa_url",$CONFIG['SITTI_PPA_SERVER']);
        $landing_script = $this->View->toString("SITTI/ads/popup_ppa_landing_script.html");
        $checkout_script = $this->View->toString("SITTI/ads/popup_ppa_checkout_script.html");
        $thanks_script = $this->View->toString("SITTI/ads/popup_ppa_thanks_script.html");
        
        $this->View->assign("landing_script",htmlentities($landing_script));
        $this->View->assign("checkout_script",htmlentities($checkout_script));
        $this->View->assign("payment_script",htmlentities($thanks_script));

        $ppa = new SITTIPPAModel($user['sittiID']);
        $ppa_profile = $ppa->getProfile();
        if ($ppa_profile)
        {
            $this->View->assign('jenis_site', $ppa_profile['jenis_site']);
            $this->View->assign('url_site', $ppa_profile['url_site']);
            $this->View->assign('update_ppa_profile', '1');
        }
        
        if (! $ppa->isKeyExist())
        {
            $ppa->generateKey();
        }
        $this->View->assign('api_key', $ppa->getKey());
        
        $popup_ppa = $this->View->toString("SITTI/ads/popup_ppa.html");
        $this->View->assign("popup_ppa",$popup_ppa);

        return $this->View->toString("SITTI/reporting/beranda_tab_konversi.html");
    }

    function ReportKonversiAdvs($user, $campaign_id){
        $inventory = new SITTIInventory();
        $reporting = new SITTIReporting($inventory);

        $campaign = new SITTICampaign();
        $campaign_detail = $campaign->getCampaignByOwner($user['sittiID'], $campaign_id);
        $this->View->assign("campaign_name",$campaign_detail['name']);

        $data = $reporting->getPPAAdvs($campaign_id);
        if ($data)
        {
            $this->View->assign("list",$data);
        }

        return $this->View->toString("SITTI/reporting/beranda_tab_konversi_iklan.html");
    }

    function ReportKonversiTransactions($user, $iklan_id){
        $inventory = new SITTIInventory();
        $reporting = new SITTIReporting($inventory);

        $query = "SELECT * 
                    FROM db_web3.sitti_ad_inventory 
                    WHERE id ='". $iklan_id ."'
                    AND advertiser_id = '". $user['sittiID'] ."'";
        
        $this->open(0);
        $ad_detail = $this->fetch($query);
        $this->close();

        $this->View->assign("ad_name",$ad_detail['nama']);
        $data = $reporting->getPPATrans($iklan_id);
        if ($data)
        {
            $this->View->assign("list",$data);
            $total_konversi = 0;
            foreach ($data as $trans)
            {
            	if($trans['nilai_konversi']>0){
                $total_konversi += intval($trans['nilai_konversi']);
            	}else{
            	$total_konversi += intval($trans['nilai_komisi']);
            	}
            }
            $this->View->assign("total_konversi",$total_konversi);    
        }

        return $this->View->toString("SITTI/reporting/beranda_tab_konversi_trans.html");
    }

    function ChartDailyKonversiAds($campaign_id, $sitti_id = false){
        if (! $sitti_id) $sitti_id = $_SESSION['sittiID'];

        $inventory = new SITTIInventory();
        $reporting = new SITTIReporting($inventory);

        return json_encode($reporting->getDailyPPAKonversi($sitti_id, $campaign_id));
    }

    function ReportPerformaIklanPPA($sitti_id = false){
        if (! $sitti_id) $sitti_id = $_SESSION['sittiID'];

        $reporting = new SITTIReporting($inventory);
        $data = $reporting->getPerformaIklanPPA($sitti_id);

        $this->View->assign("list",$data);
        return $this->View->toString("SITTIPPA/reporting/beranda_tab_performa_iklan.html");
    }

}
?>
