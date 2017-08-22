<?php 
/**
 * SITTIApp
 * base class untuk semua komponen2 SITTI
 * 
 */
include_once $APP_PATH."StaticPage/StaticPage.php";

class SITTIApp extends SQLData{
	var $Account;
	var $View;
    function SITTIApp($req,$account){
       parent::SQLData($req);
       $this->Request = $req;
       $this->View = new BasicView();
       $this->Account = $account;
    }
    //helper methods
    /**
     * 
     * helper method for POST
     * @param $name
     */
    function p($name){
    	return $this->Request->getPost($name);
    }
    /**
     * 
     * helper method for GET
     * @param $name
     */
    function g($name){
    	return $this->Request->getParam($name);
    }
    /**
     * 
     * Helper method for Request
     * @param $name
     */
    function r($name){
    	return $this->Request->getRequest($name);
    }
   
   
}
?>