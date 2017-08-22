<?php 
include_once $APP_PATH."SITTI/Model/SITTITestQuery.php";

define("HOME", "SITTI/index.html");
define("CONTACT_FORM", "SITTI/kontak.html");
define("LOGIN_FORM", "SITTI/login.html");
define("REGISTRATION_FORM", "SITTI/daftar.html");
define("CONTENT_PAGE", "SITTI/content.html");
class SITTISimulation extends SQLData{
	var $View;
	var $stmt;
	function SITTISimulation($req=null){
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
		$is_ppa = (bool) $this->Request->getParam("ppa"); 
		
		$max_n = 100;
		$txt = urldecode(strtolower($txt));

		$c = $this->Request->getParam("c");
		if(preg_match("/([0-9\'\-\"\_])+/",$txt) && (! $_SESSION['is_ppa'])){
			return $LOCALE['KEYWORD_SUGGESTION_NUMERIC_ERROR'];
		}
		//pattern for searching characters we don't want.
		$pattern = "/([^A-Za-z0-9\"\'\!\@\#\$\%\^\&\*\(\)\.\,\_\-\=\+\?\:\;\"\'\/\"\ ]+)/";
		$txt = preg_replace($pattern, "", $txt);
		$arr = explode(",",trim($txt));
		$strKey="";
		$n_main = sizeof($arr);
		for($i=0;$i<$n_main;$i++){
			
			if(strlen($arr[$i])>0){
				//inquiry keys (key yang di input oleh user)
				$input_keys[$i]['kata2'] = $arr[$i];
				//print $arr[$i]."-<br/>";
				if($i!=0){
					$strKey.=",";
				}
				$strKey.="'".$arr[$i]."'";
			}
		}
		$start = $this->Request->getParam('start');
		
		if ( $is_ppa )
		{
			$per_page = intval($this->Request->getParam('per_page'));
			$max_n = $per_page + $n_main;	
		}
		
		if($start==null){
			$start = 0;
		}
		if($start<$n_main){
			$total = $max_n-$start-$n_main;
		}else if($start>$n_main){
			$total = $max_n;
		}else{
			$total = 0 ;
		}
		//print $total;
		//$total = $max_n;
		
		$sql1 = $this->stmt->getSuggestion2($strKey,$start,$total);
	
		$this->open(1);
		$suggested = $this->fetch($sql1,1);
		
		//print mysql_error();
		$n_suggest = sizeof($suggested);
		$suggested_keys = "";
		for($i=0;$i<$n_suggest;$i++){
			if($i!=0){
				$suggested_keys.=",";
			}
			$suggested[$i]['kata2'] = strtolower($suggested[$i]['kata2']);
			$suggested_keys.="'".$suggested[$i]['kata2']."'";
			
			
		}
	
		$sql3 = "SELECT keyword, avg_cpc, jum_imp, jum_hit, ctr, jum_guna, 1 as is_main 
    FROM db_web3.sitti_keywords_simulasi
    WHERE keyword IN (".$strKey.")
    ORDER BY ctr DESC";
		//print $sql3."<br/>---------------<br/>";
		//print $start."-".$n_main;
		if($start<$n_main || $is_ppa){
			$main_keys = $this->fetch($sql3,1);
			
			if(sizeof($main_keys)!=sizeof($input_keys)){
				if(sizeof($main_keys)==0){
					$main_keys = array();
				}
				//ini artinya kata yang di input tidak ada sama sekali
				for($i=0;$i<sizeof($input_keys);$i++){
					$flag=0;
					for($j=0;$j<sizeof($main_keys);$j++){
						if($input_keys[$i]['kata2']==$main_keys[$j]['keyword']){
							$flag=1;
							break;
						}
					}
					if($flag==0){
						//print "yg gak ketemu -->".$input_keys[$i]['kata2'];
						array_push($main_keys,array("keyword"=>$input_keys[$i]['kata2'],
												"avg_cpc"=>$CONFIG['MINIMUM_BID'],
												"jum_imp"=>0,
												"jum_hit"=>0,
												"ctr"=>0,
												"jum_guna"=>0,						
												"is_main"=>1));
					}
				}
			}
		}
		//$suggested_keys = $strKey.",".$suggested_keys;
		//query data keyword summary berdasarkan list dari suggested keywords
		$sql3 = "SELECT keyword, avg_cpc, jum_imp, jum_hit, ctr, jum_guna 
    FROM db_web3.sitti_keywords_simulasi
    WHERE keyword IN (".$suggested_keys.")
    ORDER BY ctr DESC";
		
		//print $sql3."<br/>---------------<br/>";
		//print_r($suggested);
		//print $sql3;
		if($n_suggest>0){
			$list = $this->fetch($sql3,1);
			
			if(is_array($main_keys)&&sizeof($list)>0){
				$list = array_merge($main_keys,$list);
			}else if($main_keys!=null&&sizeof($list)==0){
				$list = $main_keys;
			}
		}else{
			$list = $main_keys;
		}
		
    
		$txt = mysql_escape_string(stripslashes(strip_tags($txt)));
    	$ip = getRealIP();
    	//$this->query($this->stmt->savePhraseQuery($txt, $ip,''));
    	$sql = "SELECT jum_guna FROM db_web3.sitti_keywords_simulasi ORDER BY jum_guna DESC LIMIT 1";
    	$top = $this->fetch($sql);
		$this->close();
		$n_list = sizeof($list);
		$empty_keys = array();
		for($i=0;$i<$n_suggest;$i++){
			$flag = false;
			for($j=0;$j<$n_list;$j++){
				$list[$j]['keyword'] = strtolower($list[$j]['keyword']);
				if($suggested[$i]['kata2']==$list[$j]['keyword']){
					$flag = true;
					break;
				}
			}
			if($flag==false){
				$empty_keys[sizeof($empty_keys)] = array("keyword"=>$suggested[$i]['kata2'],
														 "jum_imp"=>0,"avg_cpc"=>$CONFIG['MINIMUM_BID'],"jum_guna"=>0);
			}	
		}
		//empty keys berdasarkan input
		if($this->Request->getParam("start")==NULL || $this->Request->getParam("start")=="0"){
			for($i=0;$i<$n_main;$i++){
				$flag = false;
				for($j=0;$j<$n_list;$j++){
					if($arr[$i]==$list[$j]['keyword']){
						$flag = true;
						break;
					}
				}
				if($flag==false){
					$empty_keys[sizeof($empty_keys)] = array("keyword"=>$arr[$i],
															 "jum_imp"=>0,"avg_cpc"=>$CONFIG['MINIMUM_BID'],"jum_guna"=>0,"is_main"=>"1");
				}	
			}
		}
		//-->
		//print_r($empty_keys);
		if(sizeof($empty_keys)>0){
			if(is_array($list)){
				$list = array_merge($list,$empty_keys);
			}else{
				$list = $empty_keys;
			}
		}
		$n_list = sizeof($list);
		for($i=0;$i<$n_list;$i++){
			
			
				//print $list[$i]['jum_imp']."<br/>";
				
				$list[$i]['index'] = ($c*40)+$i;
				$list[$i]['jum_imp'] = round(floor($list[$i]['jum_imp'] / 100)*100,0);
				$list[$i]['avg_cpc'] = round(floor($list[$i]['avg_cpc'] / 10)*10,0);
				if($list[$i]['avg_cpc']==0){
					$list[$i]['avg_cpc'] = $CONFIG['MINIMUM_BID'];
				}
				$list[$i]['popularity'] = ceil(($list[$i]['jum_guna']/$top['jum_guna'])*100);
				///print $list[$i]['popularity']."<br/>";
				//$list[$i]['keyword'] = $rs[$i]['kata2'];
				
			
			
		}

		$this->View->assign("ppa",$this->Request->getParam('ppa'));
		$this->View->assign("list",$list);
        $this->View->assign("PAGE",$start);

        if ( $is_ppa )
		{
			$list_keyword = array_slice($list, 0, $n_main);
			$list_suggested_keywords = array_slice($list, $n_main, $total - $n_main + 1);

			// list suggested keywords sorted by impression
			foreach ($list_suggested_keywords as $idx => $row)
			{
				$jum_imp[$idx] = floatval($row["jum_imp"]);
			}
			array_multisort($jum_imp, SORT_DESC, $list_suggested_keywords);

			$this->View->assign("list_keyword",$list_keyword);
			$this->View->assign("list",$list_suggested_keywords);
			return $this->View->toString("SITTI/ads/create_ppa_pilih_keywords_content.html");	
		}
		else
		{
			if ($this->Request->getParam("wizard")){
				return $this->View->toString("SITTI/uji_keywords_sim_wizard.html");	
			}else{
				return $this->View->toString("SITTI/uji_keywords_sim.html");	
			}
		}
		
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
		
		//return $this->View->toString("SITTI/uji_keywords.html");
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
    function show(){
    	return $this->View->toString("SITTI/uji.html");
    }
    function showAdvanced(){
    	return $this->View->toString("SITTI/uji_advanced.html");
    }	
    function SimulationPage(){
    	global $CONFIG;
    	$req = $this->Request;
    	$q = preg_replace("/(\r\n)+|(\n|\r)+/", ";", $_POST['q']);
    	$c = $req->getPost("c");
    	$hari = $req->getPost("hari");
    	//print_r($q);
    	$main_key = $_POST['keywords'];
    	$hari = $req->getPost("hari");
    	$json = json_encode(array($hari,$main_key));
    	$_SESSION['keypicks']=base64_encode($json);
    	//$main_key = explode(";",$q);
    	//print_r($main_key);
    	$n_len = sizeof($main_key);
    	//jumlah hasil pencarian tidak dibatasi lagi.
    	#if($n_len>10){
    		#$n_len = 10;
    	#}
    	for($i=0;$i<$n_len;$i++){
    		$list[$i]['no'] = $i+1;
    		$list[$i]['list'][0] = strip_tags(mysql_escape_string($main_key[$i]));
    		   //ambil daftar saran kata untuk mempopulasi dropdown menu
    			$this->open(0);
    			$sql = $this->stmt->getSuggestion2("'".$list[$i]['list'][0]."'",0,40);
    			
    			$foo = $this->fetch($sql,1);
    			$this->close();
    			for($j=1;$j<=40;$j++){
    				if(strlen($foo[$j]['kata2'])>1){
    					$list[$i]['list'][$j] = $foo[$j]['kata2'];
    				}  
    			}
    	}
    	
    	$this->View->assign("main_key",$list);
    	$this->View->assign("hari",$hari);
    	$this->View->assign("n_slots",$n_len);
    	$this->View->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);
    	return $this->View->toString("SITTI/simulasi.html");
    }
	function Detil(){
  		global $CONFIG;
		
		$req = $this->Request;
    	$q = preg_replace("/(\r\n)+|(\n|\r)+/", ";", $_POST['q']);
    	$c = $req->getPost("c");
    	$hari = "0";
    	
    	$main_key = $_POST['keywords'];
    	$json = json_encode(array($hari,$main_key));
    	$_SESSION['keypicks']=base64_encode($json);
    	$n_len = sizeof($main_key);
    	if($n_len>100){
    		$n_len = 100;
    	}
    	for($i=0;$i<$n_len;$i++){
    		$list[$i]['no'] = $i+1;
    		$list[$i]['list'][0] = strip_tags(mysql_escape_string($main_key[$i]));
    		
    			$this->open(0);
    			$sql = $this->stmt->getSuggestion2("'".$list[$i]['list'][0]."'",0,40);
    			
    			$foo = $this->fetch($sql,1);
    			$this->close();
    			for($j=1;$j<=40;$j++){
    				if(strlen($foo[$j]['kata2'])>1){
    					$list[$i]['list'][$j] = $foo[$j]['kata2'];
    				}  
    			}
    	}
		
		$slots = $n_len;
  		$picks = array();
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['keyword'] = $list[$i]['list'][0];
  			$keywords[$i]['bid'] = $CONFIG['MINIMUM_BID'];
  			$keywords[$i]['budget'] = "";
  			$keywords[$i]['total'] = "";
  			$keydata = json_decode($this->getKeywordData($keywords[$i]['keyword']));
			$keywords[$i]['max_cpc'] = $keydata->avg_cpc;
  			array_push($picks,$keywords[$i]['keyword']);
  		}
		
