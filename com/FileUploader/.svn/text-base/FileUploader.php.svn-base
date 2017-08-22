<?php
class FileUploader extends BasicView{
	var $_id;
	var $_noInstance; //how many swf instances
	var $_labels;
	var $_tplupload;
	/**
	 * 
	 * 
	 * @param $id fileID
	 * @param $num_instance how many swf instances
	 * @param $tpl the template you want to embed the uploader into.
	 */
	function __construct($id,$num_instance,$tpl){
		$this->_id = $id;
		$this->_noInstance = $num_instance;
		$this->_tplupload = $tpl;
		parent::BasicView();
	}
	/**
	 * 
	 * set label for each uploader swf.
	 * @param $arrLabels
	 */
	function setLabels($arrLabels){
		$this->_labels = $arrLabels;
	}
	function __toString(){
		global $CONFIG,$GLOBAL_PATH;
		$n = $this->_noInstance;
		$setup = array();
		//--> banner setup ( di hack disini dulu, nanti kita pisah di class terpisah)
  		$banner = array(
  			null,
  			array("popupName"=>"popup300x250","width"=>300,"height"=>250),
  			array("popupName"=>"popup336x280","width"=>336,"height"=>280),
  			array("popupName"=>"popup728x90","width"=>728,"height"=>90),
  			array("popupName"=>"popup160x600","width"=>160,"height"=>600),
  			array("popupName"=>"popup610x60","width"=>610,"height"=>60),
  			array("popupName"=>"popup300x160","width"=>300,"height"=>160),
  			array("popupName"=>"popup940x70","width"=>940,"height"=>70),
  			array("popupName"=>"popup520x70","width"=>520,"height"=>70),
  			array("popupName"=>"popup468x60","width"=>468,"height"=>60),
  			array("popupName"=>"popup250x250","width"=>250,"height"=>250)
  		);
  		//-->
		for($i=0;$i<$this->_noInstance;$i++){
			$setup[$i]['label'] = $this->_labels[$i];
			$setup[$i]['fileID'] = $this->_id."_".($i+1);
			$setup[$i]['name'] = "d_".$setup[$i]['fileID'];
			$setup[$i]['no'] = $i+1;
			$setup[$i]['popupName'] = $banner[$i+1]['width']."x".$banner[$i+1]['height'];
		}
		
		$this->assign("uploaders",$setup);
		return $this->toString($this->_tplupload);
	}
}
?>