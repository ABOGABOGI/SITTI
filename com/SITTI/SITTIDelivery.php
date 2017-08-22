<?php
class SITTIDelivery extends SQLData{
	var $Request;
	var $advertiserID;
	var $publisherID;
	var $refURL; //referral url
	var $View;
	function SITTIDelivery($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
	}
	function getTopKeywords($uri,$total=3){
		
		/*$sql = "SELECT kata, jumlah FROM tb_web_kata
				WHERE (webs ='" . $uri . "')
				ORDER BY jumlah DESC LIMIT ".$total;*/
		
		$sql = "SELECT webs, kata
				FROM db_web2.sitti_pub_keywords
				WHERE (webs = '".$uri."')";
		
		$rs = $this->fetch($sql,1);
		
		//print mysql_error();
		$n = sizeof($rs);
		$words = "";
		for($i=0;$i<$n;$i++)
		{
			if($i>0){
				$words = $words . ", '" .$rs[$i]['kata']."'" ;
			}else{
				$words = "'".$rs[$i]['kata']."'";
			}
		}
		return $words;
	}
	function getRelevantAds($uri){
		$pub_keywords = $this->getTopKeywords($uri);
		$sql = "SELECT
				a.iklan_id, b.nama, b.judul, b.baris1, b.baris2
					, b.category, b.urlName as linkName, b.urlLink as linkURL
				FROM db_web2.sitti_ad_keywords AS a
				INNER JOIN db_web2.sitti_ad_inventory AS b 
				ON (a.iklan_id = b.id)
				WHERE (a.keyword IN (".$pub_keywords."))
				GROUP BY a.iklan_id";
		
		$list = $this->fetch($sql,1);
		return $list;
		//print mysql_error();
		//print_r($list);
	}
	function getAds(){
		$this->open(1);
		$ads = $this->getRelevantAds($this->getRefererURL());
		$this->close();
		return $ads;
	}
	
	function setRefererURL($url){
		$this->refURL = $url;
	}
	function setPublisherId($id){
		$this->publisherID = $id;
	}
	function getAdvertiserId(){
		return $this->advertiserID;
	}
	function getRefererURL(){
		return $this->refURL;
	}
	function getPublisherId(){
		return $this->publisherID;
	}
	function getDefaultAds(){
		
	}
	function show(){
	
		$ads = $this->getAds();
		if(sizeof($ads)>0){
			$this->View->assign("list",$ads);
		}else{
			//$this->View->assign("list",$this->getDefaultAds());
		}
		print $this->View->toString("SITTI/ads/ad1.html");
	}
}
?>