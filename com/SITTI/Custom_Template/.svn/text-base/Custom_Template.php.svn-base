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
class Custom_Template extends SQLData {
    var $View;
    var $Account;
    
    function Custom_Template($req){
        parent::SQLData();
		$this->Request = $req;
		$this->View = new BasicView();
        $this->Account = new SITTIAccount(&$req);
        
        
    }


    function landingView(){
        $this->Account->open(0);
        $userID = $this->Account->getActiveID();
        $id = $this->Request->getParam("id");
        $this->Account->close();
        
        $this->open(0);
        //print $userID;
        $list = $this->getLayout($id);
		
        $list['edit_layout'] = $this->Request->getParam("param");
 		$this->close();
        return $list;

       
    }

    function viewCustomField(){
        $userID = $this->Account->getActiveID();
        $id = $this->Request->getParam("id"); //visitor request landing page
        $this->open(0);

        if($userID){//advertiser lihat landing page berdasarkan id landing page dan userID nya
            $custom_field = $this->getLayoutCustomField($id, $userID);
        }else{//visitor lihat landing page berdasarkan id landing page nya
            $custom_field = $this->getLayoutCustomField2($id);
        }
        /*
        if($id){ //buat visitor lihat landing page berdasarkan id landing page nya
            $custom_field = $this->getLayoutCustomField2($id);
            return $custom_field;
            
        }else{//buat advertiser lihat landing page berdasarkan id landing page nya
            $custom_field = $this->getLayoutCustomField($_SESSION['layoutID'], $userID);
            return $custom_field;
        }
         * 
         */
         $this->close();
        return $custom_field;
        
       
    }

    

