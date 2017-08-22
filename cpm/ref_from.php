<?php
/*
 * added by Link 22-09-2010
 * purpose: TRACKING BY (ref_from.php?ref=activity_name)
 */
include_once "common.php";
include_once $ENGINE_PATH."Database/SQLData.php";
$view = new BasicView();
$db = new SQLData(&$req);

$db->open();

$activity_name = $req->getParam("ref"); //get param from url

//check gm_referer by url get ref (ref_from.php?ref=email_blast)
$checkRefByActivity = $db->fetch("SELECT * FROM gm_referer WHERE activity_name='".$activity_name."' LIMIT 1");
$hits_before = $checkRefByActivity['hits'];

if($hits_before!=NULL){ //hits before !=null
    //increment hits, update hits by activity name from url get param
    $hits_new = $hits_before+1; 
    $update = $db->query("UPDATE gm_referer SET hits='".$hits_new."', updated=NOW()
                WHERE activity_name='".$activity_name."'");
    if(!$update){
        ?><script>alert('Proses perhitungan klik tidak dapat dilakukan. Harap konfirmasi ke sistem administrator anda.')</script><?
    }
    

}else{
    //if no activity name before, insert new activity name, hits=1
    $hits_new="1";
    $insert = $db->query("INSERT INTO gm_referer(hits, updated, activity_name)
                            VALUES('".$hits_new."', NOW(), '".$activity_name."')");
    if(!$insert){
        ?><script>alert('Proses perhitungan klik tidak dapat dilakukan. Harap konfirmasi ke sistem administrator anda.')</script><?
    }
    
}

//redirect to
sendRedirect("http://www.belajarsitti.com");

$db->close();

?>