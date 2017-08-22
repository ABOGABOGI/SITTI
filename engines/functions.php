<?php
/**
 * 
 * check if the url is valid
 * @param $url
 * @return boolean
 */
function isValidUrl($url){
	$url = mysql_escape_string($url);
	$hostname = str_replace("http://","", $url);
	$hostname = str_replace("https://","",$hostname);
	$foo = explode("/",$hostname);
	$hostname = $foo[0];
	//print $hostname."<br/>";
	
	if(checkdnsrr($hostname,"A")){
		return true;
	}else{
		return false;
	}
}

//dropdown menu page
function GetDropDownMenu($req,$app,$view){
	global $APP_PATH,$ENGINE_PATH;
	//include_once $APP_PATH."Article/Article.php";
	SittiTigaLimaSembilan(&$req,&$app,&$view);
    /*
	$article = new Article($req);
	$view->assign("isArticle1",$article->hasArticle(1));
	$view->assign("isArticle4",$article->hasArticle(4));
	$view->assign("isArticle2",$article->hasArticle(2));
     * 
     */

}

function SittiTigaLimaSembilan($req,$app,$view){
	global $_REDIRECT;
	$app->setRootPageByTag("SITTI_359");
	$stls =$app->getChildrens($app->rootID);
	for($i=0;$i<sizeof($about);$i++){
		$stls[$i]['child'] = $app->getChildrens($stls[$i]['id']);
		$n = sizeof($about[$i]['child']);
		//search for custom redirection
		for($j=0;$j<$n;$j++){
			if(eregi("REDIRECT_TO_",$stls[$i]['child'][$j]['tag'])){
				$stls[$i]['child'][$j]['Redirect'] = $_REDIRECT[$stls[$i]['child'][$j]['tag']];
			}
		}
		if(eregi("REDIRECT_TO_",$stls[$i]['tag'])){
			$stls[$i]['Redirect'] = $_REDIRECT[$stls[$i]['tag']];
		}
	}
	$view->assign("stls",$stls);
}
//end dropdown

function clean($str){
	return mysql_escape_string($str);
}
/**
 * 
 * clean string from mysql related keywords
 * @param $str
 */
function cleanString($str){
	$str = eregi_replace("INSERT","", $str);
	$str = eregi_replace("DROP","", $str);
	$str = eregi_replace("SELECT","", $str);
	$str = eregi_replace("DELETE","",$str);
	$str = eregi_replace("UPDATE","",$str);
	$str = eregi_replace("UNION ALL","",$str);
	$str = eregi_replace("UNION","",$str);
	$str = eregi_replace("WHERE","",$str);
	$str = eregi_replace("AND","",$str);
	$str = eregi_replace("JOIN","",$str);
	return mysql_escape_string($str);
}
function sendRedirect($url){
	print' <meta http-equiv="refresh" content="1;URL='.$url.'" />';

}
function isImage($filename){
	if(eregi("\.jpeg|\.gif|\.jpg|\.png",$filename)){
		return true;
	}	
}
function LoadModule($moduleName,$req){
	global $APP_PATH,$ENGINE_PATH;
	$moduleName = mysql_escape_string($moduleName);
	if(file_exists($APP_PATH.$moduleName."/".$moduleName.".php")){
		include_once $APP_PATH.$moduleName."/".$moduleName.".php";	
		$obj = new $moduleName(&$req);	
		return $obj;
	}else{
		print "OBJECT NOT FOUND !";
		die();	
	}
}
function isLocal($filename){
	if(is_file($filename)){
		return true;
	}
}
/**
 * fungsi untuk mencari perbedaan tanggal.
 */
function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
{

  /*
  $interval can be:
  yyyy - Number of full years
  q - Number of full quarters
  m - Number of full months
  y - Difference between day numbers
  (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33".
                 The datediff is "-32".)
  d - Number of full days
  w - Number of full weekdays
  ww - Number of full weeks
  h - Number of full hours
  n - Number of full minutes
  s - Number of full seconds (default)
  */

  if (!$using_timestamps) {
    $datefrom = strtotime($datefrom, 0);
    $dateto = strtotime($dateto, 0);
  }
  $difference = $dateto - $datefrom; // Difference in seconds

  switch($interval) {
    case 'yyyy': // Number of full years
    $years_difference = floor($difference / 31536000);
    if (mktime(date("H", $datefrom),
                              date("i", $datefrom),
                              date("s", $datefrom),
                              date("n", $datefrom),
                              date("j", $datefrom),
                              date("Y", $datefrom)+$years_difference) > $dateto) {

    $years_difference--;
    }
    if (mktime(date("H", $dateto),
                              date("i", $dateto),
                              date("s", $dateto),
                              date("n", $dateto),
                              date("j", $dateto),
                              date("Y", $dateto)-($years_difference+1)) > $datefrom) {

    $years_difference++;
    }
    $datediff = $years_difference;
    break;

    case "q": // Number of full quarters
    $quarters_difference = floor($difference / 8035200);
    while (mktime(date("H", $datefrom),
                                   date("i", $datefrom),
                                   date("s", $datefrom),
                                   date("n", $datefrom)+($quarters_difference*3),
                                   date("j", $dateto),
                                   date("Y", $datefrom)) < $dateto) {

    $months_difference++;
    }
    $quarters_difference--;
    $datediff = $quarters_difference;
    break;

    case "m": // Number of full months
    $months_difference = floor($difference / 2678400);
    while (mktime(date("H", $datefrom),
                                   date("i", $datefrom),
                                   date("s", $datefrom),
                                   date("n", $datefrom)+($months_difference),
                                   date("j", $dateto), date("Y", $datefrom)))
                        { // Sunday
    $days_remainder--;
    }
    if ($odd_days > 6) { // Saturday
    $days_remainder--;
    }
    $datediff = ($weeks_difference * 5) + $days_remainder;
    break;

    case "ww": // Number of full weeks
    $datediff = floor($difference / 604800);
    break;

    case "h": // Number of full hours
    $datediff = floor($difference / 3600);
    break;

    case "n": // Number of full minutes
    $datediff = floor($difference / 60);
    break;

    default: // Number of full seconds (default)
    $datediff = $difference;
    break;
  }

  return $datediff;
}

