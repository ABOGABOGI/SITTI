<?php
/**
 * SITTIInventory
 * class yang menghandle pembuatan iklan, manajemen iklan,
 * dan hal2 lain yg berkaitan dengan inventory iklan advertiser
 *
 */

class SITTIInventory extends SQLData{
	var $last_insert_id;
	var $found_rows;
	function SITTIInventory(){
		parent::SQLData();
	}
	/**
	 * retrieve genre iklan
	 * @return array
	 */
	function getAdCategory(){
		$this->open(0);
		$list= $this->fetch("SELECT * FROM sitti_ad_category ORDER BY categoryName",1);
		$this->close();
		return $list;
	}
	function getAdGenre(){
		$this->open(0);
		$list= $this->fetch("SELECT * FROM sitti_ad_genre ORDER BY categoryName",1);
		$this->close();
		return $list;
	}
	function getRelatedKeyword($cat_id, $product_type){
		$arr = explode(",", $product_type);
		$product_type_arr = array();
		foreach($arr as $key){
			array_push($product_type_arr, trim($key));
		}
		$product_type_arr = array_unique($product_type_arr);
		$keys = "";
		foreach($product_type_arr as $key){
			if ($keys!=""){
				$keys .= ",";
			}
			$keys .= "'".$key."'";
		}
		
		$this->open(0);
		$cat_keys = $this->fetch("SELECT keyword FROM sitti_cat_keyword_relation WHERE cat_id=".$cat_id." ORDER BY jum DESC LIMIT 10",1);
		foreach ($cat_keys as $row){
			if ($keys!=""){
				$keys .= ",";
			}
			$keys .= "'".$row['keyword']."'";
		}
		$rel_keys = $this->fetch("SELECT DISTINCT(related_keyword) FROM sitti_keyword_relation WHERE keyword IN (".$keys.") ORDER BY jum DESC LIMIT 10",1);
		$this->close();
		foreach ($rel_keys as $row){
			$keys .= ",'".$row['related_keyword']."'";
		}
		$keys = str_replace("','", ",", $keys);
		$keys = substr($keys, 1 , sizeof($keys)-2);
		return $keys;
	}
	function addLocation($iklan_id,$location){
		//var_dump($location);
		for($i=0;$i<sizeof($location);$i++){
			$sql = "INSERT INTO db_web3.sitti_ad_location(id,kota,priority)
					VALUES(".$iklan_id.",'".$location[$i]['kota']."',".$location[$i]['priority'].")";
			//print $sql;
			$this->query($sql);
		}
	}
	
	function addBannerLocation($banner_id,$location){
		//var_dump($location);
		for($i=0;$i<sizeof($location);$i++){
			$sql = "INSERT INTO db_web3.sitti_banner_location(banner_id,kota,priority)
					VALUES(".$banner_id.",'".$location[$i]['kota']."',".$location[$i]['priority'].")";
			//print $sql;
			$this->query($sql);
		}
	}
	
