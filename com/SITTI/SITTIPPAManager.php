<?php

class SITTIPPAManager extends SQLData
{
	
  private $request;
	private $view;
	private $account;
	
  function SITTIPPAManager($req,$account)
	{
		$this->account = $account;
		$this->request = $req;
		$this->view = new BasicView();
	}
 
  function ppaHandler()
  {
    if ($this->request->getPost('next') == '2')
    {
      global $CONFIG;
      
      $main_key = array_keys(array_flip($_POST['keywords']));
      $n_len = sizeof($main_key);
      
      for ($i=0;$i<$n_len;$i++) {
        $list[$i]['no'] = $i+1;
        $list[$i]['list'][0] = strip_tags($main_key[$i]);

        $count_suggestions = 10;
        $this->open(0);
        $test_query = new SITTITestQuery();
        $query_suggestions = $test_query->getSuggestion2("'".$list[$i]['list'][0]."'",0,$count_suggestions);
        $word_suggestions = $this->fetch($query_suggestions,1);
        $this->close();
        for ($j=1; $j<=$count_suggestions; $j++) {
          if(strlen($word_suggestions[$j]['kata2'])>1){
            $list[$i]['list'][$j] = $word_suggestions[$j]['kata2'];
          }  
        }

      }

      $this->view->assign("main_key",$list);
      $this->view->assign("hari",$this->request->getPost('hari'));
      $this->view->assign("n_slots",$n_len);
      $this->view->assign("MINIMUM_BID",$CONFIG['MINIMUM_BID']);

      $add_ppa_data['interval'] = $this->request->getPost('hari');
      
      $_SESSION['add_ppa_data'] = serialize($add_ppa_data);

      return $this->view->toString("SITTI/ads/create_ppa_simulasi_keyword.html");
    }
    elseif ($this->request->getPost('next') == '3')
    {
      return $this->createIklanPage();
    }
    elseif ($this->request->getPost('next') == '4')
    {
      return $this->showConfirmPage();
    }
    elseif ($this->request->getPost('next') == '5' && $this->request->getPost('simpan_ppa'))
    {
      $msg = $this->simpanPPA();
      return $this->view->showMessage($msg,"beranda.php");
    }
    else
    {
      if ($this->request->getPost('simpan_konfigurasi_konversi'))
      {
        $advertiser_id = $_SESSION['sittiID'];
        $jenis_situs = $this->request->getPost('jenis_situs');
        $url_situs = $this->request->getPost('alamaturl');
        $url_checkout = '';//$this->request->getPost('checkout_url');
        $url_payment = '';//$this->request->getPost('payment_url');

        $this->open(0);
        $query = "INSERT INTO sitti_ppa_profile (advertiser_id, jenis_site, url_site, url_checkout, url_success) 
                    VALUES ('". mysql_real_escape_string($advertiser_id) ."',
                             '". mysql_real_escape_string($jenis_situs) ."', 
                             '". mysql_real_escape_string($url_situs) ."', 
                             '". mysql_real_escape_string($url_checkout) ."', 
                             '". mysql_real_escape_string($url_payment) ."')";

        $this->query($query);
        $this->close();
      }

      //$this->view->assign('current_date', date('Y-m-d'));
       $this->view->assign('per_page', 50);
       return $this->view->toString("SITTI/ads/create_ppa_pilih_keyword.html");
    }
  }

