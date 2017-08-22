<?php 
include_once "../../config/config.inc.php";
include_once "../../engines/Utility/XML2JSON/xml2json.php";
include_once "IndomogAPI.php";

$api = new IndomogAPI(false);
$profile['name'] = "test";
$profile['sittiID'] = "AC0002347";
$profile['mobile'] = "08155555656";
$profile['email'] = "hapsoro.renaldy4@kamisitti.com";
$profile['indomog_id'] = "951012060007";




//$out = $api->register($profile);
//$js = json_decode($out);
//print $js->response->data->rc;
//var_dump($js);
//print "<br/><br/>-----------TOPUP BCA--------------------<br/><br/>";
print "BCA\n-------\n";
$out=$api->topupBCA($profile, "SITTI0907", 10000);
print $out;
$js = json_decode($out);
print $js->response->data->rc."\n";
//$api->topupMandiri($profile, "08185555656", 10000);
//$api->topupMandiriInternet($profile, "1234567890123456", 10000);
//$api->lookup($profile);
/*$secret = "123456";
$sittiID = "AC0000004";
$gateway = "http://dev.indomog.com/sitti/";

$data1= "si";
$data3= time();
$data4= "3001";
$data6= "okta gantengs";
$data7= $sittiID;
$data8= "si";
$data9= "02112121"; //tlpn rumah
$data10= "081212121"; //mobile
$data11= "27-10-1987"; //dd-mm-yyyy
$data12= "L"; //L or P
$data13= "foo@indomog.com";
$data14= md5("okta"); //password
$data15= "apa ?";
$data16= "saya";
$data = $data1.$data3.$data4.$data6.$data8.$data7.$data9.$data10.$data11.$data12.$data13.$data14.$data15.$data16;
$signature = sha1($data.$secret);
print "-->".$signature;
$param = "<?xml version=\"1.0\"?>";
$param = $param . "<request>";
$param = $param . "<data>";
$param = $param . "<rmid>$data1</rmid>";
$param = $param . "<qid>$data3</qid>";
$param = $param . "<rc>$data4</rc>";
$param = $param . "<name>$data6</name>";
$param = $param . "<alg>$data8</alg>";
$param = $param . "<algid>$data7</algid>";
$param = $param . "<rph>$data9</rph>";
$param = $param . "<ph>$data10</ph>";
$param = $param . "<bod>$data11</bod>";
$param = $param . "<cat>$data12</cat>";
$param = $param . "<ea>$data13</ea>";
$param = $param . "<mp>$data14</mp>";
$param = $param . "<sq>$data15</sq>";
$param = $param . "<sa>$data16</sa>";
$param = $param . "</data>";
$param = $param . "<signature>$signature</signature>";
$param = $param . "</request>";
//print $param;*/
?>