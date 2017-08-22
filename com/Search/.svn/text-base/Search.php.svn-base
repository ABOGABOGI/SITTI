<?php
include_once $ENGINE_PATH."Utility/Paginate.php";
class Search extends SQLData{
	var $strHTML;
	var $View;
	var $Static;
	var $startCount;
	function Search($req){
		parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
		$this->Paging = new Paginate();
	}
	
	/****** End-User FUNCTIONS ********************************************/
	function find($total=30){
		$url = eregi_replace("([a-zA-Z]+)(\.php)","",$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		if(!eregi("http://",$url)){
			$url = "http://".$url;
		}
		$this->View->assign("url",$url);
		$q = $this->Request->getParam("q");
		if(strlen($q)>1){
		$this->View->assign("isAvailable","1");
		$q = stripslashes(htmlentities(strip_tags($q)));
		$loc = $this->Request->getParam("l");
		$totals = $this->getTotalFounds($q);
		$this->TOTALS = $totals;
		$this->View->assign("total_found",$totals['overall']);
		$this->View->assign("q",$q);
		$start = $this->Request->getParam('st');
		if($start==NULL){$start=0;}
		$cue = $start;
		$this->DIFF = 0;
		$rs = $this->sequentialSearch($q,$cue,$total);
		$n = sizeof($rs);
		for($i=0;$i<$n;$i++){
			$rs[$i]['no']=$start+1+$i;
		}
		$this->View->assign("list",$rs);
		}
		$this->View->assign("q",$q);
		//paging
		$this->View->assign("page",$this->Paging->generate($start,$total,$totals['overall'],"?q=".$q));
		return $this->View->toString("Search/result.html");
	}
	function sequentialSearch($q,$cue,$total,$step=0){
		$bar = array('article','product','kb','faq');
		//let's decide the cue point.
		if($cue!=0){
			if($cue<($this->TOTALS['article']+$this->TOTALS['product'])){
				$cue = $cue-$this->TOTALS['article'];
				$step=1;
				//print "kena 1";
			}else if($cue<($this->TOTALS['article']+$this->TOTALS['product']+$this->TOTALS['kb'])){
				$cue = $cue-($this->TOTALS['article']+$this->TOTALS['product']);
				$step=2;
				//print "kena 2";
			}else if($cue <$this->TOTALS['overall']){
				$step=3;
				//print "kena 3";
				$cue = $cue-($this->TOTALS['article']+$this->TOTALS['product']+$this->TOTALS['kb']);
			}else{
				//do nothing
				//print "kena 0";
			}
		}
	//	print_r($this->TOTALS);
	//	print $step;
		
		switch($step){
			case '1':
				$rs = $this->searchProduct($q,$cue,$total);
			break;
			case '2':
				$rs = $this->searchHelp($q,$cue,$total);
			break;
			case '3':
				$rs = $this->searchFaq($q,$cue,$total);
			break;
			default : 
				$rs = $this->searchArticle($q,$cue,$total);
			break;
		}
			$n = sizeof($rs);
			//print $n."<br/>";
			//print $cue.",".$total.",".$step.",".$n."<br/>";
			if($n<$total){
				if($step!=3){
					$cue=0;
					$tx = $this->sequentialSearch($q,$cue,$total-$n,$step+1);
				//	print $tx;
					if(is_array($tx)&&is_array($rs)){
						//print "yey";
						$rs = array_merge($rs,$tx);
					}else if(is_array($tx)&&!is_array($rs)){
						//print $step;
						//print_r($tx);
						$rs = $tx;
					}else{
						//do nothing
					}
				}
			}
			return $rs;
		
		
		/*
		
		else if($start<($totals['article']+$totals['product'])){
			$rs = $this->searchProduct($q,$cue,$total);
			if(sizeof($rs)<$total){
				$diff = ($total-sizeof($rs));
				$this->startCount=sizeof($rs);
				$tx = $this->searchHelp($q,0,$diff);
				if(is_array($ts)){
					$rs = array_merge($rs,$tx);
				}
				$cue = $diff;
			}else{
				$cue+=$total;
			}
		}else if($start<($totals['article']+$totals['product']+$totals['kb'])){
			$rs = $this->searchHelp($q,$cue,$total);
			if(sizeof($rs)<$total&&sizeof($rs)!=0){
				$diff = ($total-sizeof($rs));
				$this->startCount=sizeof($rs);
				$tx = $this->searchFAQ($q,0,$diff);
				if(is_array($ts)){
					$rs = array_merge($rs,$tx);
				}
				$cue = $diff;
			}else{
				$cue+=$total;
			}
		}else{
			$rs = $this->searchHelp($q,$cue,$total);
			if(sizeof($rs)<$total){
				//do nothing
			}else{
				$cue+=$total;
			}
		}*/
	}
	function getTotalFounds($q){
		//total articles found
		$n=0;
		$foo = $this->fetch("SELECT COUNT(*) as total FROM gm_article 
							  WHERE title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%' 
							  LIMIT 1");
		$n += $foo['total'];
		$rs['article'] = $foo['total'];
		//product
		$foo = $this->fetch("SELECT COUNT(*) as total FROM gm_static_page 
							  WHERE parentID <> '0' AND parentID <> '1' AND tag <> 'tos' AND tag <> 'privacy' AND (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$n += $foo['total'];
		$rs['product'] = $foo['total'];
		//knowledge base
		$foo = $this->fetch("SELECT COUNT(*) as total FROM bsi_knowledge 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$n += $foo['total'];
		$rs['kb'] = $foo['total'];
		//faq
		$foo = $this->fetch("SELECT COUNT(*) as total FROM bsi_faq 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$n += $foo['total'];
		$rs['faq'] = $foo['total'];
		$rs['overall'] = $n;
		return $rs;
	}
	function searchArticle($q,$start,$total){
		$list = $this->fetch("SELECT * FROM gm_article 
							  WHERE title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%' 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $this->Request->getParam("st")+$i+1+$this->startCount;
			$list[$i]['type'] = 1;
		}
		return $list;
	}
	
	function searchProduct($q,$start,$total){
		$list = $this->fetch("SELECT * FROM gm_static_page 
							  WHERE parentID <> '0' AND parentID <> '1' AND tag <> 'tos' AND tag <> 'privacy' AND (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $this->Request->getParam("st")+$i+1+$this->startCount;
			$list[$i]['type'] = 2;
		}
		return $list;
	}
	function searchHelp($q,$start,$total){
		$list = $this->fetch("SELECT * FROM bsi_knowledge 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $this->Request->getParam("st")+$i+1+$this->startCount;
			$list[$i]['type'] = 3;
		}
		return $list;
	}
	function searchFAQ($q,$start,$total){
		
		$list = $this->fetch("SELECT * FROM bsi_faq
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		$n = sizeof($list);
		//print $this->DIFF;
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $this->Request->getParam("st")+$i+1+$this->startCount;
			$list[$i]['type'] = 4;
		}
		return $list;
	}
	function performSearchArticle($q,$total=30){
		$this->View->assign("loc","1");
		$start = $this->Request->getParam("st");
		if($start==NULL){
			$start=0;
		}
		$this->View->assign("location","News & Press Releases");
		$list = $this->fetch("SELECT * FROM gm_article 
							  WHERE title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%' 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $start+$i+1;
		}
		$this->View->assign("url","article.php?id=");
		$this->View->assign("list",$list);
		$foo = $this->fetch("SELECT COUNT(*) as total FROM gm_article 
							  WHERE title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%' 
							  LIMIT 1");
		$this->View->assign("total_found",$foo['total']);
		$this->View->assign("q",$q);
		$this->View->assign("page",$this->Paging->generate($start,$total,$foo['total'],"?q=".$q."&l=1"));
	}
	function performSearchProducts($q,$total=30){
		$this->View->assign("loc","2");
		$start = $this->Request->getParam("st");
		if($start==NULL){
			$start=0;
		}
		$this->View->assign("location","Products &amp; Services");
		$list = $this->fetch("SELECT * FROM gm_static_page 
							  WHERE parentID <> '0' AND parentID <> '1' AND (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $start+$i+1;
		}
		$this->View->assign("url","page.php?id=");
		$this->View->assign("list",$list);
		$foo = $this->fetch("SELECT COUNT(*) as total FROM gm_static_page 
							  WHERE parentID <> '0' AND parentID <> '1' AND (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$this->View->assign("total_found",$foo['total']);
		$this->View->assign("q",$q);
		$this->View->assign("page",$this->Paging->generate($start,$total,$foo['total'],"?q=".$q."&l=1"));
	}
	function performSearchKnowledgeBase($q,$total=30){
		$this->View->assign("loc","3");
		$start = $this->Request->getParam("st");
		if($start==NULL){
			$start=0;
		}
		$this->View->assign("location","Knowledge Base");
		$list = $this->fetch("SELECT * FROM bsi_knowledge 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $start+$i+1;
		}
		$this->View->assign("url","help.php?id=");
		$this->View->assign("list",$list);
		$foo = $this->fetch("SELECT COUNT(*) as total FROM bsi_knowledge 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$this->View->assign("total_found",$foo['total']);
		$this->View->assign("q",$q);
		$this->View->assign("page",$this->Paging->generate($start,$total,$foo['total'],"?q=".$q."&l=1"));
	}
	function performSearchFAQ($q,$total=30){
		$this->View->assign("loc","4");
		$start = $this->Request->getParam("st");
		if($start==NULL){
			$start=0;
		}
		$this->View->assign("location","Frequently Ask Question (FAQ)");
		$list = $this->fetch("SELECT * FROM bsi_faq
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%') 
							  LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		for($i=0;$i<$n;$i++){
			$list[$i]['no'] = $start+$i+1;
		}
		$this->View->assign("url","faq.php?id=");
		$this->View->assign("list",$list);
		$foo = $this->fetch("SELECT COUNT(*) as total FROM bsi_faq 
							  WHERE (title LIKE '%".$q."%' 
							  OR detail LIKE '%".$q."%')
							  LIMIT 1");
		$this->View->assign("total_found",$foo['total']);
		$this->View->assign("q",$q);
		$this->View->assign("page",$this->Paging->generate($start,$total,$foo['total'],"?q=".$q."&l=1"));
	}
	function getLandingPage(){
		//do nothing
	}
	
	/****** ADMIN FUNCTIONS ********************************************/
	function admin(){
		
	}

}
?>