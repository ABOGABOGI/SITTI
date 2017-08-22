<?php
class Permission extends Authentication{
	function Permission(){
		parent::Authentication();
	}
	function isAllowed($reqID){
		$userID = $this->Session->getVariable("uid");
		$this->open();
		$rs = $this->fetch("SELECT * FROM gm_permission WHERE userID='".mysql_escape_string($userID)."' AND reqID='".mysql_escape_string($reqID)."'");
		$this->close();
		
		if($rs['userID']==$userID&&$rs['reqID']==$reqID){
			return true;
		}
	}
}
?>