  function createIklanPage($message = false)
  {
    global $LOCALE;
    if ( ! $message)
    {
      $add_ppa_data = unserialize($_SESSION['add_ppa_data']);
        
      $jumlah_keywords = $this->request->getPost('slots');
      $keywords = array();
      for ($i = 0; $i < $jumlah_keywords; $i++)
      {
        $keywords[$i]['keyword'] = $this->request->getPost('daily_' . $i);
        $keywords[$i]['cpc'] = $this->request->getPost('cpc_' . $i);
        $keywords[$i]['budget'] = $this->request->getPost('budget_' . $i);
      }
      $add_ppa_data['keywords'] = $keywords;

      $_SESSION['add_ppa_data'] = serialize($add_ppa_data);      
    }
    else
    {
      if (is_array($_SESSION['temp']))
      {
        $this->view->assign("temp_data",$_SESSION['temp']);
        unset($_SESSION['temp']);
      }
      $this->view->assign("msg",$message);
    }

    $advertiser_id = $_SESSION['sittiID'];
      
    $campaign = new SITTICampaign();
    $campaign_list = $campaign->getCampaignList($advertiser_id,0,30);
    if(count($campaign_list)==0){
      //kalau belum ada campaign, paksa user utk buat campaign baru dulu.
      return $this->view->showMessageError($LOCALE['USER_HAVE_NO_CAMPAIGN'],"beranda.php");
    }

    $inventory = new SITTIInventory();
    $adCategory = $inventory->getAdCategory();
    $adGenre = $inventory->getAdGenre();

    $this->view->assign("adCategory",$adCategory);
    $this->view->assign("adGenre",$adGenre);
    $this->view->assign("campaign",$campaign_list);

    return $this->view->toString("SITTI/ads/create_ppa_step_create_iklan.html");
  }

  function showConfirmPage()
  {
    if ($this->createIklanPageValidation() == false)
    {
      return $this->createIklanPage('Semua kolom harus diisi');
    }
    else
    {
      $add_ppa_data = unserialize($_SESSION['add_ppa_data']);
        
      $add_ppa_data['campaign'] = $this->request->getPost('campaign');
      
      $add_ppa_data['nama_iklan'] = $this->request->getPost('nama');
      $add_ppa_data['judul_iklan'] = $this->request->getPost('judul');
      $add_ppa_data['isi_iklan'] = $this->request->getPost('baris1');

      $add_ppa_data['url_produk'] = $this->request->getPost('url_produk');
      //$add_ppa_data['url_ditampilkan'] = $this->request->getPost('url_ditampilkan');
      //$add_ppa_data['url_sebenarnya'] = $this->request->getPost('url_sebenarnya');

      $add_ppa_data['tipe_konversi'] = $this->request->getPost('tipe_konversi');
      $add_ppa_data['harga_produk'] = 0;
      $add_ppa_data['komisi_produk'] = 0;
      $add_ppa_data['nilai_konversi_produk'] = 0;

      if ($add_ppa_data['tipe_konversi'] == 'persentase')
      {
        $add_ppa_data['harga_produk'] = $this->request->getPost('harga_produk');
        $add_ppa_data['komisi_produk'] = $this->request->getPost('komisi_produk');
      }

      if ($add_ppa_data['tipe_konversi'] == 'rupiah')
      {
        $add_ppa_data['nilai_konversi_produk'] = $this->request->getPost('nilai_konversi');  
      }

      $add_ppa_data['cities'] = $_POST['tcity'];

      $_SESSION['add_ppa_data'] = serialize($add_ppa_data);

      $this->view->assign('nama_produk', $add_ppa_data['nama_iklan']);
      $this->view->assign('url_produk', $add_ppa_data['url_produk']);
      $this->view->assign('judul_iklan', $add_ppa_data['judul_iklan']);
      $this->view->assign('isi_iklan', $add_ppa_data['isi_iklan']);

      return $this->view->toString("SITTI/ads/create_ppa_step4.html");
    }
  }

