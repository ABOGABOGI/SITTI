<?php

/**
 * Description of Custom_Template
 *
 * @author linkx
 */

include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";

define("ADD_TEXT_FIELD", "SITTI/custom_template/add_text_field.html");
define("EDIT_TEXT_FIELD", "SITTI/custom_template/edit_text_field.html");
define("EDIT_LAYOUT", "SITTI/custom_template/edit_layout.html");
define("ADD_LAYOUT", "SITTI/custom_template/add_layout.html");
define("PETUNJUK", "SITTI/custom_template/petunjuk.html");
class LandingPage extends SQLData {
    var $View;
    var $Account;
    var $upload_dir;
    function LandingPage($req,$account){
        parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
        $this->Account = $account;
        $this->upload_dir = "contents/images";
    }
	/**
	 * 
	 * control logic
	 */
    function run(){
    	$req = $this->Request;
    	if($req->getPost("action")=="save"){
    		return $this->SaveLayout();
    	}else if($req->getPost("action")=="preview"){
    		return $this->Preview();
    	}
    	if($req->getParam("create")=="1"){
    		return $this->Create();
    	}else{
			return $this->Petunjuk();
    	}
    }
    /**
     * 
     * displaying Halaman Petunjuk.
     */
    function Petunjuk(){
    	$campaign = "1863";
			$judul="Judul Iklan";
			$nama="Nama iklan";
			$baris1 = "Baris 1 nih";
			$baris2 = "Baris 2 nih";
			//save di session dulu
			$arr['detail_iklan']['campaign'] = 1863;
			$arr['detail_iklan']['judul'] = urlencode("Judul & Iklan, apalagi kek ! #@!@#$");
			$arr['detail_iklan']['nama'] = urlencode("nama");
			$arr['detail_iklan']['baris1'] = urlencode("baris 1 nih");
			$arr['detail_iklan']['baris2'] = urlencode("baris 2 nih");
			
			$_SESSION[base64_encode("sitti_custom_landing_iklan")] = base64_encode(json_encode($arr));
    		return $this->View->toString("SITTI/landing_page/petunjuk.html");
    }
    /**
     * 
     * utility method
     * @param unknown_type $session_name
     */
    function unserialized($session_name){
    	//print_r($_SESSION);
    	$str = base64_decode($_SESSION[$session_name]);
    	return json_decode($str);
    }
    /**
     * 
     * Save the Custom Landing Page
     * @return Boolean
     */
	function SaveLayout(){
		global $LOCALE;
		$params = json_decode(base64_decode($_SESSION[base64_encode("sitti_custom_landing_params")]),true);
		$bgColor = mysql_escape_string($params['layout_params']['bgcolor']);
         $fontColor = mysql_escape_string($params['layout_params']['fontcolor']);
         $metaTags = mysql_escape_string($params['layout_params']['meta_tags']);
         $metaDescription = mysql_escape_string($params['layout_params']['meta_description']);
         $link_website = mysql_escape_string($params['layout_params']['link_website']);
         $metaAuthor = mysql_escape_string($params['layout_params']['meta_author']);
         $metaTags = mysql_escape_string($params['layout_params']['meta_tags']);
         $pageTitle = mysql_escape_string($params['layout_params']['page_title']);

         $headerTitle = mysql_escape_string($params['layout_params']['header_title']);
         $headerSubTitle = mysql_escape_string($params['layout_params']['header_subtitle']);
         
         $content = mysql_escape_string(nl2br($params['layout_params']['content']));
         $footerText = mysql_escape_string($params['layout_params']['footer_text']);
			
         $filename_logo = mysql_escape_string($params['layout_params']['logo_img']);
         $filename_banner = mysql_escape_string($params['layout_params']['banner_img']);
		
         $textDefault1 = mysql_escape_string($params['layout_params']['defaultField1']);
         $textDefault2 = mysql_escape_string($params['layout_params']['defaultField2']);
         $text1 = mysql_escape_string($params['layout_params']['additional1']);
         $text2 = mysql_escape_string($params['layout_params']['additional2']);
         $text3 = mysql_escape_string($params['layout_params']['additional3']);
         $email = mysql_escape_string($params['layout_params']['email']);
         
         $ga = mysql_escape_string($params['layout_params']['google_analytics']);
         
         $published = 1;
         $status = 0;
         print_r($this->Account);
         $userID = $this->Account['sittiID'];
         
		$layoutID = $this->insertLayout($userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website, $metaAuthor, $content, $filename_logo,
                                            $filename_banner, $footerText, $headerTitle, $headerSubTitle, $status, $published,$ga);
		
		
		if($layoutID>0){
			
			$rs = $this->insertLayoutCustomField($layoutID, $userID, $textDefault1, $textDefault2, $text1, $text2, $text3, $email, $published);
		}
		if($rs){
			$msg = $LOCALE['CUSTOM_LAYOUT_SAVED'];
		}else{
			$msg = $LOCALE['CUSTOM_LAYOUT_ERROR'];
		}
		return $this->View->showMessage($msg,"landing.php?done=1");
	}
	/**
	 * 
	 * save settings into database
	 * @param unknown_type $userID
	 * @param unknown_type $bgColor
	 * @param unknown_type $fontColor
	 * @param unknown_type $pageTitle
	 * @param unknown_type $metaTags
	 * @param unknown_type $metaDescription
	 * @param unknown_type $link_website
	 * @param unknown_type $metaAuthor
	 * @param unknown_type $content
	 * @param unknown_type $filename_logo
	 * @param unknown_type $filename_banner
	 * @param unknown_type $footerText
	 * @param unknown_type $headerTitle
	 * @param unknown_type $headerSubTitle
	 * @param unknown_type $status
	 * @param unknown_type $published
	 * @return int inserted_id
	 */
	function insertLayout($userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website, $metaAuthor, $content, $filename_logo,
                                            $filename_banner, $footerText, $headerTitle, $headerSubTitle, $status, $published,$ga){
                                            
		$this->open(0);
		 $q= $this->query("INSERT INTO layout(user_id, bgcolor, fontcolor, page_title, meta_tags, meta_description ,
                                link_website, meta_author, content,
                                logo_img, banner_img, footer_text,
                                header_title, header_subtitle, created_time, status, published,google_analytics)
                                VALUES('".$userID."', '".$bgColor."', '".$fontColor."', '".$pageTitle."',
                                '".$metaTags."', '".$metaDescription."', '".$link_website."',
                                '".$metaAuthor."', '".$content."', '".$filename_logo."',
                                '".$filename_banner."', '".$footerText."',
                                '".$headerTitle."', '".$headerSubTitle."', NOW(), '".$status."', '".$published."','".$ga."')",1);
		 
		 
		$this->close();
		 if($q){
		 	return $this->lastInsertId;
		 }else{
		 	return -1;
		 }
	}
 