		// old detil
		// $req = $this->Request;
		// $slots = $req->getPost("slots");
  		// $picks = array();
  		// for($i=0;$i<$slots;$i++){
  			// $budget = $req->getPost("budget_".$i);
  			// $hari = $req->getPost("hari");
  			// $bid = $req->getPost("avg_cpc_".$i);//bid
  			// $cpc = $req->getPost("cpc_".$i); //max cpc
  			// settype($budget, 'integer');
  			// settype($hari, 'integer');
  			// settype($bid,'integer');
  			// settype($cpc,'integer');
  			// $keywords[$i]['keyword'] = $req->getPost("daily_".$i);
			// if($bid<$CONFIG['MINIMUM_BID']){
          	 	// $bid = $CONFIG['MINIMUM_BID'];
          	// }
  			// $keywords[$i]['bid'] = $bid;
  			// $keywords[$i]['budget'] = $budget;
  			// $keywords[$i]['total'] = $budget*$hari;
  			// $keywords[$i]['max_cpc'] = $cpc;
  			// array_push($picks,$keywords[$i]['keyword']);
  		// }
  		//old detil
		
  		$oldpicks = json_decode(base64_decode($_SESSION['keypicks']));
  		
  		$bids = json_encode($keywords);
  		$_SESSION['adsetskeys'] = base64_encode($bids);
  		$_SESSION['keypicks'] = base64_encode(json_encode(array($oldpicks[0],$picks)));
		
