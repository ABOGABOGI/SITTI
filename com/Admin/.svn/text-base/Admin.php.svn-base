<?php
include_once $ENGINE_PATH."Admin/UserManager.php";
class AdminConfig extends SQLData{
	var $strHTML;
	var $View;
	function AdminConfig($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
		$this->User = new UserManager();
		
	}
	
	/****** End-User FUNCTIONS ********************************************/
	
	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		if($this->Request->getPost("r")=="update_email"){
			
		}else if($this->Request->getParam("r")=="permission"){
			if($this->Request->isPostAvailable()){
				$msg = $this->applyPermissions();
			}
			return $this->showPermissionPage($msg);
		}else if($this->Request->getParam("r")=="users"){
			if($this->Request->getPost("do")=="add"){
				return $this->performCreateAccount();
			}else if($this->Request->getPost("do")=="update"){
				return $this->performUpdateAccount();
			}else if($this->Request->getParam("do")=="new"){
				return $this->showCreateAccountForm();
			}else if($this->Request->getParam("do")=="edit"){
				return $this->showEditAccountForm();
			}else if($this->Request->getParam("do")=="delete"){
				return $this->performDeleteUser();
			}else{
				return $this->showUserManagement();
			}
		}else if($this->Request->getRequest("r")=="dashboard"){
			return $this->DashboardConfig();
		}else if($this->Request->getParam("r")=="delete"){
			return $this->performDeleteUser();
		}else{
			return $this->View->toString("common/admin/config.html");
		}
	}
	function DashboardConfig(){
		global $ENGINE_PATH;
		include_once $ENGINE_PATH."Admin/DashboardManager.php";
		$dashboard = new DashboardManager(null);
		if($this->Request->getPost("do")=="save"){
			$dashboard->addItem($this->Request->getPost("name"),
								$this->Request->getPost("class"),
								$this->Request->getPost("invoker")
								,$this->Request->getPost("slot"),
								$this->Request->getPost("status"));
			$this->View->assign("msg","INFO : The addon has been added successfully !");
			
		}else if($this->Request->getParam("do")=="delete"){
			$this->View->assign("msg","INFO : The addon has been removed successfully !");
			$dashboard->removeItem($this->Request->getParam("id"));
		}
		$list = $dashboard->getConfiguration();
		$this->View->assign("list",$list);
		return $this->View->toString("common/admin/dashboard_config.html");
	}
	function performUpdateAccount(){
		$username = $this->Request->getPost("username");
		$password = $this->Request->getPost("password");
		$confirm = $this->Request->getPost("confirm");
		$userID = $this->Request->getPost("id");
		$flag=0;
		if($password!=$confirm){
			$rs['e2'] = "password doesn't match.";
			$flag=1;
		}
		if(strlen($password)<5){
			$rs['e2'] = "wrong password !";
			$flag=1;
		}
		if($flag){
			$msg = "Update failed, wrong password format and/or the password doesn't match.";
		}else{
			if($this->User->update($userID,$username,$password)){
				$msg = "Update Success !";
			}else{
				$msg = "Update failed, wrong password format and/or the password doesn't match.";
			}
		}
		return $this->View->showMessage($msg,"?s=admin&r=users");
	}
	function performCreateAccount(){
		$username = $this->Request->getPost("username");
		$password = $this->Request->getPost("password");
		$confirm = $this->Request->getPost("confirm");
		$rs = $_POST;
		$flag=0;
		if(strlen($username)<5){
			$rs['e1'] = "username is in a wrong format!";
			$flag=1;
		}
		if(!eregi("^([a-zA-Z0-9]+)$",$username)){
			$rs['e1'] = "username is in a wrong format!";
			$flag=1;
		}
		if($password!=$confirm){
			$rs['e2'] = "password doesn't match.";
			$flag=1;
		}
		if(strlen($password)<5){
			$rs['e2'] = "wrong password !";
			$flag=1;
		}
		if($flag){
			return $this->showCreateAccountForm($rs);
		}else{
			if($this->User->add(trim(stripslashes($username)),$password)){
				$msg = "New account has been created successfully !";
			}else{
				$msg = "Account creation is failed !";
			}
			return $this->View->showMessage($msg,"?s=admin&r=users");
		}
		
	}
	function showCreateAccountForm($rs=NULL){
		$this->View->assign("rs",$rs);
		return $this->View->toString("common/admin/user_new.html");
	}
	function showEditAccountForm(){
		$userID = $this->Request->getParam("id");
		$rs = $this->fetch("SELECT * FROM gm_user WHERE userID='".$userID."' LIMIT 1");
		$this->View->assign("rs",$rs);
		
		return $this->View->toString("common/admin/user_edit.html");
	}
	function performDeleteUser(){
		if($this->User->delete($this->Request->getParam("id"))){
			$msg = "The account has been deleted successfully !";
		}else{
			$msg = "Cannot delete the account, please try again later !";
		}
		return $this->View->showMessage($msg,"?s=admin&r=users");
	}
	function applyPermissions(){
			$id = $this->Request->getPost("id");
			$this->query("DELETE FROM gm_permission WHERE userID='".$id."'");
			$msg = "Permission updated!";
			$permissions = $_POST;
			foreach($permissions as $name=>$val){
				if($val=="yes"){
					$this->query("INSERT gm_permission(userID,reqID)
									  VALUES('".$id."','".$name."')");
				}
			}
			return $msg;
	}
	function showPermissionPage($msg=""){
		$user = $this->User->getUserInfo($this->Request->getParam("id"));
		$this->View->assign("rs",$user);
		//request id list
		$list = $this->fetch("SELECT * FROM gm_module_registry ORDER BY requestID",1);
		for($i=0;$i<sizeof($list);$i++){
			//get permission
			$foo = $this->fetch("SELECT * FROM gm_permission 
								  WHERE userID='".$user['userID']."' 
								  AND reqID='".$list[$i]['requestID']."' 
								  LIMIT 1");
			if($foo['userID']==$user['userID']&&$foo['reqID']==$list[$i]['requestID']){
				$list[$i]['isAllowed'] = "1";
			}
		}
		$this->View->assign("list",$list);
		$this->View->assign("msg",$msg);
		return $this->View->toString("common/admin/permission.html");
	}
	function showUserManagement(){
		$start = $this->Request->getParam('st');
		if($start==NULL){$start=0;}
		$this->User->database = $this->database;
		$list = $this->User->getAllUsers(base64_encode(date("YmdHi")),$start,30);
		
		for($i=0;$i<sizeof($list);$i++){
			$list[$i]['no'] = $start+$i+1;
		}
		$this->View->assign("list",$list);
		return $this->View->toString("common/admin/user_manage.html");
	}	
}
?>