<?php

class SITTIReferral extends SQLData
{
	private $account;
	private $request;
	private $view;
	private $sitti_id;
	
	function SITTIReferral($req,$account)
	{
		$this->account = $account;
		$this->request = $req;
		$this->view = new BasicView();
		$this->sitti_id = $_SESSION['sittiID'];
	}

	function isAlreadyJoined($publisher_id = false)
	{
		$pub_id = empty($publisher_id) ? $this->sitti_id : $publisher_id;

		$query = "SELECT sittiID FROM db_web3.sitti_account_publisher WHERE sittiID = '". $pub_id ."' AND is_referral = '1' LIMIT 1";

		$this->open(0);
		$rs = $this->fetch($query);
		$this->close();
		
		return (bool) ($rs) ? count($rs) > 0 : false;
	}

	function joinProgram($publisher_id = false)
	{
		$pub_id = empty($publisher_id) ? $this->sitti_id : $publisher_id;

		$query = "UPDATE db_web3.sitti_account_publisher SET
					is_referral = '1'
					WHERE sittiID ='".$pub_id."'";
		
		$this->open(0);
		$result = $this->query($query);
		$this->close();

		return $result;
	}

	function refer($publisher_id, $referral_id)
	{
		if (empty($publisher_id) || empty($referral_id)) return false;

		$query = "INSERT INTO db_web3.sitti_publisher_referral (publisher_id, referral_id)
					VALUES ('". $publisher_id ."', '". $referral_id ."')";

		$this->open(0);
		$result = $this->query($query);
		$this->close();

		return $result;
	}
	

}

?>