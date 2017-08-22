<?php
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIKloutData.php";

class SITTISocial extends StaticPage{
	var $KloutData;
	function SITTISocial($req){
		parent::StaticPage(&$req);
		$this->KloutData = new SITTIKloutData();
	}
	
	function fetchDataStream($category_id, $last_id=false, $brand=false){
		return $this->KloutData->getDataStream($category_id, $last_id, $brand);
	}
	
	function fetchSNAData($category_id, $method){
		return $this->KloutData->getSNAData($category_id, $method);
	}
	
	function fetchKeywordCloudData($category_id, $brand=false){
		return $this->KloutData->getKeywordCloudData($category_id, $brand);
	}
	
	function fetchTopKeyword($category_id, $brand=false){
		return $this->KloutData->getTopKeyword($category_id, $brand);
	}
	
	function fetchTopUser($category_id, $brand=false){
		return $this->KloutData->getTopUser($category_id, $brand);
	}
	
	function fetchStatsData($category_id, $brand=false){
		return $this->KloutData->getStatsData($category_id, $brand);
	}
	
	function insertFeedback($name, $telp="", $email="", $feedback=""){
		$this->KloutData->insertFeedback($name, $telp, $email, $feedback);
	}
	
	function insertBrandContact($brand, $name="", $telp="", $email="", $message=""){
		$this->KloutData->insertBrandContact($brand, $name, $telp, $email, $message);
	}
	
	function insertCategoryContact($category, $name="", $telp="", $email="", $message=""){
		$this->KloutData->insertCategoryContact($category, $name, $telp, $email, $message);
	}
	
	function InitializeMain(){
		$category = $this->KloutData->getAllCategory();
		$this->View->assign("category", $category);
		return $this->View->toString("SITTI/social/main.html");
	}
}
?>