function getRealIP(){
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
  return $ip;
}

// Parameters:
// $text = The text that you want to encrypt.
// $key = The key you're using to encrypt.
// $alg = The algorithm.
// $crypt = 1 if you want to crypt, or 0 if you want to decrypt.

function cryptare($text, $key, $alg, $crypt)
{
    $encrypted_data="";
    switch($alg)
    {
        case "3des":
            $td = mcrypt_module_open('tripledes', '', 'ecb', '');
            break;
        case "cast-128":
            $td = mcrypt_module_open('cast-128', '', 'ecb', '');
            break;   
        case "gost":
            $td = mcrypt_module_open('gost', '', 'ecb', '');
            break;   
        case "rijndael-128":
            $td = mcrypt_module_open('rijndael-128', '', 'ecb', '');
            break;       
        case "twofish":
            $td = mcrypt_module_open('twofish', '', 'ecb', '');
            break;   
        case "arcfour":
            $td = mcrypt_module_open('arcfour', '', 'ecb', '');
            break;
        case "cast-256":
            $td = mcrypt_module_open('cast-256', '', 'ecb', '');
            break;   
        case "loki97":
            $td = mcrypt_module_open('loki97', '', 'ecb', '');
            break;       
        case "rijndael-192":
            $td = mcrypt_module_open('rijndael-192', '', 'ecb', '');
            break;
        case "saferplus":
            $td = mcrypt_module_open('saferplus', '', 'ecb', '');
            break;
        case "wake":
            $td = mcrypt_module_open('wake', '', 'ecb', '');
            break;
        case "blowfish-compat":
            $td = mcrypt_module_open('blowfish-compat', '', 'ecb', '');
            break;
        case "des":
            $td = mcrypt_module_open('des', '', 'ecb', '');
            break;
        case "rijndael-256":
            $td = mcrypt_module_open('rijndael-256', '', 'ecb', '');
            break;
        case "xtea":
            $td = mcrypt_module_open('xtea', '', 'ecb', '');
            break;
        case "enigma":
            $td = mcrypt_module_open('enigma', '', 'ecb', '');
            break;
        case "rc2":
            $td = mcrypt_module_open('rc2', '', 'ecb', '');
            break;   
        default:
            $td = mcrypt_module_open('blowfish', '', 'ecb', '');
            break;                                           
    }
   
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
    mcrypt_generic_init($td, $key, $iv);
   
    if($crypt)
    {
        $encrypted_data = mcrypt_generic($td, $text);
    }
    else
    {
        $encrypted_data = @mdecrypt_generic($td, $text);
    }
   
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
   
    return $encrypted_data;
} 
function convertBase64($str){
	$str = str_replace("=",".",$str);
	$str = str_replace("+","-",$str);
	$str = str_replace("/","_",$str);
	return $str;
}
function realBase64($str){
	$str = str_replace(".","=",$str);
	$str = str_replace("-","+",$str);
	$str = str_replace("_","/",$str);
	return $str;
}
function urlencode64($str){
	$key = "sitti_gr33d1sg00d";
	$hash = cryptare($str,$key,'des',1);
	$str = convertBase64(base64_encode($hash));
	return $str;
}
function urldecode64($str){
	$key = "sitti_gr33d1sg00d";
	$secret = base64_decode(realBase64($str));
	$str = cryptare($secret,$key,'des',0);
	return trim($str);
}
function encrypt_parameter($str){
	$key = "onigisittiapi";
	$encrypted_data="";
	$td = mcrypt_module_open('rc2', '', 'ecb', '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	mcrypt_generic_init($td, $key, $iv);
	$encrypted_data = mcrypt_generic($td, $str);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$str = base64_encode($encrypted_data);
	return $str;
}
function decrypt_parameter($str){
	$key = "onigisittiapi";
	$secret = base64_decode($str);
	$decrypted_data="";
	$td = mcrypt_module_open('rc2', '', 'ecb', '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	mcrypt_generic_init($td, $key, $iv);
	$decrypted_data = @mdecrypt_generic($td, $secret);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return trim($decrypted_data);
}
function get_correct_utf8_mysql_string($s) 
{ 
    if(empty($s)) return $s; 
    $s = preg_match_all("#[\x09\x0A\x0D\x20-\x7E]| 
[\xC2-\xDF][\x80-\xBF]| 
\xE0[\xA0-\xBF][\x80-\xBF]| 
[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}| 
\xED[\x80-\x9F][\x80-\xBF]#x", $s, $m ); 
    return implode("",$m[0]); 
}
function is_token_valid($token){
	$salt = urlencode64($_COOKIE['PHPSESSID']);
	$data = json_decode(urldecode64($_SESSION[$salt]));
	$secret = urlencode64($_COOKIE['PHPSESSID'].$token);
	if($data->token==$token&&$data->secret==$secret){
		$_SESSION[$salt] = null;
		return true;
	}
}
?>