<?php 

class CategoryKeywordModel extends SQLData
{
	private $category;
	
	public function CategoryKeywordModel($category = false)
	{
		parent::SQLData();
		if ($category) $this->category = $category;
	}
	
	public function getCategory()
	{
		return $this->category;
	}
	
	public function setCategory($category)
	{
		$this->category = $category;
	}
	
	public function getCategories()
	{
		$query = "SELECT DISTINCT(kategori) 
					FROM db_web3.sitti_keywords_kategori
					ORDER BY kategori ASC";
		
		$this->open(1);
		$rs = $this->fetch($query, 1);
		$this->close();

		return $rs;
	}
	
	public function getData()
	{
		$query = "SELECT * 
					FROM db_web3.sitti_banner_simulasi 
					WHERE kategori = '" .$this->category. "'";
					
		$this->open(1);
		$rs = $this->fetch($query);
		$this->close();
		
		return $rs;
	}
	
	public function getKeywords()
	{
		$query = "SELECT keyword 
					FROM db_web3.sitti_keywords_kategori 
					WHERE kategori = '" .$this->category. "' LIMIT 100";
					
		$this->open(1);
		$rs = $this->fetch($query, 1);
		$this->close();
		
		return $rs;
	}

}

?>
