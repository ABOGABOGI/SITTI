<?php
include_once "locale.inc.php";
$GLOBAL_PATH = "../";
$APP_PATH = "../com/";
$ENGINE_PATH = "../engines/";
$WEBROOT = "../html/";
//DB WEB, local ex method: open()
$CONFIG['DATABASE'][0]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][0]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][0]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][0]['DATABASE'] = "db_web3";

/*
 $CONFIG['DATABASE'][0]['HOST'] = "202.52.131.12";
 $CONFIG['DATABASE'][0]['USERNAME'] = "juragansitti";
 $CONFIG['DATABASE'][0]['PASSWORD'] = "cotaxEdonatagosE";
 $CONFIG['DATABASE'][0]['DATABASE'] = "db_web2";
 */

//DB WORDS, online ex method : open(1)


$CONFIG['DATABASE'][1]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][1]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][1]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][1]['DATABASE'] = "db_words";

//REPORTING DATABASE
$CONFIG['DATABASE'][2]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][2]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][2]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][2]['DATABASE'] = "db_adlogs_adm";

//QUEUING DB
$CONFIG['DATABASE'][3]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][3]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][3]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][3]['DATABASE'] = "db_publisher";

//BILLING DB
$CONFIG['DATABASE'][4]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][4]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][4]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][4]['DATABASE'] = "db_billing";

//API DB
$CONFIG['DATABASE'][5]['HOST'] = "202.80.113.52";
$CONFIG['DATABASE'][5]['USERNAME'] = "sitti";
$CONFIG['DATABASE'][5]['PASSWORD'] = "manager";
$CONFIG['DATABASE'][5]['DATABASE'] = "db_api";
/**
 * Email settings
 */
$CONFIG['EMAIL_FROM_DEFAULT'] = "mailbot@sittibelajar.com";
$CONFIG['EMAIL_SMTP_HOST'] = "smtp.gmail.com";
$CONFIG['EMAIL_SMTP_PORT'] = 465;
$CONFIG['EMAIL_SMTP_USER'] = "sittizen@gmail.com";
$CONFIG['EMAIL_SMTP_PASSWORD'] = "b4b00nb4b00n";
$CONFIG['EMAIL_SMTP_SSL'] = 1;

$CONFIG['EMAIL_FROM_DEFAULT'] = "sitti@sitti.co.id";
$CONFIG['EMAIL_SMTP_HOST'] = "mail.sitti.co.id";
$CONFIG['EMAIL_SMTP_PORT'] = 465;
$CONFIG['EMAIL_SMTP_USER'] = "sitti";
$CONFIG['EMAIL_SMTP_PASSWORD'] = "manager";
$CONFIG['EMAIL_SMTP_SSL'] = 1;


//--->
$CONFIG['FileUploaderServicePath'] = "http://www.sittibelajar.com/beta/html/";
$CONFIG['WebsiteURL'] = "http://192.168.77.60/sitti/svn/src/html/";//SITTIBELAJAR
$CONFIG['WebsiteURL2'] = "http://192.168.77.60/sitti/svn/src/zen/";//BELAJARSITTI
$UPLOAD_DIRS[0] = "contents/";
$UPLOAD_DIRS[1] = "contents/Picture_Gallery/";
$UPLOAD_DIRS[2] = "contents/media/";

/*openx webservice config*/
$OX_CONFIG['username'] = "admin";
$OX_CONFIG['password'] = "admin";
$OX_CONFIG['uri'] = "localhost";
//$OX_CONFIG['uri'] = "soekarno";
$OX_CONFIG['host'] = "localhost";
$OX_CONFIG['service'] = '/openx/www/api/v2/xmlrpc/';
$OX_CONFIG['tracker_uri'] = "http://localhost/openx";
$OX_CONFIG['debug'] = false;

/*PS Adserving Engine Configuration*/
$PS_CONFIG['tracker_uri'] = "http://localhost/SITTI3/svn/sitti/PSAd/delivery/";
$PS_CONFIG['dev_mode'] = true;
$CONFIG['MAINTENANCE_MODE'] = false;
//minimum bid yang diperbolehkan untuk masing2 iklan (dalam rupiah)
$CONFIG['MINIMUM_BID'] = 600;
$CONFIG['MAXIMUM_BID_CAP'] = 999999999;
//CAPTCHA SETTINGS
$CONFIG['BELAJARSITTI_CAPTCHA_PUBLIC'] = "6LciY70SAAAAAE6uymE8jKdjVmD1KzTfLh1w8KRn";
$CONFIG['BELAJARSITTI_CAPTCHA_PRIVATE'] = "6LciY70SAAAAABbvamLhKF7RBNoDEZWd5J1cXR3r";
$CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'] = "6LfTYr0SAAAAAHfif1EK3WG2cdRF309bbqYpNEHQ";
$CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE'] = "6LfTYr0SAAAAAEDE9nhQtD4oFdpbk-6O9Gh406he";

$CONFIG['LOGIN_URL'] = "login.php";
$CONFIG['LOGIN_URL_2'] = "login.php";


$CONFIG['MAILCHIMP_API_KEY'] = "926dd3712d6532b377d978de604583c5-us2";
$CONFIG['MAILCHIMP_LIST_ID'] = "341aff733b";

$CONFIG['INDOMOG_API_URL'] = "http://dev.indomog.com/sitti/index.php";
$CONFIG['INDOMOG_MERCHANT_ID'] = "1011403791";
$CONFIG['INDOMOG_SECRET_KEY'] = "123456";

$CONFIG['EMAIL_RECOVERY_SALT'] = md5("thesecretbehindthetruthisalie");

$CONFIG['SITTI_PAYMENT_SERVICE_URI'] = "http://202.52.131.4:8900";
$CONFIG['SITTI_PAYMENT_SERVICE_KEY'] = "e0ce8c957e7468a5b3cb4105cabd02580b6721eb6d48d";
?>