    /*
     * INSERT LANDING PAGE CUSTOM FIELD
     */
    function insertLayoutCustomField($layoutID, $userID, $textDefault1, $textDefault2, $text1, $text2, $text3, $email, $published){
        $this->open(0);
    	$sql = $this->query("INSERT INTO layout_custom_field(layoutID, userID, defaultField1, defaultField2,
                            additional1, additional2, additional3, email, published)
                            VALUES('".$layoutID."', '".$userID."', '".$textDefault1."', '".$textDefault2."', '".$text1."',
                            '".$text2."', '".$text3."', '".$email."', '".$published."')");
        $this->close();
        return $sql;
    }
	function Preview(){
		
		$raw_params = $_POST;
		$layout_params = $this->map_params($raw_params);
		
		if($_FILES['logo']['name']&&eregi("image",$_FILES['logo']['type'])){
			$logo_file_ext = explode(".",$_FILES['logo']['name']);
			if($_SESSION[base64_encode("sitti_landing_img_logo")]!=null
			&&eregi($logo_file_ext[sizeof($logo_file_ext)-1],$_SESSION[base64_encode("sitti_landing_img_logo")])){
				$logo_img = $_SESSION[base64_encode("sitti_landing_img_logo")];
			}else{
				$logo_img = md5("logo_".date("YmdHis")).".".$logo_file_ext[sizeof($logo_file_ext)-1];
				$_SESSION[base64_encode("sitti_landing_img_logo")] = $logo_img;
			}
			$raw_params['logo_img'] = $logo_img;
			
			$this->UploadFile($logo_img,"logo");
			
			
		}else{
			$logo_img = $_SESSION[base64_encode("sitti_landing_img_logo")];
			$raw_params['logo_img'] = $logo_img;
		}
		if($_FILES['banner']['name']&&eregi("image",$_FILES['banner']['type'])){
			$banner_file_ext = explode(".",$_FILES['banner']['name']);
			if($_SESSION[base64_encode("sitti_landing_img_banner")]!=null
				&&eregi($banner_file_ext[sizeof($banner_file_ext)-1],$_SESSION[base64_encode("sitti_landing_img_banner")])){
				$banner_img = $_SESSION[base64_encode("sitti_landing_img_banner")];
			}else{
				$banner_img = md5("banner_".date("YmdHis")).".".$banner_file_ext[sizeof($banner_file_ext)-1];
				$_SESSION[base64_encode("sitti_landing_img_banner")] = $banner_img;
			}
			$raw_params['banner_img'] = $banner_img;
			
			$this->UploadFile($banner_img,"banner");
			
		}else{
			
			$banner_img = $_SESSION[base64_encode("sitti_landing_img_banner")];
			$raw_params['banner_img'] = $banner_img;
		}
		
		$layout_params = $this->map_params($raw_params);
		
		$params['layout_params'] = $layout_params;
		//print_r($layout_params);
		//$params['images'] = array("logo"=>$logo_img,"banner"=>$banner_img);
		$json_arr = json_encode($params);
		$_SESSION[base64_encode("sitti_custom_landing_params")] = base64_encode($json_arr);
		$this->View->assign("list",$layout_params);
		$this->View->assign("field",$layout_params);
		return $this->View->toString("SITTI/landing_page/edit_layout.html");
	}
	function UploadFile($filename,$handler){
		$flag = true;
		$dir_path = $this->upload_dir;
		if(!is_dir($dir_path)){
			if(!mkdir($dir_path)){
				$flag = false;
			}
		}
		if($flag){
			//upload mechanism
			if(move_uploaded_file($_FILES[$handler]['tmp_name'],$dir_path."/".$filename)){
				$flag = true;
			}else{
				$flag = false;
			}
		}
		return $flag;
	}
	function map_params($p){
		$arr = array(
		"user_id"=>$p['user_id'],
		"bgcolor"=>$p['colorPicker'],
		"fontcolor"=>$p['colorPickerText'],
		"page_title"=>$p['titlePage'],
		"meta_tags"=>$p['meta'],
		"meta_description"=>$p['meta_description'],
		"link_website"=>$p['link_website'],
		"meta_author"=>$p['meta_author'],
		"content"=>$p['content'],
		"logo_img"=>$p['logo_img'],
		"banner_img"=>$p['banner_img'],
		"footer_text"=>$p['footerText'],
		"header_title"=>$p['headerTitle'],
		"header_subtitle"=>$p['headerSubTitle'],
		"google_analytics"=>$p['google_analytics'],
		"defaultField1"=>$p['textDefault1'],
		"defaultField2"=>$p['textDefault2'],
		"additional1"=>$p['text1'],
		"additional2"=>$p['text2'],
		"additional3"=>$p['text3'],
		"additional4"=>$p['text4'],
		"additional5"=>$p['text5'],
		"email"=>$p['email']
		);
		return $arr;
	}
	function Create(){
		$params_iklan = $this->unserialized(base64_encode("sitti_custom_landing_iklan"));
    	
		$ikan['judul'] = $params_iklan->detail_iklan->judul;
    	$iklan['nama'] = $params_iklan->detail_iklan->nama;
    	$iklan['baris1'] = $params_iklan->detail_iklan->baris1;
    	$iklan['baris2'] = $params_iklan->detail_iklan->baris2;
    	$iklan['campaign'] = $params_iklan->detail_iklan->campaign;
    	
		return $this->View->toString("SITTI/landing_page/add_layout.html");
	}
    function resetSessionLandingPage(){
    	$_SESSION[base64_encode("sitti_landing_img_logo")] = null;
       $_SESSION[base64_encode("sitti_landing_img_banner")] = null;
       $_SESSION[base64_encode("sitti_custom_landing_params")] = null;
       $_SESSION[base64_encode("sitti_custom_landing_iklan")] = null;
    }


}
?>