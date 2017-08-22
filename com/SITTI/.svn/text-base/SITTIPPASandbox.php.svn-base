<?php 

class SITTIPPASandbox extends SQLData
{

	private $view;

	function SITTIPPASandbox()
	{
		$this->view = new BasicView();
	}

	function handler()
	{
		// menyimpan api_key ke dalam session
		if (! isset($_SESSION['api_key']))
		{
			$query_api = "SELECT api_key
							FROM zz_sitti.`ppa_api_key`
							WHERE advertiser_id = '". $_SESSION['sittiID'] ."'
							LIMIT 1";
			$this->open(0);
			$result_api = $this->fetch($query_api);
			$this->close();

			if (is_array($result_api))
			{
				$_SESSION['api_key'] = $result_api['api_key'];
			}
		}
		
		// mengambil list iklan
		$this->view->assign('ads', $this->getAds());

		return $this->view->toString("SITTIPPA_sandbox/home.html");
	}

	function getAds($sitti_id = false)
	{
		$sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
		
		$query = "SELECT ad.`id`, ad.`nama` 
					FROM db_web3.`sitti_ad_inventory` ad 
					INNER JOIN db_web3.`sitti_campaign` cmp 
					ON ad.`ox_campaign_id` = cmp.`ox_campaign_id` 
					WHERE cmp.`sittiID` = '". $sitti_id ."'
					AND ad.serve_type = 1
					ORDER BY ad.`id` DESC";
		
		$this->open(0);
		$result = $this->fetch($query, 1);
		$this->close();

		return $result;
	}

	function getAdsDetail($iklan_id)
	{
		$query_ads = "SELECT a.advertiser_id,b.* FROM zz_sitti.ppa_api_key a
						INNER JOIN db_web3.sitti_ad_inventory b
						ON a.advertiser_id = b.advertiser_id
						WHERE a.api_key = '". $_SESSION['api_key'] ."'
						AND b.id = '". $iklan_id ."' 
						AND b.serve_type=1
						LIMIT 1";
		
		$this->open(0);
		$result_ads = $this->fetch($query_ads);
		$this->close();

		if (is_array($result_ads))
		{
			global $PS_CONFIG;

			$_GET['rand'] = "148416642565280";
			$_GET['sitti_pub_id'] = "BC0001095";
			$_GET['type'] = "10";
			$_GET['sitti_ad_number'] = "3";
			$_GET['d'] = "1248";
			
			$_REQUEST['rand'] = "148416642565280";
			$_REQUEST['sitti_pub_id'] = "BC0001095";
			$_REQUEST['type'] = "10";
			$_REQUEST['sitti_ad_number'] = "3";
			$_REQUEST['d'] = "1248";

			$params = array("a"=>$result_ads['id'],
							"p"=>"BC0001095",
							"dest"=>"".$result_ads['urlLink'],
							"ref"=>"sitti.sandbox",
							"k"=>"test",
							"d"=>"0",
							"r"=>"".rand(0,9999),
							"v"=>"");

			$p = json_encode($params);
			$clickURL = $PS_CONFIG['tracker_uri']."ppa.php?rc=".urlencode64($p);
			$result_ads['clickURL'] = $clickURL;
		}

		$this->view->assign('detail', $result_ads);
		echo $this->view->toString("SITTIPPA_sandbox/detail_iklan.html");
	}

	function getLastTokenTime()
	{
		$query = "SELECT click_time as time
					FROM zz_sitti.ppa_token
					WHERE advertiser_id ='". $_SESSION['sittiID'] ."'
					ORDER BY click_time DESC
					LIMIT 1";

		$this->open(0);
		$result = $this->fetch($query);
		$this->close();

		echo $result['time'];
	}

}

?>