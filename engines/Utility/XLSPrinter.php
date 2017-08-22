<?php
class XLSPrinter{
	var $filename;
	var $fields;
	var $labels;
	var $_data;
	var $_docID;
	
	function XLSPrinter($docID){
		$this->_docID = $docID;
	}
	
	function setFields(){
		$this->fields = func_get_args();
		//print_r($fields);
	}
	
	function setLabels(){
		$this->labels = func_get_args();
	}
	
	function setData($data){
		
		$this->_data = $data;
	}
	
	function xlsBOF() { 
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
		return; 
	} 

	function xlsEOF() { 
		echo pack("ss", 0x0A, 0x00); 
		return; 
	} 

	function xlsWriteNumber($Row, $Col, $Value) { 
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
		echo pack("d", $Value); 
		return; 
	} 

	function xlsWriteLabel($Row, $Col, $Value ) { 
		$L = strlen($Value); 
		echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
		echo $Value; 
		return; 
	}

	function output(){
		$filename = $this->_docID.date("_YmdHis");
		// Send Header
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=$filename.xls ");
		header("Content-Transfer-Encoding: binary ");

		// XLS Data Cell
		$this->xlsBOF();
		$k = sizeof($this->labels);
		for ($i=0;$i<$k;$i++){
			$this->xlsWriteLabel(0,$i,$this->labels[$i]);
		}
		$n = sizeof($this->_data);
		for ($j=1;$j<=$n;$j++){
			for ($i=0;$i<$k;$i++){
				if($i==0){
					$this->xlsWriteNumber($j,$i,$j);
				}else{
					$value = $this->_data[$j-1][$this->fields[$i-1]];
					$value = str_replace("\\","",$value);
					//if (($i-1)==0){
						$this->xlsWriteLabel($j,$i,$value);
					//}else{
					//	$this->xlsWriteNumber($j,$i,$this->_data[$j-1][$this->fields[$i-1]]);
					//}
				}
			}
		}
		$this->xlsEOF();
		exit();
	}
}
 ?>