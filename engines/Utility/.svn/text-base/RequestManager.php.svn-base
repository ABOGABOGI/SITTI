<?php
class RequestManager{
	var $requests = array(array());
	function RequestManager($autoClean=false){
		$this->requests['POST'] = $_POST;
		$this->requests['GET'] = $_GET;
		$this->requests['SESSION'] = $_SESSION;
		$this->requests['COOKIE'] = $_COOKIE;
		$this->requests['ENV'] = $_ENV;
		$this->requests['SERVER'] = $_SERVER;
		$this->requests['FILE'] =	$_FILES;
		$this->requests['REQUEST'] = $_REQUEST;
		if(strlen($_GET['xn'])>0){
			$this->secureURL($_GET['xn']);
		}
		//if($autoClean){clean();}
	}
	function secureURL($hash){
		$params = json_decode(urldecode64($hash));
		for($i=0;$i<sizeof($params);$i++){
			$this->requests['GET'][$params[$i]->name] = $params[$i]->value;
			$this->requests['REQUEST'][$params[$i]->name] = $params[$i]->value;
		}
		
	}
	function clean($str){
		//cleaning up process here
		$str = mysql_escape_string($str);
		
		$str = str_replace("UNION","",$str);
		$str = str_replace("UPDATE%20","",$str);
		$str = str_replace("SELECT%20","",$str);
		$str = str_replace("CREATE%20TABLE","",$str);
		$str = str_replace("DELETE%20","",$str);
		$str = str_replace("DROP%TABLE","",$str);
		$str = str_replace("DROP%INDEX","",$str);
		$str = str_replace("DROP%USER","",$str);
		$str = str_replace("DROP%DATABASE","",$str);
		
		$str = str_replace("union","",$str);
		$str = str_replace("update%20","",$str);
		$str = str_replace("select%20","",$str);
		$str = str_replace("create%20table","",$str);
		$str = str_replace("delete%20","",$str);
		$str = str_replace("drop%20table","",$str);
		$str = str_replace("drop%20index","",$str);
		$str = str_replace("drop%20user","",$str);
		$str = str_replace("drop%20database","",$str);
		
		return $str;
	}
	function getParam($name){
		return $this->clean($this->requests['GET'][$name]);
	}
	function getRequest($name){
		return $this->clean($this->requests['REQUEST'][$name]);
	}
	function setParam($name,$value){
		$this->requests['GET'][$name] = $value;
	}
	function getPost($name){
		return $this->clean($this->requests['POST'][$name]);
	}
	function getCookie($name){
		return $this->requests['COOKIE'][$name];
	}
	function getSession($name){
		return $this->requests['SESSION'][$name];
	}
	function getFileName($name){
		return $this->requests['FILE'][$name]['name'];
	}
	function getFileTemp($name){
		return $this->requests['FILE'][$name]['tmp_name'];
	}
	function getFileSize($name){
		return $this->requests['FILE'][$name]['file_size'];
	}
	function isPostAvailable(){
		if(sizeof($_POST)>0){
			return true;
		}	
	}
	function isFileExist($name){
		if($this->requests['FILE'][$name]['name']!=NULL&&
		$this->requests['FILE'][$name]['tmp_name']!=NULL)
		{return true;}
	}
	function peek(){
		print "GET :<br/>";
		print_r($_GET);
		print "<br/>POST:<br/>";
		print_r($_POST);
		print "<br/>SESSION:<br/>";
		print_r($_SESSION);
	}
}
?>
