<?php 
/**
 * SITTIInventory
 * class yang menghandle pembuatan iklan, manajemen iklan, 
 * dan hal2 lain yg berkaitan dengan inventory iklan advertiser
 * 
 */

class SITTICampaign extends SQLData{
	var $ox_campaign_id;
	var $lastInsertId = -1;
    function SITTICampaign(){
       parent::SQLData();
    }
    /**
     * method untuk menambahkan campaign
     */
    function addCampaign($sittiID,$name,$tgl_mulai,$tgl_berakhir,$description,$ox_advertiser_id){
    	global $APP_PATH,$OX_CONFIG;
    		
    		$this->open(0);
    		
    		$rs = $this->fetch("SELECT ox_campaign_id FROM sitti_campaign ORDER BY ox_campaign_id DESC LIMIT 1");
    		//$campaign_id = $rs['ox_campaign_id']+1;
    		$sql = "INSERT INTO sitti_campaign
    						(sittiID,name,description,campaign_start,
    						campaign_end,created_date,
    						ox_advertiser_id)
    						VALUES('".$sittiID."','".$name."','".$description."',
    								'".$tgl_mulai."','".$tgl_berakhir."',NOW(),
    								'".$ox_advertiser_id."')";
    	 //print $sql;
    		$stmt = $this->query($sql);
    	
    		$this->lastInsertId = mysql_insert_id();
    		$this->close();
    		return $stmt;
    	
    }
    function addCampaignToReporting($advertiser_id,$kampanye_id,$nama_kampanye){
    	$this->open(2);
    	$sql = "INSERT INTO 
    			db_report.tbl_performa_akun_kampanye_total (advertiser_id, 
    														kampanye_id, 
    														kampanye, 
    														STATUS, 
    														jum_imp, 
    														jum_klik, 
    														ctr, 
    														harga, 
    														budget_harian, 
    														budget_total, 
    														budget_sisa, 
    														last_update) 
    														VALUES 
    														('".$advertiser_id."',
    														'".$kampanye_id."',
    														'".$nama_kampanye."',
    														0,0,0, '0.0000',
    														'0',
    														'0', 
    														'0',
    														'0',
    														NOW());";
    	$this->query($sql);
		$sql = "INSERT INTO 
    			db_report.tbl_performa_akun_kampanye (datee,
														advertiser_id, 
														kampanye_id, 
														kampanye, 
														status, 
														jum_imp, 
														jum_klik, 
														ctr, 
														average_cpm,
														harga,
														posisi,
														budget_harian, 
														budget_total, 
														budget_sisa, 
														last_update) 
														VALUES 
														(DATE(NOW()),
														'".$advertiser_id."',
														'".$kampanye_id."',
														'".$nama_kampanye."',
														0,0,0, '0.0000', '0.0000',
														'0',
														'0',
														'0', 
														'0',
														'0',
														NOW());";
    	$this->query($sql);
    	$this->close();
    }
    /**
     * 
     * Update campaign Detail
     * @param $sittiID
     * @param $name
     * @param $tgl_mulai
     * @param $tgl_berakhir
     * @param $description
     * @param $ox_advertiser_id
     * @return boolean
     */
	function updateCampaign($sittiID,$campaign_id,$name,$tgl_mulai,$tgl_berakhir,$description){
    	global $APP_PATH;
    	$sittiID = mysql_escape_string($sittiID);
    	$campaign_id = mysql_escape_string($campaign_id);
    	$name = mysql_escape_string($name);
    	$tgl_mulai = mysql_escape_string($tgl_mulai);
    	$tgl_berakhir = mysql_escape_string($tgl_berakhir);
    	$description = mysql_escape_string($description);
    	
    	$this->open(0);	
    	$sql = "UPDATE db_web3.sitti_campaign 
    			SET name='".$name."',description='".$description."',
   				campaign_start='".$tgl_mulai."',campaign_end='".$tgl_berakhir."' 
   				WHERE sittiID = '".$sittiID."' AND ox_campaign_id='".$campaign_id."'";   
    		
    	$stmt = $this->query($sql);
    	$this->close();
		
		$this->open(2);	
    	$sql = "UPDATE db_report.tbl_performa_akun_kampanye_total 
    			SET kampanye='".$name."' 
   				WHERE advertiser_id = '".$sittiID."' AND kampanye_id='".$campaign_id."'";   
    		
    	$stmt = $this->query($sql);
    	$this->close();
    	return $stmt;
    	
    }
    function deleteCampaign($sittiID,$campaignID){
         $sql = "UPDATE sitti_campaign SET camp_flag=2, action_flag=1,finish_time=NOW() 
        					 WHERE sittiID='".$sittiID."' AND ox_campaign_id=".$campaignID."";
         //print $sql;
        //return $this->query("DELETE FROM sitti_campaign WHERE sittiID='".$sittiID."' AND ox_campaign_id='".$campaignID."'");
        return $this->query($sql);
    }
    function flagCampaignInReport($sittiID,$campaignID){
    	$this->open(2);
    	 $sql = "UPDATE db_report.tbl_performa_akun_kampanye_total ak
    			SET 
    			ak.status = 2,
    			ak.last_update = NOW()
    			WHERE ak.advertiser_id = '".$sittiID."'
    			AND ak.kampanye_id = ".$campaignID.";
			    ";
        $rs = $this->query($sql);
    	$this->close();
    	return $rs;
    }
    /**
     * 
     * get created OpenX Campaign ID
     */
    function getInsertedOXID(){
    	return $this->ox_campaign_id;
    }
    /**
     * 
     * mengambil daftar campaign advertiser.
     * @param $sittiID
     * @return array Result Sets
     */
    function getCampaignList($sittiID,$start=0,$total=50){
    	$this->open(0);
    	$sql = "SELECT * FROM sitti_campaign WHERE sittiID='".$sittiID."' ORDER BY created_date DESC LIMIT ".$start.",".$total;
    	$rs = $this->fetch($sql,1);
    	$this->close();
    	return $rs;
    }

