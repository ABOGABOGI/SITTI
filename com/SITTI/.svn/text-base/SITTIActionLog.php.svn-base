<?php

class SITTIActionLog extends SQLData{
	function SITTIActionLog(){
		parent::SQLData();
	}
	
	function actionLog($action_code, $param1 = "", $param2 = "", $param3 = ""){
		$this->open(0);
		$this->insertActionLog($action_code, $param1, $param2, $param3);
		$this->close();
	}
	
	function insertActionLog($action_code, $param1 = "", $param2 = "", $param3 = ""){
		return $this->query("INSERT INTO zz_sitti.sitti_action_log(action_code, param1, param2, param3) VALUES('".$action_code."', '".$param1."', '".$param2."', '".$param3."')");
	}
}

?>