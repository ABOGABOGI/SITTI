<?php
class CSVPrinter{
	var $_job;
	var $_QueryLimit;
	var $_docID;
	var $secretKey = "Th3L1ttl3H0bb1t5";
	var $fields;
	var $labels;
	var $_data;
	function CSVPrinter($docID){
		$this->_QueryLimit = 20;
		$this->_docID = $docID;
	}
	function getKey(){
		return md5($this->_docID.date("YmdHi").$this->secretKey);
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
	function output(){
		$k = sizeof($this->fields);
		$filename = $this->_docID.date("_YmdHis");
		
		array_unshift($this->labels, 'no');

		/*$strOutput = "no";
		for($j=0;$j<$k;$j++){
			$strOutput.=",".$this->labels[$j];
		}*/

		$n = sizeof($this->_data);
		$csv_data = array();
		
		for($i=0;$i<$n;$i++){
			//$strOutput.="\n";
			//$strOutput.="'".($i+1)."'";
			$csv_data[$i][0] = $i+1;
			for($j=0;$j<$k;$j++){
				$value = $this->_data[$i][$this->fields[$j]];
				$value = str_replace("\\","",$value);
				$csv_data[$i][$j+1] = $value;
				//$strOutput.=",'".$this->_data[$i][$this->fields[$j]]."'";
			}
		}

		array_unshift($csv_data, $this->labels);

		/*header("Pragma: no-cache");
    	header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: text/csv");
    	header("Content-Disposition: attachment; filename=".$filename.".csv");
    	header("Content-Transfer-Encoding: binary");*/
    	
    	header('Pragma: Public');
    	header("Content-type: text/csv");
		header("Cache-Control: no-store, no-cache");
		header("Content-Disposition: attachment; filename=".$filename.".csv");

		$outstream = fopen("php://output",'w');

		foreach( $csv_data as $row )
		{
			fputcsv($outstream, $row, ',');
		}

		fclose($outstream);
    	
		//echo $strOutput;
		exit();
	}
	function printing($job,$requestID){
		if($this->authenticate($requestID)){
			$this->_job = $job;

			//we gonna do printing in the next 3 seconds.
			sleep(3);
			$this->_doPrinting();
		}else{
			print "Maaf, anda tidak dapat mendownload document ini. silahkan coba kembali beberapa saat lagi.";
		}
	}
	
	function authenticate($requestID){
		$the_key = md5($this->_docID.$requestID.$this->secretKey);
		if($the_key==$this->getKey()){
			return true;
		}
	}
	function _doPrinting(){
		$total_pages = $this->_job['pages'];
		$sql = $this->_job['sql'];
		$current = 0;
		$total_rows = $this->_QueryLimit;
		$nLoop = ceil($total_pages/$total_rows);
		//print $nLoop;
		//untuk sementara kita print bego dulu -->
		
		//-->
	}
	
}
?>