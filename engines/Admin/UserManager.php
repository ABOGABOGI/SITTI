<?php
class UserManager extends SQLData{
	var $userID;
	function UserManager(){
		parent::SQLData();
		
	}
	function add($username,$password){
		$enc_key = md5(base64_encode($username.md5($password)));
		$password = md5($password);
		$this->open();
		$rs = $this->query("INSERT INTO gm_user(username,password,enc_key)
					  VALUES('".$username."','".$password."','".$enc_key."')");
		$this->close();
		return $rs;
	}
	function delete($userID){
		$this->open();
		$f = $this->query("DELETE FROM gm_user WHERE userID='".$userID."'");
		$this->close();
		return $f;
	}
	function update($userID,$username,$password){
		$enc_key = md5(base64_encode($username.md5($password)));
		$password = md5($password);
		
		$f = $this->query("UPDATE gm_user SET username='".$username."', 
						   password='".$password."',enc_key='".$enc_key."' 
						   WHERE userID='".$userID."'");
		return $f;
	}
	function getAllUsers($AC,$start,$total=30){
		
		if($AC==base64_encode(date("YmdHi"))){
			//$this->open();
			
			$rs = $this->fetch("SELECT * FROM gm_user ORDER BY userID LIMIT ".$start.",".$total,1);
			//$this->close();	
			
			return $rs;
		}else{
			
			return false;
		}
	}
	function getUserInfo($userID){
		if(strlen(stripslashes($userID))>0){
			return $this->fetch("SELECT * FROM gm_user WHERE userID='".$userID."' LIMIT 1");
		}
	}
	function check($username,$password){
		$enc_key = md5(base64_encode($username.md5($password)));
		$password = md5($password);
		$this->open();
		$rs = $this->fetch("SELECT userID,username,password,enc_key FROM gm_user
							WHERE username='".mysql_escape_string($username)."' AND password='".$password."'");
		
		if($rs['username'] == $username && $rs['password'] == $password && $rs['enc_key'] = $enc_key){
			$this->userID = $rs['userID'];
			$this->close();
			return true;
		}else{
			print mysql_error();
		}
		$this->close();
	}
}
?>