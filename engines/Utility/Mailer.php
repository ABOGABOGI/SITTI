<?php
class Mailer{
	var $headers;
	var $subject;
	var $message;
	var $from;
	var $to;
	function Mailer(){
		$this->setDefaultHeaders();
	}
	function setSubject($str){
		$this->subject = $str;
	}
	function setMessage($msg){
		$this->message = $msg;
	}

    
	function setDefaultHeaders(){
		global $CONFIG;
		$headers  = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-Type: text/plain". "\r\n".
                   //"Content-Transfer-Encoding: 7bit". "\r\n";
		$headers .= "List-Unsubscribe: <mailto:".$CONFIG['MAIL_UNSUBSCRIBE'].">\r\n";
		$headers .= "X-campaignID: ".base64_encode(date("YmdHis"))."\r\n";
		$headers .= "Message-ID: <".date("YmdHis").".mailer@".$CONFIG['MAILER'].">\r\n";
		$this->headers = $headers;
	}
     

	function setSender($str){
		$this->from = $str;	
	}
	function setRecipient($str){
		$this->to = $str;		
	}
	function send(){
		$this->headers .= 'From: '.$this->from. "\r\n";
		$this->headers .= 'To: '.$this->to. "\r\n";
		return mail($this->to,$this->subject,$this->message,$this->headers);
	}
}
?>