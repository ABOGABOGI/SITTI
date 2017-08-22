<?php
include_once $ENGINE_PATH."Utility/Paginate.php";
class Sitemap extends SQLData{
	var $strHTML;
	var $View;
	var $Static;
	var $startCount;
	var $menus;
	function Sitemap($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
		$this->Paging = new Paginate();
		
	}
	function addMenu($handle,$value){
		$this->menus[$handle] = $value;
	}
	/****** End-User FUNCTIONS ********************************************/
	function show(){
		$this->View->assign("menu",$this->menus['MENU_ABOUT']);
		$this->View->assign("url","page.php?id=");
		$this->View->assign("MENU_ABOUT",$this->View->toString("sitemap/menu.html"));
		$this->View->assign("menu",$this->menus['MENU_SERVICE']);
		$this->View->assign("url","page.php?id=");
		$this->View->assign("MENU_SERVICE",$this->View->toString("sitemap/menu.html"));
		return $this->View->toString("sitemap/page.html");
	}
	
	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		
	}

}
?>