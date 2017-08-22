<?php
/**
 * showads.php
 * ad deployment script
 * 
 */
/**
 * Includes
 */
include_once "../com/kana/SITTI_OX_RPC.php";
/**
 * Database config
 */
$host = "202.52.131.12";
$username = "juragansitti";
$password = "cotaxEdonatagosE";
$database = "db_words";


//tracker configuration
$PS_CONFIG['tracker_uri'] = "http://202.52.131.6";
//The codes ---> 

//User IP Address
if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
{
  $ip=$_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
{
  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}else
{
  $ip=$_SERVER['REMOTE_ADDR'];
}
 //-->

// WEB REFERER / URL Halaman Website dari publisher
if($_SERVER['HTTP_REFERER']){
	$uri = $_SERVER['HTTP_REFERER'];
}else if($_ENV['HTTP_REFERER']){
	$uri = $_ENV['HTTP_REFERER'];
}else{
	$uri = "";
}
/**
 * passed Parameters
 */
$uri = mysql_escape_string($uri);
$uri = preg_replace('/[w]{3}\\x2E|\\x2F$/', '', $uri); 

$publisherID = mysql_escape_string($_GET['sitti_pub_id']);
$ox_zone_id = mysql_escape_string($_GET['sitti_zone_id']);
$sitti_ad_type = mysql_escape_string($_GET['type']);
//--> testing only
//uncomment kalau mau test di live.
//$uri = "http://pencolengweb.blogspot.com/2010/03/semua-burung-sudah-membuat-sarang.html";
//---- test line --//
if($uri){


//open db connection
$conn = mysql_connect($host,$username,$password);



//get related keywords berdasarkan halaman yang membuka iklan
$sql1 = "SELECT webs, kata
		FROM db_web3.sitti_pub_keywords
		WHERE (webs = '".$uri."')";
$rs = mysql_db_query($database, $sql1);
$i=0;

while($fetch = mysql_fetch_assoc($rs)){
	$list[$i] = $fetch;
	$i++;
}

mysql_free_result($rs);
//untuk debugging, hapus line ini di produksi
/*$list[0]['kata']="mobil";
$list[1]['kata']="rumah";
$list[2]['kata']="tim";*/
//pilah2 kata2nya
$n = sizeof($list);
if($n>0){
	$pub_keywords = "";
	
	//--> 
	for($i=0;$i<$n;$i++)
	{
		if($i>0){
			$pub_keywords = $pub_keywords . ", '" .$list[$i]['kata']."'" ;
		}else{
			$pub_keywords = "'".$list[$i]['kata']."'";
		}
	}
	//-->
	/**
 	* 
 	* query untuk mendapatkan iklan
 	* output field : id,nama,judul,baris1,baris2,category_id,advertiser_id,linkName,lunkURL,ox_campaign_id,ox_banner_id
 	*/
		$sql2 = " SELECT b.id, b.nama, b.judul, b.baris1, b.baris2, b.category_id, b.advertiser_id, b.urlName AS linkName,
b.urlLink AS linkURL, b.ox_campaign_id, b.ox_banner_id,b.ox_zone_id
FROM ( SELECT iklan_id FROM db_web2.sitti_ad_keywords
WHERE (keyword IN(".$pub_keywords."))) 
a INNER JOIN db_web2.sitti_ad_inventory b ON a.iklan_id = b.id
ORDER BY RAND()
LIMIT 4";


	$rs = mysql_db_query($database, $sql2);
	$i=0;
	$list=null;
	if($rs){
		while($fetch = mysql_fetch_assoc($rs)){
			$list[$i] = $fetch;
			$i++;
			//print_r($list);
			//masukkan url referer ke dalam table indexing.
			$sql0 = "INSERT INTO zz_sitti.sitti_showadlog(id_iklan,sittiID,web_name,tglisidata,datee,ipaddress)
					 VALUES('".$fetch['id']."','".$publisherID."','".$uri."',NOW(),NOW(),'".ip2long($ip)."')";
			$q=mysql_db_query($database,$sql0);
			//print mysql_error();
		}
		mysql_free_result($rs);
	}
	
}
	/**
 	* uda selesai. so kita tutup dbnya.
 	*/
	mysql_close($conn);
}
if($list==null){
	//iklan default
	$list[0]['id']="0";
	$list[0]['judul']="Ingin Iklan Anda Disini?";
	$list[0]['baris1']="Daftar sekarang di SITTI.";
	$list[0]['baris2']="Masih gratis. Waktu terbatas.";
	$list[0]['linkName']="http://www.sittibelajar.com";
	$list[0]['linkURL']="http://www.sittibelajar.com";
	
	//open db connection
	$conn = mysql_connect($host,$username,$password);
	//masukkan url referer ke dalam table indexing.
	$sql0 = "INSERT INTO zz_sitti.sitti_showadlog(id_iklan,sittiID,web_name,tglisidata,datee,ipaddress)
					 VALUES('0','".$publisherID."','".$uri."',NOW(),NOW(),'".ip2long($ip)."')";
	$q=mysql_db_query($database,$sql0);
	mysql_close($conn);
	//-->
}

if($sitti_ad_type=="2"){
	include_once "ad_template2.php";
}else if($sitti_ad_type=="3"){
	include_once "ad_template3.php";
}else{
	include_once "ad_template1.php";
}
?>