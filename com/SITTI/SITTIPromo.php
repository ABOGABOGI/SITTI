<?php

class SITTIPromo extends SQLData
{
	
  function SITTIPromo()
	{}
 
  function writeDBLog($ip_address, $page)
  {
    $query = "INSERT INTO db_web3.promo_log 
                (ip_address, page, accessed_time) 
                VALUES 
                ('". $ip_address ."', '". $page ."', NOW())";

    $this->open(0);
    $result = $this->query($query);
    $this->close();

    return $result;
  }

  function writeEmailTrack($email, $voucher_code)
  {
    $query = "INSERT INTO db_web3.sitti_email_track 
              (email, voucher_code, tglisidata) 
              VALUES 
              ('$email', '$voucher_code', NOW())";
    $this->open(0);
    $result = $this->query($query);
    $this->close();

    return $result;  
  }

}

?>
