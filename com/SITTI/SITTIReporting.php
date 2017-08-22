<?php
/**
 * SITTIInventory
 * class yang menghandle pembuatan iklan, manajemen iklan,
 * dan hal2 lain yg berkaitan dengan inventory iklan advertiser
 *
 */

class SITTIReporting extends SQLData{
	var $ox_campaign_id;
	var $Inventory;
	var $found_rows=0;
	function SITTIReporting($objInventory){
		parent::SQLData();
		$this->Inventory = $objInventory;
	}
	
	function getAdCounts($profile){
		global $APP_PATH,$OX_CONFIG;
		//buat campaign id di openx
		include_once $APP_PATH."kana/SITTI_OX_RPC.php";
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
		$ox->logon();
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$start = strftime("%Y-%m-%d",strtotime("2010-08-01"));
		$stats = $ox->getAdvertiserStatistic($profile['ox_adv_id'],$start,$tommorow);

		$ox->logout();
		return $stats;
	}
	function getBannerStats($profile){
		global $APP_PATH,$OX_CONFIG;
		//buat campaign id di openx
		include_once $APP_PATH."kana/SITTI_OX_RPC.php";
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
		$ox->logon();
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$start = strftime("%Y-%m-%d",strtotime("2010-08-01"));
		$stats = $ox->getAdvBannerStats($profile['ox_adv_id'],$start,$tommorow);
		$ox->logout();
		return $stats;
	}
	function getCampaignBannerStats($campaignID){
		global $APP_PATH,$OX_CONFIG;
		//buat campaign id di openx
		include_once $APP_PATH."kana/SITTI_OX_RPC.php";
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
		$ox->logon();
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$start = strftime("%Y-%m-%d",strtotime("2010-08-01"));
		$stats = $ox->getCampaignBannerStatistic($campaignID,$start,$tommorow);
		$ox->logout();
			
		//datanya dilengkapi
		$n = sizeof($stats);
		for($i=0;$i<$n;$i++){
			$stats[$i]['ctr'] = round($stats[$i]['clicks']/$stats[$i]['impressions'] * 100);
		}
		return $stats;
	}
	function getCampaignPublisherStatistic($campaignID){
		global $APP_PATH,$OX_CONFIG;
		//buat campaign id di openx
		include_once $APP_PATH."kana/SITTI_OX_RPC.php";
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
		$ox->logon();
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$start = strftime("%Y-%m-%d",strtotime("2010-08-01"));
		$stats = $ox->getCampaignPublisherStatistic($campaignID,$start,$tommorow);
		$ox->logout();
			
		//datanya dilengkapi
		$n = sizeof($stats);
		for($i=0;$i<$n;$i++){
			$stats[$i]['ctr'] = round($stats[$i]['clicks']/$stats[$i]['impressions'] * 100);
		}
		return $stats;
	}
	function getBannerSummary($campaign_id,$start=0,$total=100){
		//get all the banners first
		$this->open();

		$list = $this->fetch("SELECT id as iklan_id,nama FROM sitti_ad_inventory WHERE ox_campaign_id='".$campaign_id."' ORDER BY iklan_id ASC LIMIT ".$start.",".$total,1);
		$this->close();
			
		$n=sizeof($list);
		$iklan_id="";

		for($i=0;$i<$n;$i++){
			if($i!=0){
				$iklan_id.=",";
			}
			$iklan_id .= "'".$list[$i]['iklan_id']."'";

		}
		//stats summary
		//print "SELECT iklan_id,t_view as impressions,t_click as clicks FROM db_adslogs.adlog WHERE iklan_id IN (".$iklan_id.") ORDER BY iklan_id";
		$this->open(1);
		$stats = $this->fetch("SELECT iklan_id,t_view as impressions,t_click as clicks FROM db_adslogs.adlog WHERE iklan_id IN (".$iklan_id.") ORDER BY iklan_id",1);
		$this->close();
		$k = sizeof($stats);
		$l=0;
		//print_r($stats);
		for($i=0;$i<$k;$i++){
			if($stats[$i]['iklan_id']==$list[$i]['iklan_id']){
				$banners[$l] = $list[$i];
				$banners[$l]['impressions'] = $stats[$i]['impressions'];
				$banners[$l]['clicks'] = $stats[$i]['clicks'];
				$banners[$l]['ctr'] = round(($stats[$i]['clicks']/$stats[$i]['impressions'])*100,2);
				$l++;
			}
		}
		return $banners;
	}
	/**
	 * Campaign Summary
	 * @param $sittiID
	 * @param $campaign list of advertiser's campaign
	 */
	function getCampaignSummary($sittiID,$campaign){
			
		$n = sizeof($campaign);
		//retrieve all the banners first
		if($sittiID!=null){
			for($i=0;$i<$n;$i++){
					
				$iklan = $this->Inventory->getAdvertiserAdByCampaignID($campaign[$i]['ox_campaign_id'],$sittiID);
				$impressions = 0;
				$clicks = 0;
				$ctr = 0;
				$m = sizeof($iklan);
				$this->open(1);
				for($j=0;$j<$m;$j++){
					$rs = $this->fetch("SELECT * FROM db_adslogs.ps_report WHERE iklan_id='".$iklan[$j]['id']."' LIMIT 1");
					$impressions+=$rs['t_view'];
					$clicks+=$rs['t_click'];
				}
				$this->close();
				if($impressions!=0){
					$ctr = round(($clicks/$impressions)*100,2);
				}
				$campaign[$i]['no'] = $i+1;
				$campaign[$i]['impressions'] = $impressions;
				$campaign[$i]['clicks'] = $clicks;
				$campaign[$i]['ctr'] = $ctr;
			}
		}
		//-->
		return $campaign;
	}
/**
	 * Campaign Summary ver 2
	 * @param $sittiID
	 * @param $campaign list of advertiser's campaign
	 */
	function getAdvertiserCampaignSummary($sittiID,$start=0,$total=20,$active_only = false){
			//$sittiID = "AC0000181";
		$active_only_query = (bool) $active_only ? " AND a.status = 0 " : "";
		$this->open(2);
		//kita harus inner join data report dengan data di web3, agar nama kampanyenya tetap refresh/sinkron.
		$sql = "SELECT a.advertiser_id,a.kampanye_id as campaign_id,b.name as campaign_name,
				a.jum_imp as imp,a.jum_klik as click,a.average_cpm,a.harga as bids,a.posisi as rank,
				a.budget_harian as budget_dailies,
				a.budget_total as budget_totals,a.budget_sisa,a.ctr,a.avg_cpc,a.last_update,a.status 
				FROM db_report.tbl_performa_akun_kampanye_total a INNER JOIN db_web3.sitti_campaign b
				ON a.kampanye_id = b.ox_campaign_id
				WHERE a.advertiser_id='".$sittiID."' AND status <> 2 
				". $active_only_query ."
				ORDER BY ctr DESC 
				LIMIT ".$start.",".$total;
		// print $sql;
	    $sql2 = "SELECT COUNT(kampanye_id) as total
				FROM db_report.tbl_performa_akun_kampanye_total 
				WHERE advertiser_id='".$sittiID."' AND status <> 2 
				". $active_only_query ."
				LIMIT 1";				
		
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		$this->found_rows = $rows['total'];
		
		$this->close();
		$n = sizeof($rs);
		
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			$rs[$i]['avg_cpc'] = number_format(round($rs[$i]['avg_cpc'],2));
		}
		
