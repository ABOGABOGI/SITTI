<?php 
class PluginManager extends SQLData{
	function PluginManager(){
		$this->SQLData();
	}
	function getPluginByRequestID($reqID){
		$this->open();
		$rs = $this->fetch("SELECT * FROM gm_plugin 
							 WHERE requestID='".$reqID."' 
							 LIMIT 1");
		$this->close();
		return $rs;
	}
	function getPlugins(){
		$this->open();
		$rs = $this->fetch("SELECT * FROM gm_plugin ORDER BY plugin_name",1);
		$this->close();
		return $rs;
	}
	
}
?>