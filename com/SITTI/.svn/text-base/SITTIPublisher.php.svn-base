<?php 
/**
 * SITTIApp
 * base class untuk semua komponen2 SITTI
 * 
 */
include_once $APP_PATH."StaticPage/StaticPage.php";
include_once "SITTIReporting.php";
include_once "SITTIActionLog.php";

define("PUBLISHER_HOME", "SITTIZEN/beranda.html");
define("DAFTAR_WEBSITE1", "SITTIZEN/daftar_website1.html");
define("SNIPPET_PAGE", "SITTIZEN/daftar_website2.html");
define("DEPLOYMENT_SNIPPET", "SITTIZEN/deployment_snippet1.html");

class SITTIPublisher extends SITTIApp{
    function SITTIPublisher($req,$account){
       parent::SITTIApp($req,$account);
	   $this->ActionLog = new SITTIActionLog();
    }
    function showSummary($total=100){
    
    	global $LOCALE,$ENGINE_PATH,$APP_PATH;
       include_once $ENGINE_PATH."Utility/Paginate.php";

    	//pastikan user sudah terdaftar ke openx
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
      $last_login = $this->Account->publisherLastLoginTime();
    	$this->Account->close();
    	
      $this->View->assign("last_login", $last_login);
    	//dapatkan profile lengkap publisher
    	$sql0 = "SELECT * FROM sitti_publisher_profile WHERE publisher_id=".$profile['id'].";";
    	
		//income kemarin
		$sql1 = "SELECT datee, slotid, SUM(pub_share) AS pub_share FROM db_web3.sitti_deploy_setting sds
		         INNER JOIN db_report_raw.publisher_share2 ps
		         ON sds.id = ps.slotid WHERE sds.sittiID = '".$profile['sittiID']."'
		         AND datee = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)";
		
		//unpaid income
    $sql2 = "SELECT saldo AS unpaid
              FROM db_billing_report.pub_account_history
              WHERE publisher_id = '".$profile['sittiID']."'
              ORDER BY datee DESC, row_id DESC
              LIMIT 1";
		
