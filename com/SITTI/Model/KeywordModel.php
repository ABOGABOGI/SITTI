<?php 

class KeywordModel extends SQLData
{
	private $keyword;

	public function KeywordModel($keyword = false)
	{
		parent::SQLData();
		if (isset($keyword)) 
		{
			if (is_array($keyword))
			{
				array_walk($keyword, function(&$word) {
					$word = strtolower($word);
				});
			}
			else
			{
				$keyword = strtolower($keyword);
			}
			$this->keyword = $keyword;	
		}
	}

	function getKeyword()
	{
		return $this->keyword;
	}

	function setKeyword($keyword)
	{
		$this->keyword = $keyword;
	}

	function getPPABidValue($keyword = false)
	{
		$keyword = isset($keyword) ? $keyword : $this->keyword;
		$query = "SELECT bid FROM db_publisher.sitti_ppa_price WHERE keyword = '". $keyword ."'";

		//$this->open(2);
		$rs = $this->fetch($query);
		//$this->close();
		
		return intval($rs['bid']);
	}
}

?>