  function createIklanPageValidation()
  {
    $return_value = true;

    $_SESSION['temp']['campaign'] = $this->request->getPost('campaign');
    $_SESSION['temp']['cities'] = $_POST['tcity'];
    
    $_SESSION['temp']['nama_iklan'] = $nama_iklan = htmlentities(trim($this->request->getPost('nama')));
    $_SESSION['temp']['judul_iklan'] = $judul_iklan = htmlentities(trim($this->request->getPost('judul')));
    $_SESSION['temp']['isi_iklan'] = $isi_iklan = htmlentities(trim($this->request->getPost('baris1')));
    $_SESSION['temp']['url_produk'] = $url_produk = htmlentities(trim($this->request->getPost('url_produk')));

    if (strlen($nama_iklan) == 0 
        || strlen($judul_iklan) == 0 
        || strlen($isi_iklan) == 0 
        || (strlen($url_produk) == 0 || $url_produk == 'http://')
      )
    {
        $return_value = false;
    }

    $tipe_konversi = $this->request->getPost('tipe_konversi');
    $_SESSION['temp']['tipe_konversi'] = $tipe_konversi;
    if ($tipe_konversi == 'persentase')
    {
      //$_SESSION['temp']['harga_produk'] = $harga_produk = htmlentities(trim($this->request->getPost('harga_produk')));
      $_SESSION['temp']['komisi_produk'] = $komisi_produk = htmlentities(trim($this->request->getPost('komisi_produk')));

      if (/*strlen($harga_produk) == 0 || */strlen($komisi_produk) == 0)
      {
        $return_value = false;
      }
    }
    elseif ($tipe_konversi == 'rupiah')
    {
      $_SESSION['temp']['nilai_konversi_produk'] = $nilai_konversi_produk = htmlentities(trim($this->request->getPost('nilai_konversi')));
      if (strlen($nilai_konversi_produk) == 0)
      {
        $return_value = false;
      }
    }

    return $return_value;
  }

