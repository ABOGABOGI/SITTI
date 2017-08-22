<?php
if (!@include('XML/RPC.php')) {
	die('Error: cannot load the PEAR XML_RPC class');
}

/**
 * SITTI to OX XML-RPC Wrapper
 * Wrapper bridge untuk komunikasi antara SITTI ad_net / ad_sense dari dan ke OpenX.
 * openx bertindak sebagai tracker activity banner.
 * @author Hapsoro Renaldy <hapsoro.renaldy@kana.co.id>
 */
class SITTI_OX_RPC{
	var $xmlRpcHost;
	var $webXmlRpcDir;
	var $serviceUrl;
	var $username;
	var $password;
	var $debug;
	var $oClient; //XML-RPC instance
	var $_status; //Status
	var $_sessionID; //OpenX Session ID
	function SITTI_OX_RPC($username,$password,$service_host="localhost",$service_dir='/openx/www/api/v2/xmlrpc/',$debug=true){
		if(session_id()==NULL){
			session_start();
		}
		
		$this->username = $username;
		$this->password = $password;
		$this->debug = $debug;
		$this->xmlRpcHost = $service_host;
		$this->webXmlRpcDir = $service_dir;
		$this->init();
	}
	function init(){

		$this->serviceUrl = $this->webXmlRpcDir;
	//	print $this->xmlRpcHost;
		//Establish XML_RPC_CLIENT Connection
		$this->oClient = new XML_RPC_Client($this->serviceUrl, $this->xmlRpcHost);
		$this->oClient->setdebug($this->debug);
		$this->logon();
	}
	/**
	 *
	 * logon ke openx webservice
	 */
	function logon(){
		if($_SESSION['ox_rpc_sessid']==NULL){
			$aParams = array(
			new XML_RPC_Value($this->username, 'string'),
			new XML_RPC_Value($this->password, 'string')
			);
			$oMessage  = new XML_RPC_Message('ox.logon', $aParams);
			$oResponse = $this->oClient->send($oMessage);
			if (!$oResponse) {
				$this->_status = -1;
				die('Communication error: ' . $oClient->errstr);
					
			}
			$this->_sessionID = $this->returnXmlRpcResponseData($oResponse);
			$_SESSION['ox_rpc_sessid'] = $this->_sessionID;
			$this->_status = 1;
			if($this->debug){
				echo '*** User logged on with session Id : ' . $this->_sessionID . "<br>\n";
			}
		}else{
			$this->_sessionID = $_SESSION['ox_rpc_sessid'];
			$this->_status=1;
		}
	}
	function logout(){
		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string')
		);
		$oMessage  = new XML_RPC_Message('ox.logoff', $aParams);
		$oResponse = $this->oClient->send($oMessage);
		if (!$oResponse) {
			$this->_status = -1;
			die('Communication error: ' . $oClient->errstr);
				
		}
		$this->_sessionID = "";
		$_SESSION['ox_rpc_sessid'] = "";

	}
	/**
	 *
	 *@internal
	 * @param $oResponse
	 */
	function returnXmlRpcResponseData($oResponse)
	{
		if (!$oResponse->faultCode()) {
			$oVal = $oResponse->value();
			$data = XML_RPC_decode($oVal);
			return $data;
		} else {
			/*die('Fault Code: ' . $oResponse->faultCode() . "\n" .
         'Fault Reason: ' . $oResponse->faultString() . "\n");*/
			$this->_status = -1;
		}
	}
	/**
	 * mendapatkan status operasi
	 */
	function getStatus() {
		return $this->_status;
	}
	/**
	 *
	 * get web service sessionID
	 * @return int
	 */
	function getSessionID(){
		return $this->_sessionID;
	}
	/**
	 *
	 * register SITTI user ke OpenX Advertiser dan Publisher database
	 * @param $userInfo array
	 */
	function registerToOpenX($userInfo){

		if($this->registerAsAdvertiser(&$userInfo)){
			if($this->registerAsPublisher(&$userInfo)){
				return true;
			}
		}
	}
	/**
	 *
	 * Register user SITTI ke openX Advertiser Database
	 * @param $userInfo
	 */
	function registerAsAdvertiser($userInfo){

		$aDetails = array("advertiserName"=>new XML_RPC_Value($userInfo['advertiserName'],"string"),
						  "contactName"=>new XML_RPC_Value($userInfo['contactName'],"string"),
						 "emailAddress"=>new XML_RPC_Value($userInfo['emailAddress'],"string")
		);

		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($aDetails, 'struct')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.addAdvertiser', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	/**
	 *
	 * Register user SITTI ke openX Publisher Database
	 * @param $userInfo
	 */
	function registerAsPublisher($userInfo){
		$aDetails = array("publisherName"=>new XML_RPC_Value($userInfo['publisherName'],"string"),
						  "website"=>new XML_RPC_Value($userInfo['website'],"string"),
		 					"contactName"=>new XML_RPC_Value($userInfo['contactName'],"string"),
							"emailAddress"=>new XML_RPC_Value($userInfo['emailAddress'],"string"),
		);

		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($aDetails, 'struct')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.addPublisher', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	function registerZone($userInfo){
		$aDetails = array("publisherId"=>new XML_RPC_Value($userInfo['pubID'],"int"),
						  "zoneName"=>new XML_RPC_Value($userInfo['zoneName'],"string"),
		 					"type"=>new XML_RPC_Value("3","int"),
							"block"=>new XML_RPC_Value("1","int"),
							"sessionCapping"=>new XML_RPC_Value("1","int")
		);

		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($aDetails, 'struct')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.addZone', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	function addCampaign($userInfo){
		$aDetails = array("advertiserId"=>new XML_RPC_Value($userInfo['advertiserId'],"int"),
						  "campaignName"=>new XML_RPC_Value($userInfo['campaignName'],"string"),
							"priority"=>new XML_RPC_Value("6","int"),
							"weight"=>new XML_RPC_Value("0","int"),
							"targetClicks"=>new XML_RPC_Value($userInfo['targetClicks'],"int")
		);

		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($aDetails, 'struct')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.addCampaign', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	function addBanner($userInfo){
		$aDetails = array("campaignId"=>new XML_RPC_Value($userInfo['campaignId'],"int"),
							"target"=>new XML_RPC_Value("_blank","string"),
							"url"=>new XML_RPC_Value($userInfo['url'],"string"),
							"storageType"=>new XML_RPC_Value("txt","string"),
							"bannerName"=>new XML_RPC_Value($userInfo['bannerName'],"string"),
							"bannerText"=>new XML_RPC_Value($userInfo['bannerText'],"string"),
							"adserver"=>new XML_RPC_Value("SITTI","string"),
							"status"=>new XML_RPC_Value('1',"int")
		);

		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($aDetails, 'struct')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.addBanner', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	function getCode($zoneID){
		/*$aDetails = array("publisherId"=>new XML_RPC_Value($userInfo['pubID'],"int"),
		 "zoneName"=>new XML_RPC_Value($userInfo['zoneName'],"string"),
		 "type"=>new XML_RPC_Value("text","string"),
		 "block"=>new XML_RPC_Value("1","int"),
		 "sessionCapping"=>new XML_RPC_Value("1","int")
		 );

		 $aParams = array(
		 new XML_RPC_Value($this->getSessionID(), 'string'),
		 new XML_RPC_Value($aDetails, 'struct')
		 	
		 );
		 //send the message
		 $oMessage  = new XML_RPC_Message('ox.addZone', $aParams);

		 $oResponse = $this->oClient->send($oMessage);

		 //print_r($this->oClient->headers);
		 $results = $this->returnXmlRpcResponseData($oResponse);
		 return $results;*/
	}
	/**
	 * 
	 * link banner ke Zone
	 * method ini dijalankan ketika sedang meload ad dari SITTI di website publisher.
	 * @param $zoneID
	 * @param $bannerID
	 */
	function linkBannerToZone($zoneID,$bannerID){


		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($zoneID, 'int'),
		new XML_RPC_Value($bannerID, 'int'),
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.linkBanner', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	/**
	 * 
	 * Link campaign ke Zone
	 * method ini dijalankan ketika ad sedang di dipanggil di website publisher.
	 * @param $zoneID
	 * @param $campaignId
	 */
	function linkCampaignToZone($zoneID,$campaignId){


		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($zoneID, 'int'),
		new XML_RPC_Value($campaignId, 'int'),
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.linkCampaign', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	/**
	 * 
	 * ambil statistic untuk advertiser dari openx, berdasarkan advertiser id
	 * @param $advertiserID
	 * @param $start
	 * @param $end
	 */
	function getAdvertiserStatistic($advertiserID,$start,$end){
		//getAdvertiserCampaignStatistics  
		//dateTime.iso8601
		/*if($end==""){
			$end = date("Y-m-d H:i:s");
		}*/
		
		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($advertiserID, 'int'),
		new XML_RPC_Value($start, 'dateTime.iso8601'),
		new XML_RPC_Value($end, 'dateTime.iso8601')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.advertiserCampaignStatistics', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	/**
	 * 
	 * Advertiser's Banner Statistic 
	 * @param $advertiserID
	 * @param $start
	 * @param $end
	 */
	function getAdvBannerStats($advertiserID,$start,$end){
		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($advertiserID, 'int'),
		new XML_RPC_Value($start, 'dateTime.iso8601'),
		new XML_RPC_Value($end, 'dateTime.iso8601')
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.advertiserBannerStatistics', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	function getCampaignPublisherStatistic($campaignID,$start,$end){
		
		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($campaignID, 'int'),
		new XML_RPC_Value($start, 'dateTime.iso8601'),
		new XML_RPC_Value($end, 'dateTime.iso8601')
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.campaignPublisherStatistics', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	
	function getCampaignBannerStatistic($campaignID,$start,$end){
		$aParams = array(
			new XML_RPC_Value($this->getSessionID(), 'string'),
			new XML_RPC_Value($campaignID, 'int'),
			new XML_RPC_Value($start, 'dateTime.iso8601'),
			new XML_RPC_Value($end, 'dateTime.iso8601')
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.campaignBannerStatistics', $aParams);
		$oResponse = $this->oClient->send($oMessage);
		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}
	/**
	 * 
	 * ambil statistic untuk publisher dari openx, berdasarkan zoneid
	 * @param string $zoneID
	 */
	function getPublisherStatistic($zoneID,$start,$end=""){
		//getZoneDailyStatistics 
		//dateTime.iso8601
		/*if($end==""){
			$end = date("Y-m-d H:i:s");
		}*/
		
		$aParams = array(
		new XML_RPC_Value($this->getSessionID(), 'string'),
		new XML_RPC_Value($zoneID, 'int'),
		new XML_RPC_Value($start, 'dateTime.iso8601'),
		new XML_RPC_Value($end, 'dateTime.iso8601')
			
		);
		//send the message
		$oMessage  = new XML_RPC_Message('ox.zoneDailyStatistics', $aParams);

		$oResponse = $this->oClient->send($oMessage);

		//print_r($this->oClient->headers);
		$results = $this->returnXmlRpcResponseData($oResponse);
		return $results;
	}

}
?>