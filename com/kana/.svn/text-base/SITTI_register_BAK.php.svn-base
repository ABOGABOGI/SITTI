<?php
include_once "SQLData.php";
include_once "smtp.php";
include_once "sasl.php";

class SITTI_Register extends SQLData{

	function SITTI_Register(){
		global $dbHost,$dbPass,$dbUser,$nameDb;

		//$this->SQLData();
		$this->host = $dbHost;
		$this->username = $dbUser;
		$this->password = $dbPass;
		$this->database = $nameDb;


	}
	function confirm(){
		$email = mysql_escape_string(urldecode($_GET['email']));
		$key = mysql_escape_string(urldecode($_GET['key']));
		$this->open();
		$rs = $this->fetch("SELECT * FROM user_register_temp WHERE email='".$email."' AND confirm_key='".$key."' LIMIT 1");
		$this->close();
		if($rs['email']==$email&&$rs['confirm_key']==$key&&$rs['is_confirmed']=='0'){
			$this->open();
			$foo = $this->query("UPDATE user_register_temp set is_confirmed='1' WHERE id='".$rs['id']."'");
			$this->close();
			if($foo){
				return $this->registration_process($rs['name'],$rs['email']);
			}
		}else{
			print "Email anda telah diaktivasi sebelumnya.<br/>";
			return false;
		}
	}
	function notifyUser($key){
		global $namaWebPageAds;
		$smtp=new smtp_class;
		$smtp->host_name="smtp.gmail.com";
		$smtp->host_port=465;                /* Change this variable to the port of the SMTP server to use, like 465 */
		$smtp->ssl=1;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
		$smtp->start_tls=0;                 /* Change this variable if the SMTP server requires security by starting TLS during the connection */
		$smtp->localhost="soekarno";       /* Your computer address */
		$smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
		$smtp->timeout=10;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
		$smtp->data_timeout=10;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
		Set to 0 to use the same defined in the timeout variable */
		//	$smtp->debug=1;                     /* Set to 1 to output the communication with the SMTP server */
		$smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
		//	$smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */
		$smtp->html_debug=0;                /* Set to 1 to format the debug output as HTML */
		$smtp->pop3_auth_host="";           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
		$smtp->user="sittizen@gmail.com";                     /* Set to the user name if the server requires authetication */
		$smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
		$smtp->password="b4b00nb4b00n";                 /* Set to the authetication password */
		$smtp->workstation="";              /* Workstation name for NTLM authentication */
		$smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
		Leave it empty to make the class negotiate if necessary */
		$name = mysql_escape_string($_POST['name']);
		$email = mysql_escape_string($_POST['email']);
		$to      = $email;
		$subject = '[SITTI] Mohon Konfirmasi Pendaftaran anda.';
		$message = 'Halo,<br/><br/>';
		$message .='Terima kasih telah mendaftar untuk uji coba SITTI. Untuk melengkapi proses pendaftaran, silahkan konfirmasi email Anda terlebih dahulu dengan klik atau copy paste link berikut :<br/>';
		$message .= '<a href="http://'.$namaWebPageAds.'/confirm.php?do=confirm&email='.urlencode($email).'&key='.urlencode($key).'">http://'.$namaWebPageAds.'/confirm.php?do=confirm&email='.urlencode($email).'&key='.urlencode($key).'</a>';
		$message .="<br/><br/><br/>SITTI.";
		$headers = 'From: mailbot@sittibelajar.com' . "\r\n" .
    'Reply-To: no-reply@sittibelajar.com' . "\r\n";
		//print $to;
		
		if($smtp->SendMessage("mailbot@sittibelajar.com",array($to),array(
			"MIME-Version: 1.0",
			"Content-Type: text/html",
			"From: mailbot@sittibelajar.com",
			"To: $to",
			"Subject: $subject",
			"Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")
		),
		trim($message))){
			//print "berhasil";
			return true;
		}else{
			//print "gagal-->".$smtp->error;
			return false;
		}
		/*		if(mail($to, $subject, $message, $headers)){

		return true;
		}else{
		//print "gagal";
		return false;
		}*/
	}
	function register(){
		$name = mysql_escape_string($_POST['name']);
		$email = mysql_escape_string($_POST['email']);
		$this->open();
		if($this->query("INSERT INTO user_register_temp(name,email,register_date,confirm_key,is_confirmed)
						 VALUES('".$name."','".$email."',NOW(),'".md5(rand(1000,999).date("Ymdhis"))."','0')")){
		$id = mysql_insert_id();
						 }else{
						 	$id = 0;
						 }
						 $this->close();
						 if($id>0){
						 	$this->open();
						 	$rs = $this->fetch("SELECT * FROM user_register_temp WHERE id='".$id."' LIMIT 1");
						 	$this->close();
						 	return $rs['confirm_key'];
						 }else{
						 	return -1;
						 }
	}
	function registration_process($name,$email){
		//include "include/config.php";
		//include "include/db.php";
		//OpenX Hacks by duf 2010/05/08
		//include_once "include/kana/SITTI_OX_RPC.php";
		//include_once "include/openx_config.php";
		global $dbHost,$dbUser,$dbPass,$nameDb,$OX_CONFIG;
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],false);
		//login ke service
		$ox->logon();

		$db = new db($dbHost,$dbUser,$dbPass,$nameDb);
		//duf - ini nanti dibuka lagi
		if($_SESSION['formcode']!=$_POST['formcode']){
			echo '<script> window.location.href="index.php"; </script>' ;
			exit;
		}


		$nama=$name;
		$email=$email;
		$password= uniqid();
		$the_password = uniqid();
		$password = md5(crypt($password,md5($email)));
		$address="";
		$city="";
		$zipcode="";
		$web="";
		$telp="";
		$hp="";
		$listChannel = array("http://www.bataviase.com");

		$idUser = uniqid(rand(100,999));
		$publisherID = "pub_".$idUser;
		$advertiserID = "adv_".$idUser;

		//print $advertiserID."<br/>";
		//register ke openx
		$params['advertiserName'] = $advertiserID;
		$params['publisherName'] = $publisherID;
		$params['website'] = "http://www.bataviase.com";
		$params['emailAddress'] = $email;
		$params['contactName'] = $nama;
		//print $email;
		$ox_adv_id = $ox->registerAsAdvertiser(&$params);
		$ox_pub_id = $ox->registerAsPublisher(&$params);

		$sql_ins = "INSERT INTO `user` (
					`user_id` , 
					`level_id` , 
					`publisher_id` , 
					`nama_lengkap` , 
					`email` , 
					`password` , 
					`alamat` , 
					`kota` , 
					`kodepos` , 
					`nomor_telepon` , 
					`nomor_hp` ,					 
					`lastlogin`,`adv_id`,
					ox_adv_id,ox_pub_id
					 )
					VALUES (
					'$idUser', 
					'user', 
					'$publisherID', 
					'$nama', 
					'$email', 
					'$password', 
					'$address', 
					'$city', 
					'$zipcode', 
					'$telp', 
					'$hp' , 
					'0000-00-00',
					'$advertiserID',
					'$ox_adv_id','$ox_pub_id'
					);
		";

		if($db->exequery($sql_ins)) $submit = 1; else $submit = 0;
		if($submit==1){
			//print mysql_error();
			//echo count($listChannel);
			if(count($listChannel)>0){
				foreach($listChannel as $key=>$val){
					$idWeb = uniqid(md5(microtime()));
					//$params['pubID'] = $ox_pub_id;
					//$params['zoneName'] = $val;
					//$ox_zone_id = $ox->registerZone(&$params);
					//print $ox_zone_id."<br/>";
					$sql = "insert into user_web(`id_web`,`id_user`,`web_address`)
						values('$idWeb','$idUser','$val')
						";

					$db->exequery($sql);
					//print mysql_error();
				}
			}
			//Kirim email untuk password
			$smtp=new smtp_class;
		$smtp->host_name="smtp.gmail.com";
		$smtp->host_port=465;                /* Change this variable to the port of the SMTP server to use, like 465 */
		$smtp->ssl=1;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
		$smtp->start_tls=0;                 /* Change this variable if the SMTP server requires security by starting TLS during the connection */
		$smtp->localhost="soekarno";       /* Your computer address */
		$smtp->direct_delivery=0;           /* Set to 1 to deliver directly to the recepient SMTP server */
		$smtp->timeout=10;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
		$smtp->data_timeout=10;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server.
		Set to 0 to use the same defined in the timeout variable */
		//	$smtp->debug=1;                     /* Set to 1 to output the communication with the SMTP server */
		$smtp->debug=0;                     /* Set to 1 to output the communication with the SMTP server */
		//	$smtp->html_debug=1;                /* Set to 1 to format the debug output as HTML */
		$smtp->html_debug=0;                /* Set to 1 to format the debug output as HTML */
		$smtp->pop3_auth_host="";           /* Set to the POP3 authentication host if your SMTP server requires prior POP3 authentication */
		$smtp->user="sittizen@gmail.com";                     /* Set to the user name if the server requires authetication */
		$smtp->realm="";                    /* Set to the authetication realm, usually the authentication user e-mail domain */
		$smtp->password="b4b00nb4b00n";                 /* Set to the authetication password */
		$smtp->workstation="";              /* Workstation name for NTLM authentication */
		$smtp->authentication_mechanism=""; /* Specify a SASL authentication method like LOGIN, PLAIN, CRAM-MD5, NTLM, etc..
		Leave it empty to make the class negotiate if necessary */
			global $namaWebPageAds;
			$name = mysql_escape_string($name);
			$email = mysql_escape_string($email);
			$to      = $email;
			$subject = '[SITTI]KONFIRMASI SUKSES';
			$message = 'Email Anda telah di konfirmasi dengan sukses.<br/><br/>';
			$message .= "Email Anda : ".$email."<br/>";
			$message .= 'Password anda : '.$the_password.'<br/><br/><br/>';
			$message .= "<a href='http://www.sittibelajar.com' target='_blank'>Klik disini</a> untuk masuk ke halaman login SITTI.<br/><br/><br/>SITTI.";
			$headers = 'From: no-reply@sittibelajar.com' . "\r\n" .
    'Reply-To: no-reply@sittibelajar.com' . "\r\n";
			//print $to;
			//print $message;
			$ox->logout();
		if($smtp->SendMessage("mailbot@sittibelajar.com",array($to),array(
			"MIME-Version: 1.0",
			"Content-Type: text/html",
			"From: mailbot@sittibelajar.com",
			"To: $to",
			"Subject: $subject",
			"Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")
		),
		$message)){
			//print "berhasil";
			return true;
		}else{
			//print "gagal-->".$smtp->error;
			return false;
		}
			/*if(mail($to, $subject, $message, $headers)){
					
				return true;
			}else{
				//print "gagal";
				return false;
			}*/
			//logout service kalo uda kelar.

			return true;
		}

	}
}
?>