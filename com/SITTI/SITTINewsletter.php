<?php
include_once $ENGINE_PATH."Utility/MailChimp/MCAPI.class.php";
class SITTINewsletter extends SQLData{
	var $View;
	var $Mailer;
	var $Request;
	var $Account;
	var $API_KEY;
	var $LIST_ID;
	function SITTINewsletter($req,$account){
		global $CONFIG;
		parent::SQLData();
		$this->Request = $req;
		$this->Account = $account;
		$this->View = new BasicView();
		$this->InitConfig();

	}
	function InitConfig(){
		global $CONFIG;
		$this->API_KEY = $CONFIG['MAILCHIMP_API_KEY'];
		$this->LIST_ID = $CONFIG['MAILCHIMP_LIST_ID'];

	}
	function register(){
		return $this->View->toString("SITTI/newsletter.html");
	}
	function subscribe($profile){
		
		$api = new MCAPI($this->API_KEY);
		$merge_vars = array('FNAME'=>$profile['sittiID'], 'LNAME'=>$profile['name']);
		$retval = $api->listSubscribe( $this->LIST_ID, $profile['email'], $merge_vars );

		if ($api->errorCode){
			$msg = "Pendaftaran newsletter tidak berhasil. Silahkan coba kembali.";
			//echo "\tCode=".$api->errorCode."\n";
			//echo "\tMsg=".$api->errorMessage."\n";
		} else {
			$msg = "Pendaftaran berhasil. Anda kini terdaftar untuk mendapatkan info terkini secara berkala dari SITTI!\n";
		}
		return $this->View->showMessage($msg,"beranda.php");
	}
	function createCampaign(){

		$api = new MCAPI($this->API_KEY);

		$type = 'regular';

		$opts['list_id'] = $this->LIST_ID;
		$opts['subject'] = 'SITTI Announcement';
		$opts['from_email'] = 'info@sitti.me';
		$opts['from_name'] = 'SITTI';

		$opts['tracking']=array('opens' => true, 'html_clicks' => true, 'text_clicks' => true);

		$opts['authenticate'] = true;
		//$opts['analytics'] = array('google'=>'my_google_analytics_key');
		$opts['title'] = 'SITTI - ANNOUNCEMENTS';

		$content = array('html'=>'hallo <strong>user</strong>  ini adalah contoh html email <br/> *|UNSUB|* Click disini untuk mematikan layanan email otomatis ini',
		  'text' => 'ini cuman contoh email aja *|UNSUB|*'
		  );
		  /** OR we could use this:
		   $content = array('html_main'=>'some pretty html content',
		   'html_sidecolumn' => 'this goes in a side column',
		   'html_header' => 'this gets placed in the header',
		   'html_footer' => 'the footer with an *|UNSUB|* message',
		   'text' => 'text content text content *|UNSUB|*'
		   );
		   $opts['template_id'] = "1";
		   **/

		  $retval = $api->campaignCreate($type, $opts, $content);

		  if ($api->errorCode){
		  	echo "Unable to Create New Campaign!";
		  	echo "\n\tCode=".$api->errorCode;
		  	echo "\n\tMsg=".$api->errorMessage."\n";
		  } else {
		  	echo "New Campaign ID:".$retval."\n";
		  }
	}
	function send(){
		$campaign_id = "4d49069a67";
		$api = new MCAPI($this->API_KEY);

		$retval = $api->campaignSendNow($campaign_id);

		if ($api->errorCode){
			echo "Unable to Send Campaign!";
			echo "\n\tCode=".$api->errorCode;
			echo "\n\tMsg=".$api->errorMessage."\n";
		} else {
			echo "Campaign Sent!\n";
		}
	}
	function getAPIKey(){
		return $this->API_KEY;
	}
	function getListId(){
		return $this->LIST_ID;
	}

}