    function getCampaigns($sittiID,$start=0,$total=50){
        $query = "SELECT a.*
                    FROM db_web3.`sitti_campaign` a
                    INNER JOIN db_report.`tbl_performa_akun_kampanye_total` b
                    ON a.`ox_campaign_id` = b.`kampanye_id`
                    WHERE a.`sittiID` = '".$sittiID."'
                    ORDER BY a.created_date DESC
                    LIMIT ".$start.",".$total;

        $this->open(2);
        $rs = $this->fetch($query,1);
        $this->close();
        return $rs;
    }

    /**
     * cek apakah advertiser memiliki campaign.
     * @param $sittiID
     */
    function hasCampaign($sittiID){
    	$this->open(0);
    	$rs = $this->fetch("SELECT COUNT(*) as total FROM sitti_campaign WHERE sittiID='".$sittiID."' LIMIT 1");
    	
    	$this->close();
    	if($rs['total']!=0){
    		return true;
    	}
    }
    function isCampaignOwner($sittiID,$campaignID){
      if(strlen($sittiID)>1&&$campaignID!=0){
      	$sql = "SELECT * FROM db_web3.sitti_campaign WHERE sittiID='$sittiID' AND ox_campaign_id='".$campaignID."' LIMIT 1";
        $rs = $this->fetch($sql);
        if($rs['sittiID']==$sittiID&&$rs['ox_campaign_id']==$campaignID){
          return true;
       }
      }
    }
    /**
     * get campaign detail
     * @param $sittiID
     * @param $campaignID
     * @return array
     */
    function getCampaignByOwner($sittiID,$campaignID){
    	$sittiID = mysql_escape_string($sittiID);
    	$campaignID = mysql_escape_string($campaignID);
    	if(strlen($sittiID)>1&&$campaignID!=0){
    		$sql = "SELECT sittiID,ox_campaign_id,name,description,
    				DATE_FORMAT(campaign_start,'%d/%m/%Y') as campaign_start,
    				DATE_FORMAT(campaign_end,'%d/%m/%Y') as campaign_end 
    				FROM db_web3.sitti_campaign WHERE sittiID='".$sittiID."' AND ox_campaign_id='".$campaignID."' LIMIT 1";
            $this->open(0);
            $rs = $this->fetch($sql);
            $this->close();
    		if($rs['sittiID']==$sittiID&&$rs['ox_campaign_id']==$campaignID){
          		return $rs;
       		}
    	}
    }
    /**
     * hitung jumlah semua iklan yang dimiliki oleh campaign, termasuk iklan2 yang masih pending
     */
    /*
     function getTotalAds($sittiID,$campaignID){
         $active_ads = $this->fetch("SELECT COUNT(id) as total FROM sitti_ad_inventory WHERE advertiser_id='".$sittiID."' AND ox_campaign_id='".$campaignID."' LIMIT 1");
         $pending_ads = $this->fetch("SELECT COUNT(id) as total FROM sitti_ad_inventory_temp WHERE advertiser_id='".$sittiID."' AND ox_campaign_id='".$campaignID."' LIMIT 1");
         $rs['active_ads'] = $active_ads['total'];
         $rs['pending_ads'] = $pending_ads['total'];
         return $rs;
     }
     */
	function getTotalAds($sittiID,$campaignID){
         $active_ads = $this->fetch("SELECT COUNT(id) as total FROM sitti_ad_inventory WHERE advertiser_id='".$sittiID."' AND ox_campaign_id='".$campaignID."' AND ad_flag<>2 LIMIT 1");
         $rs['active_ads'] = $active_ads['total'];
         return $rs;
     }

     function getCampaignInfo($campaign_id){
         $query = "SELECT * 
                    FROM db_web3.sitti_campaign 
                    WHERE ox_campaign_id = '".$campaign_id."'
                    LIMIT 1";
        
        $this->open(0);
        $rs = $this->fetch($query);
        $this->close();

        return $rs; 
     }
     
	function Enable($sittiID,$campaignID){
		$sql = "UPDATE sitti_campaign SET camp_flag=0, action_flag=1 WHERE sittiID='".$sittiID."' AND ox_campaign_id=".$campaignID."";
		return $this->query($sql);
	}
	
	function Disable($sittiID,$campaignID){
		$sql = "UPDATE sitti_campaign SET camp_flag=1, action_flag=1 WHERE sittiID='".$sittiID."' AND ox_campaign_id=".$campaignID."";
		return $this->query($sql);
	}
}
?>