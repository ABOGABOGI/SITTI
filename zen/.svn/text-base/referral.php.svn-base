<?php
include_once "common.php";
include_once $ENGINE_PATH."Utility/SessionManager.php";
include_once $APP_PATH."SITTI/SITTIAccount.php";
include_once $APP_PATH."SITTI/SITTIReferral.php";

$view = new BasicView();

$account = new SITTIAccount(&$req);
$account->open(0);
$isLogin = $account->isLogin(1);
$info = $account->getPublisherProfile();
$account->close();

$referral = new SITTIReferral($req, $account);

$publisher_id = htmlentities($req->getRequest("publisher_id"));
if ($publisher_id && ($referral->isAlreadyJoined($publisher_id)))
{
	$random_number = mt_rand();
	$_SESSION['publisher_referral_token'] = $random_number;
	$publisher_referral = base64_encode(serialize(array("publisher_id" => $publisher_id, "publisher_referral_token" => $random_number)));
	$view->assign('publisher_referral', $publisher_referral);
	$view->assign('publisher_id', $publisher_id);
	echo $view->toString("SITTIZEN/referral_publisher.html");
	exit();
}
else
{
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'index.php';
	header("Location: http://$host$uri/$extra");
}

if($isLogin)
{
	
	switch ($req->getRequest("request_type"))
	{
		case "check_publisher_status" :
			echo json_encode($referral->isAlreadyJoined());
		break;

		case "join_referral_program" :
			echo json_encode($referral->joinProgram());
		break;
	}

}
else
{
	print $view->showMessage($LOCALE['SESSION_EXPIRED'],"index.php");	
}

?>