	function createAd($sittiID,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$campaign_id,$genre_id,$serve_type=0){
		global $OX_CONFIG,$APP_PATH;
		 $sql = "INSERT INTO sitti_ad_inventory(nama,judul,baris1,baris2,category_id,
    	   													urlName,urlLink,advertiser_id,created_date,
    	   													ox_campaign_id,ox_banner_id,genre_id,action_flag,serve_type)
    	   						VALUES('".$nama."','".$judul."','".$baris1."','".$baris2."',
    	   						'".$category."','".$urlName."','".$urlLink."','".$sittiID."',NOW(),'".$campaign_id."','0','".$genre_id."',1,'".$serve_type."')";
		// $this->open(0);
		$rs = $this->query($sql);
		if($rs){
			$this->last_insert_id = mysql_insert_id();
		}
		// $this->close();
		return $rs;

	}
	function createImageAd($sittiID,$nama,$judul,$category,$ad_size,$asset_file,$urlLink,$campaign_id,$genre_id,$tgl_mulai,$tgl_selesai){
		global $OX_CONFIG,$APP_PATH;
		$ad_size = intval($ad_size);
		 $asset_file = mysql_escape_string($asset_file);
		 $sql = "INSERT INTO sitti_banner_inventory(nama,judul,ad_type,fileName,category_id,
    	   													urlLink,advertiser_id,created_date,
    	   													ox_campaign_id,banner_flag,genre_id,action_flag,tglmulai,tglselesai)
    	   						VALUES('".$nama."','".$judul."','".$ad_size."','".$asset_file."',
    	   						'".$category."','".$urlLink."','".$sittiID."',NOW(),'".$campaign_id."','0','".$genre_id."',1,'".$tgl_mulai."','".$tgl_selesai."')";
		// $this->open(0);
		$rs = $this->query($sql);
		if($rs){
			$this->last_insert_id = mysql_insert_id();
		}
		// $this->close();
		return $rs;

	}
	//untuk sementara asumsi semua banner flash adalah sama semua.
	function createFlashAd($sittiID,$nama,$judul,$category,$ad_size,$asset_file,$urlLink,$campaign_id,$genre_id,$tgl_mulai,$tgl_selesai){
		global $OX_CONFIG,$APP_PATH;
		$ad_size = intval($ad_size);
		 $asset_file = mysql_escape_string($asset_file);
		 $sql = "INSERT INTO sitti_banner_inventory(nama,judul,ad_type,fileName,category_id,
    	   													urlLink,advertiser_id,created_date,
    	   													ox_campaign_id,banner_flag,genre_id,action_flag,tglmulai,tglselesai)
    	   						VALUES('".$nama."','".$judul."','".$ad_size."','".$asset_file."',
    	   						'".$category."','".$urlLink."','".$sittiID."',NOW(),'".$campaign_id."','1','".$genre_id."',1,'".$tgl_mulai."','".$tgl_selesai."')";
		// $this->open(0);
		$rs = $this->query($sql);
		if($rs){
			$this->last_insert_id = mysql_insert_id();
		}
		// $this->close();
		return $rs;

	}

	function createPPAAd($sittiID, $nama, $judul, $baris1, $urlLink, $campaignID){
		global $OX_CONFIG,$APP_PATH;
		$sql = "INSERT INTO sitti_ad_inventory (advertiser_id,nama,judul,baris1,urlLink,ox_campaign_id,serve_type,created_date,urlName,action_flag)
    	   			VALUES('".$sittiID."','".$nama."','".$judul."','".$baris1."','".$urlLink."','".$campaignID."','1',NOW(),'".$urlLink."','1')";
		
		$rs = $this->query($sql);
		if ($rs) $this->last_insert_id = mysql_insert_id();
		return $rs;
	}

	function insertKomisiPPA($iklan_id, $harga_produk, $persen_komisi, $rupiah_komisi)
	{
		$harga_produk = intval($harga_produk);
		$persen_komisi = floatval($persen_komisi);
		$rupiah_komisi = intval($rupiah_komisi);

		$query = "INSERT INTO sitti_ppa_komisi (iklan_id, harga_produk, persen_komisi, harga_konversi) 
					VALUES ('". $iklan_id ."', ".$harga_produk.", ".$persen_komisi.", ".$rupiah_komisi.")";

		return $this->query($query);
	}

	function updateAd($iklan_id,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$genre_id){
		$rs = $this->query("UPDATE sitti_ad_inventory
    	   					   SET nama='".$nama."',judul='".$judul."',baris1='".$baris1."',
    	   					   baris2='".$baris2."',category_id='".$category."',
    	   					   urlName='".$urlName."',urlLink='".$urlLink."',genre_id='".$genre_id."'
    	   					   WHERE id='".$iklan_id."'");
		return $rs;
	}
	function updatePendingAd($iklan_id,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$genre_id){
		$rs = $this->query("UPDATE sitti_ad_inventory_temp
    	   					   SET nama='".$nama."',judul='".$judul."',baris1='".$baris1."',
    	   					   baris2='".$baris2."',category_id='".$category."',genre_id='".$genre_id."',
    	   					   urlName='".$urlName."',urlLink='".$urlLink."',nflag='1'
    	   					   WHERE id='".$iklan_id."'");
		return $rs;
	}
	function queueNewAd($sittiID,$nama,$judul,$category,$baris1,$baris2,$urlName,$urlLink,$campaign_id,$genre_id){
		global $OX_CONFIG,$APP_PATH;
		 
		$this->open(0);
		$rs = $this->query("INSERT INTO sitti_ad_inventory_temp(nama,judul,baris1,baris2,category_id,
    	   													urlName,urlLink,advertiser_id,created_date,
    	   													ox_campaign_id,ox_banner_id,genre_id)
    	   						VALUES('".$nama."','".$judul."','".$baris1."','".$baris2."',
    	   						'".$category."','".$urlName."','".$urlLink."','".$sittiID."',NOW(),'".$campaign_id."','0','".$genre_id."')");
		if($rs){
			$this->last_insert_id = mysql_insert_id();
		}
		$this->close();
		return $rs;

	}
	/**
	 *
	 * moving the panding ad to final table.
	 * @param $iklan_id --> temporary id
	 */
	function moveAdFromQueue($iklan_id){
		//$this->open(0);
		$flag = false;
		$rs = $this->fetch("SELECT * FROM sitti_ad_inventory_temp WHERE id='".$iklan_id."' LIMIT 1");
		 
		if($rs['nama']!=null&&$rs['ox_campaign_id']!=null&&$rs['advertiser_id']!=null){
			if($this->createAd($rs['advertiser_id'],
			$rs['nama'],
			$rs['judul'],
			$rs['category_id'],
			$rs['baris1'],
			$rs['baris2'],
			$rs['urlName'],
			$rs['urlLink'],
			$rs['ox_campaign_id'],$rs['genre_id'])){
				$new_id = $this->last_insert_id;
					
				$ads = $this->fetch("SELECT * FROM sitti_ad_keywords_temp WHERE iklan_id='".$iklan_id."'",1);
					
				for($i=0;$i<sizeof($ads);$i++){

					if(strlen($ads[$i]['keyword'])>0){
						$this->addKeyword($new_id,$ads[$i]['keyword'],
						$ads[$i]['bid'],$ads[$i]['budget_daily'],
						$ads[$i]['budget_total']);
					}
				}
				$this->prepareAdsForBidding($new_id, $ads);
				// die();
				//delete the old keywords
				$this->deleteKeywordFromQueue($iklan_id);
				print mysql_error();
				//delete the ad from queue
				$this->deleteAdFromQueue($iklan_id);
				$flag = true;
			}
		}
		//$this->close();
		return $flag;
	}
	function deleteAdFromQueue($iklan_id){
		return $this->query("DELETE FROM sitti_ad_inventory_temp WHERE id='".$iklan_id."'");
	}
	function setAdStatusInQueue($iklan_id,$status){
		return $this->query("UPDATE sitti_ad_inventory_temp SET nflag='".$status."' WHERE id='".$iklan_id."'");
	}
	function queueNewKeyword($iklan_id,$keyword,$bid,$daily_budget,$total_budget){
		$this->open(0);
		$rs = $this->query("INSERT INTO sitti_ad_keywords_temp(iklan_id,keyword,bid,budget_daily,budget_total)
    	   						VALUES('".$iklan_id."','".$keyword."','".$bid."','".$daily_budget."','".$total_budget."')");
		$this->close();
		return $rs;
	}
	function deleteKeywordFromQueue($iklan_id){
		return $this->query("DELETE FROM sitti_ad_keywords_temp WHERE iklan_id='".$iklan_id."'");
	}
	function deleteKeywords($iklan_id){
		return $this->query("DELETE FROM sitti_ad_keywords WHERE iklan_id='".$iklan_id."'");
	}
	function addKeyword($iklan_id,$keyword,$bid,$daily_budget,$total_budget){
		//$this->open(0);
		//check sudah ada apa belum keywordnya
		$keyword = trim($keyword);
		$sql = "SELECT * FROM db_web3.sitti_ad_keywords WHERE iklan_id=".mysql_escape_string($iklan_id)." 
				AND keyword='".mysql_escape_string($keyword)."' LIMIT 1";
		$rs = $this->fetch($sql);
		
		
		if($rs['iklan_id']==$iklan_id&&$rs['keyword']==$keyword){
			//action_flag di set ke 1 untuk memaksa ad_update_observer bot utk menghitung score ulang.
			$sql = "UPDATE db_web3.sitti_ad_keywords 
					SET last_update=NOW(), action_flag=1,keyword_flag = 0,
					bid=".$bid.",budget_daily=".$daily_budget.",budget_total=".$total_budget."
					WHERE iklan_id=".mysql_escape_string($iklan_id)." 
					AND keyword='".mysql_escape_string($keyword)."'
    	   			";	
		}else{
			$sql = "INSERT INTO db_web3.sitti_ad_keywords(iklan_id,keyword,bid,budget_daily,budget_total,action_flag,created_date,last_update)
    	   						VALUES('".$iklan_id."','".$keyword."','".$bid."','".$daily_budget."','".$total_budget."',0,NOW(),NOW())";
		}
		
		$rs = $this->query($sql);
		
		
		$sql1= "INSERT IGNORE INTO db_web3.sitti_keywords_avg_cpc(keyword, avg_cpc, last_update) 
				VALUES ('".$keyword."',0,NOW())";
		$sql2 = "INSERT IGNORE INTO db_web3.sitti_keywords_max_bid
    				(keyword, bids, last_update) VALUES ('".$keyword."',".$bid.",NOW())";
		
		$sql = "SELECT * FROM db_web3.sitti_ad_inventory WHERE id=".mysql_escape_string($iklan_id)." LIMIT 1";
		$result = $this->fetch($sql);
		$advertiser_id = $result['advertiser_id'];
		$campaign_id = $result['ox_campaign_id'];
		
		$sql3 = "INSERT IGNORE INTO db_report.tbl_performa_kampanye_kata_kunci
    				(datee, advertiser_id, kampanye_id, keyword, harga, budget_harian, budget_total) VALUES 
					(DATE(NOW()), '".$advertiser_id."', '".$campaign_id."', '".$keyword."', '".$bid."', '".$daily_budget."', '".$total_budget."')";
		
		$this->query($sql1);
		$this->query($sql2);
		$this->query($sql3);
		
		//   $this->close();
		return $rs;
	}
	/**
	 * 
	 * @todo mesti dipikirin soal $sql1 dan $sql2
	 * @param $iklan_id
	 * @param $keyword
	 * @param $bid
	 * @param $daily_budget
	 * @param $total_budget
	 */
	function addBannerKeyword($banner_id,$keyword,$bid,$impression_total){
		//$this->open(0);
		//check sudah ada apa belum keywordnya
		$sql_select = "SELECT banner_id, keyword FROM db_web3.sitti_banner_keywords WHERE banner_id=".mysql_escape_string($iklan_id)." 
				AND keyword='".mysql_escape_string($keyword)."' LIMIT 1";
		$rs_select = $this->fetch($sql_select);
		
		if($rs_select['banner_id']==$iklan_id && $rs['keyword']==$keyword){
			//action_flag di set ke 1 untuk memaksa ad_update_observer bot utk menghitung score ulang.
			$sql = "UPDATE db_web3.sitti_banner_keywords 
					SET action_flag=1,keyword_flag = 0,
					bid=".$bid.",impression_total=".$impression_total."
					WHERE banner_id=".mysql_escape_string($banner_id)." 
					AND keyword='".mysql_escape_string($keyword)."'
    	   			";	
		}else{
			$sql = "INSERT INTO db_web3.sitti_banner_keywords(banner_id,keyword,bid,impression_total, action_flag)
    	   						VALUES('".$banner_id."','".$keyword."','".$bid."','".$impression_total."',0)";
		}
		//print $sql."<br/>";
		$rs = $this->query($sql);
		/*
		$sql1= "INSERT IGNORE INTO db_web3.sitti_keywords_avg_cpc(keyword, avg_cpc, last_update) 
				VALUES ('".$keyword."',0,NOW())";
		$sql2 = "INSERT IGNORE INTO db_web3.sitti_keywords_max_bid
    				(keyword, bids, last_update) VALUES ('".$keyword."',".$bid.",NOW())";
		*/
		//$this->query($sql1);
		//$this->query($sql2);
		//   $this->close();
		return $rs;
	}

	function addBannerCategory($banner_id,$kategori,$bid,$impression_total)
	{
		$query = "INSERT INTO db_web3.sitti_banner_kategori (banner_id,kategori,bid,impression_total) 
					VALUES ('".$banner_id."','".$kategori."','".$bid."','".$impression_total."')";
		
		$rs = $this->query($query);
		return $rs;
	}

	function addPPACategory($iklan_id, $kategori, $harga_produk, $komisi)
	{
		$query = "INSERT INTO db_web3.sitti_ppa_kategori (iklan_id, kategori, harga_produk, komisi_produk, processed) 
					VALUES ('".$iklan_id."', '".$kategori."', '".$harga_produk."', '".$komisi."',0)";
		
		$rs = $this->query($query);
		return $rs;	
	}

	/**
	 * 
	 * remove keyword
	 * @param unknown_type $iklan_id
	 * @param unknown_type $keyword
	 */
	function removeKeyword($iklan_id,$keyword){
		$rs = $this->query("DELETE FROM sitti_ad_keywords WHERE iklan_id='".$iklan_id."' AND keyword='".$keyword."'");
		return $rs;
	}
	/**
	 * 
	 * flag the keyword for removal.
	 * keyword_flag --> 0 keyword aktif
	 * keyword_flag --> 1 keyword akan dihapus.
	 * @param $iklan_id
	 * @param $keyword
	 * return #resource
	 */
	function flagKeywordForRemoval($iklan_id,$keyword){
		$rs = $this->query("UPDATE sitti_ad_keywords 
							SET keyword_flag = 2,finish_time = NOW(),last_update = NOW(),action_flag = 1 
							WHERE iklan_id='".$iklan_id."' AND keyword='".$keyword."'");
		if($rs){
			$this->onKeywordDeleted($iklan_id, $keyword);
		}
		return $rs;
	}
	/**
	 * 
	 * force rerank when a keyword is deleted.
	 * @param $iklan_id
	 * @param $keyword
	 */
	function onKeywordDeleted($iklan_id,$keyword){
		//delete keyword dari ad tertentu dari imp_hit
    	$sql = "DELETE FROM db_publisher.sitti_imp_hit_100
    			WHERE iklan_id = ".$iklan_id." AND keyword ='".$keyword."'";
    	//force rerank
    	$sql2 = "UPDATE db_publisher.tb_counter AS tb INNER JOIN 
    			db_publisher.sitti_imp_hit_100 AS ih
    			ON ih.keyword = tb.keyword
    			SET tb.jum_imp = tb.jum_imp + ih.maxcount-ih.jum_imp
    			WHERE ih.maxcount > 0 AND ih.jum_imp < ih.maxcount 
    			AND ih.iklan_id = ".$iklan_id." AND ih.keyword = '".$keyword."';
    			"; 
    	
    	$this->query($sql2);
    	$this->query($sql);
	}
	/**
	 *
	 * fungsi ini untuk mengambil detil iklan dari sisi User (advertiser)
	 * @param $id
	 * @param $sittiID
	 */
	function getAdDetail($id,$sittiID){
		$sql = "SELECT * FROM db_web3.sitti_ad_inventory WHERE advertiser_id='".$sittiID."'
    						 AND id='".$id."' LIMIT 1";
		
		$this->open(0);
		$ad = $this->fetch($sql);
		$rs = $this->fetch("SELECT keyword,budget_daily,budget_total,bid FROM sitti_ad_keywords WHERE iklan_id='".$id."'",1);
		$this->close();
		$keywords = "";
		for($j=0;$j<sizeof($rs);$j++){
			if($j!=0){
				$keywords.=", ";
			}
			$keywords .= $rs[$j]['keyword'];
		}
		$ad['keywords'] = $keywords;
		$ad['biddings'] = $rs;
		
		return $ad;
	}
	/**
	 * get ad details with unflagged keywords
	 * @param $id
	 * @param $sittiID
	 */
	function getAdDetailUnflagged($id,$sittiID){
		$sql = "SELECT * FROM db_web3.sitti_ad_inventory WHERE advertiser_id='".$sittiID."'
    						 AND id='".$id."' LIMIT 1";
		
		$this->open(0);
		$ad = $this->fetch($sql);
		$rs = $this->fetch("SELECT keyword,budget_daily,budget_total,bid FROM sitti_ad_keywords 
							WHERE iklan_id='".$id."' AND keyword_flag = 0",1);
		$this->close();
		$keywords = "";
		for($j=0;$j<sizeof($rs);$j++){
			if($j!=0){
				$keywords.=", ";
			}
			$keywords .= $rs[$j]['keyword'];
		}
		$ad['keywords'] = $keywords;
		$ad['biddings'] = $rs;
		
		return $ad;
	}
	function getKeywordsByIklanID($iklan_id,$auto=false){
		if($auto){$this->open(0);}
		$rs = $this->fetch("SELECT keyword,budget_daily,budget_total,bid FROM sitti_ad_keywords WHERE iklan_id='".$iklan_id."'",1);
		if($auto){$this->close();}
		return $rs;
	}
	
	function getUnflaggedKeywords($iklan_id,$auto=false){
		if($auto){$this->open(0);}
		$rs = $this->fetch("SELECT keyword,budget_daily,budget_total,bid 
							FROM sitti_ad_keywords 
							WHERE iklan_id='".$iklan_id."'
							AND keyword_flag = 0",1);
		if($auto){$this->close();}
		return $rs;
	}
	
	/**
	 *
	 * retrieve pending ad detail
	 * @param $id
	 * @param $sittiID
	 */
	function getPendingAdDetail($id,$sittiID){
		$this->open(0);
		$ad = $this->fetch("SELECT * FROM sitti_ad_inventory_temp WHERE advertiser_id='".$sittiID."'
    						 AND id='".$id."' LIMIT 1");
		$rs = $this->fetch("SELECT keyword,budget_daily,budget_total,bid FROM sitti_ad_keywords_temp WHERE iklan_id='".$id."'",1);
		$keywords = "";
		for($j=0;$j<sizeof($rs);$j++){
			if($j!=0){
				$keywords.=", ";
			}
			$keywords .= $rs[$j]['keyword'];
		}
		$ad['biddings'] = $rs;
		$ad['keywords'] = $keywords;
		 
		$this->close();
		return $ad;
	}
	/**
	 * ini untuk mengambil detail iklan dari sisi SITTI Administrator.
	 * @param $id iklan_id
	 */
	function getAdFromInventory($id){
		//$this->open(0);
		$ad = $this->fetch("SELECT * FROM sitti_ad_inventory WHERE id='".$id."' LIMIT 1");
		$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$id."'",1);
		$keywords = "";
		for($j=0;$j<sizeof($rs);$j++){
			if($j!=0){
				$keywords.=", ";
			}
			$keywords .= $rs[$j]['keyword'];
		}
		$ad['keywords'] = $keywords;
		//	$this->close();
		return $ad;
	}
	function getAds($sittiID,$start=0,$total=20){
		$this->open(0);
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory WHERE advertiser_id='".$sittiID."'
    						 ORDER BY id DESC 
    						 LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		$this->close();
		return $list;
	}
	function getAdvertiserAdByCampaignID($campaign_id,$sittiID,$start=0,$total=20){
		$this->open(0);
		 
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory
    						WHERE ox_campaign_id='".$campaign_id."' 
    						AND advertiser_id='".$sittiID."' AND ad_flag = 0
    						 ORDER BY id DESC 
    						 LIMIT ".$start.",".$total,1);
		
		$q = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory
    						WHERE ox_campaign_id='".$campaign_id."' 
    						AND advertiser_id='".$sittiID."' AND ad_flag = 0
    						 LIMIT 1");
		
		$this->found_rows = $q['total'];
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		$this->close();
		 
		return $list;
	}

	function getBannerAdsByCampaignID($campaign_id,$sittiID,$start=0,$total=20)
	{
		$this->open(0);

		$query = "SELECT * FROM db_web3.sitti_banner_inventory
					WHERE ox_campaign_id='". $campaign_id ."' 
					AND advertiser_id='". $sittiID ."'
					ORDER BY id DESC
					LIMIT ".$start.",".$total;
		
		$banners = $this->fetch($query, 1);

		$this->close();

		return $banners;
	}

	function getMaxCPC($keyword){
		global $CONFIG;
    	$this->open(0);
    	/*
    	$rs_cpc = $this->fetch("SELECT keyword, (SELECT A.bid
                          FROM db_web3.sitti_ad_keywords A, db_web3.sitti_ad_keywords B
                          WHERE A.keyword = B.keyword AND A.keyword = c.keyword ORDER BY bid DESC LIMIT 1) AS bids
                          FROM db_web3.sitti_ad_keywords c
                          WHERE (keyword IN ('".$keyword."'))
                          GROUP BY keyword ORDER BY keyword");
        */
    	//$rs_cpc = $this->fetch("SELECT * FROM db_web3.sitti_keywords_max_bid WHERE keyword IN ('".$keyword."')");
    	$rs_cpc = $this->fetch("SELECT keyword,avg_cpc as bids FROM db_web3.sitti_keywords_avg_cpc WHERE keyword IN ('".$keyword."')");
    	
    	//print_r($rs_cpc);
    	$this->close();
    	
		if($rs_cpc['bids']==null||$rs_cpc['bids']==0){
    		$rs_cpc['bids'] = $CONFIG['MINIMUM_BID'];
    	}
    	return $rs_cpc;
    }
    /**
     * 
     * a method to trigger action_flag.  
     * action_flag is used by ad_update_observer.py bot for quick update the ads reporting
     * it also force re-scoring the ads in imp_hit.
     * it updates ad's current status, bids and budgets.
     * @param $iklan_id
     */
    function flagAdForUpdate($iklan_id){
    	$this->open(0);
    	$sql = "UPDATE db_web3.sitti_ad_inventory 
    			SET action_flag = 1 
    			WHERE id=".$iklan_id;
    	$this->query($sql);
    	
    	//force re-scoring di imp_hit
    	$sql = "UPDATE db_publisher.sitti_imp_hit_100 
    			SET flag = 0,last_update = NOW() 
    			WHERE iklan_id=".$iklan_id;
    	$this->query($sql);
    	
    	$this->close();
    }
	function getPendingAdByCampaignID($campaign_id,$sittiID,$start=0,$total=20){
		$this->open(0);
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory
    						WHERE ox_campaign_id='".$campaign_id."' 
    						AND advertiser_id='".$sittiID."' AND ad_flag = 1
    						 ORDER BY id DESC 
    						 LIMIT ".$start.",".$total,1);
		
		$q = $this->fetch("SELECT COUNT(ox_campaign_id) as total FROM sitti_ad_inventory
    						WHERE ox_campaign_id='".$campaign_id."' 
    						AND advertiser_id='".$sittiID."' AND ad_flag = 1
    						 LIMIT 1");
		$this->found_rows = $q['total'];
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		$this->close();
		return $list;
	}
	function getAdsByCampaignID($campaign_id,$start=0,$total=20){
		$this->open(0);
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory WHERE ox_campaign_id='".$campaign_id."'
    						 ORDER BY id DESC 
    						 LIMIT ".$start.",".$total,1);
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		$this->close();
		return $list;
	}
	function getInventory($start=0,$total=50){
		//$this->open(0);
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory
    						 ORDER BY id DESC LIMIT ".$start.",".$total,1);
		$rows = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory
    						 ORDER BY id DESC LIMIT 1");
		$this->found_rows = $rows['total'];
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		//$this->close();
		return $list;
	}
	function getAdFromQueue($iklan_id){
		$rs = $this->fetch("SELECT * FROM sitti_ad_inventory_temp WHERE id='".$iklan_id."' LIMIT 1");
		return $rs;
	}
	function getPendingAdsQueue($start,$total=50){
		$list = $this->fetch("SELECT * FROM sitti_ad_inventory_temp WHERE nflag='1'
    						 ORDER BY id DESC LIMIT ".$start.",".$total,1);
		 
		$rows = $this->fetch("SELECT COUNT(*) as total FROM sitti_ad_inventory_temp WHERE nflag='1'
    						  LIMIT 1");
		$this->found_rows = $rows['total'];
		$n = sizeof($list);
		 
		for($i=0;$i<$n;$i++){
			$rs = $this->fetch("SELECT keyword FROM sitti_ad_keywords_temp WHERE iklan_id='".$list[$i]['id']."'",1);
			$keywords = "";
			for($j=0;$j<sizeof($rs);$j++){
				if($j!=0){
					$keywords.=", ";
				}
				$keywords .= $rs[$j]['keyword'];
			}
			$list[$i]['no'] = $i+1+$start;
			$list[$i]['keywords'] = $keywords;
		}
		 
		return $list;
	}
	function deleteAd($id){
		$id = mysql_escape_string($id);
		//return $this->query("DELETE FROM sitti_ad_inventory WHERE id='".$id."'");
		$sql = "UPDATE db_web3.sitti_ad_inventory 
							 SET ad_flag = 2, finish_time = NOW(), 
							 action_flag = 1 
							 WHERE id=".$id."";
		
		return $this->query($sql);
	}
	/**
	 * the function need to be run after the delete has been deleted. to force re-rank.
	 * @param $id
	 */
	function onAdDeleted($id){
		//delete ad dari imp_hit
    	$sql = "DELETE FROM db_publisher.sitti_imp_hit_100
    			WHERE iklan_id = ".$id;
        //force rerank
    	$sql2 = "UPDATE db_publisher.tb_counter AS tb INNER JOIN 
    			db_publisher.sitti_imp_hit_100 AS ih
    			ON ih.keyword = tb.keyword
    			SET tb.jum_imp = tb.jum_imp + ih.maxcount-ih.jum_imp
    			WHERE ih.maxcount > 0 AND ih.jum_imp < ih.maxcount AND ih.iklan_id = ".$id;
    	$this->query($sql);
    	$this->query($sql2);
	}
	/**
	 * 
	 * persiapkan iklan untuk proses bidding
	 * @param $iklan_id
	 * @param $keywordArr
	 * @param $conn
	 * @param $debug
	 * method ini nanti dipisahkan ke class SITTIBidding
	 */
	function prepareAdsForBidding($iklan_id, $keywordArr, $debug=false) {
	
		//hitung ukuran $keywordArr sekaligus grouping $keywords
		$keywordArr_len = sizeof($keywordArr);
		for ($i = 0; $i < $keywordArr_len; $i++) {
			if ($i > 0) {
				$keywords .= ",";
			}
			$keywords .= "'" . $keywordArr[$i]['keyword'] . "'";
		}

		//STEP#1: insert ke sitti_imp_hit_100, data ad baru (beserta keywordnya)
		$sql1 = "INSERT INTO db_publisher.sitti_imp_hit_100
           (iklan_id, keyword, budget_daily_last, budget_total_last) VALUES ";

		for ($i = 0; $i < $keywordArr_len; $i++) {
			if ($i > 0) {
				$sql1 .= ",";
			}
			$sql1 .= "(" . $iklan_id . ",'" . $keywordArr[$i]['keyword'] . "'," . $keywordArr[$i]['budget_daily'] . "," . $keywordArr[$i]['budget_total'] . ")";
		}
	//	print $sql1."<br/>-------------<br/>";
		if(!$this->query($sql1)){
			return false;
		}

		//STEP#2: insert ke tb_counter, keyword2 baru yang tidak ada di tb_counter
		$sql2 = "SELECT keyword FROM db_publisher.tb_counter
            WHERE keyword IN (" . $keywords . ")";

		//print $sql2."<br/>-------------<br/>";
		$existedKeywords = $this->fetch($sql2,1);
		$existedKeywords_len = sizeof($existedKeywords);
		//print "Existed keywords -->".$existedKeywords_len;
		//print_r($existedKeywords);
		//print"<br/>-------------</br>";
		$new_keywords = array();
		for($i=0;$i<$existedKeywords_len;$i++){
			for($j=0;$j<$keywordArr_len;$j++){
				if($keywordArr[$j]['keyword']==$existedKeywords[$i]['keyword']){
					//print "sama nih!---> ".$keywordArr[$j]['keyword']." dan ".$existedKeywords[$i]['keyword']."<br/>";
					$keywordArr[$j] = null;
					break;
				}
			}
		}
		//if($existedKeywords_len==0){return false;}
		//tandai keywords yang belum ada di tb_counter
		/*
			$notExistedKeywords_len = 0;
			for ($i = 0; $i < $keywordArr_len; $i++) {
				if ($existedKeywords_len > 0) {
					for ($j = 0; $j < $existedKeywords_len; $j++) {
						if ($keywordArr[$i]['keyword'] == trim($existedKeywords[$j]['keyword'])) {
							$keywordArr[$i]['new'] = 1;
							$notExistedKeywords_len++;
						} else {
							$keywordArr[$i]['new'] = 0;
						}
					}
				} else {
					
				}
			} */
	
		//STEP#4: insert ke tb_counter, keyword2 baru yang tidak ada di tb_counter
		//if ($notExistedKeywords_len <> 0) {
			$sql3 = "INSERT INTO db_publisher.tb_counter (jum_imp, keyword) VALUES ";
			$j = 0;
			for ($i = 0; $i < $keywordArr_len; $i++) {
				if($keywordArr[$i]!=null){
					if ($j > 0) {
						$sql3 .= ",";
					}
					
						$sql3 .= "(100,'" . $keywordArr[$i]['keyword'] . "')";
						$j++;
					
				}
			}
			//print $sql3."<br/>-------------<br/>";
			$this->query($sql3);
			
		//}

		//STEP#5: update tabel untuk memaksa rerank
		$sql4 = "UPDATE db_publisher.tb_counter SET jum_imp = 100
				WHERE keyword IN (" . $keywords . ")";
		//print $sql4."<br/>-------------<br/>";
		if (!$this->query($sql4)) {
			return false;
		}

		return true;
	}
	/**
	 * 
	 * mengambil daftar iklan_id yang telah di flag untuk diupdate / dihapus
	 * @param unknown_type $sittiID
	 * @return array
	 */
	function getFlaggedAds($sittiID, $campaign_id){
		$this->open(0);
		if (!$campaign_id){
			$sql = "SELECT id FROM db_web3.sitti_ad_inventory 
							WHERE advertiser_id='".$sittiID."'
							AND action_flag <> 0";
		}else {
			$sql = "SELECT id FROM db_web3.sitti_ad_inventory 
							WHERE advertiser_id='".$sittiID."' AND ox_campaign_id = '".$campaign_id."'
							AND action_flag <> 0";
		}
		
		$rs = $this->fetch($sql,1);
		$this->close();
		return $rs;
	}
	/**
	 * 
	 * mengambil daftar keyword yang telah di flag untuk diupdate / dihapus
	 * @param string $sittiID, string $keyword
	 * @return array
	 */
	function getFlaggedKeywords($sittiID){
		$this->open(0);
		$sql = "SELECT b.keyword AS keyword FROM db_web3.sitti_ad_inventory a
							INNER JOIN db_web3.sitti_ad_keywords b
							ON a.id=b.iklan_id
							WHERE a.advertiser_id='".$sittiID."'
							AND b.action_flag <> 0";
		$rs = $this->fetch($sql,1);
		$this->close();
		return $rs;
	}
	/**
	 * 
	 * retrieve the geotarget locations for these ad.
	 * @param $iklan_id
	 * @return array
	 */
	function getLocation($iklan_id,$autoconnect=true){
		if($autoconnect) $this->open(0);
		$sql = "SELECT kota,priority FROM db_web3.sitti_ad_location 
							WHERE id=".$iklan_id."";
		
		$rs = $this->fetch($sql,1);
		
		if($autoconnect)$this->close();
		return $rs;
	}
	function updateLocation($iklan_id,$city){
		if(sizeof($city)>0){
    	$this->open(0);
    	$sql = "DELETE FROM db_web3.sitti_ad_location WHERE id=".$iklan_id;
    	$this->query($sql);
    	for($i=0;$i<sizeof($city);$i++){
    		$kota = trim($city[$i]);
    		if(strlen($kota)>0&&eregi("([A-Za-z\ ]+)",$kota)){
    		$sql = "INSERT INTO db_web3.sitti_ad_location(id,kota,priority) VALUES(".$iklan_id.",'".$kota."',5)";
    		//print $sql."<br/>";
    			$this->query($sql);
    		}
    	}
    	$this->close();
		}
    }
    function setup_asset_lookup($iklan_id,$fileID,$ext){
    	global $CONFIG;
    	$sql1 = "INSERT INTO db_web3.sitti_banner_asset(banner_id,tipe) VALUES ";
    	$n=0;
	 	for($i=1;$i<=10;$i++){
        	if(file_exists($CONFIG['BANNER_ASSET_PATH'].$fileID."_".$i.$ext)){
        		if($n>0){
        			$sql1.=",";
        		}
        		$sql1.="(".$iklan_id.",".$i.")";
        		$n++;
        	}
	    }
    	//if we have data to add.. then add.. 
    	if($n>0){
    		$this->query($sql1);
    	}
    	//else.. let's assume nothing will happen.
    	
    }

    function add_ad_asset($iklan_id, $banner_type)
    {
    	$query = "INSERT INTO db_web3.sitti_ad_asset(iklan_id, tipe) 
    				VALUES ('$iklan_id', '$banner_type')";
    	
    	return $this->query($query);
    }

    function add_ad_file_asset($iklan_id, $filename)
    {
    	$query =  "INSERT IGNORE INTO db_web3.sitti_ad_file_asset(iklan_id, fileName) 
    				VALUES ('$iklan_id', '$filename')";

    	return $this->query($query);
    }
}
?>
