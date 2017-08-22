<?php 

class SITTIPPAModel extends SQLData
{
	public $advertiser_id;
	
	public function SITTIPPAModel($advertiser_id = false)
	{
		parent::SQLData();
		if ($advertiser_id) 
		{
			$this->advertiser_id = $advertiser_id;
		}
		else
		{
			$this->advertiser_id = $_SESSION['sittiID'];
		}
	}

	public function profileExist($advertiser_id = false)
	{
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;

		$query = "SELECT advertiser_id FROM db_web3.sitti_ppa_profile 
					WHERE advertiser_id = '". $adv_id ."'";

		$this->open(0);
		$rs = $this->fetch($query);
		$this->close();
		
		return (bool) ($rs) ? count($rs) > 0 : $rs;
	}

	public function getProfile($advertiser_id = false)
	{
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;

		$query = "SELECT * FROM db_web3.sitti_ppa_profile prof
					WHERE prof.`advertiser_id` = '". $adv_id ."'
					LIMIT 1";

		$this->open(0);
		$result = $this->fetch($query);
		$this->close();

		return $result;
	}

	public function updateProfile($data, $advertiser_id = false)
	{
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;
		if (! is_array($data)) return false;

		$query = "UPDATE db_web3.sitti_ppa_profile SET
					jenis_site = '".$data['jenis_site']."',
					url_site = '".$data['url_site']."'
					WHERE advertiser_id ='".$adv_id."'";
		
		$this->open(0);
		$result = $this->query($query);
		$this->close();

		return $result;
	}

	public function isKeyExist($advertiser_id = false)
	{
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;

		$query = "SELECT * FROM zz_sitti.`ppa_api_key`
					WHERE advertiser_id = '". $adv_id ."'
					LIMIT 1";
		
		$this->open(0);
		$result = $this->fetch($query);
		$this->close();
		
		return is_array($result) ? count($result) > 0 : false;
	}

	public function generateKey($advertiser_id = false)
	{
		global $CONFIG;
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;
		$salt = rand(0, 9999);
		$api_key = sha1($adv_id . $salt . $CONFIG['SITTI_PPA_SECRET']);

		$query = "INSERT INTO zz_sitti.`ppa_api_key` 
					(advertiser_id, api_key, salt) 
					VALUES ('". $adv_id ."', '". $api_key ."', '". $salt ."')";

		$this->open(0);
		$result = $this->query($query);
		$this->close();

		return $result;
	}

	public function getKey($advertiser_id = false)
	{
		$adv_id = empty($advertiser_id) ? $this->advertiser_id : $advertiser_id;

		$query = "SELECT api_key FROM zz_sitti.`ppa_api_key`
					WHERE advertiser_id = '". $adv_id ."'
					LIMIT 1";
		
		$this->open(0);
		$result = $this->fetch($query);
		$this->close();
		
		return (is_array($result) && count($result) > 0) ? $result['api_key'] : false;
	}

}
