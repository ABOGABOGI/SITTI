<?php
/**
 * SITTINotification
 */
class SITTINotification extends SQLData{
	var $sitti_id='';
	function SITTINotification(){
		parent::SQLData();
	}
	function add(){
	}
	function remove(){
	}
	function getNotificationsByAdvertiserId($sitti_id='',$type=0,$status=0){		
	}
	function getAlertsByAdvertiserId($sitti_id='',$read=0){
	}	
	function getAlertsAndNotificationsByAdvertiserId($sitti_id='',$page=1){
		
		$sql = "SELECT 
				count(*) total 
				FROM 
				(
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_alert WHERE n_status=1 && advertiser_id='$sitti_id')
					UNION 
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_notification WHERE n_status=1 && is_read=0 && advertiser_id='$sitti_id')
				) t
				ORDER BY tgl DESC;";
		$this->open(2);
		$total=$this->fetch($sql);
		$total=$total['total'];
		
		$pagex = (intval($page) == 0) ? 0 : intval($page) - 1;
		$start = $pagex * 4;
		$prev = intval($page) - 1; 
		$next = (intval($page)==0)? 2 : intval($page) + 1;
		
		//echo $total.'<hr />'.$next;exit;
		
		if( $total <= (($next-1)*4) ){
			$next = 0;
		}
		
		$sql = "SELECT 
				tgl 
				FROM 
				(
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_alert WHERE n_status=1 && advertiser_id='$sitti_id')
					UNION 
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_notification WHERE n_status=1 && is_read=0 && advertiser_id='$sitti_id')
				) t
				ORDER BY tgl DESC
				LIMIT $start,4;";
		$tgl=$this->fetch($sql,1);
		
		//echo $sql;
		//print_r($tgl);exit;
		
		$numdata = count($tgl);
		$start_date = $tgl[0]['tgl'];
		$end_date = $tgl[$numdata-1]['tgl'];
		
		$sql = "SELECT * FROM
				((SELECT 
					n.id,
					n.advertiser_id,
					DATE(n.posted_date) AS posted_date,
					n.message,
					n.is_read,
					n.n_status,
					0 AS iklan_id,
					-1 AS type_alert,
					0 AS total,
					n.url AS url,
					'' AS keyword
				FROM
					db_web3.sitti_notification n
				WHERE
					n.n_status=1 &&
					n.is_read=0 &&
					n.advertiser_id='$sitti_id' &&
					(DATE(n.posted_date) <= '$start_date' && DATE(n.posted_date) >= '$end_date'))
				UNION
				(SELECT 
					a.id,
					a.advertiser_id,
					DATE(a.posted_date) AS posted_date,
					'' AS message,
					0 AS is_read,
					a.n_status,
					a.iklan_id,
					a.type_alert,
					COUNT(*) AS total,
					'' AS url,
					a.keyword 
				FROM
					db_web3.sitti_alert a
				WHERE
					a.n_status=1 &&
					a.advertiser_id='$sitti_id' &&
					(DATE(a.posted_date) <= '$start_date' && DATE(a.posted_date) >= '$end_date')
				GROUP BY
					DATE(a.posted_date),
					a.type_alert)) a
				ORDER BY posted_date DESC;";
		//echo $sql;exit;		
		$r=$this->fetch($sql,1);
		
		global $LOCALE;
		$num=count($r);
		for($i=0;$i<$num;$i++){
			$r[$i]['alert_msg'] = $LOCALE["ALERT_".$r[$i]['type_alert']];
			
			//cari list keyword yang alert
			if($r[$i]['type_alert'] > 0){
				$qkey = "SELECT 
							av.nama, a.keyword, av.id
						FROM
							db_web3.sitti_alert a
							LEFT JOIN db_web3.sitti_ad_inventory av
							ON a.iklan_id=av.id
						WHERE
							a.n_status=1 &&
							a.advertiser_id='$sitti_id' &&
							a.type_alert='".$r[$i]['type_alert']."' &&
							DATE(a.posted_date) = '".$r[$i]['posted_date']."';";
				//echo $qkey;exit;
				$rkey=$this->fetch($qkey,1);
				$r[$i]['list_iklan'] = $rkey;
			}
		}
		$this->close();
		
		//print_r($r);
		//exit;
		
		return array("list"=>$r,"prev"=>$prev,"next"=>$next);
	}
	
	function dashboardGetAlertsAndNotificationsByAdvertiserId($sitti_id=''){
		
		$current_date = date('Y-m-d');
		
		$sql = "SELECT * FROM
				((SELECT 
					n.id,
					n.advertiser_id,
					DATE(n.posted_date) AS posted_date,
					n.message,
					n.is_read,
					n.n_status,
					0 AS iklan_id,
					-1 AS type_alert,
					0 AS total, 
					n.url AS url
				FROM
					db_web3.sitti_notification n
				WHERE
					n.n_status=1 &&
					n.is_read=0 &&
					n.advertiser_id='$sitti_id' &&
					DATE(n.posted_date) = '$current_date')
				UNION
				(SELECT 
					a.id,
					a.advertiser_id,
					DATE(a.posted_date) AS posted_date,
					'' AS message,
					0 AS is_read,
					a.n_status,
					a.iklan_id,
					a.type_alert,
					COUNT(*) AS total,
					'' AS url
				FROM
					db_web3.sitti_alert a
				WHERE
					a.n_status=1 &&
					a.advertiser_id='$sitti_id' &&
					DATE(a.posted_date) = '$current_date'
				GROUP BY
					DATE(a.posted_date),
					a.type_alert)) a
				ORDER BY posted_date DESC;";
		
		$this->open(2);
		$r=$this->fetch($sql,1);
		
		global $LOCALE;
		$num=count($r);
		for($i=0;$i<$num;$i++){
			$r[$i]['alert_msg'] = $LOCALE["ALERT_".$r[$i]['type_alert']];
			
			//cari list keyword yang alert
			if($r[$i]['type_alert'] > 0){
				$qkey = "SELECT 
							av.nama, a.keyword, av.id
						FROM
							db_web3.sitti_alert a
							LEFT JOIN db_web3.sitti_ad_inventory av
							ON a.iklan_id=av.id
						WHERE
							a.n_status=1 &&
							a.advertiser_id='$sitti_id' &&
							a.type_alert='".$r[$i]['type_alert']."' &&
							DATE(a.posted_date) = '".$r[$i]['posted_date']."';";
				//echo $qkey;exit;
				$rkey=$this->fetch($qkey,1);
				$r[$i]['list_iklan'] = $rkey;
			}
		}
		$this->close();
		
		return $r;
	}
	
	function getAlertsAndNotificationsByPublisherId($sitti_id='',$page=1){
		
		$sql = "SELECT 
				count(*) total 
				FROM 
				(
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_pub_alert WHERE n_status=1 && publisher_id='$sitti_id')
					UNION 
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_pub_notification WHERE n_status=1 && is_read=0 && publisher_id='$sitti_id')
				) t
				ORDER BY tgl DESC;";
		$this->open(2);
		$total=$this->fetch($sql);
		$total=$total['total'];
		//echo $sql;
		//echo $total;exit;
		$pagex = (intval($page) == 0) ? 0 : intval($page) - 1;
		$start = $pagex * 4;
		$prev = intval($page) - 1; 
		$next = (intval($page)==0)? 2 : intval($page) + 1;
		
		//echo $total.'<hr />'.$next;exit;
		
		if( $total <= (($next-1)*4) ){
			$next = 0;
		}
		
		$sql = "SELECT 
				tgl 
				FROM 
				(
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_pub_alert WHERE n_status=1 && publisher_id='$sitti_id')
					UNION 
					(SELECT DATE(posted_date) AS tgl FROM db_web3.sitti_pub_notification WHERE n_status=1 && is_read=0 && publisher_id='$sitti_id')
				) t
				ORDER BY tgl DESC
				LIMIT $start,4;";
		$tgl=$this->fetch($sql,1);
		
		//echo $sql;
		//print_r($tgl);exit;
		
		$numdata = count($tgl);
		$start_date = $tgl[0]['tgl'];
		$end_date = $tgl[$numdata-1]['tgl'];
		
		$sql = "SELECT * FROM
				((SELECT 
					n.id,
					n.publisher_id,
					DATE(n.posted_date) AS posted_date,
					n.message,
					n.is_read,
					n.n_status,
					0 AS iklan_id,
					-1 AS type_alert,
					0 AS total,
					n.url AS url
				FROM
					db_web3.sitti_pub_notification n
				WHERE
					n.n_status=1 &&
					n.is_read=0 &&
					n.publisher_id='$sitti_id' &&
					(DATE(n.posted_date) <= '$start_date' && DATE(n.posted_date) >= '$end_date'))
				UNION
				(SELECT 
					a.id,
					a.publisher_id,
					DATE(a.posted_date) AS posted_date,
					'' AS message,
					0 AS is_read,
					a.n_status,
					a.iklan_id,
					a.type_alert,
					COUNT(*) AS total,
					'' AS url
				FROM
					db_web3.sitti_pub_alert a
				WHERE
					a.n_status=1 &&
					a.publisher_id='$sitti_id' &&
					(DATE(a.posted_date) <= '$start_date' && DATE(a.posted_date) >= '$end_date')
				GROUP BY
					DATE(a.posted_date),
					a.type_alert)) a
				ORDER BY posted_date DESC;";
		//echo $sql;exit;		
		$r=$this->fetch($sql,1);
		$this->close();
		
		global $LOCALE;
		$num=count($r);
		for($i=0;$i<$num;$i++){
			$r[$i]['alert_msg'] = $LOCALE["ALERT_PUB_".$r[$i]['type_alert']];
		}
		return array("list"=>$r,"prev"=>$prev,"next"=>$next);
		//print_r($r);
		//exit;
	}
	
	function dashboardGetAlertsAndNotificationsByPublisherId($sitti_id=''){
		
		$current_date = date('Y-m-d');
		$yesterday = date("Y-m-d", strtotime("yesterday"));
		
		$sql = "SELECT * FROM
				((SELECT 
					n.id,
					n.publisher_id,
					DATE(n.posted_date) AS posted_date,
					n.message,
					n.is_read,
					n.n_status,
					0 AS iklan_id,
					-1 AS type_alert,
					0 AS total,
					n.url AS url
				FROM
					db_web3.sitti_pub_notification n
				WHERE
					n.n_status=1 &&
					n.is_read=0 &&
					n.publisher_id='$sitti_id' &&
					(DATE(n.posted_date) = '$current_date' OR DATE(n.posted_date) = '$yesterday'))
				UNION
				(SELECT 
					a.id,
					a.publisher_id,
					DATE(a.posted_date) AS posted_date,
					'' AS message,
					0 AS is_read,
					a.n_status,
					a.iklan_id,
					a.type_alert,
					COUNT(*) AS total,
					'' AS url
				FROM
					db_web3.sitti_pub_alert a
				WHERE
					a.n_status=1 &&
					a.publisher_id='$sitti_id' &&
					(DATE(a.posted_date) = '$current_date' OR DATE(a.posted_date) = '$yesterday')
				GROUP BY
					DATE(a.posted_date),
					a.type_alert)) a
				ORDER BY posted_date DESC;";
		
		$this->open(2);
		$r=$this->fetch($sql,1);
		$this->close();
		global $LOCALE;
		$num=count($r);
		for($i=0;$i<$num;$i++){
			$r[$i]['alert_msg'] = $LOCALE["ALERT_PUB_".$r[$i]['type_alert']];
		}
		return $r;
	}
}
