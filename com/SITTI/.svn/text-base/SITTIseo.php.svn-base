<?php

class SITTIseo extends SQLData{
	function SITTIseo(){
		parent::SQLData();
	}
	
	function insertParticipant($nama, $email, $website, $kontak, $kodepos){
		$this->open();
		$flag = $this->query("INSERT INTO seo_participant (nama, email, website, kontak, kodepos) VALUES('".$nama."', '".$email."', '".$website."', '".$kontak."', '".$kodepos."')");
		$this->close();
		return $flag;
	}
	
	function getRecentParticipant(){
		$this->open();
		$rs = $this->fetch("SELECT id, website FROM seo_participant ORDER BY id DESC LIMIT 6;",1);
		$this->close();
		return $rs;
	}
}

?>