  		$hari = $_POST['hari'];
  		for($i=0;$i<$slots;$i++){
  			$keywords[$i]['hari'] = $hari;
  		}
  		$son = json_encode($keywords);
		//print_r($son);
  		
  		$_SESSION['adbids'] = base64_encode($son);
  		//print_r($_SESSION);
  		//return $this->FormIklanBaru2(null,$_SESSION['ad_create_step1']);
  		//return $this->View->toString("SITTI/form_iklan_baru/reg1.html");
  	}
    function getAvgCPC($keyword){
    	$param['avg_cpc'] = 200;
    	return json_encode($param);
    }
    function getKeywordData($keyword){
    	$this->open(0);
    	
    	$sql = "SELECT keyword, avg_cpc, jum_imp as imp, jum_hit as hit, ctr, jum_guna, max_bid,min_bid 
				FROM db_web3.sitti_keywords_simulasi 
				WHERE keyword IN ('".mysql_escape_string($keyword)."')";
    	
    	$rs2 = $this->fetch($sql);
    	
    	$this->close(0);

    	$sql_cpm = "SELECT bid AS cpm 
    				FROM db_publisher.sitti_keywords_cpm 
    				WHERE keyword = '". mysql_escape_string($keyword) ."'";

    	$this->open(0);
    	$rs_cpm = $this->fetch($sql_cpm);
    	$this->close(0);

    	$rs2['status']="1";
    	
    	//print mysql_error();
    	$this->close();
    	if($rs2['avg_cpc']==null){
    		$rs2['avg_cpc'] = 0;
    	}
    	if($rs2['imp']==null){
    		$rs2['imp'] = 0;
    		$rs2['status'] = -1;
    	}
    	if($rs2['hit']==null){
    		$rs2['hit'] = 0;
    		$rs2['status'] = -1;
    	}  
    	if($rs2['ctr']==null){
    		$rs2['ctr'] = 0;
    		
    	}
    	if($rs2['max_bid']==null){
    		$rs2['max_bid'] = 0;
    	}
    	if($rs2['min_bid']==null){
    		$rs2['min_bid'] = 0;
    	}
    	if($rs2['imp']==0){
    		$rs2['status'] = -1;
    	}
    	$param['avg_cpc'] = $this->roundto50(round(floor($rs2['avg_cpc']/10)*10,0));
    	$param['imp'] = floor($rs2['imp']/100)*100;
    	$param['click'] = $rs2['hit'];
    	$param['ctr'] = round($rs2['ctr'],2);
    	$param['max_bid'] = $rs2['max_bid'];
    	$param['min_bid'] = $rs2['min_bid'];
    	$param['status'] = $rs2['status'];
    	$param['cpm'] = $rs_cpm['cpm'];
    	if($param['ctr']==0){
    		$param['ctr'] = "0";
    	}else{
    		$param['ctr'] = $param['ctr'];
    	}
    	//print_r($param);
    	return json_encode($param);
    }
	
	function roundto50($num){
		$mod = $num%50;
		$bid = $num-$mod;
		$round = 0;
		if ($mod>=25){
			$round = 50;
		}else{
			$round = 0;
		}
		$num = $bid+$round;
		return $num;
	}

    function getRestrictedWords()
    {
    	$query = "SELECT kata FROM db_web3.keyword_porn";
    	$this->open(0);
    	$result = $this->fetch($query, 1);
    	$this->close();

    	$words = array();
    	if (is_array($result) && count($result) > 0)
    	{
    		foreach ($result as $row)
    		{
    			$words[] = strtolower($row['kata']);
    		}	
    	}
    	
    	return json_encode($words);
    }
}
?>