  function simpanPPA()
  {
    global $CONFIG;
    $add_ppa_data = unserialize($_SESSION['add_ppa_data']);
    
    $advertiser_id = $_SESSION['sittiID'];
    
    $tgl_awal = date('Y-m-d', strtotime('now'));
    $tgl_akhir = date('Y-m-d', strtotime('+' . $add_ppa_data['interval'] . ' day'));
    $interval = intval($add_ppa_data['interval']);
    $cities = $add_ppa_data['cities'];
    $keywords = $add_ppa_data['keywords'];

    // tambah informasi bid untuk setiap keyword
    $keyword_model = new KeywordModel();
    foreach ($keywords as $idx => $keyword)
    {
      $keywords[$idx]['bid'] = $keyword_model->getPPABidValue($keyword['keyword']);
    }

    $campaign_id = $add_ppa_data['campaign'];

    $harga_produk = intval($add_ppa_data['harga_produk']);
    $persen_komisi = floatval($add_ppa_data['komisi_produk']);
    $rupiah_komisi = intval($add_ppa_data['nilai_konversi_produk']);
    
    $inventory = new SITTIInventory();
    $inventory->open();

    $nama_iklan = mysql_real_escape_string($add_ppa_data['nama_iklan']);
    $judul_iklan = mysql_real_escape_string($add_ppa_data['judul_iklan']);
    $isi_iklan = mysql_real_escape_string($add_ppa_data['isi_iklan']);
    $url_iklan = mysql_real_escape_string($add_ppa_data['url_produk']);

    $inventory->createPPAAd($advertiser_id,$nama_iklan,$judul_iklan,$isi_iklan,$url_iklan,$campaign_id);
    $iklan_id = $inventory->last_insert_id;
    
    $inventory->insertKomisiPPA($iklan_id, $harga_produk, $persen_komisi, $rupiah_komisi);

    $locations = array();
    if ( ! is_array($cities))
    {
      $locations = array(array("kota"=>"ALL","priority"=>"0"));  
    }
    else
    {
      for($idx = 0; $idx < sizeof($cities); $idx++)
      {
        array_push($locations,array("kota"=>$cities[$idx],"priority"=>"5"));
      }
    }
    $inventory->addLocation($iklan_id,$locations);

    foreach ($keywords as $keyword)
    {
      $word = $keyword['keyword'];
      //$bid = 500; // instruksi dari mr. ridwan
      $bid = $keyword['bid'] == 0 ? 600 : $keyword['bid'];
      //$budget = intval($keyword['budget']);
      $budget = 1000000; // instruksi dari mr. ridwan
      //$budget_total = $interval * $budget;
      $budget_total = 1000000; // instruksi dari mr. ridwan

      $inventory->addKeyword($iklan_id,$word,$bid,$budget,$budget_total);
    }

    $inventory->close();
    $this->open(2);
    $query_insert_reporting = "INSERT INTO db_report.tbl_performa_iklan_total
          (advertiser_id, id_iklan, nama_iklan, keywords, STATUS,
          jum_imp, jum_klik, ctr, harga, 
          budget_harian, budget_total, last_update)
          VALUES ('".$advertiser_id."',".$iklan_id.",'".$nama_iklan."','',0,
          0,0,'0.000','0','0','0',NOW())";

    $this->query($query_insert_reporting);

    /* Insert data dummy ke PPA report tables */
    // Insert ke tabel db_report.ppa_campaign
    $query_insert_ppa_campaign = "INSERT INTO db_report.ppa_campaign 
        (advertiser_id, campaign_id, jum_konversi, harga_produk, nilai_komisi, nilai_konversi, tglisidata)
        VALUES
        ('".$advertiser_id."', '".$campaign_id."','0', '0', '0', '0', NOW())";
    $this->query($query_insert_ppa_campaign);

    // Insert ke tabel db_report.ppa_ad
    $query_insert_ppa_ad = "INSERT INTO db_report.ppa_ad 
        (advertiser_id, iklan_id, jum_konversi, harga_produk, nilai_komisi, nilai_konversi, tglisidata)
        VALUES
        ('".$advertiser_id."', '".$iklan_id."','0', '0', '0', '0', NOW())";
    $this->query($query_insert_ppa_ad);

    // Insert ke tabel db_report.ppa_ad_transaction
    $query_insert_ppa_ad = "INSERT INTO db_report.ppa_ad_transaction 
        (advertiser_id, iklan_id, hittime, conversiontime, session_id, transaction_id, ipaddress, lokasi, harga_produk, nilai_komisi, nilai_konversi, datee)
        VALUES
        ('".$advertiser_id."', '".$iklan_id."',NOW(), NOW(), '0', '0', '0', '0', '0', '0', '0', '". date("Y-m-d") ."')";
    $this->query($query_insert_ppa_ad);
   
    // Insert ke tabel db_report.daily_ppa_ad
     $query_insert_daily_ppa_ad = "INSERT INTO db_report.daily_ppa_ad 
        (datee, advertiser_id, iklan_id, jum_konversi, harga_produk, nilai_komisi, nilai_konversi, tglisidata)
        VALUES
        ('". date("Y-m-d") ."', '".$advertiser_id."', '".$iklan_id."','0', '0', '0', '0', NOW())";
    $this->query($query_insert_daily_ppa_ad);

    $this->close();

    $msg = "Iklan PPA berhasil disimpan.";
    return $msg;
  }

  function is_valid_url($url)
  {
      $url = @parse_url($url);
   
      if (!$url)
      {
          return false;
      }
   
      $url = array_map('trim', $url);
      $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
      $path = (isset($url['path'])) ? $url['path'] : '';
   
      if ($path == '')
      {
          $path = '/';
      }
   
      $path .= (isset($url['query'])) ? "?$url[query]" : '';
   
      if (isset($url['host']) AND $url['host'] != gethostbyname($url['host']))
      {
          if (PHP_VERSION >= 5)
          {
              $headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
          }
          else
          {
              $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
   
              if (!$fp)
              {
                  return false;
              }
              fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
              $headers = fread($fp, 4096);
              fclose($fp);
          }
          $headers = (is_array($headers)) ? implode("\n", $headers) : $headers;
          return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
      }
      return false;
  }

  function saveProfile()
  {
    $data['jenis_site'] = $this->request->getPost('jenis_situs');
    $data['url_site'] = $this->request->getPost('alamaturl');

    //var_dump($data);

    $ppa_model = new SITTIPPAModel();
    return $ppa_model->updateProfile($data);
  }

}

?>