    function admin(){
        $req = $this->Request;

        $this->Account->open(0);
        $userID = $this->Account->getActiveID();//user yang sedang login
        
        $this->Account->close();
        //print $userID;
        //print_r($_SESSION);
        
		
        if($req->getParam("r")=='petunjuk'){

            return $this->View->toString(PETUNJUK);

        }else if($req->getParam("r")=='edit_layout'){
            $this->open(0);
            $id = $this->Request->getParam("id");
            $list=$this->getLayout($id);
            //print_r($list);
            $this->close();
            //print $userID;

            $this->View->assign("list", $list);
            return $this->View->toString(EDIT_LAYOUT);

        }else if($req->getParam("do")=='saveLayout'){ //SAVE NEW LAYOUT
            $action = $this->Request->getPost("action"); //get url action (save or preview)
            

            $bgColor = $req->getPost("colorPicker"); //get color code
            $fontColor = $req->getPost("colorPickerText"); //set all font color
            $metaTags = $req->getPost("meta"); //get meta tag
            $metaDescription = $req->getPost("meta_description");
            $link_website = $req->getPost("link_website");
            $metaAuthor = $req->getPost("meta_author");
            $metaTags = $req->getPost("meta");
            $pageTitle = $req->getPost("titlePage");  //get title page

            $headerTitle = $req->getPost("headerTitle");
            $headerSubTitle = $req->getPost("headerSubTitle");
            $c = $req->getPost("content");
            $content = str_replace('\n', '<br/>', $c);
            $footerText = $req->getPost("footerText");


            $textDefault1 = $req->getPost("textDefault1");
            $textDefault2 = $req->getPost("textDefault2");
            $text1 = $req->getPost("text1");
            $text2 = $req->getPost("text2");
            $text3 = $req->getPost("text3");
            $email = $req->getPost("email");


            if($textDefault1 || $textDefault2 || $text1 || $text2 || $text3){
                if(!$email){
                  //@angling ini harusnya gak seperti ini ! -duf-
                    print("
                        <script>
                            alert(\"Anda telah mengaktifkan formulir. Harap masukkan email anda!\");
                            document.location=\"modif_template.php\";
                        </script>
                    ");
                    //sendRedirect("preview_template.php?id=".$layoutID);
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
                //$q = $this->fetch("SELECT logo_img, banner_img FROM layout WHERE id='".$_SESSION[layoutID]."' AND user_id='".$userID."' LIMIT 1");

                //$logo_img_old = $q['logo_img'];
                //$banner_img_old = $q['banner_img'];


                if($logo_img_tmp){
                    $filename_logo = md5(date("Ymdhis")."_".str_replace(" ","_",$logo_img)).".jpg";
                    if($filename_logo){
                        move_uploaded_file($logo_img_tmp, $path.$filename_logo);
                    }
                }else{
                    //print_r($_FILES)."<br/>";
                    //print "----- TEMP IMAGE TIDAK DITEMUKAN.FILE TIDAK BISA DI UPLOAD -----";
                    //$filename_logo = $logo_img_old;
                }

                if($banner_img_tmp){
                    $filename_banner = "BANNER_".md5(date("Ymdhis")."_".str_replace(" ","_",$banner_img)).".jpg";
                    if($filename_banner){
                        move_uploaded_file($banner_img_tmp, $path.$filename_banner);
                    }
                }else{
                    //print_r($_FILES)."<br/>";
                    //print "----- TEMP IMAGE TIDAK DITEMUKAN.FILE TIDAK BISA DI UPLOAD -----";
                    //$filename_banner = $banner_img_old;
                }

                if($action=='preview'){
                    $published = '0';
                }else if($action=='save'){
                    $published = '1';
                }

                $query = $this->insertLayout($userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website, $metaAuthor, $content, $filename_logo,
                                            $filename_banner, $footerText, $headerTitle, $headerSubTitle, $status, $published);

                $_SESSION['layoutID'] = mysql_insert_id();


                if($query){

                    //buat save text field


                    $query = $this->insertLayoutCustomField($_SESSION['layoutID'], $userID, $textDefault1, $textDefault2, $text1, $text2, $text3, $email, $published);
                    //end save text field

					
                    if($action=='preview'){ //buat preview
                        //$published = '0';
                        //print $action;
                        $urlBack = "preview_template.php?id=".$_SESSION['layoutID'];
                        ?>
                        <script>document.location='<?= $urlBack?>';</script>
                        <?
                        //sendRedirect($urlBack);

                    }else if($action=='save'){ //buat save
                        //$published = '1';
                        $urlBack = "beranda.php?buat_iklan=1&step=4&finish=1&l=".$_SESSION['layoutID'];
                    }

                    $this->View->assign("list", $select);

                    $msg = "Landing page berhasil dibuat.";
                     
                    $strHTML= $this->View->showMessage($msg, $urlBack);


                }else{

                    $er = "Seting halaman tidak berhasil di ubah. Segera hubungi administrator sistem anda.";
                     
                    $strHTML= $this->View->showMessageError($er, $urlBack);
                }
            
				 $this->close();
				 return $strHTML;

          


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


            $content = str_replace(array("\\\\n", "\n", "\\r\\n", "\r\n", "\r", "\\\r"), "<br/>", $c);
                    
            //die();
            
            $footerText = $req->getPost("footerText");



            $textDefault1 = $req->getPost("textDefault1");
            $textDefault2 = $req->getPost("textDefault2");
            $text1 = $req->getPost("text1");
            $text2 = $req->getPost("text2");
            $text3 = $req->getPost("text3");
            $email = $req->getPost("email");

            if($textDefault1 || $textDefault2 || $text1 || $text3){
                if(!$email){
                    ?>
                        <script>
                            alert("Anda telah mengaktifkan formulir. Harap masukkan email anda!");
                            document.location="preview_template.php?id=<?= $layoutID?>";
                        </script>
                    <?
                    //sendRedirect("preview_template.php?id=".$layoutID);
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

            $check = $this->checkLayout($layoutID, $userID);


            if($check['total']>0){ //user sudah punya landing page ini(berdasarkan id yang sedang di edit),(update)
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

                if($action=='preview'){
                    $published = '0';
                }else if($action=='save'){
                    $published = '1';
                }

                $query = $this->updateLayout($layoutID, $userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website,
                                            $metaAuthor, $content, $filename_logo,
                                            $filename_banner, $footerText, $headerTitle, $headerSubTitle,$google_analytics, $status, $published);

            }

            if($query){
               // $this->open(0);


                $query = $this->updateLayoutCustomField($layoutID, $userID, $textDefault1,
                                                        $textDefault2, $text1, $text2, $text3, $email);

                if($action=='preview'){
                    //$published = '0';
                    $msg = "Seting halaman berhasil di perbaharui.";

                    $urlBack = "preview_template.php?id=".$layoutID;
                    ?>
                    <script>document.location='<?= $urlBack?>';</script>
                    <?
                    //sendRedirect($urlBack);

                }else if($action=='save'){
                    //$published = '1';
                    $msg = "Seting halaman berhasil di perbaharui.<br> Klik tombol di bawah untuk menuju ke halaman buat iklan.";

                    //$urlBack = "beranda.php?buat_iklan=1";
                    $urlBack = "beranda.php?buat_iklan=1&step=4&finish=1&l=".$layoutID;
                }

                if($query){

                    $strHTML= $this->View->showMessage($msg, $urlBack);

                }else{
                    //print mysql_error();
                    $er = "Field tidak berhasil ditambah. Segera hubungi administrator sistem anda.";
                   // $this->close();
                     $strHTML= $this->View->showMessageError($er, $urlBack);
                }


            }else{
                $er = "Seting halaman tidak berhasil di ubah. Segera hubungi administrator sistem anda.";
                //$this->close();
                 $strHTML= $this->View->showMessageError($er, "preview_template.php?id=".$layoutID);
            }
            
                
			$this->close();
			return $strHTML;


        }else{
        	$this->close();
            sendRedirect("modif_template.php");
        }
	}



    function sendTo($flag, $id){
        
        //buat keperluan send email
        
        $this->open();
        $userID = $this->Account->getActiveID();
            
        /*
        if($userID){ //BUAT ADVERTISER SUBMIT FORM LANDING PAGE (yang sedang login) --> di hold dulu
            //mengambil informasi 'landing page' berdasarkan id landing page dan user_id nya
            $q = $this->fetch("SELECT * FROM layout
                                WHERE id='".$_SESSION[layoutID]."' AND user_id='".$userID."' LIMIT 1");


            //mengambil informasi 'landing page custom field' berdasarkan id landing page dan user_id nya
            $q2 = $this->fetch("SELECT * FROM layout_custom_field
                                WHERE layoutID='".$_SESSION[layoutID]."' AND userID='".$userID."' LIMIT 1");
        
        }else{ //BUAT VISITOR SUBMIT FORM LANDING PAGE 
         *
         */
            //mengambil informasi 'landing page' berdasarkan id landing page nya
            $q = $this->fetch("SELECT * FROM layout
                                WHERE id='".$id."' LIMIT 1");


            //mengambil informasi 'landing page custom field' berdasarkan id landing page nya
            $q2 = $this->fetch("SELECT * FROM layout_custom_field
                                WHERE layoutID='".$id."' LIMIT 1");
        
            
        //}

        
        //print_r($q2);
        $page_title = $q['page_title'];
        $emailTo = $q2['email'];
        //print $to;
        $this->close();

        $subject = $page_title." - Respons Landing Page SITTI";

        $message = "Hi,<br>
                    Formulir landing page SITTI Anda telah diisi dan dikirimkan oleh : <br/><br/>";
                    foreach($_POST as $name => $val){
                       $message.=$name." : ".$val."<br/><br/>";

                    }
                     $message.="Regards,<br/>
                                SITTI

                                <br/><br/><br/>
                                *Email ini dibuat secara otomatis oleh SITTI.
                                            Apabila Anda memiliki pertanyaan seputar email ini,
                                            silahkan kirim email ke support@sitti.zendesk.com";
        //print $message;
        //die();
        //kirim email
        $smtp = new SITTIMailer();
        $smtp->setSubject($subject);
        $smtp->setRecipient($emailTo);
        $smtp->setMessage($message);
        //$smtp->send();
        //print $smtp->status;
        //print_r($_SESSION);
        //die();
        if($smtp->send()){
            //$this->View->assign("msg", "SUKSES");
            //return $this->View->showMessage("SUKSES", "index.php?id=".$q['id']);
            print "<script>alert(\"Proses Kirim Email Berhasil, Silahkan Melanjutkan.\")</script>";

            if($flag=='0'){ //setelah visitor berhasil submit form landing page
                sendRedirect("index.php?id=".$id);

            }else{//setelah advertiser berhasil submit form landing page -->hold dulu
                sendRedirect("beranda.php?buat_iklan=1");
                
            }
            
        }else{
            
            print "<script>alert(\"Proses Kirim Email Gagal.\");</script>";

            if($flag=='0'){//setelah visitor berhasil submit form landing page
                sendRedirect("index.php?id=".$id);
                
            }else{//setelah advertiser berhasil submit form landing page -->hold dulu
                sendRedirect("beranda.php?buat_iklan=1");
                
            }
        }
        
        
        
    }




    /*
     * UNTUK MENGAMBIL LANDING PAGE BERDASARKAN id nya
     */
    function getLayout($id){
        return $this->fetch("SELECT * FROM layout WHERE id='".$id."' LIMIT 1");
    }
    /*
     * UNTUK MENGAMBIL LANDING PAGE BERDASARKAN id nya
     */
    function getLayoutByUser($userID,$id){
        return $this->fetch("SELECT * FROM layout WHERE user_id= '".$userID."' AND id='".$id."' LIMIT 1");
    }

    /*
     * UNTUK MENGAMBIL id DARI LANDING PAGE YANG TERAKHIR DI BUAT, BERDASARKAN user_id yang aktif
     */
    function getLayoutLastID($userID){
        return $this->fetch("SELECT * FROM layout WHERE user_id='".$userID."' ORDER BY id DESC LIMIT 1");
    }


    /*
     * CEK LANDING PAGE SEBELUMNYA BERDASARKAN id LANDING PAGE nya
     */
    function checkLayout($id, $userID){
    	
        return $this->fetch("SELECT COUNT(*) AS total, user_id FROM layout
                                WHERE id='".$id."' AND user_id='".$userID."' GROUP BY user_id LIMIT 1");
    }


   



    /*
     * INSERT NEW LAYOUT
     */
    function insertLayout($userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website, $metaAuthor, $content, $logoImg,
                            $bannerImg, $footerText, $headerTitle, $headerSubTitle, $status, $published){

        return $this->query("INSERT INTO layout(user_id, bgcolor, fontcolor, page_title, meta_tags, meta_description ,
                                link_website, meta_author, content,
                                logo_img, banner_img, footer_text,
                                header_title, header_subtitle, created_time, status, published)
                                VALUES('".$userID."', '".$bgColor."', '".$fontColor."', '".$pageTitle."',
                                '".$metaTags."', '".$metaDescription."', '".$link_website."',
                                '".$metaAuthor."', '".$content."', '".$logoImg."',
                                '".$bannerImg."', '".$footerText."',
                                '".$headerTitle."', '".$headerSubTitle."', NOW(), '".$status."', '".$published."')");

    }


    /*
     * INSERT LANDING PAGE CUSTOM FIELD
     */
    function insertLayoutCustomField($layoutID, $userID, $textDefault1, $textDefault2, $text1, $text2, $text3, $email, $published){
        return $this->query("INSERT INTO layout_custom_field(layoutID, userID, defaultField1, defaultField2,
                            additional1, additional2, additional3, email, published)
                            VALUES('".$layoutID."', '".$userID."', '".$textDefault1."', '".$textDefault2."', '".$text1."',
                            '".$text2."', '".$text3."', '".$email."', '".$published."')");
    }



    /*
     * UPDATE LANDING PAGE
     */
    function updateLayout($id, $userID, $bgColor, $fontColor, $pageTitle, $metaTags, $metaDescription, $link_website, $metaAuthor, $content, $logoImg,
                            $bannerImg, $footerText, $headerTitle, $headerSubTitle, $google_analytics, $status, $published){

        return $this->query("UPDATE layout SET bgcolor='".$bgColor."', fontcolor='".$fontColor."',
                                page_title='".$pageTitle."', meta_tags='".$metaTags."',
                                meta_description ='".$metaDescription."', link_website='".$link_website."',
                                meta_author='".$metaAuthor."', content='".$content."',
                                logo_img='".$logoImg."', banner_img='".$bannerImg."', 
                                footer_text='".$footerText."', header_title='".$headerTitle."',
                                header_subtitle='".$headerSubTitle."', created_time=NOW(),
                                google_analytics='".$google_analytics."',
                                status='".$status."', published='".$published."' 
                                WHERE id='".$id."' AND user_id='".$userID."'");

    }

    
    /*
     * UPDATE LANDING PAGE CUSTOM FIELD
     */
    function updateLayoutCustomField($layoutID, $userID, $textDefault1, $textDefault2, $text1, $text2, $text3, $email){
        return $this->query("UPDATE layout_custom_field SET defaultField1='".$textDefault1."', 
                            defaultField2='".$textDefault2."', additional1='".$text1."',
                            additional2='".$text2."', additional3='".$text3."', email='".$email."'
                            WHERE layoutID='".$layoutID."' AND userID='".$userID."'");
    }


    /*
     * UNTUK MENGAMBIL LANDING PAGE CUSTOM FIELD BERDASARKAN layoutID(id landing page) dan user_id nya
     */
    function getLayoutCustomField($layoutID, $userID){
    	
        return $this->fetch("SELECT * FROM layout_custom_field WHERE layoutID='".$layoutID."' AND userID='".$userID."' LIMIT 1");
    }


    
    /**
     *ini buat visitor, diambil berdasarkan layoutID nya, tanpa userID atau login
     * @param <type> $layoutID
     * @return <type>
     */
    function getLayoutCustomField2($layoutID){
        return $this->fetch("SELECT * FROM layout_custom_field WHERE layoutID='".$layoutID."' LIMIT 1");
    }


    function resetSessionLandingPage(){
        //reset session create iklan
        $_SESSION['nama_iklan']="";
        $_SESSION['baris1']="";
        $_SESSION['baris2']="";
        $_SESSION['judulIklan']="";
        $_SESSION['campaignSelected']="";
        $_SESSION['landingSelected']="";
        //print_r($_SESSION);
    }


}
?>
