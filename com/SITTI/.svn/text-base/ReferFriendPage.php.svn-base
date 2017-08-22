<?php
class ReferFriendPage {
  var $Account;
  var $Mailer;
  var $Profile;
  var $View;

  function ReferFriendPage($profile, $account, $mailer) {
    $this->Account = $account;
    $this->Mailer = $mailer;
    $this->Profile = $profile;
    $this->View = new BasicView();
  }

  function sendEmail($nama, $email, $pesan) {
    //setSubject
    $this->Mailer->setSubject("Invitation to SITTI");
    //setMessage
    $this->Mailer->setMessage($this->populateMessage($pesan));
    //setRecipient
    $this->Mailer->setRecipient($email);
    //send
    $ret = $this->Mailer->send();
    print $this->Mailer->status; //101 = success/berhasil,
    return $ret;
  }

  function populateMessage($pesan) {
    $view = new BasicView();
    $view->assign("profile", $this->Profile);
    $view->assign("PESAN", $pesan);
    return $view->toString("SITTI/refer_content.html");
  }

  function showForm() {
    return $this->View->toString("SITTI/refer_form.html");
  }

}

?>
