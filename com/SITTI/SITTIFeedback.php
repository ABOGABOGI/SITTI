<?php
class SITTIFeedback extends SITTIApp
{
	private $account;
	private $request;
	private $view;
	private $sitti_id;
	
	function SITTIReferral($req,$account)
	{
		parent::SITTIApp($req, $account);
	}
	function send_email(){
		$to = "beriklan@sitti.zendesk.com";
		$name = strip_tags($this->Request->getPost('name'));
		$email = $this->Request->getPost('email');
		$body = strip_tags($this->Request->getPost('saran'));
		
		$headers = 'From: '.$name.'<'.$email.'>' . "\r\n" .
    				'Reply-To: '.$email.'' . "\r\n" .
    				'X-Mailer: PHP/' . phpversion();
		if(mail($to,"User Feedback",$body,$headers)){
			$msg = "Terima kasih atas masukan anda.";
		}else{
			$msg = "Maaf, gagal mengirimkan pesan anda. Silahkan coba kembali";
		}
		return $this->View->showMessage($msg, "index.php");
	}
	function run(){
		return $this->send_email();
	}
}

?>