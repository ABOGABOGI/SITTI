<?php

class IndomogAPI{
	var $DEBUG;
	function IndomogAPI($debug=false){
		$this->DEBUG = $debug;
	}
	function log($msg){
		
	}
	function register($profile){
		global $CONFIG;
		//print_r($CONFIG);
		$URL =$CONFIG['INDOMOG_API_URL'];
		$merchant_id = $CONFIG['INDOMOG_MERCHANT_ID'];
		$request_id = "st".date("YmdHis").floor(rand(0,9999));
		$secret = $CONFIG['INDOMOG_SECRET_KEY'];
		//print $secret."<br/>";
		$signature = sha1($merchant_id."si".
					$profile['sittiID'].
					$request_id."3001".
					$profile['sittiID'].
					$profile['name'].
					$profile['mobile']."-"."-"."-".
					$profile['email']."-"."-"."-".$secret);
					//print $merchant_id;
		$xml_data = "
					<request>
					<data>
						<rmid>".$merchant_id."</rmid>
						<alg>si</alg>
						<algid>".$profile['sittiID']."</algid>
						<qid>".$request_id."</qid>
						<rc>3001</rc>
						<uid>".$profile['sittiID']."</uid>
						<name>".$profile['name']."</name>
						<rph>".$profile['mobile']."</rph>
						<ph>-</ph>
						<bod>-</bod>
						<cat>-</cat>
						<ea>".$profile['email']."</ea>
						<mp>-</mp>
						<sq>-</sq>
						<sa>-</sa>
					</data>
					<signature>".$signature."</signature>
					</request>";
		
		if($this->DEBUG){print $xml_data."<br/>";}
		$ch = curl_init($URL);
		//curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		if($this->DEBUG){
			print nl2br($output);
		}
		$converter = new xml2json();
		$json = @$converter->transformXmlStringToJson($output);
		return $json;
	}
	/**
	 * 
	 * Topup Klikbca
	 * @param $profile
	 * @param $bcalogin
	 * @param $amount
	 */
	function topupBCA($profile,$bcalogin,$amount){
		global $CONFIG;
		//print_r($CONFIG);
		settype($amount, "integer");
		$URL =$CONFIG['INDOMOG_API_URL'];
		$merchant_id = $CONFIG['INDOMOG_MERCHANT_ID'];
		$request_id = "st".date("YmdHis").floor(rand(0,9999));
		$secret = $CONFIG['INDOMOG_SECRET_KEY'];
		$signature = sha1($merchant_id.$request_id."3002"."si".$profile['indomog_id'].$bcalogin.$amount.$secret);
					//print $merchant_id;
		
		$xml_data = "<request>
					<data>
						<rmid>".$merchant_id."</rmid>
						<qid>".$request_id."</qid>
						<rc>3002</rc>
						<alg>si</alg>
						<algid>".$profile['indomog_id']."</algid>
							<usr>".$bcalogin."</usr>
							<amt>".$amount."</amt>
					</data>
					<signature>".$signature."</signature>
					</request>";
		if($this->DEBUG){print $xml_data."<br/>";}
		$ch = curl_init($URL);
		//curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		if($this->DEBUG){
			print "\n--------\n";
			print nl2br($output);
		}
		$converter = new xml2json();
		$json = @$converter->transformXmlStringToJson($output);
		return $json;
	}
	/**
	 * 
	 * TOPUP Mandiri Mobile
	 * @param $profile
	 * @param $usr Mobile Phone Number
	 * @param $amount
	 */
	function topupMandiri($profile,$usr,$amount){
		global $CONFIG;
		//print_r($CONFIG);
		settype($amount, "integer");
		$URL =$CONFIG['INDOMOG_API_URL'];
		$merchant_id = $CONFIG['INDOMOG_MERCHANT_ID'];
		$request_id = "st".date("YmdHis").floor(rand(0,9999));
		$secret = $CONFIG['INDOMOG_SECRET_KEY'];
		$signature = sha1($merchant_id.$request_id."3003"."si".$profile['indomog_id'].$usr.$amount.$secret);
					//print $merchant_id;
		
		$xml_data = "<request>
					<data>
						<rmid>".$merchant_id."</rmid>
						<qid>".$request_id."</qid>
						<rc>3003</rc>
						<alg>si</alg>
						<algid>".$profile['indomog_id']."</algid>
						
							<hp>".$usr."</hp>
							<amt>".$amount."</amt>
						
					</data>
					<signature>".$signature."</signature>
					</request>";
		if($this->DEBUG){print $xml_data."<br/>";}
		$ch = curl_init($URL);
		//curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		if($this->DEBUG){
			print nl2br($output);
		}
		$converter = new xml2json();
		$json = @$converter->transformXmlStringToJson($output);
		return $json;
	}
	/**
	 * 
	 * TOPUP Mandiri Internet Banking
	 * @param $profile
	 * @param $usr ATM Card No
	 * @param $amount
	 * @param $token
	 */
	function topupMandiriInternet($profile,$usr,$trans_id,$amount,$token){
		global $CONFIG;
		//print_r($CONFIG);
		//print $usr.",".$amount;
		settype($amount, "integer");
		$URL =$CONFIG['INDOMOG_API_URL'];
		$merchant_id = $CONFIG['INDOMOG_MERCHANT_ID'];
		$request_id = $trans_id;
		$token = cleanString($token);
		$secret = $CONFIG['INDOMOG_SECRET_KEY'];
		$signature = sha1($merchant_id.$request_id."3004"."si".$profile['indomog_id'].$usr.$amount.$token.$secret);
					//print $merchant_id;
		
		$xml_data = "<request>
					<data>
						<rmid>".$merchant_id."</rmid>
						<qid>".$request_id."</qid>
						<rc>3004</rc>
						<alg>si</alg>
						<algid>".$profile['indomog_id']."</algid>
						
							<usr>".$usr."</usr>
							<amt>".$amount."</amt>
							<tkn>".$token."</tkn>
						
					</data>
					<signature>".$signature."</signature>
					</request>";
		if($this->DEBUG){print $xml_data."<br/>";}
		
		$ch = curl_init($URL);
		//curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		if($this->DEBUG){
			print nl2br($output);
		}
		
		$converter = new xml2json();
		$json = @$converter->transformXmlStringToJson($output);
		return $json;
	}
/**
	 * 
	 * lookup indomog_id
	 * @param $profile
	 * @param $usr ATM Card No
	 * @param $amount
	 */
	function lookup($profile){
		global $CONFIG;
		//print_r($CONFIG);
		settype($amount, "integer");
		$URL =$CONFIG['INDOMOG_API_URL'];
		$merchant_id = $CONFIG['INDOMOG_MERCHANT_ID'];
		$request_id = date("st_YmdHis");
		$secret = $CONFIG['INDOMOG_SECRET_KEY'];
		$signature = sha1($merchant_id.$request_id."3006"."si".$profile['sittiID'].$secret);
					//print $merchant_id;
		$xml_data = "<request>
					<data>
          			<rmid>".$merchant_id."</rmid>
          			<qid>".$request_id."</qid>
           			<rc>3006</rc>
          			<alg>si</alg> 
          			<algid>".$profile['sittiID']."</algid>
					</data>
					<signature>".$signature."</signature>
					</request>";
		
		if($this->DEBUG){print $xml_data."<br/>";}
		
		$ch = curl_init($URL);
		//curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		if($this->DEBUG){
			print nl2br($output);
		}
		$converter = new xml2json();
		$json = @$converter->transformXmlStringToJson($output);
		return $json;
	}
}
?>