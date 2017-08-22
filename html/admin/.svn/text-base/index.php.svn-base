<?php
/**
* ADMINISTRATION PAGE
* @author Hapsoro Renaldy N <hapsoro.renaldy@winixmedia.com>
*/

include_once "common.php";
//header('Pragma: public');        
//header('Cache-control: private');
//header('Expires: -1');
$view = new BasicView();

$admin = new Admin();
//$admin->DEBUG=true;
//assign sections
if($admin->auth->isLogin()){
	switch($req->getRequest("s")){
		case "page":
			include_once $APP_PATH."StaticPage/StaticPage.php";
			$admin->execute(new StaticPage($req),"static");
		break;
		case "splash":
			include_once $APP_PATH."BSI/SplashScreen.php";
			
			$admin->execute(new SplashScreen($req),"splash");
		break;
		
		case "admin":
			include_once $APP_PATH."Admin/Admin.php";
			$admin->execute(new AdminConfig($req),"admin");
		break;
		case "article":
			include_once $APP_PATH."Article/Article.php";
			$admin->execute(new Article($req),"article");
		break;
		case "builder":
			include_once $APP_PATH."Builder/Builder.php";
			$admin->execute(new Builder($req),"builder");
		break;


        //link was here 18-05-2010

        //start group management
        case "groupLanding";
            include_once $APP_PATH."Avo_Group_Manager/Avo_Group_Landing.php";
            $admin->execute(new Avo_Group_Landing($req),"groupLanding");
        break;

        case "acArticleGroup":
            include_once $APP_PATH."Avo_Article_Manager/Avo_Content_Article_Group.php";
            $admin->execute(new Avo_Content_Group($req),"acArticleGroup");
        break;

        case "acMediaGroup":
            include_once $APP_PATH."Avo_Group_Manager/Avo_Group_Media.php";
            $admin->execute(new Avo_Group_Media($req),"acMediaGroup");
        break;

        case "acGalleryGroup":
            include_once $APP_PATH."Avo_Group_Manager/Avo_Group_Gallery.php";
            $admin->execute(new Avo_Group_Gallery($req),"acGalleryGroup");
        break;
        //end group management

        

        case "acArticle":
            include_once $APP_PATH."Avo_Article_Manager/Avo_Content_Article.php";
            $admin->execute(new Avo_Content_Article($req),"acArticle");
        break;

        case "acaComments":
            include_once $APP_PATH."Avo_Article_Manager/Avo_Content_Article_Comments.php";
            $admin->execute(new Avo_Content_Article_Comments($req),"acaComments");
        break;

        case "amManagerContent":
            include_once $APP_PATH."Avo_Media_Manager/Avo_Media_Manager.php";
            $admin->execute(new Avo_Media_Manager($req),"amManagerContent");
        break;

        case "pictureGallery": 
            include_once $APP_PATH."Avo_Picture_Gallery/Avo_Picture_Gallery.php";
            $admin->execute(new Avo_Picture_Gallery($req),"pictureGallery");
        break;

        case "picture":
            include_once $APP_PATH."Avo_Picture/Avo_Picture.php";
            $admin->execute(new Avo_Picture($req),"picture");
        break;
        
        

        //////////////////////////////

		default:
			//$view->assign("mainContent","dashboard");
			//load Plugin
			if($req->getRequest("s")!=NULL){
				$plugin = $admin->loadPlugin(&$req,$req->getRequest("s"));
				//print_r($plugin);
				if($plugin){
					$admin->execute($plugin,$req->getRequest("s"));
				}
			}else{
				//or load dashboard if there's no request specified.
				$admin->showDashboard();
			}
		break;
	}
}
//assign content to main template
$admin->show();
$view->assign("mainContent",$admin->toString());
//output the populated main template
print $view->toString($MAIN_TEMPLATE);
?>