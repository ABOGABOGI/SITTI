<?php
class Authentication extends SQLData{
	function Authentication(){
		parent::SQLData();
		$this->Session = new SessionManager("GM_ADMIN");
	}
	function isLogin(){
		if($this->Session->getVariable("isLogin")=="1"){
			return true;
		}
	}
	function login(){
		$this->Session->addVariable("isLogin","1");
		
	}
	function getUserID(){
		
	}
	function getUserName(){
			
	}
	function logout(){
		
	}
}
?>