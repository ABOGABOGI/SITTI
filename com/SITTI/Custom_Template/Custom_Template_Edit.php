<?php

/**
 * Description of Custom_Template_Edit
 *
 * @author linkx
 */

include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $APP_PATH."SITTI/Custom_Template/Custom_Template.php";

define("EDIT_LAYOUT", "SITTI/custom_template/edit_layout.html");
define("PETUNJUK", "SITTI/custom_template/petunjuk.html");
class Custom_Template_Edit extends SQLData {
    var $View;
    var $Account;
    var $Custom_Template;
    
    function Custom_Template_Edit($req){
        parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
        $this->Account = new SITTIAccount(&$req);
        $this->Custom_Template = new Custom_Template(&$req);
        
        
    }

    function process(){
        $req = $this->Request;

        $this->Account->open(0);
        $userID = $this->Account->getActiveID();//user yang sedang login
        $this->Account->close();
        //print $userID;
        //print_r($_SESSION);
        
		
        if($req->getParam("r")=='petunjuk'){
            return $this->View->toString(PETUNJUK);

        }else if($this->Request->getParam("do")=='saveEditLayout'){ //SAVE EDIT LANDING PAGE
            $action = $this->Request->getPost("action"); //get url action (save or preview)
            //print $action;
            $layoutID = $this->Request->getPost("layoutID");
            $customFieldID = $this->Request->getPost("fieldID");

            
            $bgColor = $req->getPost("colorPicker"); //get color code
            $fontColor = $req->getPost("colorPickerText"); //get font color code
            $metaTags = $req->getPost("meta"); //get meta tag
            $metaDescription = $req->getPost("meta_description");
            $google_analytics = $req->getPost("google_analytics");
            $link_website = $req->getPost("link_website");
            $metaAuthor = $req->getPost("meta_author");
            $metaTags = $req->getPost("meta");
            $pageTitle = $req->getPost("titlePage");  //get title page

            $headerTitle = $req->getPost("headerTitle");
            $headerSubTitle = $req->getPost("headerSubTitle");
            $c = $req->getPost("content");

            /*
             * REPLACE KARAKTER PADA TEXTAREA ISI/CONTENT
             */
            $content = str_replace(array("\\\\n", "\n", "\\r\\n", "\r\n", "\r", "\\\r"), "<br/>", $c);
            //die();
            
            $footerText = $req->getPost("footerText");
            
            /*
             * FORM FIELD
             */
            $textDefault1 = $req->getPost("textDefault1");
            $textDefault2 = $req->getPost("textDefault2");
            $text1 = $req->getPost("text1");
            $text2 = $req->getPost("text2");
            $text3 = $req->getPost("text3");
            $email = $req->getPost("email");

            

            /*
             * PARAMETER YANG DI AMBIL DARI EDIT IKLAN
             */
            $id_iklan = $this->Request->getParam("id_iklan");
            $campaign = $this->Request->getParam("c");
            //END
            
            if($textDefault1 || $textDefault2 || $text1 || $text3){
                if(!$email){
                    ?>
                        <script>
                            alert("Anda telah mengaktifkan formulir. Harap masukkan email anda!");
                            document.location="preview_template_edit.php?id=<?= $layoutID?>";
                        </script>
                    <?
                    //sendRedirect("preview_template_edit.php?id=".$layoutID);
                }
            }

            $logo_img = $_FILES['logo']['name'];
            //print $logo_img;
            $logo_img_tmp = $_FILES['logo']['tmp_name'];

            $banner_img = $_FILES['banner']['name'];
            $banner_img_tmp = $_FILES['banner']['tmp_name'];


            $path = "contents/images/";
            if(!is_dir($path)){
                mkdir($path);
            }

            //print_r($_FILES);

            $this->open(0);
            $q = $this->fetch("SELECT logo_img, banner_img FROM layout WHERE id='".$layoutID."' AND user_id='".$userID."' LIMIT 1");

            $logo_img_old = $q['logo_img'];
            $banner_img_old = $q['banner_img'];

            $check = $this->Custom_Template->checkLayout($layoutID, $userID);


            if($check['total']>0){ //cek user, sudah punya landing page ini?(berdasarkan id yang sedang di edit),(update)
                if($logo_img_tmp){
                    if($logo_img_old){
                        @unlink($path.$logo_img_old);
                    }
                    $filename_logo = md5(date("Ymdhis")."_".str_replace(" ","_",$logo_img)).".jpg";
                    if($filename_logo){
                       move_uploaded_file($logo_img_tmp, $path.$filename_logo);

                    }


                }else{
                    $filename_logo = $logo_img_old;
                }

                if($banner_img_tmp){
                    if($banner_img_old){
                        @unlink($path.$banner_img_old);
                    }
                    $filename_banner = md5(date("Ymdhis")."_".str_replace(" ","_",$banner_img)).".jpg";
                    if($filename_banner){
                        move_uploaded_file($banner_img_tmp, $path.$filename_banner);
                    }


                }else{
                    $filename_banner = $banner_img_old;
                }

                if($action=='preview'){ //JIKA BUTTON PREVIEW DI KLIK
                    $published = '0';
                }else if($action=='save'){//JIKA BUTTON SAVE DI KLIK
                    $published = '1';
                }

                /*
                 * UPDATE LAYOUT QUERY
                 */
                $query = $this->Custom_Template->updateLayout($layoutID, $userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website,
                                            $metaAuthor, $content, $filename_logo,
                                            $filename_banner, $footerText, $headerTitle, $headerSubTitle,$google_analytics, $status, $published);

            }

            if($query){
               // $this->open(0);

                /*
                 * UPDATE LAYOUT CUSTOM FIELD QUERY (FORM PROCESSING)
                 */
                $query = $this->Custom_Template->updateLayoutCustomField($layoutID, $userID, $textDefault1,
                                                        $textDefault2, $text1, $text2, $text3, $email);

                if($action=='preview'){//JIKA BUTTON PREVIEW DI KLIK
                    $msg = "Seting halaman berhasil di perbaharui.";
                    $urlBack = "preview_template_edit.php?id=".$layoutID."&param=edit_layout&edit_pending=1&id_iklan=".$id_iklan."&c=".$campaign; //KEMBALI KE HALAMAN PREVIEW EDIT LAYOUT
                    
                    ?>
                    <script>document.location='<?= $urlBack?>';</script>
                    <?
                    //sendRedirect($urlBack);

                }else if($action=='save'){//JIKA BUTTON SAVE DI KLIK
                    $msg = "Seting halaman berhasil di perbaharui.<br> Klik tombol di bawah untuk menuju ke halaman edit iklan.";
                    $urlBack = "beranda.php?edit_pending=1&id=".$id_iklan."&c=".$campaign; //KEMBALI KE HALAMAN EDIT IKLAN

                }

                if($query){
                    $strHTML= $this->View->showMessage($msg, $urlBack);

                }else{
                    //print mysql_error();
                    $er = "Field tidak berhasil ditambah. Segera hubungi administrator sistem anda.";
                    $strHTML= $this->View->showMessageError($er, $urlBack);
                }


            }else{
                $er = "Seting halaman tidak berhasil di ubah. Segera hubungi administrator sistem anda.";
                $strHTML= $this->View->showMessageError($er, "preview_template_edit.php?id=".$layoutID); //KEMBALI KE HALAMAN PREVIEW EDIT LAYOUT
            }
            
                

            $this->close();
            return $strHTML;

            
        }else{
            //KEMBALI KE HALAMAN EDIT LAYOUT
            $this->close();
            sendRedirect("modif_edit_template.php?id=".$id_iklan."&param=edit_layout&edit_pending=1&id_iklan=".$id_iklan."&c=".$campaign);
        }
	}

  
}
?>