		//ringkasan akun anda
    $sql3 = "SELECT DATE_FORMAT(ZZ.datee,'%d-%m-%Y') AS tgl, ZZ.publisher_id,
               SUM(ZZ.jum_hit) AS jum_hit, SUM(ZZ.jum_imp) AS jum_imp, SUM(ZZ.jum_hit)*100/SUM(ZZ.jum_imp) AS ctr,
               SUM(ZZ.pub_share) AS pub_share,SUM(ZZ.free_clicks) AS free_clicks,
               SUM(ZZ.paid_clicks) AS paid_clicks, SUM(ZZ.shown_free_clicks) AS shown_free_clicks
              FROM (
              SELECT A.datee, A.publisher_id,
                   jum_hit, jum_imp, ps.pub_share, ps.free_clicks,
                   ps.paid_clicks, ps.shown_free_clicks
                   FROM (
                     SELECT pcg.datee, pcg.publisher_id,
                     pcg.slotid, pcg.jum_hit, pcg.jum_imp, pcg.ctr
                     FROM db_web3.sitti_deploy_setting sds
                     INNER JOIN db_report_raw.pubs_ctr_gen_date pcg
                     ON sds.id = pcg.slotid
                     WHERE sds.sittiID = '".$profile['sittiID']."'
                     AND pcg.datee BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)
                     AND pcg.datee >= '2011-01-01'
                   ) AS A LEFT JOIN db_report_raw.publisher_share2 ps
                   ON A.slotid = ps.slotid AND A.datee = ps.datee
              UNION ALL
              SELECT B.datee, B.publisher_id, C.jum_hit, C.jum_imp, B.pub_revenue AS pub_share, 0 AS free_clicks,
               C.jum_hit AS paid_clicks, C.jum_hit AS shown_free_clicks
              FROM db_report_raw.publisher_banner_revenue B
              INNER JOIN db_report_raw.daily_banner_slot_performance C
              ON B.datee = C.datee AND B.slotid = C.slotid
              WHERE B.publisher_id = '".$profile['sittiID']."'
              AND B.datee BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)

              ) ZZ
              GROUP BY ZZ.datee ORDER BY ZZ.datee DESC";
		
		//jumlah slot aktif
    $sql4 = "SELECT COUNT(DISTINCT A.slotid) AS total
              FROM (
              SELECT pcg.slotid, pcg.publisher_id
              FROM db_report_raw.pubs_ctr_gen_date pcg
              INNER JOIN db_web3.sitti_deploy_setting sds
              ON pcg.publisher_id = sds.sittiID AND pcg.slotid = sds.id
              WHERE pcg.datee = DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND sds.sittiID = '".$profile['sittiID']."'
              AND pcg.datee >= '2011-01-01'

              UNION ALL

              SELECT pcg.slotid, pcg.publisher_id
              FROM db_report_raw.publisher_banner_revenue pcg
              INNER JOIN db_web3.sitti_deploy_setting sds
              ON pcg.publisher_id = sds.sittiID AND pcg.slotid = sds.id
              WHERE pcg.datee = DATE_SUB(CURDATE(),INTERVAL 1 DAY) AND sds.sittiID = '".$profile['sittiID']."'
              AND pcg.datee >= '2011-01-01'


              ) A
              GROUP BY A.publisher_id";
    	
      $this->open(0);
      $profile_lengkap = $this->fetch($sql0);
      $this->View->assign("profile_lengkap",$profile_lengkap);
      $this->close();
      
      $this->open(2);
      $income = $this->fetch($sql1);
      $ringkasan = $this->fetch($sql3,1);
      $n_slot = $this->fetch($sql4);
      $this->close();

      // db billing
      $this->open(4);
      $unpaid = $this->fetch($sql2);
      $this->close();

      $n_rk = count($ringkasan);
      //echo $sql3;exit;
      // simpan date dengan data available di array
      $data_available = array();
      
      if ($n_rk > 0) {
        foreach ($ringkasan as $key => $data) {
          $data_available[substr($data['tgl'], 0, 5)] = $key;
        }  
      }

      $jumlah_hari = 7;
      $ringkasan_akun = array();
      $current_time = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
      
      for($i=0;$i<$jumlah_hari;$i++){
      	  
          if (array_key_exists(date("d-m", $current_time), $data_available)) {
            $data_idx = $data_available[date("d-m", $current_time)];
            $ringkasan[$data_idx]['jum_hit'] = $ringkasan[$data_idx]['paid_clicks']+(min(array($ringkasan[$data_idx]['free_clicks'],floor(0.25*$ringkasan[$data_idx]['paid_clicks']))));
            $ringkasan[$data_idx]['ctr'] = round($ringkasan[$data_idx]['jum_hit']/$ringkasan[$data_idx]['jum_imp']*100,3);
            $ringkasan_akun[$i] = $ringkasan[$data_idx];
          } else {
            $ringkasan_akun[$i] = array("tgl"=>date("d-m-Y",$current_time),"jum_imp"=>"0","jum_hit"=>"0","ctr"=>"0");  
          }

        $current_time -= 86400;
      }

      $this->View->assign("last_income",$income['pub_share']);
      $this->View->assign("unpaid_income",$unpaid['unpaid']);
      $this->View->assign("active_slot",$n_slot['total']);
      $this->View->assign("ringkasan",$ringkasan_akun);
     
      //daftar performance slot iklan
      $report = new SITTIReporting(null);
      $start = $this->Request->getParam("st");
      if($start==null){$start=0;}
       
      $rs = $report->getPublisherSummary($profile['sittiID'], $start,$total);
      $this->View->assign("list",$rs['list']);
    
      //paging
      $page = new Paginate();
      $this->View->assign("PAGING",$page->generate($start,$total,$rs['total_rows'],"beranda.php?"));
      
      //daftar top 10 halaman
      $pages = $report->getPublisherTopPages($profile['sittiID'],10);
       if(sizeof($pages)>0){
      		foreach($pages as $n => $val){
        		$pages[$n]['no'] = $n+1;
      		}
     
     		 $this->View->assign("pages",$pages);
     	} 
     	
     	include_once "SITTINotification.php";
      	$sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
        $notification = new SITTINotification();
      	$data = $notification->dashboardGetAlertsAndNotificationsByPublisherId($sitti_id);
     	$this->View->assign("notif",$data);
      
      include_once $APP_PATH."SITTI/SITTIReferral.php";
      $referral = new SITTIReferral($req, $account);
      $this->View->assign("is_joined_referral", $referral->isAlreadyJoined());
      $this->View->assign("publisher_id", $sitti_id);
      $this->View->assign("host_uri", $_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));
            
      return $this->View->toString(PUBLISHER_HOME); 
    }

     

    /**
     * 
     * Create OpenX Advertiser Account for current User
     * @param array $profile
     */
    function createOXAccount($profile){
    	global $APP_PATH,$OX_CONFIG;
    	//buat campaign id di openx
    	include_once $APP_PATH."kana/SITTI_OX_RPC.php";
    	//SOAP Request Parameters
    	$params['publisherName'] = $profile['sittiID'];
    	$params['website'] = "N/A";
		$params['contactName'] = $profile['name'];
		$params['emailAddress'] = $profile['email'];
		//-->
		$ox = new SITTI_OX_RPC($OX_CONFIG['username'],$OX_CONFIG['password'],$OX_CONFIG['host'],$OX_CONFIG['service'],$OX_CONFIG['debug']);
    	$ox->logon();
    	
    	$ox_pub_id = $ox->registerAsPublisher(&$params);
    	
    	$ox->logout();
    	//die($ox_adv_id);
    	
    	return $ox_pub_id;
    }
    /**
     * 
     * menampilkan form daftar slot iklan
     */
    function FormDaftarWebsite(){
    	
    	return $this->View->toString(DAFTAR_WEBSITE1);
    }
	/**
     * 
     * menampilkan form edit slot
     */
    function EditPage(){
    	$req = $this->Request;
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
    	$this->Account->close();
    	$slotID = $req->getParam("id");
    	
		settype($slotID,"integer");
		
    	$this->open(0);
    	$sql = "SELECT * FROM db_web3.sitti_deploy_setting 
    			WHERE id=".$slotID." 
    			AND sittiID='".$profile['sittiID']."' LIMIT 1";
    	
    	$rs = $this->fetch($sql);
    	$this->close();
    	$this->View->assign("rs",$rs);
		if($rs['sittiID']!=$profile['sittiID']){
			$msg = "Mohon maaf, data slot anda tidak ditemukan.";
			return $this->View->showMessage($msg,"beranda.php");
		}else{
    	return $this->View->toString("SITTIZEN/edit_website1.html");;
		}
    }
    /**
     * 
     * Halaman untuk copy-paste Ad Deployment Snippet.
     */
    function SnippetPage(){
    	global $LOCALE,$CONFIG,$PS_CONFIG;
    	
    	$req = $this->Request;
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
    	$this->Account->close();
    	$slotID = $req->getParam("id");
		settype($slotID,'integer');
    	$this->open(0);
    	$sql = "SELECT * FROM db_web3.sitti_deploy_setting 
    			WHERE id=".$slotID." 
    			AND sittiID='".$profile['sittiID']."' LIMIT 1";
    	
    	$rs = $this->fetch($sql);
    	$this->close();
    	
    	$name = $rs["name"];
    	$ad_type = $rs["ad_type"];
    	$category = $rs["category_id"];
    	$website = $rs["website"];
    	$jenisFont = $rs["jenisFont"];
    	$colorPicker = $rs["color_bg"];
    	$colorPickerBorder = $rs["color_border"];
    	$colorPickerJudul = $rs["color_title"];
    	$colorPickerText = $rs["color_text"];
    	$colorPickerUrl = $rs["color_url"];
    	$slotID = $rs['id'];
    	
    	
    	/** untuk sementara settingan iklan ada disini **/
    	$template_iklan[0] = array("width"=>"300","height"=>"250","slot"=>"3");
    	$template_iklan[1] = array("width"=>"300","height"=>"250","slot"=>"3");
    	$template_iklan[2] = array("width"=>"336","height"=>"280","slot"=>"3");
    	$template_iklan[3] = array("width"=>"728","height"=>"90","slot"=>"3");
    	$template_iklan[4] = array("width"=>"160","height"=>"600","slot"=>"6");
      	$template_iklan[5] = array("width"=>"610","height"=>"60","slot"=>"2");
      	$template_iklan[6] = array("width"=>"300","height"=>"160","slot"=>"2");
      	$template_iklan[7] = array("width"=>"940","height"=>"70","slot"=>"3");
      	$template_iklan[8] = array("width"=>"520","height"=>"70","slot"=>"2");
    	$template_iklan[9] = array("width"=>"468","height"=>"60","slot"=>"2");
    	$template_iklan[10] = array("width"=>"250","height"=>"250","slot"=>"3");
      	$template_iklan[11] = array("width"=>"635","height"=>"100","slot"=>"3");
    	
    	
    	if($rs){
    		//deployment script
    		$arr['ox_pub_id'] = $profile['ox_pub_id'];
    		$arr['sittiID'] = $profile['sittiID'];
    		$arr['dep_id'] = $slotID;
    		$arr['name'] = $name;
    		$arr['width'] = $template_iklan[$ad_type]['width'];
    		$arr['height'] = $template_iklan[$ad_type]['height'];
    		$arr['type'] = $ad_type;
    		$arr['number'] = $template_iklan[$ad_type]['slot'];;
    		$this->View->assign("pub",$arr);
    		$this->View->assign("DELIVERY_URL",$PS_CONFIG['tracker_uri']);
    		$this->View->assign("DEV_MODE",$PS_CONFIG['dev_mode']);
    		$strHTML = $this->View->toString(DEPLOYMENT_SNIPPET);
    		
    		$this->View->assign("DEPLOYMENT_SNIPPET",$strHTML);
    		return $this->View->toString(SNIPPET_PAGE);
    	}else{
    		$msg = $LOCALE['PUB_WEB_SNIPPET_ERROR'];
    		return $this->View->showMessage($msg,"beranda.php");
    	}
    }
    function DeleteSlot(){
    	global $LOCALE,$CONFIG,$PS_CONFIG;
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
    	$this->Account->close();
    	$slotID = intval($this->Request->getParam("id"));
    	$sittiID = mysql_escape_string($profile['sittiID']);
    	if($this->Request->getParam("confirm")=="1"){
    		$sql = "UPDATE db_web3.sitti_deploy_setting SET active_flag = 0 
    				WHERE id=".$slotID." AND sittiID='".$sittiID."'";
    		$this->open(0);
    		$q = $this->query($sql);
    		$this->close();
    		if($q){
				// action log delete publisher slot (213)
				$this->ActionLog->actionLog(213,$profile['sittiID'],$slotID);
    			$msg = "Slot ini telah berhasil dihapus.";
    		}else{
    			$msg = "Maaf, gagal menghapus slot, silahkan coba kembali";
    		}
    		return $this->View->showMessage($msg,"beranda.php");
    	}else{
    		$msg = "Penghapusan slot akan membuat semua data pada slot ini ikut terhapus. Anda yakin ?";
    		$onYes = array("label"=>"Ya","url"=>"beranda.php?delete=1&id=".$slotID."&confirm=1");
    		$onNo = array("label"=>"Tidak","url"=>"beranda.php");
    		return $this->View->confirm($msg,$onYes,$onNo);
    	}
    	print $this->Request->getParam("id");
    }
    
    function SaveSlot(){
    	global $LOCALE,$CONFIG,$PS_CONFIG;
    	
    	$req = $this->Request;
    	$name = $req->getPost("name");
    	$ad_type = $req->getPost("ad_type");
    	$category = $req->getPost("category");
    	$website = $req->getPost("url");
    	$jenisFont = $req->getPost("jenisFont");
    	$colorPicker = $req->getPost("colorPicker");
    	$colorPickerBorder = $req->getPost("colorPickerBorder");
    	$colorPickerJudul = $req->getPost("colorPickerJudul");
    	$colorPickerText = $req->getPost("colorPickerText");
    	$colorPickerUrl = $req->getPost("colorPickerUrl");
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
    	$this->Account->close();
    	
    	//save deployment settings
    	$sql= "INSERT INTO sitti_deploy_setting(sittiID,ox_publisher_id,name,ad_type,created_date,category_id,website,
    											jenisFont,color_bg,color_border,color_title,color_text,color_url)
    	       VALUES('".$profile['sittiID']."','".$profile['ox_pub_id']."','".$name."','".$ad_type."',NOW(),'".$category."','".$website."',
    	       		   '".$jenisFont."','".$colorPicker."','".$colorPickerBorder."','".$colorPickerJudul."','".$colorPickerText."','".$colorPickerUrl."')";
    	
    
    	//-->
    	$this->open(0);
    	$q = $this->query($sql);
    	$deployment_id = mysql_insert_id();
    	$this->close();
    	
    	//-->update reporting db
    	$this->open(2);
    	$sql = "INSERT INTO db_report.tbl_performa_slot_total(publisher_id,slotid,jum_imp,jum_hit,ctr,last_update)
				VALUES('".$profile['sittiID']."',".$deployment_id.",0,0,0,NOW())";
    	$this->query($sql);
    	$this->close();
    	if($q){
			// action log create publisher slot (211)
			$this->ActionLog->actionLog(211,$profile['sittiID'],$deployment_id);
    		$msg = $LOCALE['PUB_WEB_REG_SUCCESS'];
    		return $this->View->showMessage($msg,"beranda.php?snippet=1&id=".$deployment_id);
    	}else{
    		$this->View->assign("msg",$LOCALE['PUB_WEB_REG_ERROR']);
    		return $this->View->toString(DAFTAR_WEBSITE1);
    	}
    }
	function UpdateSlot(){
    	global $LOCALE,$CONFIG,$PS_CONFIG;
    	
    	$req = $this->Request;
    	$slotID = $req->getPost("id");
    	
    	settype($slotID,'integer');
    	
    	$name = $req->getPost("name");
    	$ad_type = $req->getPost("ad_type");
    	$category = $req->getPost("category");
    	$website = $req->getPost("url");
    	$jenisFont = $req->getPost("jenisFont");
    	$colorPicker = $req->getPost("colorPicker");
    	$colorPickerBorder = $req->getPost("colorPickerBorder");
    	$colorPickerJudul = $req->getPost("colorPickerJudul");
    	$colorPickerText = $req->getPost("colorPickerText");
    	$colorPickerUrl = $req->getPost("colorPickerUrl");
    	
    	//user profile
    	$this->Account->open(0);
    	$profile = $this->Account->getPublisherProfile();
    	$this->Account->close();
    	
    	//save deployment settings
    	$sql= "UPDATE sitti_deploy_setting 
    			SET name = '".$name."',ad_type='".$ad_type."',category_id='".$category."',website='".$website."',
    		 jenisFont='".$jenisFont."',color_bg='".$colorPicker."',color_border='".$colorPickerBorder."',
    		 color_title='".$colorPickerJudul."',color_text='".$colorPickerText."',color_url='".$colorPickerUrl."'
    		 WHERE id=".$slotID." AND sittiID='".$profile['sittiID']."'";
    	
    
    	//-->
    	$this->open(0);
    	$q = $this->query($sql);
    	$this->close();
    	
    	if($q){
			// action log edit publisher slot (212)
			$this->ActionLog->actionLog(212,$profile['sittiID'],$slotID);
    		$msg = $LOCALE['PUB_WEB_UPD_SUCCESS'];
    		return $this->View->showMessage($msg,"beranda.php?snippet=1&id=".$slotID);
    	}else{
    		$this->View->assign("msg",$LOCALE['PUB_WEB_UPD_ERROR']);
    		return $this->View->showMessage($msg,"beranda.php?edit=1&id=".$slotID);
    	}
    }
    function getPublishers($start,$total=50){
    	$list = $this->fetch("SELECT * FROM sitti_account_publisher WHERE status='1' ORDER BY id LIMIT ".$start.",".$total,1);
    	$q = $this->fetch("SELECT COUNT(*) as total FROM sitti_account_publisher WHERE status='1' LIMIT 1");
    	$rs['list'] = $list;
    	$rs['total_rows'] = $q['total'];
    	return $rs;
    }
    function getPendingPublishers($start,$total=50){
    	$list = $this->fetch("SELECT * FROM sitti_account_publisher WHERE status='0' ORDER BY id LIMIT ".$start.",".$total,1);
    	$q = $this->fetch("SELECT COUNT(*) as total FROM sitti_account_publisher WHERE status='0' LIMIT 1");
    	$rs['list'] = $list;
    	$rs['total_rows'] = $q['total'];
    	return $rs;
    }
    function approve($id){
    	return $this->query("UPDATE sitti_account_publisher SET status='1' WHERE id='".$id."'");
    }
    function reject($id){
    	return $this->query("DELETE FROM sitti_account_publisher WHERE id='".$id."'");
    }
    function getPublisherProfileByID($id){
    	$rs = $this->fetch("SELECT * FROM sitti_account_publisher
    							WHERE id='".mysql_escape_string($id)."'
    							LIMIT 1");
    		
    	return $rs;
    }
    function Notifikasi(){
    	include_once "SITTINotification.php";
      	$sitti_id = (bool) $sitti_id ? $sitti_id : $_SESSION['sittiID'];
      	$notification = new SITTINotification();
      	$data=$notification->getAlertsAndNotificationsByPublisherId($sitti_id,$this->Request->getParam('page'));
      	$this->View->assign('prev',$data['prev']);
      	$this->View->assign('next',$data['next']);
      	$this->View->assign('list',$data['list']);
      	return $this->View->toString("SITTI/publisher/daftar_notifikasi.html");
    }
}
?>
