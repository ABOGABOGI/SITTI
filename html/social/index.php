<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTISocial.php";

$view = new BasicView();
$social = new SITTISocial($req);

$view->assign("meta",$view->toString("SITTI/social/meta.html"));
$view->assign("header",$view->toString("SITTI/social/header.html"));
$view->assign("topmenu",$view->toString("SITTI/social/topmenu.html"));
$view->assign("main",$social->InitializeMain());
$view->assign("footer",$view->toString("SITTI/social/footer.html"));

print $view->toString("SITTI/social/index.html");
?>