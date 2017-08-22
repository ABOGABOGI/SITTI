<?php 

include_once $APP_PATH."SITTI/Model/SITTITestQuery.php";

define("HOME", "SITTI/index.html");
define("CONTACT_FORM", "SITTI/kontak.html");
define("LOGIN_FORM", "SITTI/login.html");
define("REGISTRATION_FORM", "SITTI/daftar.html");
define("CONTENT_PAGE", "SITTI/content.html");
class SITTITest extends SQLData{
	var $View;
	var $stmt;
	function SITTITest($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
		$this->stmt = new SITTITestQuery();
	}
  
  /**
   * fungsi cari kata baru untuk halaman uji sitti
   * @param $txt
   */
	function search2($txt){
		global $CONFIG,$LOCALE;
		$txt = strtolower($txt);
		if(preg_match("/([0-9\'\-\"\_])+/",$txt)){
			return $LOCALE['KEYWORD_SUGGESTION_NUMERIC_ERROR'];
		}
		$arr = explode(" ",trim($txt));
		$strKey="";
		for($i=0;$i<sizeof($arr);$i++){
			if($i!=0){
				$strKey.=",";
			}
			$strKey.="'".$arr[$i]."'";
			
		}
		$sql1 = $this->stmt->getSuggestion($strKey);
		$this->open(1);
		$rs = $this->fetch($sql1,1);
   
    //save new phrases from user input
		$txt = mysql_escape_string(stripslashes(strip_tags($txt)));
    	$ip = getRealIP();
    	$this->query($this->stmt->savePhraseQuery($txt, $ip,''));
		$this->close();
		for($i=0;$i<sizeof($rs);$i++){
				$list[$i] = $rs[$i]['kata2'];
		}
		$this->View->assign("list",$list);
   
		return $this->View->toString("SITTI/uji_keywords.html");
	}
  /**
   * fungsi mencari saran frase.
   */
  function searchPhrase($txt){
    global $CONFIG;
    $arr = explode(" ",trim($txt));
    $strKey="";
    for($i=0;$i<sizeof($arr);$i++){
      if($i!=0){
        $strKey.=",";
      }
      $strKey.="'".$arr[$i]."'";
      
    }
    $sql1 = $this->stmt->getPhraseSuggestion($strKey);
    $this->open(1);
    $list = $this->fetch($sql1,1);
    $this->close();
    $n = sizeof($list);
   	for($i=0;$i<$n;$i++){
   		$list[$i]['combo_encrypted'] = urlencode64($list[$i]['kata1']."_".$list[$i]['kata2']);
   	}
    $this->View->assign("list",$list);
    return $this->View->toString("SITTI/form_iklan/daftar_phrase.html");
  }
	function search($txt,$type=1){
		global $CONFIG;
		//query semua
		
		if(sizeof(explode(" ",$txt))>1){
			$arr = explode(" ",$txt);
			$txt = trim($arr[0]." ".$arr[1]);
			//lebih dari 1 kata
			$sql1 = $this->stmt->getRelatedKeywordOverall2($txt);
			//print_r($sql1);
			$sql2 = $this->stmt->getRelatedWebKeyword2($txt);
		}else{
			//1 kata
			$sql1 = $this->stmt->getRelatedKeywordOverall($txt);
			$sql2 = $this->stmt->getRelatedWebKeyword($txt);
		}
		
		  $this->open(1);
		if($type==1){
			$rs = $this->fetch($sql1,1);
			
		}else{
			$rs = $this->fetch($sql2,1);
			print mysql_error();
		}
		$this->close();
		//print_r($rs);
		for($i=0;$i<sizeof($rs);$i++){
				$list[$i] = $rs[$i]['kata1'];
			}
			
		if(sizeof($list)>0){
			$merge[0] = $txt;
			$list = array_merge($merge,$list);
		}
		$this->View->assign("list",$list);
		return $this->View->toString("SITTI/uji_keywords.html");
	}
	
	function searchAdvanced($txt,$type=1){
		global $CONFIG;
		  $this->open(1);
		  //2 kata
		  if(sizeof(explode(" ",$txt))>1){
		  	$arr = explode(" ",$txt);
		  	$txt = trim($arr[0]." ".$arr[1]);
		  	if($this->Request->getParam("n")=="1"){
		  		$this->SearchSITTIDataAdvanced2($txt,$type);
		  	}else{
		  		$this->SearchSocialAdvanced2($txt,$type);
		  	}
		  	
		  }else{
		  	//1 kata
		  	if($this->Request->getParam("n")=="1"){
		  		$this->SearchSITTIDataAdvanced($txt,$type);
		  	}else{
		  		$this->SearchSocialAdvanced($txt,$type);
		  	}
		  }
		return $this->View->toString("SITTI/uji_keywords.html");
	}
	function SearchSITTIDataAdvanced($txt,$type){
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getRelatedByWordType($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	function SearchSocialAdvanced($txt,$type){
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getWebKeywordByType($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	
	function SearchSITTIDataAdvanced2($txt,$type){
		//kita batasi jadi 2 keyword dulu.
		$arr = explode(" ",$txt);
		$txt = trim($arr[0]." ".$arr[1]);
		
		//-->
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getRelatedByWordType2($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		//print_r($rs);
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
	function SearchSocialAdvanced2($txt,$type){
		$arr = explode(" ",$txt);
		$txt = trim($arr[0]." ".$arr[1]);
		if($type==1){
			//kata kerja
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "2"),1);
		}else if($type==2){
			//kata sifat
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "1"),1);
		}else if($type==3){
			//kata benda
			$rs = $this->fetch($this->stmt->getWebKeywordByType2($txt, "3"),1);
		}else{
			//brand
			$rs = $this->fetch($this->stmt->getRelatedBrand($txt),1);
			//print_r($rs);
		}
		$this->close();
		
		for($i=0;$i<sizeof($rs);$i++){
			$list[$i] = $rs[$i]['kata2'];
		}
		
		//pastikan listnya kosong, kalau memang kosong.
		if(strlen(trim($list[0]))==0){
			$list = null;
		}
		$this->View->assign("list",$list);
	}
    function show($keywords=""){
		$this->View->assign("keywords",urldecode($keywords));
    	return $this->View->toString("SITTI/uji.html");
    }
    function showAdvanced(){
    	return $this->View->toString("SITTI/uji_advanced.html");
    }		
}
?>