		return $rs;
	}
	
	function getAllAdvertiserCampaign($sittiID){
		$this->open(2);
		$sql = "SELECT kampanye_id AS campaign_id,kampanye AS campaign_name,status,last_update,a.harga AS bids,a.budget_harian AS budget_dailies,a.budget_total AS budget_totals
				FROM db_report.tbl_performa_akun_kampanye_total a
				WHERE advertiser_id='".$sittiID."' AND STATUS <> 2 
				ORDER BY kampanye_id DESC";
		
		$rs = $this->fetch($sql,1);
		$this->close();
		return $rs;
	}
	
	function getAdvertiserCampaignSummaryDaily($sittiID,$start=0,$total=20,$tgl_awal = false,$tgl_akhir = false, $active_only = false){
			//$sittiID = "AC0000181";
		$active_only_query = (bool) $active_only ? " AND a.status = 0 " : "";
		if (! $tgl_awal && ! $tgl_akhir)
		{
			$tgl_awal = date("Y-m-d", strtotime("-30 days"));
			$tgl_akhir = date("Y-m-d");
		}
		$c_id = "";
		$rs = $this->getAllAdvertiserCampaign($sittiID);
		for($i=0;$i<sizeof($rs);$i++){
			if ($c_id!=""){
				$c_id .=",";
			}
			$c_id .="'".$rs[$i]['campaign_id']."'";
		}
		$temp = $rs;
		$this->open(2);
		$sql = "SELECT a.advertiser_id,a.kampanye_id AS campaign_id,b.name AS campaign_name,
				SUM(a.jum_imp) AS imp,SUM(a.jum_klik) AS click,a.average_cpm,a.harga AS bids,a.posisi AS rank,
				a.budget_harian AS budget_dailies,
				a.budget_total AS budget_totals,a.budget_sisa,IFNULL(SUM(a.jum_klik)*100/SUM(a.jum_imp),0) AS ctr,IFNULL(SUM(a.total_cpc)/SUM(a.jum_klik),0) AS avg_cpc,a.last_update,a.status 
				FROM db_report.tbl_performa_akun_kampanye a INNER JOIN db_web3.sitti_campaign b
				ON a.kampanye_id = b.ox_campaign_id
				WHERE a.advertiser_id='".$sittiID."' AND status <> 2 AND a.kampanye_id IN (".$c_id.")
				". $active_only_query ." 
				AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				GROUP BY campaign_id 
				ORDER BY campaign_id DESC
				LIMIT ".$start.",".$total;
		// print $sql;
	    $sql2 = "SELECT COUNT(DISTINCT(kampanye_id)) as total
				FROM db_report.tbl_performa_akun_kampanye a
				WHERE advertiser_id='".$sittiID."' AND status <> 2 AND kampanye_id IN (".$c_id.")
				". $active_only_query ." 
				AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				LIMIT 1";				
		
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		$this->found_rows = $rows['total'];
		
		$this->close();
		$n = sizeof($rs);
		
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			$rs[$i]['avg_cpc'] = number_format(round($rs[$i]['avg_cpc'],2));
			for($j=0;$j<sizeof($temp);$j++){
				if($rs[$i]['campaign_id']==$temp[$j]['campaign_id']){
					$rs[$i]['bids'] = $temp[$j]['bids'];
					$rs[$i]['budget_dailies'] = $temp[$j]['budget_dailies'];
					$rs[$i]['budget_totals'] = $temp[$j]['budget_totals'];
				}
			}
		}
		
		return $rs;
	}

	function getHistoricalAdvertiserCampaignSummary($sitti_id, $total = false){
		$total_campaign = (bool) $total ? $total : 100;
		$active_campaign = $this->getAdvertiserCampaignSummary($sitti_id, 0, $total_campaign, true);
		$campaign_ids = array();
		foreach ($active_campaign as $campaign)
		{
			array_push($campaign_ids, $campaign['campaign_id']);
		}
		
		$today = date("Y-m-d", strtotime("-1 day"));
		$yesterday = date("Y-m-d", strtotime("-2 day"));

		$query_today_campaign = "SELECT kampanye_id, jum_klik, jum_imp, ctr, IFNULL(total_cpc/jum_klik, 0) AS avg_cpc
									FROM db_report.tbl_performa_akun_kampanye
									WHERE advertiser_id = '".$sitti_id."'
									AND datee = '". $today ."'
									AND kampanye_id IN (".implode(",", $campaign_ids).")";

		$query_yesterday_campaign = "SELECT kampanye_id, jum_klik, jum_imp, ctr, IFNULL(total_cpc/jum_klik, 0) AS avg_cpc
									FROM db_report.tbl_performa_akun_kampanye
									WHERE advertiser_id = '".$sitti_id."'
									AND datee = '". $yesterday ."'
									AND kampanye_id IN (".implode(",", $campaign_ids).")";

		$this->open(4);
		$result_today = $this->fetch($query_today_campaign, 1);
		$result_yesterday = $this->fetch($query_yesterday_campaign, 1);
		$this->close();

		$data_today = array();
		if (is_array($result_today))
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['kampanye_id']]['jum_klik'] = $res['jum_klik'];
				$data_today[$res['kampanye_id']]['jum_imp'] = $res['jum_imp'];
				$data_today[$res['kampanye_id']]['ctr'] = $res['ctr'];
				$data_today[$res['kampanye_id']]['avg_cpc'] = $res['avg_cpc'];
			}
		}

		$data_yesterday = array();
		if (is_array($result_yesterday))
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['kampanye_id']]['jum_klik'] = $res['jum_klik'];
				$data_yesterday[$res['kampanye_id']]['jum_imp'] = $res['jum_imp'];
				$data_yesterday[$res['kampanye_id']]['ctr'] = $res['ctr'];
				$data_yesterday[$res['kampanye_id']]['avg_cpc'] = $res['avg_cpc'];
			}
		}

		$count_campaign = count($active_campaign);
		if ($count_campaign > 0)
		{
			for ($i = 0; $i < $count_campaign; $i++)
			{
				$campaign_id = $active_campaign[$i]['campaign_id'];
				$klik_change = $imp_change = $ctr_change = $avg_cpc_change = 0;
				if ( array_key_exists($campaign_id, $data_today) && array_key_exists($campaign_id, $data_yesterday) )
				{
					$klik_change = (($data_today[$campaign_id]['jum_klik'] - $data_yesterday[$campaign_id]['jum_klik'])/$data_yesterday[$campaign_id]['jum_klik']) * 100;
					$imp_change = (($data_today[$campaign_id]['jum_imp'] - $data_yesterday[$campaign_id]['jum_imp'])/$data_yesterday[$campaign_id]['jum_imp']) * 100;
					$ctr_change = $data_today[$campaign_id]['ctr'] - $data_yesterday[$campaign_id]['ctr'];
					$avg_cpc_change = (($data_today[$campaign_id]['avg_cpc'] - $data_yesterday[$campaign_id]['avg_cpc'])/$data_yesterday[$campaign_id]['avg_cpc']) * 100;
				}
				$active_campaign[$i]['klik_change'] = $klik_change;
				$active_campaign[$i]['imp_change'] = $imp_change;
				$active_campaign[$i]['ctr_change'] = $ctr_change;
				$active_campaign[$i]['avg_cpc_change'] = $avg_cpc_change;
				$active_campaign[$i]['enc_campaign_id'] = urlencode64($active_campaign[$i]['campaign_id']);
			}
		}

		return $active_campaign;
	}

	function getAdvertiserBannerCampaignSummary($sittiID,$start=0,$total=20){
			//$sittiID = "AC0000181";
		$this->open(2);
		//kita harus inner join data report dengan data di web3, agar nama kampanyenya tetap refresh/sinkron.
		$sql = "SELECT a.advertiser_id,a.kampanye_id as campaign_id,b.name as campaign_name,
				a.jum_imp as imp,a.jum_klik as click,a.average_cpm,a.harga as bids,a.posisi as rank,
				a.budget_harian as budget_dailies,
				a.budget_total as budget_totals,a.budget_sisa,a.ctr,a.last_update,a.status 
				FROM db_report.tbl_performa_akun_kampanye_total a 
				INNER JOIN db_web3.sitti_campaign b
				ON a.kampanye_id = b.ox_campaign_id
				INNER JOIN db_web3.sitti_banner_inventory ban
				ON a.kampanye_id = ban.ox_campaign_id
				WHERE a.advertiser_id='".$sittiID."' AND a.status <> 2 
				GROUP BY a.advertiser_id, a.kampanye_id
				ORDER BY ctr DESC 
				LIMIT ".$start.",".$total;
		//print $sql;
	    $sql2 = "SELECT COUNT(kampanye_id) as total
				FROM db_report.tbl_performa_akun_kampanye_total a
				INNER JOIN db_web3.sitti_banner_inventory ban
				ON a.kampanye_id = ban.ox_campaign_id
				WHERE a.advertiser_id='".$sittiID."' AND a.status <> 2 
				LIMIT 1";				
		
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		$this->found_rows = $rows['total'];
		
		$this->close();
		$n = sizeof($rs);
		
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
		}
		
		return $rs;
	}

	/**
	 * Active Campaign Summary only
	 * @param $sittiID
	 * @param $campaign list of advertiser's campaign
	 */
	function getActiveAdvertiserCampaignSummary($sittiID,$start=0,$total=20){
			//$sittiID = "AC0000181";
		$this->open(2);
		
		$sql = "SELECT advertiser_id,kampanye_id as campaign_id,kampanye as campaign_name,
				jum_imp as imp,jum_klik as click,average_cpm,harga as bids,posisi as rank,budget_harian as budget_dailies,
				budget_total as budget_totals,budget_sisa,ctr,last_update,status 
				FROM db_report.tbl_performa_akun_kampanye_total 
				WHERE advertiser_id='".$sittiID."' AND status = 0
				ORDER BY ctr DESC 
				LIMIT ".$start.",".$total;
		//print $sql;
	    $sql2 = "SELECT COUNT(kampanye_id) as total
				FROM db_report.tbl_performa_akun_kampanye_total 
				WHERE advertiser_id='".$sittiID."' AND status <> 2 
				LIMIT 1";				
		
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		$this->found_rows = $rows['total'];
		
		$this->close();
		$n = sizeof($rs);
		
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
		}
		
		return $rs;
	}
	/**
	 * 
	 * Performa keyword
	 * @param $sittiID
	 * @param $start
	 * @param $total
	 */
	function getAdvertiserKeywordSummary($sittiID,$start=0,$total=50){
	
		
		$sql = "SELECT advertiser_id,keyword,jum_imp as imp,jum_hit as click,ctr,status,avg_cpc,avg_top5_cpc
				FROM db_report.tbl_performa_kata_kunci_total
		         WHERE advertiser_id='".$sittiID."'
		         ORDER BY status,ctr DESC
		         LIMIT ".$start.",".$total;
		
		$sql2 = "SELECT COUNT(keyword) as total
				FROM db_report.tbl_performa_kata_kunci_total
		         WHERE advertiser_id='".$sittiID."'";
		$this->open(2);
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		//print $sql."<br/>-------------<br/>";
		
		$keywords = array();
		foreach ($rs as $keyword)
		{
			array_push($keywords, "'".$keyword['keyword']."'");
		}

		$today = date("Y-m-d", strtotime("-1 day"));
		$yesterday = date("Y-m-d", strtotime("-2 day"));

		$query_keywords_today = "SELECT keyword, jum_hit 
									FROM db_report.tbl_performa_kata_kunci
									WHERE advertiser_id = '".$sittiID."'
									AND datee = '". $today ."'
									AND keyword IN (".implode(",", $keywords).")";

		$query_keywords_yesterday = "SELECT keyword, jum_hit 
									FROM db_report.tbl_performa_kata_kunci
									WHERE advertiser_id = '".$sittiID."'
									AND datee = '". $yesterday ."'
									AND keyword IN (".implode(",", $keywords).")";

		$result_today = $this->fetch($query_keywords_today, 1);
		$result_yesterday = $this->fetch($query_keywords_yesterday, 1);

		$this->close();
		
		$this->found_rows = $rows['total'];

		$data_today = array();
		if (is_array($result_today) && count($result_today) > 0)
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['keyword']]['jum_klik'] = $res['jum_hit'];
			}	
		}

		$data_yesterday = array();
		if (is_array($result_yesterday) && count($result_yesterday) > 0)
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['keyword']]['jum_klik'] = $res['jum_hit'];
			}
		}
		
		$n = sizeof($rs);
		if ($n > 0)
		{
			for($i=0; $i<$n; $i++)
			{
				$keyword = $rs[$i]['keyword'];
				$klik_change = 0;

				if ( array_key_exists($keyword, $data_today) && array_key_exists($keyword, $data_yesterday) )
				{
					$klik_change = (($data_today[$keyword]['jum_klik'] - $data_yesterday[$keyword]['jum_klik'])/$data_yesterday[$keyword]['jum_klik']) * 100;
				}

				$rs[$i]['no'] = $i+1;
				$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
				$rs[$i]['klik_change'] = $klik_change;
			}
		}
		
		return $rs;
	}
	
	function getAdvertiserKeywordSummaryByCampaign($sittiID,$start=0,$total=50,$c_id){
	
		
		$sql = "SELECT advertiser_id,keyword,SUM(jum_imp) as imp,SUM(jum_klik) as click,IFNULL(SUM(jum_klik)*100/SUM(jum_imp),0) AS ctr,status,IFNULL(SUM(total_cpc)/SUM(jum_klik),0) AS avg_cpc,avg_top5_cpc
				FROM db_report.tbl_performa_kampanye_kata_kunci
		         WHERE advertiser_id='".$sittiID."' AND kampanye_id='".$c_id."' AND status <> 2
				 GROUP BY keyword
		         ORDER BY status,ctr DESC
		         LIMIT ".$start.",".$total;
		
		$sql2 = "SELECT COUNT(DISTINCT(keyword)) AS total
				FROM db_report.tbl_performa_kampanye_kata_kunci
		         WHERE advertiser_id='".$sittiID."' AND kampanye_id='".$c_id."'";
		$this->open(2);
		$rs = $this->fetch($sql,1);
		$rows = $this->fetch($sql2);
		//print $sql."<br/>-------------<br/>";
		
		$keywords = array();
		foreach ($rs as $keyword)
		{
			array_push($keywords, "'".$keyword['keyword']."'");
		}

		$today = date("Y-m-d", strtotime("-1 day"));
		$yesterday = date("Y-m-d", strtotime("-2 day"));

		$query_keywords_today = "SELECT keyword, jum_klik 
									FROM db_report.tbl_performa_kampanye_kata_kunci
									WHERE advertiser_id = '".$sittiID."' AND kampanye_id='".$c_id."'
									AND datee = '". $today ."'
									AND keyword IN (".implode(",", $keywords).")";

		$query_keywords_yesterday = "SELECT keyword, jum_klik 
									FROM db_report.tbl_performa_kampanye_kata_kunci
									WHERE advertiser_id = '".$sittiID."' AND kampanye_id='".$c_id."'
									AND datee = '". $yesterday ."'
									AND keyword IN (".implode(",", $keywords).")";

		$result_today = $this->fetch($query_keywords_today, 1);
		$result_yesterday = $this->fetch($query_keywords_yesterday, 1);

		$this->close();
		
		$this->found_rows = $rows['total'];

		$data_today = array();
		if (is_array($result_today) && count($result_today) > 0)
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['keyword']]['jum_klik'] = $res['jum_klik'];
			}	
		}

		$data_yesterday = array();
		if (is_array($result_yesterday) && count($result_yesterday) > 0)
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['keyword']]['jum_klik'] = $res['jum_klik'];
			}
		}
		
		$n = sizeof($rs);
		if ($n > 0)
		{
			for($i=0; $i<$n; $i++)
			{
				$keyword = $rs[$i]['keyword'];
				$klik_change = 0;

				if ( array_key_exists($keyword, $data_today) && array_key_exists($keyword, $data_yesterday) )
				{
					$klik_change = (($data_today[$keyword]['jum_klik'] - $data_yesterday[$keyword]['jum_klik'])/$data_yesterday[$keyword]['jum_klik']) * 100;
				}

				$rs[$i]['no'] = $i+1;
				$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
				$rs[$i]['klik_change'] = $klik_change;
			}
		}
		
		return $rs;
	}
	/**
	 * 
	 * Performa Iklan
	 * @param unknown_type $sittiID
	 * @param unknown_type $start
	 * @param unknown_type $total
	 */
	function getAdvertiserAdSummary($sittiID,$campaign_id = false,$start=0,$total=50){
		//kita harus inner join data report dengan data di web3, agar nama iklannya tetap refresh/sinkron.
		$query_campaign = '';
		if ($campaign_id)
		{
			$query_campaign = " AND b.ox_campaign_id = '". $campaign_id ."' ";
		}

		$sql = "SELECT a.advertiser_id,a.id_iklan as id,b.nama as nama,a.keywords,a.jum_imp as imp,
		 		a.jum_klik as click, a.ctr, a.harga as bids, a.budget_harian as budget_dailies, a.budget_total as budget_totals,
		 		a.posisi,a.budget_sisa,a.avg_cpm,a.status
		 		FROM db_report.tbl_performa_iklan_total a 
		 		INNER JOIN db_web3.sitti_ad_inventory b
		 		ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."' 
				".$query_campaign."
				AND a.status <> 2
				ORDER BY ctr DESC
				LIMIT ".$start.",".$total;
		
		$sql2 = "SELECT COUNT(id_iklan) as total
		 		FROM db_report.tbl_performa_iklan_total 
				WHERE advertiser_id='".$sittiID."'
				AND status <> 2
				LIMIT 1";
    	
    	$this->open(2);
		$rs = $this->fetch($sql,1);
		//print mysql_error();
		$rows = $this->fetch($sql2);
		//print mysql_error();
		$this->found_rows = $rows['total'];
		$this->close();
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			$rs[$i]['keywords'] = str_replace(",",", ",$rs[$i]['keywords']);
		}

		$active_ads = $rs;
		$ads_ids = array();
		if(sizeof($active_ads)>0){
			foreach ($active_ads as $ads)
			{
				$ads_ids[] = $ads["id"];
			}
			
			$today = date("Y-m-d", strtotime("-1 day"));
			$yesterday = date("Y-m-d", strtotime("-2 day"));
	
			$query_today_ads = "SELECT id_iklan, jum_klik, jum_imp, ctr 
										FROM db_report.tbl_performa_iklan
										WHERE advertiser_id = '".$sittiID."'
										AND datee = '". $today ."'
										AND id_iklan IN (".implode(",", $ads_ids).")";
	
			$query_yesterday_ads = "SELECT id_iklan, jum_klik, jum_imp, ctr 
										FROM db_report.tbl_performa_iklan
										WHERE advertiser_id = '".$sittiID."'
										AND datee = '". $yesterday ."'
										AND id_iklan IN (".implode(",", $ads_ids).")";
	
			$this->open(4);
			$result_today = $this->fetch($query_today_ads, 1);
			$result_yesterday = $this->fetch($query_yesterday_ads, 1);
			$this->close();
		}
		$data_today = array();
		if (is_array($result_today))
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['id_iklan']]['jum_klik'] = $res['jum_klik'];
				$data_today[$res['id_iklan']]['jum_imp'] = $res['jum_imp'];
				$data_today[$res['id_iklan']]['ctr'] = $res['ctr'];
			}
		}

		$data_yesterday = array();
		if (is_array($result_yesterday))
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['id_iklan']]['jum_klik'] = $res['jum_klik'];
				$data_yesterday[$res['id_iklan']]['jum_imp'] = $res['jum_imp'];
				$data_yesterday[$res['id_iklan']]['ctr'] = $res['ctr'];
			}
		}

		$count_ads = count($active_ads);
		if ($count_ads > 0)
		{
			for ($i = 0; $i < $count_ads; $i++)
			{
				$ads_id = $active_ads[$i]['id'];
				$klik_change = $imp_change = $ctr_change = 0;
				if ( array_key_exists($ads_id, $data_today) && array_key_exists($ads_id, $data_yesterday) )
				{
					$klik_change = (($data_today[$ads_id]['jum_klik'] - $data_yesterday[$ads_id]['jum_klik'])/$data_yesterday[$ads_id]['jum_klik']) * 100;
					$imp_change = (($data_today[$ads_id]['jum_imp'] - $data_yesterday[$ads_id]['jum_imp'])/$data_yesterday[$ads_id]['jum_imp']) * 100;
					$ctr_change = $data_today[$ads_id]['ctr'] - $data_yesterday[$ads_id]['ctr'];
				}
				$active_ads[$i]['klik_change'] = $klik_change;
				$active_ads[$i]['imp_change'] = $imp_change;
				$active_ads[$i]['ctr_change'] = $ctr_change;
			}
		}

		return $active_ads;
	}
	
	function getAllAdvertiserAdInCampaign($sittiID,$campaign_id){
		$this->open(2);
		$sql = "SELECT id_iklan as id,b.nama as nama ,keywords,a.status AS status,last_update, a.harga as bids, a.budget_harian as budget_dailies, a.budget_total as budget_totals
				FROM db_report.tbl_performa_iklan_total a
				INNER JOIN db_web3.sitti_ad_inventory b
				ON a.id_iklan = b.id
				WHERE 
				a.advertiser_id='".$sittiID."'
				AND b.ox_campaign_id = '". $campaign_id ."'
				AND a.status <> 2
				ORDER BY a.id_iklan DESC";
		
		$rs = $this->fetch($sql,1);
		$this->close();
		return $rs;
	}
	
	function getAdvertiserAdSummaryDaily($sittiID,$campaign_id = false,$start=0,$total=50,$tgl_awal,$tgl_akhir){
		//kita harus inner join data report dengan data di web3, agar nama iklannya tetap refresh/sinkron.
		$query_campaign = '';
		if ($campaign_id)
		{
			$query_campaign = " AND b.ox_campaign_id = '". $campaign_id ."' ";
		}
		if (! $tgl_awal && ! $tgl_akhir)
		{
			$tgl_awal = date("Y-m-d", strtotime("-30 days"));
			$tgl_akhir = date("Y-m-d");
		}
		$ad_id = "";
		$rs = $this->getAllAdvertiserAdInCampaign($sittiID,$campaign_id);
		for($i=0;$i<sizeof($rs);$i++){
			if ($ad_id!=""){
				$ad_id .=",";
			}
			$ad_id .="'".$rs[$i]['id']."'";
		}
		$temp = $rs;
		$sql = "SELECT a.advertiser_id,a.id_iklan as id,b.nama as nama,a.keywords,SUM(a.jum_imp) as imp,
		 		SUM(a.jum_klik) as click,IFNULL(SUM(a.jum_klik)*100/SUM(a.jum_imp),0) AS ctr, IFNULL(SUM(a.total_cpc)/SUM(a.jum_klik),0) AS avg_cpc, a.harga as bids, a.budget_harian as budget_dailies, a.budget_total as budget_totals,
		 		a.posisi,a.budget_sisa,a.avg_cpm,a.status
		 		FROM db_report.tbl_performa_iklan a 
		 		INNER JOIN db_web3.sitti_ad_inventory b
		 		ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."' 
				".$query_campaign."
				AND a.id_iklan IN (".$ad_id.")
				AND a.datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				AND a.status <> 2
				GROUP BY id
				ORDER BY id DESC
				LIMIT ".$start.",".$total;
		// print $sql;
		$sql2 = "SELECT COUNT(DISTINCT(id_iklan)) as total
		 		FROM db_report.tbl_performa_iklan
				WHERE advertiser_id='".$sittiID."'
				AND a.id_iklan IN (".$ad_id.")
				AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				AND status <> 2
				LIMIT 1";
    	
    	$this->open(2);
		$rs = $this->fetch($sql,1);
		//print mysql_error();
		$rows = $this->fetch($sql2);
		//print mysql_error();
		$this->found_rows = $rows['total'];
		$this->close();
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			$rs[$i]['avg_cpc'] = number_format(round($rs[$i]['avg_cpc'],2));
			for($j=0;$j<sizeof($temp);$j++){
				if($rs[$i]['id']==$temp[$j]['id']){
					$rs[$i]['keywords'] = $temp[$j]['keywords'];
					$rs[$i]['bids'] = $temp[$j]['bids'];
					$rs[$i]['budget_dailies'] = $temp[$j]['budget_dailies'];
					$rs[$i]['budget_totals'] = $temp[$j]['budget_totals'];
				}
			}
			$rs[$i]['keywords'] = str_replace(",",", ",$rs[$i]['keywords']);
		}

		$active_ads = $rs;
		$ads_ids = array();
		if(sizeof($active_ads)>0){
			foreach ($active_ads as $ads)
			{
				$ads_ids[] = $ads["id"];
			}
			
			$today = date("Y-m-d", strtotime("-1 day"));
			$yesterday = date("Y-m-d", strtotime("-2 day"));
	
			$query_today_ads = "SELECT id_iklan, jum_klik, jum_imp, ctr , IFNULL(total_cpc/jum_klik, 0) AS avg_cpc
										FROM db_report.tbl_performa_iklan
										WHERE advertiser_id = '".$sittiID."'
										AND datee = '". $today ."'
										AND id_iklan IN (".implode(",", $ads_ids).")";
	
			$query_yesterday_ads = "SELECT id_iklan, jum_klik, jum_imp, ctr , IFNULL(total_cpc/jum_klik, 0) AS avg_cpc
										FROM db_report.tbl_performa_iklan
										WHERE advertiser_id = '".$sittiID."'
										AND datee = '". $yesterday ."'
										AND id_iklan IN (".implode(",", $ads_ids).")";
	
			$this->open(4);
			$result_today = $this->fetch($query_today_ads, 1);
			$result_yesterday = $this->fetch($query_yesterday_ads, 1);
			$this->close();
		}
		$data_today = array();
		if (is_array($result_today))
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['id_iklan']]['jum_klik'] = $res['jum_klik'];
				$data_today[$res['id_iklan']]['jum_imp'] = $res['jum_imp'];
				$data_today[$res['id_iklan']]['ctr'] = $res['ctr'];
				$data_today[$res['id_iklan']]['avg_cpc'] = $res['avg_cpc'];
			}
		}

		$data_yesterday = array();
		if (is_array($result_yesterday))
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['id_iklan']]['jum_klik'] = $res['jum_klik'];
				$data_yesterday[$res['id_iklan']]['jum_imp'] = $res['jum_imp'];
				$data_yesterday[$res['id_iklan']]['ctr'] = $res['ctr'];
				$data_yesterday[$res['id_iklan']]['avg_cpc'] = $res['avg_cpc'];
			}
		}

		$count_ads = count($active_ads);
		if ($count_ads > 0)
		{
			for ($i = 0; $i < $count_ads; $i++)
			{
				$ads_id = $active_ads[$i]['id'];
				$klik_change = $imp_change = $ctr_change = $avg_cpc_change = 0;
				if ( array_key_exists($ads_id, $data_today) && array_key_exists($ads_id, $data_yesterday) )
				{
					$klik_change = (($data_today[$ads_id]['jum_klik'] - $data_yesterday[$ads_id]['jum_klik'])/$data_yesterday[$ads_id]['jum_klik']) * 100;
					$imp_change = (($data_today[$ads_id]['jum_imp'] - $data_yesterday[$ads_id]['jum_imp'])/$data_yesterday[$ads_id]['jum_imp']) * 100;
					$ctr_change = $data_today[$ads_id]['ctr'] - $data_yesterday[$ads_id]['ctr'];
					$avg_cpc_change = (($data_today[$ads_id]['avg_cpc'] - $data_yesterday[$ads_id]['avg_cpc'])/$data_yesterday[$ads_id]['avg_cpc']) * 100;
				}
				$active_ads[$i]['klik_change'] = $klik_change;
				$active_ads[$i]['imp_change'] = $imp_change;
				$active_ads[$i]['ctr_change'] = $ctr_change;
				$active_ads[$i]['avg_cpc_change'] = $avg_cpc_change;
			}
		}

		return $active_ads;
	}

	function getAdvertiserBannerAdSummary($sittiID,$start=0,$total=50){
		//kita harus inner join data report dengan data di web3, agar nama iklannya tetap refresh/sinkron.
		$sql = "SELECT a.advertiser_id, a.banner_id as id, b.judul as nama,a.kategori, a.jum_imp as imp,
		 		a.jum_klik as click, a.ctr, a.posisi, b.ad_flag as banner_flag, a.cpm
		 		FROM db_report.tbl_performa_banner_total a 
		 		INNER JOIN db_web3.sitti_banner_inventory b
		 		ON a.banner_id = b.id
		 		WHERE a.advertiser_id='".$sittiID."'
				ORDER BY ctr DESC
				LIMIT ".$start.",".$total;
		
		$sql2 = "SELECT COUNT(banner_id) as total
		 		FROM db_report.tbl_performa_banner_total
		 		WHERE advertiser_id='".$sittiID."'";

    	$this->open(2);
		$rs = $this->fetch($sql,1);

		//print mysql_error();
		$rows = $this->fetch($sql2);
		//print mysql_error();
		$this->found_rows = $rows['total'];
		$this->close();
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1+$start;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			$rs[$i]['kategori'] = str_replace(",",", ",$rs[$i]['kategori']);
		}
		return $rs;
	}

	/**
	 * 
	 * Performa Penempatan Iklan
	 * @param $sittiID
	 * @param $start
	 * @param $total
	 */
	function getAdvertiserPlacementSummary($sittiID,$start=0,$total=5){
		$sql = "SELECT advertiser_id as sittiID,publisher_id,pub_username as username,
				web_name,keywords,user_keyword as top_keyword,ctr,last_update
    			FROM db_report.tbl_performa_penempatan_iklan_total WHERE advertiser_id = '".$sittiID."'
    			ORDER BY ctr DESC
   	 			LIMIT ".$start.",".$total;
    	$this->open(2);
		$rs = $this->fetch($sql,1);
		
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no'] = $i+1;
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
		}
		$this->close();
		return $rs;
	}
	/**
	 * mengambil laporan summary banner. disini narik data langsung dari tabel final.
	 * @param $sittiID
	 * @param $campaignID
	 */
	function getBannerSummary2($sittiID,$campaignID,$start,$total){

		$iklan = $this->Inventory->getAdvertiserAdByCampaignID($campaignID,$sittiID,$start,$total);
		$this->Inventory->found_rows;
		$m = sizeof($iklan);
		$this->open(2);
		for($j=0;$j<$m;$j++){

			$rs = $this->fetch("SELECT t_view as impressions,t_click as clicks FROM db_adslogs.ps_report WHERE iklan_id='".$iklan[$j]['id']."' LIMIT 1");

			if($rs['impressions']!=0){
				$rs['ctr'] = round(($rs['clicks']/$rs['impressions'])*100,2);
			}
			$iklan[$j]['stats'] = $rs;
		}
		$this->close();
		$result['list'] = $iklan;
		$result['total'] = $this->Inventory->found_rows;
		return $result;
	}
	/**
	 * menarik daftar summary publisher (IMPRESSION/CLICK/CTR)
	 * @param string $sittiID Publisher ID
	 * @param $start
	 * @param $total
	 * @return array
	 */
	function getPublisherSummary($sittiID,$start,$total=20){
		
		$this->open(2);
		/*$list = $this->fetch("SELECT sittiID, slotid, jum_klik, jum_imp, ctr, last_update
                            FROM db_adslogs.pubs_ctr_gen
                            WHERE (sittiID = '".$sittiID."')
                            ORDER BY slotid ASC LIMIT ".$start.",".$total,1);*/
		/*
		$list = $this->fetch("SELECT
    						slotid
    						, SUM(jum_imp) AS jum_imp
    						, SUM(jum_klik) AS jum_klik
							FROM
    						db_adslogs.ps_publisher_slot
							WHERE (publisher_id = '".$sittiID."')
							GROUP BY slotid LIMIT ".$start.",".$total,1);
		*/
		/*$sql = "SELECT publisher_id, slotid, jum_imp, jum_hit as jum_klik, ctr, last_update
				FROM db_report.tbl_performa_slot_total
				WHERE publisher_id = '".$sittiID."' LIMIT ".$start.",".$total;*/
		
		$sql = "SELECT sds.sittiID as publisher_id, sds.id as slotid, st.jum_imp, st.jum_hit AS jum_klik, ctr, last_update
			    FROM db_web3.sitti_deploy_setting sds
			    LEFT JOIN db_report.tbl_performa_slot_total st
			    ON sds.id = st.slotid AND sds.sittiID = st.publisher_id
			    WHERE sds.sittiID = '".$sittiID."' LIMIT ".$start.",".$total;
		$list = $this->fetch($sql,1);
		$this->close();
		if(sizeof($list)==0){
			$this->open(0);
			$list = $this->fetch("SELECT id as slotid, 0 AS jum_imp, 0 AS jum_klik FROM
							db_web3.sitti_deploy_setting
							WHERE sittiID  = '".$sittiID."' LIMIT ".$start.",".$total,1);
			
			//print mysql_error();
			$rows = $this->fetch("SELECT COUNT(id) as total FROM
							db_web3.sitti_deploy_setting
							WHERE sittiID  = '".$sittiID."' LIMIT 1");
			$this->close();
		}else{
			/*$rows = $this->fetch("SELECT COUNT(*) as total
                            FROM db_adslogs.pubs_ctr_gen
                            WHERE (sittiID = '".$sittiID."')
                            LIMIT 1");*/
			$this->open(2);
			/*$rows = $this->fetch("SELECT
    							COUNT(`capture_date`) AS `total`
								FROM
    							`db_adslogs`.`ps_publisher_slot`
							  WHERE (`publisher_id` = '".$sittiID."') 
							  ");
			*/
			$rows = $this->fetch("SELECT COUNT(slotid) as total
				FROM db_report.tbl_performa_slot_total
				WHERE publisher_id = '".$sittiID."'");
			$this->close();
		}
		$this->open(0);
		for($i=0;$i<sizeof($list);$i++){
			$list[$i]['no'] = $i+$start+1;
			$rs = $this->fetch("SELECT * FROM db_web3.sitti_deploy_setting WHERE id='".$list[$i]['slotid']."' LIMIT 1");
			
			if(is_array($rs)){
				$list[$i] = array_merge($list[$i],$rs);
			}
			if($list[$i]['jum_imp']>0){
				$list[$i]['ctr'] =  round($list[$i]['jum_klik'] /  $list[$i]['jum_imp'] * 100,2);
			}else{
				$list[$i]['ctr'] =  0;
			}
		}
		$this->close();
		//print_r($list)
		
		$slot_ids = array();
		foreach ($list as $slot)
		{
			array_push($slot_ids, $slot['slotid']);
		}

		$today = date("Y-m-d", strtotime("-1 day"));
		$yesterday = date("Y-m-d", strtotime("-2 day"));

		$query_today_slot = "SELECT slotid, jum_hit, jum_imp, ctr 
									FROM db_report_raw.pubs_ctr_gen_date
									WHERE publisher_id = '".$sittiID."'
									AND datee = '". $today ."'
									AND slotid IN (".implode(",", $slot_ids).")";

		$query_yesterday_slot = "SELECT slotid, jum_hit, jum_imp, ctr 
									FROM db_report_raw.pubs_ctr_gen_date
									WHERE publisher_id = '".$sittiID."'
									AND datee = '". $yesterday ."'
									AND slotid IN (".implode(",", $slot_ids).")";
		
		$this->open(4);
		$result_today = $this->fetch($query_today_slot, 1);
		$result_yesterday = $this->fetch($query_yesterday_slot, 1);
		$this->close();

		$data_today = array();
		if (is_array($result_today))
		{
			foreach ($result_today as $res)
			{
				$data_today[$res['slotid']]['jum_hit'] = $res['jum_hit'];
				$data_today[$res['slotid']]['jum_imp'] = $res['jum_imp'];
				$data_today[$res['slotid']]['ctr'] = $res['ctr'];
			}
		}

		$data_yesterday = array();
		if (is_array($result_yesterday))
		{
			foreach ($result_yesterday as $res)
			{
				$data_yesterday[$res['slotid']]['jum_hit'] = $res['jum_hit'];
				$data_yesterday[$res['slotid']]['jum_imp'] = $res['jum_imp'];
				$data_yesterday[$res['slotid']]['ctr'] = $res['ctr'];
			}
		}

		$count_slot = count($list);
		if ($count_slot > 0)
		{
			for ($i = 0; $i < $count_slot; $i++)
			{
				$slotid = $list[$i]['slotid'];
				$klik_change = $imp_change = $ctr_change = 0;
				if ( array_key_exists($slotid, $data_today) && array_key_exists($slotid, $data_yesterday) )
				{
					$klik_change = (($data_today[$slotid]['jum_hit'] - $data_yesterday[$slotid]['jum_hit'])/$data_yesterday[$slotid]['jum_hit']) * 100;
					$imp_change = (($data_today[$slotid]['jum_imp'] - $data_yesterday[$slotid]['jum_imp'])/$data_yesterday[$slotid]['jum_imp']) * 100;
					$ctr_change = $data_today[$slotid]['ctr'] - $data_yesterday[$slotid]['ctr'];
				}
				elseif ( array_key_exists($slotid, $data_today) && ! array_key_exists($slotid, $data_yesterday) )
				{
					$klik_change = $data_today[$slotid]['jum_hit'] * 100;
					$imp_change = $data_today[$slotid]['jum_imp'] * 100;
					$ctr_change = $data_today[$slotid]['ctr'];
				}
				elseif ( ! array_key_exists($slotid, $data_today) && array_key_exists($slotid, $data_yesterday) )
				{
					$klik_change = (0 - $data_yesterday[$slotid]['jum_hit']) * 100;
					$imp_change = (0 - $data_yesterday[$slotid]['jum_imp']) * 100;
					$ctr_change = 0 - $data_yesterday[$slotid]['ctr'];
				}
				$list[$i]['klik_change'] = $klik_change;
				$list[$i]['imp_change'] = $imp_change;
				$list[$i]['ctr_change'] = $ctr_change;
			}
		}

		$resultset['list'] = $list;
		$resultset['total_rows'] = $rows['total'];
		return $resultset;
	}
	/**
	 * menarik daftar top pagesnya publisher
	 * @param $sittiID
	 */
	function getPublisherTopPages($sittiID,$total=10){
		$this->open(2);
		$sql = "SELECT publisher_id, web_name, jum_hit, jum_imp, ctr,last_update
              FROM
              db_report.tbl_performa_halaman_total
              WHERE publisher_id='".$sittiID."'
              ORDER BY ctr DESC LIMIT ".$total;
		$list = $this->fetch($sql,1);
		//print mysql_error();
		$this->close();
		return $list;
	}
	/**
	 *
	 *Advertiser Account Summary report
	 */
	function getAdvertiserAccountSummary($sittiID){
	  
		$this->open(2);
		$sql1 = "SELECT * FROM db_report.tbl_rekap_beranda WHERE advertiser_id='".$sittiID."' LIMIT 1";
		$rs1 = $this->fetch($sql1);
		
		
		$this->close();
		
		$summary = false;
		if ($rs1)
		{
			$summary['best_campaign_ctr'] = round($rs1['ctr_kampanye_terbaik'],2);
			$summary['best_campaign_name'] = $rs1['nama_kampanye_terbaik'];
			$summary['best_ad_ctr'] = round($rs1['ctr_iklan_terbaik'],2);
			$summary['best_ad_name'] = $rs1['nama_iklan_terbaik'];
			$summary['best_ad_campaign'] = $rs1['kampanye_iklan_terbaik'];
			$summary['best_keyword_ctr'] = round($rs1['ctr_keyword_terbaik'],2);
			$summary['best_keyword_name'] = $rs1['nama_keyword_terbaik'];
			$summary['best_publisher_ctr'] = round($rs1['ctr_publisher_terbaik'],2);
			$summary['best_publisher_name'] = $rs1['nama_publisher_terbaik'];
		}
		return $summary;
		 
	}

	function getAdvertiserBannerAccountSummary($sittiID){
	  
		$this->open(2);
		$sql1 = "SELECT cmp.name, rep.ctr AS ctr_kampanye_terbaik
					FROM db_report.tbl_performa_akun_kampanye_total rep
					INNER JOIN db_web3.sitti_campaign cmp
					ON rep.kampanye_id = cmp.ox_campaign_id
					WHERE cmp.sittiID = '".$sittiID."' 
					AND cmp.ox_campaign_id IN (
					  SELECT ban.ox_campaign_id FROM db_web3.sitti_banner_inventory ban
					  WHERE ban.advertiser_id = '".$sittiID."' 
					)
					ORDER BY ctr DESC
					LIMIT 1";
		
		$rs1 = $this->fetch($sql1);
		$this->close();
		
		$this->open(2);
		$sql2 = "SELECT ban.`judul`, rep.`ctr` FROM db_report.`tbl_performa_banner_total` rep
					INNER JOIN db_web3.sitti_banner_inventory ban
					ON rep.`banner_id` = ban.id
					WHERE ban.`advertiser_id` = '".$sittiID."'
					ORDER BY ctr DESC
					LIMIT 1";
		
		$rs2 = $this->fetch($sql2);
		$this->close();

		$summary = false;
		if ($rs1 & $rs2)
		{
			$summary['best_campaign_ctr'] = round($rs1['ctr_kampanye_terbaik'],3);
			$summary['best_campaign_name'] = $rs1['name'];
			$summary['best_ad_ctr'] = round($rs2['ctr'],3);
			$summary['best_ad_name'] = $rs2['judul'];
			/*$summary['best_ad_campaign'] = $rs1['kampanye_iklan_terbaik'];
			$summary['best_keyword_ctr'] = round($rs1['ctr_keyword_terbaik'],2);
			$summary['best_keyword_name'] = $rs1['nama_keyword_terbaik'];
			$summary['best_publisher_ctr'] = round($rs1['ctr_publisher_terbaik'],2);
			$summary['best_publisher_name'] = $rs1['nama_publisher_terbaik'];*/
		}
		return $summary;
		 
	}

	function getAdvertiserPPAAccountSummary($sittiID)
	{
		$summary = false;
		$this->open(2);

		$sql_best_campaign = "SELECT A.campaign_id, B.name, A.nilai_konversi 
								FROM db_report.ppa_campaign A
								INNER JOIN db_web3.sitti_campaign B
								ON A.campaign_id = B.ox_campaign_id
								WHERE A.jum_konversi > 0
								AND A.advertiser_id = '$sittiID'
								ORDER BY A.jum_konversi DESC";

		$res_best_campaign = $this->fetch($sql_best_campaign);
		if ($res_best_campaign)
		{
			$summary['best_campaign_name'] = $res_best_campaign['name'];
			$summary['best_campaign_value'] = round($res_best_campaign['nilai_konversi'], 3);
		}

		$sql_best_ad = "SELECT A.iklan_id, B.nama, A.nilai_konversi 
							FROM db_report.ppa_ad A
							INNER JOIN db_web3.sitti_ad_inventory B
							ON A.iklan_id = B.id
							WHERE A.jum_konversi > 0
							AND A.advertiser_id = '$sittiID'
							ORDER BY A.jum_konversi DESC";

		$res_best_ad = $this->fetch($sql_best_ad);
		if ($res_best_ad)
		{
			$summary['best_ad_name'] = $res_best_ad['nama'];
			$summary['best_ad_value'] = round($res_best_ad['nilai_konversi'], 3);
		}

		$this->close();

		return $summary;
	}

	/**
	 *
	 *Advertiser Detailed Account Performance report
	 */
	function getCampaignPerformanceOverall($sittiID){
		
		$this->open(2);
		$sql = "SELECT advertiser_id,jum_kampanye_aktif as kampanye_aktif,jum_kampanye_nonaktif,
				total_impressi as total_impresi,total_CTR as total_ctr,last_update 
				FROM db_report.tbl_rekap_performa_akun_kampanye 
				WHERE advertiser_id='".$sittiID."' LIMIT 1";
		$rs1 = $this->fetch($sql);
		$rs1['total_ctr'] = round($rs1['total_ctr'],2);
		//print mysql_error();
		$this->close();
		
		return $rs1;
		 
	}

	function getBannerCampaignPerformanceOverall($sittiID){
		
		$query_kampanye = "SELECT COUNT(DISTINCT cmp.ox_campaign_id) jumlah_kampanye_aktif
							FROM db_web3.sitti_campaign cmp
							INNER JOIN db_web3.sitti_banner_inventory ban
							ON cmp.`ox_campaign_id` = ban.`ox_campaign_id`
							WHERE ban.advertiser_id = '".$sittiID."' AND cmp.`camp_flag` = 0";

		$this->open(0);
		$rs_kampanye = $this->fetch($query_kampanye);
		$this->close();

		$query_imp ="SELECT SUM(jum_imp) imp 
						FROM db_report.tbl_performa_akun_kampanye_total rep 
						INNER JOIN db_web3.sitti_campaign cmp 
						ON rep.kampanye_id = cmp.ox_campaign_id 
						WHERE rep.advertiser_id = '".$sittiID."' 
						AND cmp.camp_flag IN (0,1) 
						AND rep.kampanye_id IN (SELECT ox_campaign_id FROM db_web3.sitti_banner_inventory WHERE advertiser_id = '".$sittiID."')";

		$this->open(2);
		$rs_imp = $this->fetch($query_imp);
		$this->close();

		$query_ctr = "SELECT SUM(jum_klik)*100/SUM(jum_imp) ctrs
						FROM db_report.tbl_performa_akun_kampanye_total rep
						INNER JOIN db_web3.sitti_campaign cmp
						ON rep.kampanye_id = cmp.ox_campaign_id
						WHERE rep.advertiser_id = '".$sittiID."'
						AND cmp.camp_flag IN (0,1)
						AND rep.kampanye_id IN (SELECT ox_campaign_id FROM db_web3.sitti_banner_inventory WHERE advertiser_id = '".$sittiID."');";

		$this->open(2);
		$rs_ctr = $this->fetch($query_ctr);
		$this->close();

		$result = array();
		$result['kampanye_aktif'] = $rs_kampanye['jumlah_kampanye_aktif'];
		$result['total_impresi'] = $rs_imp['imp'];
		$result['total_ctr'] = $rs_ctr['ctrs'];

		return $result;
		 
	}

	function getAdPerformanceOverall($sittiID, $campaign_id = false)
	{
		if ( ! $campaign_id)
		{
			//query untuk jumlah kampanye aktif/non-aktif untuk sementara tarik langsung dari database web
			$this->open(2);
			
			$sql = "SELECT advertiser_id,
					jum_iklan_aktif as iklan_aktif,
					jum_iklan_nonaktif as iklan_nonaktif,
					jum_iklan_menunggu,
					nama_kampanye as campaign_name,
					nama_iklan_terbaik as iklan_terbaik,
					ctr_iklan_terbaik,last_update 
					FROM db_report.tbl_rekap_performa_iklan WHERE advertiser_id='".$sittiID."'";
			$summary = $this->fetch($sql);
			
			$this->close();
		}
		else
		{
			$query_jumlah_iklan_aktif = "SELECT COUNT(a.`id_iklan`) as iklan_aktif
											FROM db_report.`tbl_performa_iklan_total` a
											INNER JOIN db_web3.`sitti_ad_inventory` b
											ON a.`id_iklan` = b.`id`
											WHERE a.advertiser_id = '".$sittiID."'
											AND a.`status` = '0'
											AND b.`ox_campaign_id` = '".$campaign_id."'";

			$query_jumlah_iklan_tidak_aktif = "SELECT COUNT(a.`id_iklan`) as iklan_nonaktif
											FROM db_report.`tbl_performa_iklan_total` a
											INNER JOIN db_web3.`sitti_ad_inventory` b
											ON a.`id_iklan` = b.`id`
											WHERE a.advertiser_id = '".$sittiID."'
											AND a.`status` = '1'
											AND b.`ox_campaign_id` = '".$campaign_id."'";

			$query_iklan_terbaik_ctr = "SELECT a.`nama_iklan` as iklan_terbaik
											FROM db_report.`tbl_performa_iklan_total` a
											INNER JOIN db_web3.`sitti_ad_inventory` b
											ON a.`id_iklan` = b.`id`
											WHERE a.advertiser_id = '".$sittiID."'
											AND a.status = '0'
											AND b.`ox_campaign_id` = '".$campaign_id."'
											ORDER BY a.`ctr` DESC
											LIMIT 1";

			$this->open(2);

			$res_query_jumlah_iklan_aktif = $this->fetch($query_jumlah_iklan_aktif);
			$res_query_jumlah_iklan_tidak_aktif = $this->fetch($query_jumlah_iklan_tidak_aktif);
			$res_query_iklan_terbaik_ctr = $this->fetch($query_iklan_terbaik_ctr);

			$this->close();

			$summary['iklan_aktif'] = $res_query_jumlah_iklan_aktif['iklan_aktif'];
			$summary['iklan_nonaktif'] = $res_query_jumlah_iklan_tidak_aktif['iklan_nonaktif'];
			$summary['iklan_terbaik'] = $res_query_iklan_terbaik_ctr['iklan_terbaik'];
		
		}
		return $summary;
	}

	function getBannerPerformanceOverall($sittiID){
		
		$query_banner_aktif = "SELECT COUNT(id) jumlah_banner_aktif FROM db_web3.`sitti_banner_inventory`
						WHERE ad_flag = 0 AND advertiser_id = '". $sittiID ."'";

		$this->open(0);
		$rs_banner_aktif = $this->fetch($query_banner_aktif);
		$this->close();

		$query_banner_tdk_aktif = "SELECT COUNT(id) jumlah_banner_tdk_aktif FROM db_web3.`sitti_banner_inventory`
						WHERE ad_flag = 1 AND advertiser_id = '". $sittiID ."'";

		$this->open(0);
		$rs_banner_tdk_aktif = $this->fetch($query_banner_tdk_aktif);
		$this->close();

		$query_banner_ctr = "SELECT ban.`judul`
								FROM db_report.`tbl_performa_banner_total` rep
								INNER JOIN db_web3.sitti_banner_inventory ban
								ON rep.`banner_id` = ban.id
								WHERE ban.`advertiser_id` = '". $sittiID ."'
								ORDER BY ctr DESC
								LIMIT 1";

		$this->open(2);
		$rs_banner_ctr = $this->fetch($query_banner_ctr);
		$this->close();

		$result = array();
		$result['iklan_aktif'] = $rs_banner_aktif['jumlah_banner_aktif'];
		$result['iklan_nonaktif'] = $rs_imp['jumlah_banner_tdk_aktif'];
		$result['iklan_terbaik'] = $rs_banner_ctr['judul'];

		return $result;
	}

	function getKeywordPerformanceOverall($sittiID){
		$this->open(2);
		
		// $sql = "SELECT advertiser_id,jum_keyword as total_keyword,
								// nama_kampanye as campaign_name,best_keyword as keyword_terbaik 
								// FROM db_report.tbl_rekap_performa_kata_kunci 
								// WHERE advertiser_id='".$sittiID."' LIMIT 1";
		
		// Total keyword
		$sql = "SELECT i.advertiser_id, COUNT(DISTINCT k.keyword) AS total
					FROM `db_web3`.`sitti_ad_inventory` i
					INNER JOIN db_web3.sitti_ad_keywords k
					ON i.id = k.iklan_id 
					WHERE i.advertiser_id = '".$sittiID."' AND ad_flag IN (0,1) AND keyword_flag IN (0,1);";
		$rs = $this->fetch($sql);
		$summary['total'] = $rs['total'];
		
		// Keyword aktif
		$sql = "SELECT i.advertiser_id, COUNT(DISTINCT k.keyword) AS aktif
				FROM `db_web3`.`sitti_ad_inventory` i
				INNER JOIN db_web3.sitti_ad_keywords k
				ON i.id = k.iklan_id
				WHERE i.advertiser_id = '".$sittiID."' AND keyword_flag = 0 AND ad_flag = 0;";
		$rs = $this->fetch($sql);
		$summary['aktif'] = $rs['aktif'];
		
		// keyword inaktif
		$summary['inaktif'] = $summary['total']-$summary['aktif'];
		
		$this->close();
		

		//print $sql."<br/>-------------<br/>";
		return $summary;
	}
	function getCampaignDailyTotal($sittiID, $tgl_awal = false, $tgl_akhir = false){

		if (! $tgl_awal && ! $tgl_akhir)
		{
			$tgl_awal = date("Y-m-d", strtotime("-30 days"));
			$tgl_akhir = date("Y-m-d");
		}
		
		$this->open(2);
		
		/*$sql = "SELECT a.kampanye_id, b.name as kampanye,ctr
				FROM db_report.tbl_performa_akun_kampanye_total a, db_web3.sitti_campaign b
				WHERE a.advertiser_id='".$sittiID."' 
				AND a.kampanye_id = b.ox_campaign_id
				AND b.camp_flag <> 2
				ORDER BY ctr DESC
				LIMIT 5";*/
				
		$sql = "SELECT a.kampanye_id, b.name AS kampanye,IFNULL(SUM(a.jum_klik)*100/SUM(a.jum_imp),0) AS ctr
				FROM db_report.tbl_performa_akun_kampanye a
				INNER JOIN db_web3.sitti_campaign b
				ON a.kampanye_id = b.ox_campaign_id
				WHERE a.advertiser_id='".$sittiID."' 
				AND b.camp_flag <> 2
				AND datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				GROUP BY kampanye_id 
				ORDER BY ctr DESC
				LIMIT 5;";
		
		$rs = $this->fetch($sql,1);
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$sql = "SELECT datee AS capture_date,
					jum_imp AS imp,jum_klik AS click, (jum_klik*100/jum_imp) as ctr
					FROM db_report.tbl_performa_akun_kampanye
					WHERE advertiser_id='".$sittiID."' AND datee >= '2011-01-01'
					AND datee BETWEEN '". $tgl_awal ."' AND '".$tgl_akhir."'
					AND kampanye_id IN(".$rs[$i]['kampanye_id'].")
					ORDER BY datee";

			$rs[$i]['stats'] =  $this->fetch($sql,1);
		}
		$this->close();
		
		return $rs;
	}

	function getBannerCampaignDailyTotal($sittiID)
	{
		
		$query_kampanye = "SELECT cmp.ox_campaign_id kampanye_id, cmp.name kampanye, rep.ctr
							FROM db_report.tbl_performa_akun_kampanye_total rep
							INNER JOIN db_web3.sitti_campaign cmp
							ON cmp.ox_campaign_id = rep.kampanye_id
							INNER JOIN db_web3.sitti_banner_inventory ban
							ON rep.kampanye_id = ban.ox_campaign_id
							WHERE ban.advertiser_id = '".$sittiID."'
							GROUP BY cmp.ox_campaign_id";
		
		$this->open(2);
		
		$rs = $this->fetch($query_kampanye, 1);
		foreach ($rs as $idx => $row)
		{
			$query_stats = "SELECT datee AS capture_date,
					jum_imp AS imp,jum_klik AS click, (jum_klik*100/jum_imp) as ctr
					FROM db_report.tbl_performa_akun_kampanye
					WHERE advertiser_id='".$sittiID."' AND datee >= '2011-01-01'
					AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE
					AND kampanye_id IN(".$rs[$idx]['kampanye_id'].")
					ORDER BY datee
					LIMIT 30";
			$rs[$idx]['stats'] =  $this->fetch($query_stats,1);
		}

		$this->close();

		return $rs;
	}

	function getDailyAdStatistic($sittiID,$iklan_id,$type=0,$startFrom=null,$endTo=null){
		switch($type){
			case 1:
				//data kemarin dan hari ini
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND CURRENT_DATE";	
			break;
			case 2:
				//Data 3 hari lalu dan hari ini
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 3 DAY) AND CURRENT_DATE";	
			break;
			case 3:
				//data 7 hari lalu dan hari ini
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE";	
			break;
			case 4:
				//data 14 hari lalu dan hari ini
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY) AND CURRENT_DATE";	
			break;
			case 5:
				//data 30 hari lalu dan hari ini
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";	
			break;
			case 6:
				
				$arr_start = explode("/",mysql_escape_string($startFrom));
				$startDate = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
				$arr_end = explode("/",mysql_escape_string($endTo));
				$endDate = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
				$tdiff = datediff('s',$startDate,$endDate);
				
				if(($tdiff/(60*60*24))<=31){
					$strCond = " AND datee >= '".$startDate."' AND datee <= '".$endDate."'";
				}	
			break;
			case 0:
				//data hari ini
				$strCond = " AND datee = CURRENT_DATE";		
			break;
			default:
				//defaultnya data 30 hari terakhir
				$strCond = " AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";
			break;
		}
		$this->open(2);
		$sql = "SELECT datee AS tanggal,jum_imp AS imp,jum_klik AS click,ctr,avg_cpm 
				FROM db_report.tbl_performa_iklan 
				WHERE id_iklan = ".$iklan_id."".$strCond;
		
		$rs = $this->fetch($sql,1);
		
		//print_r($rs);
		$this->close();
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['ctr'] = round($rs[$i]['ctr'],2);
			
		}
		return $rs;
	}
	function getTop5AdsDaily($sittiID,$total=5,$campaign_id, $tgl_awal = false, $tgl_akhir = false){
		
		#AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE
		if (! $tgl_awal && ! $tgl_akhir)
		{
			$tgl_awal = date("Y-m-d", strtotime("-30 days"));
			$tgl_akhir = date("Y-m-d");
		}
		
		$this->open(2);
		/*$sql = "SELECT a.id_iklan AS id, b.nama as nama_iklan, a.ctr 
				FROM db_report.tbl_performa_iklan_total a
				INNER JOIN db_web3.sitti_ad_inventory b
				ON a.id_iklan = b.id
				WHERE 
				a.advertiser_id='".$sittiID."'
				AND b.ox_campaign_id = '". $campaign_id ."'
				AND
				b.ad_flag <> 2
				ORDER BY jum_imp DESC, ctr DESC
				LIMIT ".$total
				;*/
		$sql = "SELECT a.id_iklan AS id, b.nama AS nama_iklan,IFNULL(SUM(a.jum_klik)*100/SUM(a.jum_imp),0) AS ctr
				FROM db_report.tbl_performa_iklan a
				INNER JOIN db_web3.sitti_ad_inventory b
				ON a.id_iklan = b.id
				WHERE a.advertiser_id='".$sittiID."'
				AND b.ox_campaign_id = '".$campaign_id."'
				AND
				b.ad_flag <> 2
				AND a.datee BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."' 
				GROUP BY id
				ORDER BY ctr DESC,jum_imp DESC
				LIMIT ".$total.";";
		
		$rs = $this->fetch($sql,1);
		
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$sql = "SELECT a.datee as capture_date,a.jum_imp as imp,a.jum_klik as click,a.ctr
					FROM db_report.tbl_performa_iklan a 
					WHERE a.advertiser_id='".$sittiID."'
					AND a.id_iklan = ".$rs[$i]['id']."
					AND a.datee BETWEEN '". $tgl_awal ."' AND '". $tgl_akhir ."'";

			$rs[$i]['stats'] = $this->fetch($sql,1);
		}
		
		$this->close();
		return $rs;
	}

	function getTop5BannerAdsDaily($sittiID,$total=5){
		$this->open(2);
		
		$query_banner = "SELECT ban.id id, ban.judul nama_iklan, rep.ctr ctr
							FROM db_report.tbl_performa_banner_total rep
							INNER JOIN db_web3.sitti_banner_inventory ban
							ON rep.banner_id = ban.id
							WHERE ban.advertiser_id = '". $sittiID ."'
							GROUP BY ban.id
							LIMIT " . $total;
		
		//print $query_banner."<br />";
		
		$rs = $this->fetch($query_banner,1);
		#AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE
		
		
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$sql = "SELECT a.datee as capture_date,a.jum_imp as imp,a.jum_klik as click,a.ctr
					FROM db_report.tbl_performa_banner a 
					WHERE a.advertiser_id='".$sittiID."'
					AND a.banner_id = ".$rs[$i]['id']."
					AND a.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";
			//echo $sql."<br />";
			$rs[$i]['stats'] = $this->fetch($sql,1);
		}
		
		$this->close();
		return $rs;
	}

	function getTopKeywordChartData($sittiID,$total=5){
		$this->open(2);
		$retval = array();
		$sql = "SELECT keyword AS kata,jum_imp AS imp FROM db_report.tbl_performa_kata_kunci_total a 
				WHERE a.advertiser_id='".$sittiID."' 
				AND jum_imp <> 0
				ORDER BY jum_imp DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		if (sizeof($rs)>0){
			$retval = $rs;
		}
		$sql = "SELECT keyword AS kata, jum_hit AS click FROM db_report.tbl_performa_kata_kunci_total a 
				WHERE a.advertiser_id='".$sittiID."' 
				AND jum_hit <> 0
				ORDER BY jum_hit DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		if (sizeof($rs)>0){
			$retval = array_merge($retval,$rs);
		}
		$sql = "SELECT keyword AS kata, ctr FROM db_report.tbl_performa_kata_kunci_total a 
				WHERE a.advertiser_id='".$sittiID."' 
				AND ctr <> 0
				ORDER BY ctr DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		if (sizeof($rs)>0){
			$retval = array_merge($retval,$rs);
		}
		$this->close();
		return $retval;
	}
	
	function getTopKeywordChartDataByCampaign($sittiID,$total=5,$c_id){
		$this->open(2);
		$retval;
		$sql = "SELECT keyword AS kata,SUM(jum_imp) AS imp FROM db_report.tbl_performa_kampanye_kata_kunci a 
				WHERE a.advertiser_id='".$sittiID."' AND kampanye_id='".$c_id."' 
				GROUP BY keyword
				HAVING imp <> 0
				ORDER BY imp DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		$retval = $rs;
		$sql = "SELECT keyword AS kata,SUM(jum_klik) AS click FROM db_report.tbl_performa_kampanye_kata_kunci a 
				WHERE a.advertiser_id='".$sittiID."' AND kampanye_id='".$c_id."' 
				GROUP BY keyword
				HAVING click <> 0
				ORDER BY click DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		$retval = array_merge($retval,$rs);
		$sql = "SELECT keyword AS kata, IFNULL(SUM(a.jum_klik)*100/SUM(a.jum_imp),0) AS ctr FROM db_report.tbl_performa_kampanye_kata_kunci a 
				WHERE a.advertiser_id='".$sittiID."' AND kampanye_id='".$c_id."' 
				GROUP BY keyword
				HAVING ctr <> 0
				ORDER BY ctr DESC
				LIMIT ".$total;
		$rs = $this->fetch($sql,1);
		$retval = array_merge($retval,$rs);
		$this->close();
		return $retval;
	}

	function getPublisherSlotTopImp($sitti_id, $limit = 5)
	{
		$query = "SELECT C.name, C.id AS slotid, SUM(C.jum_imp) AS sum_imp  
					FROM (
					SELECT a.name, a.id, b.jum_imp, b.datee
					FROM db_web3.sitti_deploy_setting a
					LEFT JOIN db_report_raw.pubs_ctr_gen_date b
					ON a.id = b.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					UNION ALL
					SELECT a.name, a.id, b.jum_imp, b.datee
					FROM db_web3.sitti_deploy_setting a
					LEFT JOIN db_report_raw.daily_banner_slot_performance b
					ON a.id = b.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					) C
					GROUP BY id
					ORDER BY sum_imp DESC
					LIMIT ". $limit;

		$this->open(2);
		$result_slot = $this->fetch($query, 1);

		$count_slot = count($result_slot);
		if (is_array($result_slot) && $count_slot > 0)
		{
			for ($i = 0; $i < $count_slot; $i++)
			{
				$query_data = "SELECT datee AS capture_date, SUM(jum_imp) AS imp
								FROM (
								SELECT b.slotid, b.jum_imp, b.datee
								FROM db_report_raw.pubs_ctr_gen_date b
								WHERE b.slotid = '".$result_slot[$i]['slotid']."'
								AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
								UNION ALL
								SELECT b.slotid, b.jum_imp, b.datee
								FROM db_report_raw.daily_banner_slot_performance b
								WHERE b.slotid = '".$result_slot[$i]['slotid']."'
								AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
								) C
								GROUP BY datee
								ORDER BY capture_date ASC";

				$result_slot[$i]['stats'] =  $this->fetch($query_data, 1);

			}

		}

		$this->close();

		for ($key = 0; $key < $count_slot; $key++)
		{
			
			$result_slot_date = array();
			foreach ($result_slot[$key]['stats'] as $stats)
			{
				$result_slot_date[] = $stats['capture_date'];	
			}

			$past_day = 7;
			
			$result = array();
			for ($idx = 0; $idx < $past_day; $idx++)
			{
				$iday = date("Y-m-d", strtotime("-".($past_day - $idx)." day"));
				$data_key = array_search($iday, $result_slot_date);
				if ($data_key === FALSE)
				{
					$result[$idx]['capture_date'] = $iday;
					$result[$idx]['imp'] = 0;
				}
				else
				{
					$result[$idx]['capture_date'] = $result_slot[$key]['stats'][$data_key]['capture_date'];
					$result[$idx]['imp'] = $result_slot[$key]['stats'][$data_key]['imp'];
				}
			}

			$result_slot[$key]['stats'] = $result;
		}

		return $result_slot;
	}

	function getPublisherSlotTopClick($sitti_id, $limit = 5)
	{
		$query = "SELECT C.name, C.id AS slotid, SUM(C.jum_hit) AS sum_hit  
			FROM (
			SELECT a.name, a.id, b.paid_clicks AS jum_hit, b.datee
			FROM db_web3.sitti_deploy_setting a
			LEFT JOIN db_report_raw.publisher_share2 b
			ON a.id = b.slotid
			WHERE a.sittiID = '".$sitti_id."'
			AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
			UNION ALL
			SELECT a.name, a.id, b.jum_hit, b.datee
			FROM db_web3.sitti_deploy_setting a
			LEFT JOIN db_report_raw.daily_banner_slot_performance b
			ON a.id = b.slotid
			WHERE a.sittiID = '".$sitti_id."'
			AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
			) C
			GROUP BY id
			ORDER BY sum_hit  DESC
			LIMIT ". $limit;

		$this->open(2);
		$result_slot = $this->fetch($query, 1);

		$count_slot = count($result_slot);
		if (is_array($result_slot) && $count_slot > 0)
		{
			for ($i = 0; $i < $count_slot; $i++)
			{
				$query_data = "SELECT datee AS capture_date, SUM(jum_hit) AS click
					FROM (
					SELECT b.slotid, b.paid_clicks AS jum_hit, b.datee
					FROM db_report_raw.publisher_share2 b
					WHERE b.slotid = '".$result_slot[$i]['slotid']."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					UNION ALL
					SELECT b.slotid, b.jum_hit, b.datee
					FROM db_report_raw.daily_banner_slot_performance b
					WHERE b.slotid = '".$result_slot[$i]['slotid']."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					) C
					GROUP BY datee
					ORDER BY capture_date ASC";

				$result_slot[$i]['stats'] =  $this->fetch($query_data, 1);

			}

		}

		$this->close();

		for ($key = 0; $key < $count_slot; $key++)
		{
			
			$result_slot_date = array();
			foreach ($result_slot[$key]['stats'] as $stats)
			{
				$result_slot_date[] = $stats['capture_date'];	
			}

			$past_day = 7;
			
			$result = array();
			for ($idx = 0; $idx < $past_day; $idx++)
			{
				$iday = date("Y-m-d", strtotime("-".($past_day - $idx)." day"));
				$data_key = array_search($iday, $result_slot_date);
				if ($data_key === FALSE)
				{
					$result[$idx]['capture_date'] = $iday;
					$result[$idx]['click'] = 0;
				}
				else
				{
					$result[$idx]['capture_date'] = $result_slot[$key]['stats'][$data_key]['capture_date'];
					$result[$idx]['click'] = $result_slot[$key]['stats'][$data_key]['click'];
				}
			}

			$result_slot[$key]['stats'] = $result;
		}

		return $result_slot;
	}

	function getPublisherSlotTopCtr($sitti_id, $limit = 5)
	{
		$query = "SELECT C.name, C.id AS slotid, SUM(C.jum_hit)*100/SUM(C.jum_imp) AS sum_ctr
					FROM (
					SELECT a.name, a.id, IFNULL(c.paid_clicks,0) AS jum_hit, b.jum_imp, b.datee
					FROM db_web3.sitti_deploy_setting a
					LEFT JOIN db_report_raw.pubs_ctr_gen_date b
					ON a.id = b.slotid
					LEFT JOIN db_report_raw.publisher_share2 c
					ON a.id = c.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					UNION ALL
					SELECT a.name, a.id, b.jum_hit, b.jum_imp, b.datee
					FROM db_web3.sitti_deploy_setting a
					LEFT JOIN db_report_raw.daily_banner_slot_performance b
					ON a.id = b.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
					) C
					GROUP BY id
					ORDER BY sum_ctr DESC
					LIMIT ". $limit;

		$this->open(2);
		$result_slot = $this->fetch($query, 1);

		$count_slot = count($result_slot);
		if (is_array($result_slot) && $count_slot > 0)
		{
			for ($i = 0; $i < $count_slot; $i++)
			{
				$query_data = "SELECT datee AS capture_date, SUM(C.jum_hit)*100/SUM(C.jum_imp) AS ctr
								FROM (
								SELECT b.slotid, IFNULL(c.paid_clicks, 0) AS jum_hit, b.jum_imp, b.datee
								FROM db_report_raw.pubs_ctr_gen_date b
								LEFT JOIN db_report_raw.publisher_share2 c
								ON b.datee = c.datee AND b.slotid = c.slotid
								WHERE b.slotid = '".$result_slot[$i]['slotid']."'
								AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
								UNION ALL
								SELECT b.slotid, b.jum_hit, b.jum_imp, b.datee
								FROM db_report_raw.daily_banner_slot_performance b
								WHERE b.slotid = '".$result_slot[$i]['slotid']."'
								AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
								) C
								GROUP BY datee
								ORDER BY capture_date ASC";

				$result_slot[$i]['stats'] =  $this->fetch($query_data, 1);

			}

		}

		$this->close();

		for ($key = 0; $key < $count_slot; $key++)
		{
			
			$result_slot_date = array();
			foreach ($result_slot[$key]['stats'] as $stats)
			{
				$result_slot_date[] = $stats['capture_date'];	
			}

			$past_day = 7;
			
			$result = array();
			for ($idx = 0; $idx < $past_day; $idx++)
			{
				$iday = date("Y-m-d", strtotime("-".($past_day - $idx)." day"));
				$data_key = array_search($iday, $result_slot_date);
				if ($data_key === FALSE)
				{
					$result[$idx]['capture_date'] = $iday;
					$result[$idx]['ctr'] = 0;
				}
				else
				{
					$result[$idx]['capture_date'] = $result_slot[$key]['stats'][$data_key]['capture_date'];
					$result[$idx]['ctr'] = $result_slot[$key]['stats'][$data_key]['ctr'];
				}
			}

			$result_slot[$key]['stats'] = $result;
		}

		return $result_slot;
	}

	function getPublisherSlotTopShare($sitti_id, $limit = 5)
	{
		$query = "SELECT C.name, C.id AS slotid, SUM(C.pub_share) AS sum_share
					FROM (
					SELECT a.name, a.id, b.pub_share
					FROM db_web3.sitti_deploy_setting a
					LEFT JOIN db_report_raw.publisher_share2 b
					ON a.id = b.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)

					UNION ALL

					SELECT a.name, a.id, b.pub_revenue AS pub_share
					FROM db_web3.sitti_deploy_setting a
					INNER JOIN db_report_raw.publisher_banner_revenue b
					ON a.id = b.slotid
					WHERE a.sittiID = '".$sitti_id."'
					AND b.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)

					) C
					GROUP BY id
					ORDER BY sum_share DESC
					LIMIT ". $limit;

		$this->open(2);
		$result_slot = $this->fetch($query, 1);

		if (is_array($result_slot))
		{
			$count_slot = count($result_slot);
			for ($i = 0; $i < $count_slot; $i++)
			{
				$query_data = "SELECT datee AS capture_date, SUM(pub_share) AS share
								FROM (
								SELECT datee, pub_share
								FROM db_report_raw.publisher_share2 b
								WHERE slotid = '".$result_slot[$i]['slotid']."'
								AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)

								UNION ALL

								SELECT datee, pub_revenue AS pub_share
								FROM db_report_raw.publisher_banner_revenue b
								WHERE slotid = '".$result_slot[$i]['slotid']."'
								AND datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)

								) C
								GROUP BY datee
								ORDER BY capture_date ASC";

				$result_slot[$i]['stats'] =  $this->fetch($query_data, 1);
			}

		}

		$this->close();

		for ($key = 0; $key < $count_slot; $key++)
		{
			
			$result_slot_date = array();
			foreach ($result_slot[$key]['stats'] as $stats)
			{
				$result_slot_date[] = $stats['capture_date'];	
			}

			$past_day = 7;
			
			$result = array();
			for ($idx = 0; $idx < $past_day; $idx++)
			{
				$iday = date("Y-m-d", strtotime("-".($past_day - $idx)." day"));
				$data_key = array_search($iday, $result_slot_date);
				if ($data_key === FALSE)
				{
					$result[$idx]['capture_date'] = $iday;
					$result[$idx]['share'] = 0;
				}
				else
				{
					$result[$idx]['capture_date'] = $result_slot[$key]['stats'][$data_key]['capture_date'];
					$result[$idx]['share'] = $result_slot[$key]['stats'][$data_key]['share'];
				}
			}

			$result_slot[$key]['stats'] = $result;
		}
		
		return $result_slot;
	}

	function getPublisherSlotSummary($sitti_id)
	{
		$query = "SELECT name, id AS slotid
					FROM db_web3.sitti_deploy_setting
					WHERE sittiID = '".$sitti_id."'
					ORDER BY created_date DESC";

		$this->open(2);
		$result_slot = $this->fetch($query, 1);

		if (is_array($result_slot))
		{
			$count_slot = count($result_slot);
			for ($i = 0; $i < $count_slot; $i++)
			{
				$query_slot_data = "SELECT a.datee AS capture_date, a.jum_hit AS click, a.jum_imp AS imp, a.ctr, b.pub_share
								FROM db_report_raw.pubs_ctr_gen_date a
								LEFT JOIN db_report_raw.publisher_share2 b
								ON a.slotid = b.slotid
								WHERE a.publisher_id = '".$sitti_id."'
								AND a.slotid = '".$result_slot[$i]['slotid']."'
								AND a.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE
								ORDER BY a.ctr DESC";

				$result_slot[$i]['stats'] =  $this->fetch($query_slot_data, 1);
			}
			
		}

		$this->close();
		return $result_slot;
	}
	/*
	function getPubSlotPerformance($sittiID,$slotid,$type=0,$startFrom=null,$endTo=null){
		
		switch($type){
			case 1:
				//data kemarin dan hari ini
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND CURRENT_DATE";	
			break;
			case 2:
				//Data 3 hari lalu dan hari ini
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 3 DAY) AND CURRENT_DATE";	
			break;
			case 3:
				//data 7 hari lalu dan hari ini
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE";	
			break;
			case 4:
				//data 14 hari lalu dan hari ini
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY) AND CURRENT_DATE";	
			break;
			case 5:
				//data 30 hari lalu dan hari ini
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";	
			break;
			case 6:
				
				$arr_start = explode("/",mysql_escape_string($startFrom));
				$startDate = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
				$arr_end = explode("/",mysql_escape_string($endTo));
				$endDate = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
				$tdiff = datediff('s',$startDate,$endDate);
				
				if(($tdiff/(60*60*24))<=30){
					$strCond = " AND ps.capture_date >= '".$startDate."' AND ps.capture_date <= '".$endDate."'";
				}	
			break;
			default:
				//$strCond = " AND ps.capture_date = CURRENT_DATE";	
				$strCond = " AND ps.capture_date BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";
			break;
		}
		$slotid = mysql_escape_string($slotid);
		$sql = "SELECT ps.capture_date, DATE_FORMAT(ps.capture_date,'%d-%m-%Y') as tanggal,
				ps.jum_imp, ps.jum_klik
    			FROM db_adslogs.ps_publisher_slot ps
   	 			WHERE ps.publisher_id = '".$sittiID."' AND ps.slotid = ".$slotid.$strCond;
		
		$this->open(2);
		$rs = $this->fetch($sql,1);
		$this->close();
		
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['ctr'] = round(($rs[$i]['jum_klik']/$rs[$i]['jum_imp']*100),2);
		}
		
		return $rs;
		
	}
	*/
	function getPubSlotPerformance($sittiID,$slotid,$type=0,$startFrom=null,$endTo=null){
		
		switch($type){
			case 1:
				//data kemarin dan hari ini
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND CURRENT_DATE";	
			break;
			case 2:
				//Data 3 hari lalu dan hari ini
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 3 DAY) AND CURRENT_DATE";	
			break;
			case 3:
				//data 7 hari lalu dan hari ini
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE";	
			break;
			case 4:
				//data 14 hari lalu dan hari ini
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 14 DAY) AND CURRENT_DATE";	
			break;
			case 5:
				//data 30 hari lalu dan hari ini
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";	
			break;
			case 6:
				
				$arr_start = explode("/",mysql_escape_string($startFrom));
				$startDate = trim($arr_start[2])."-".trim($arr_start[1])."-".trim($arr_start[0]);
				$arr_end = explode("/",mysql_escape_string($endTo));
				$endDate = trim($arr_end[2])."-".trim($arr_end[1])."-".trim($arr_end[0]);
				$tdiff = datediff('s',$startDate,$endDate);
				
				if(($tdiff/(60*60*24))<=30){
					$strCond = " AND pcg.datee >= '".$startDate."' AND pcg.datee <= '".$endDate."'";
				}	
			break;
			default:
				//$strCond = " AND pcg.datee = CURRENT_DATE";	
				$strCond = " AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) AND CURRENT_DATE";
			break;
		}
		$slotid = mysql_escape_string($slotid);
		/*$sql = "SELECT DATE_FORMAT(datee,'%d-%m-%Y') as tgl,publisher_id,slotid,jum_hit as jum_klik,jum_imp,ctr
        		FROM db_report_raw.pubs_ctr_gen_date rep
        		WHERE rep.publisher_id='".$sittiID."' AND slotid=".$slotid.$strCond;*/
		/*$sql = "SELECT DATE_FORMAT(pcg.datee,'%d-%m-%Y') AS tgl, pcg.publisher_id, pcg.slotid, pcg.jum_hit as jum_klik, pcg.jum_imp, pcg.ctr, 
				ps.pub_share,ps.free_clicks,ps.paid_clicks, ps.shown_free_clicks
			    FROM db_report_raw.pubs_ctr_gen_date pcg 
			    LEFT JOIN db_report_raw.publisher_share ps
			    ON pcg.slotid = ps.slotid AND pcg.datee = ps.datee
			    WHERE pcg.publisher_id = '".$sittiID."' AND ps.slotid = ".$slotid.$strCond."
			    AND pcg.datee >= '2011-01-01'
			    ORDER BY pcg.datee DESC
			    LIMIT 30";
*/		
		/*$sql = "SELECT DATE_FORMAT(pcg.datee,'%d-%m-%Y') AS tgl, pcg.publisher_id, pcg.slotid, pcg.jum_hit, pcg.jum_imp, pcg.ctr, 
			    ps.pub_share,ps.free_clicks,ps.paid_clicks, ps.shown_free_clicks
			    FROM db_web3.sitti_deploy_setting sds
			    INNER JOIN db_report_raw.pubs_ctr_gen_date pcg 
			    INNER JOIN db_report_raw.publisher_share ps
			    ON sds.id = pcg.slotid AND pcg.slotid = ps.slotid AND pcg.datee = ps.datee
			    WHERE sds.sittiID = '".$sittiID."' AND pcg.datee >= '2011-01-01' AND ps.slotid = ".$slotid.$strCond."
			    ORDER BY pcg.datee DESC LIMIT 30
    			";*/
		$sql = "SELECT DATE_FORMAT(A.datee,'%d-%m-%Y') AS tgl, A.publisher_id, A.slotid, A.jum_hit, A.jum_imp, 
				IFNULL(ps.paid_clicks*100/A.jum_imp, 0) AS ctr, 
			    ps.pub_share,ps.free_clicks,ps.paid_clicks, ps.shown_free_clicks
			    FROM (
			    SELECT DATE_FORMAT(pcg.datee,'%d-%m-%Y') AS tgl, pcg.datee, pcg.publisher_id, 
			    pcg.slotid, pcg.jum_hit, pcg.jum_imp, pcg.ctr
			    FROM db_web3.sitti_deploy_setting sds
			    INNER JOIN db_report_raw.pubs_ctr_gen_date pcg 
			    ON sds.id = pcg.slotid
			    WHERE sds.sittiID = '".$sittiID."' AND pcg.datee >= '2011-01-01' AND pcg.slotid = ".$slotid.$strCond."
			    ORDER BY pcg.datee DESC LIMIT 30
			    ) AS A LEFT JOIN db_report_raw.publisher_share2 ps
			    ON A.slotid = ps.slotid AND A.datee = ps.datee
			    ";
		//print $sql;
		$this->open(2);
		$rs = $this->fetch($sql,1);
		$this->close();
		
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			
			$rs[$i]['jum_klik'] = $rs[$i]['paid_clicks']+(min(array($rs[$i]['free_clicks'],floor(0.25*$rs[$i]['paid_clicks']))));
			//$rs[$i]['jum_klik'] = $rs[$i]['paid_clicks'];
			//$rs[$i]['ctr'] = round(($rs[$i]['paid_clicks']/$rs[$i]['jum_imp']*100),3);
		}
		
		return $rs;
		
	}
	/**
	 * Demography data for charting
	 */
	function getGeoChartData($sitti_id,$iklan_id){
		if(eregi("([0-9]+)",$sitti_id)&&eregi("([AC0-9]+)",$iklan_id)){
			//pastikan ownershipnya valid
			$this->open(2);
			$sql = "SELECT COUNT(id) as total FROM db_web3.sitti_ad_inventory 
					WHERE advertiser_id='".$sitti_id."' AND id=".$iklan_id;
			$cek = $this->fetch($sql);
			//==>
			if($cek['total']==1){
				$sql = "SELECT iklan_id, city, SUM(jum_hit) as jum
						FROM db_report_raw.daily_location_ad
						WHERE iklan_id = ".$iklan_id."
						GROUP BY iklan_id, city";
				
				$rs = $this->fetch($sql,1);
				
				
			}
			$this->close();
			return $rs;
		}
		return null;
	}

	function getPPACampaign($sitti_id)
	{
		$this->open(2);

		$query = "SELECT a.*, b.name FROM db_report.ppa_campaign a
				  INNER JOIN db_web3.sitti_campaign b
				  ON a.campaign_id = b.ox_campaign_id
					WHERE a.advertiser_id = '". $sitti_id ."'";

		$result = $this->fetch($query, 1);
		$this->close();

		return $result;
	}

	function getPPAAdvs($campaign_id)
	{
		$this->open(2);

		$query = "SELECT a.*, b.`nama` FROM db_report.`ppa_ad` a
					INNER JOIN db_web3.`sitti_ad_inventory` b
					ON a.`iklan_id` = b.`id`
					WHERE b.`ox_campaign_id` = '". $campaign_id ."'";

		$result = $this->fetch($query, 1);
		$this->close();

		return $result;
	}

	function getPPATrans($iklan_id)
	{
		$this->open(2);

		$query = "SELECT hittime, conversiontime, session_id, transaction_id, INET_NTOA(ipaddress) as ip, lokasi, nilai_konversi,nilai_komisi 
					FROM db_report.`ppa_ad_transaction`
					WHERE iklan_id = '". $iklan_id ."'";

		$result = $this->fetch($query, 1);
		$this->close();

		return $result;
	}

	function getDailyPPAKonversi($sitti_id, $campaign_id)
	{
		$query = "SELECT b.id as iklan_id, b.nama as nama_iklan
					FROM db_report.tbl_performa_iklan_total a
					INNER JOIN db_web3.sitti_ad_inventory b
					INNER JOIN db_report.ppa_ad c
					ON a.id_iklan = b.id AND b.id = c.iklan_id
					WHERE
					a.advertiser_id='$sitti_id'
					AND b.ox_campaign_id = '$campaign_id'
					AND b.ad_flag = 0
					AND serve_type = 1
					ORDER BY c.nilai_konversi DESC, c.jum_hit DESC
					LIMIT 5";
		/*$query = "SELECT a.iklan_id, b.nama AS nama_iklan
					FROM db_report.daily_ppa_ad a
					LEFT JOIN db_web3.sitti_ad_inventory b
					ON a.iklan_id = b.id
					WHERE a.advertiser_id = '". $sitti_id ."' 
					GROUP BY a.iklan_id";*/

		$this->open(2);

		$rs = $this->fetch($query, 1);

		if (is_array($rs) && count($rs) > 0)
		{
			$is_empty = true;

			// populate last 30 days stats array
			$day_count = 30;
			$daily_stats = array();
			for ($i_day = 0; $i_day < $day_count; $i_day++)
			{
				$daily_stats[$i_day]['capture_date'] = date("Y-m-d", strtotime("-". $day_count + $i_day ." days"));
				$daily_stats[$i_day]['nilai'] = 0;
				$daily_stats[$i_day]['jum_imp'] = 0;
				$daily_stats[$i_day]['jum_hit'] = 0;
			}
			
			foreach ($rs as &$row)
			{
				$current_stats = null;
				$current_stats = $daily_stats;
				foreach ($current_stats as &$stats)
				{
					$query_stats = "SELECT a.datee AS capture_date, a.jum_imp, c.jum_hit, c.jum_konversi, c.nilai_komisi, c.nilai_konversi 
									FROM db_report.tbl_performa_iklan a
									LEFT JOIN db_report.daily_ppa_ad c
									ON a.id_iklan = c.iklan_id AND a.datee = c.datee
									WHERE a.advertiser_id = '". $sitti_id ."' 
									AND a.datee = '". $stats['capture_date'] ."'
									AND a.id_iklan = '". $row['iklan_id'] ."'
									ORDER BY a.datee";

					$stat = $this->fetch($query_stats);
					if (is_array($stat) && count($stat) > 0)
					{
						/*$nilai = 0;
						if (intval($stat['nilai_konversi']) > 0)
						{
							$nilai = $stat['nilai_konversi'];
						}
						elseif (intval($stat['nilai_komisi']) > 0)
						{
							$nilai = $stat['nilai_komisi'];
						}*/
						
						$stats['nilai'] = $stat['jum_konversi'];;
						$stats['jum_imp'] = $stat['jum_imp'];
						$stats['jum_hit'] = $stat['jum_hit'];

						if (intval($stats['nilai']) != 0 || intval($stats['jum_imp']) != 0 || intval($stats['jum_hit']) != 0)
						{
							$is_empty = false;
						}
					}
				}
				$row['stats'] = $current_stats;
			}
		}

		$this->close();

		return $is_empty ? false : $rs;

	}

	public function getPerformaIklanPPA($sitti_id, $limit = false)
	{
		$limit = (bool) $limit ? $limit : 100;

		$query = "SELECT B.id_iklan, A.nama, B.jum_imp, C.jum_hit, C.jum_konversi
					FROM db_web3.sitti_ad_inventory A
					INNER JOIN db_report.tbl_performa_iklan_total B
					ON A.id = B.id_iklan
					INNER JOIN db_report.ppa_ad C
					ON B.id_iklan = C.iklan_id
					AND A.advertiser_id = '$sitti_id'
					AND A.serve_type = 1
					LIMIT $limit";

		$this->open(2);
		$rs = $this->fetch($query, 1);
		$this->close();

		if (is_array($rs) && count($rs) > 0)
		{
			foreach ($rs as &$row)
			{
				$ctr = ($row['jum_hit'] * 100) / $row['jum_imp'];
				$row['ctr'] = (bool) $ctr ? $ctr : 0;	
			}
		}

		return $rs;
	}

}
?>
