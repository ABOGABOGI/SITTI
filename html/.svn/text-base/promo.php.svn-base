<?php 
include_once "common.php";
include_once $APP_PATH."SITTI/SITTIBilling.php";
include_once $APP_PATH."SITTI/SITTIMailer.php";
include_once $ENGINE_PATH."Utility/CaptchaUtil.php";
include_once $APP_PATH."SITTI/SITTIPromo.php";

global $CONFIG;
$billing = new SITTIBilling($req, $account);
$view = new BasicView();
$promo = new SITTIPromo();
$ref = intval($_REQUEST['ref']);
if ($req->getParam('p'))
{
	$p = $req->getParam('p');
	$params = unserialize(base64_decode($p));
	if (is_array($params) && array_key_exists('email', $params) && array_key_exists('code', $params))
	{
		$promo->writeEmailTrack($params['email'], $params['code']);
		sendRedirect('index.php?registration=1');
	}
}
else
{
	$message = '';
	/*if ($req->getParam("act") == 'promo')
	{*/
		$captcha = new CaptchaUtil($CONFIG['SITTIBELAJAR_CAPTCHA_PUBLIC'], $CONFIG['SITTIBELAJAR_CAPTCHA_PRIVATE']);
		$captcha_verified = false;
		
		if ($req->getPost("submit_promo"))
		{
			$captcha_verified = $captcha->verify();

			if ($captcha_verified)
			{
				$nama = htmlentities(trim($req->getPost("nama")));
				$email = htmlentities(trim($req->getPost("email")));
				$telp = htmlentities(trim($req->getPost("telp")));
				$event = $req->getPost("event");

				if (($nama != '' && $email != '' && $telp != ''))
				{
					if (! $billing->isHaveRedeemCode($email,$event))
					{
						if ($voucher_code = $billing->setRedeemCode($event, $email, $nama, $telp, 100000,$ref))
						{
							// create hashed parameter
							$params = base64_encode(serialize(array('email' => $email, 'code' => $voucher_code)));
						//print $smtp->status;
							// kirim email yang berisi voucher_code
							$smtp = new SITTIMailer();
				            $smtp->setSubject("[SITTI] Anda mendapatkan voucher dari kami");
							$smtp->setRecipient($email);
							$view->assign("nama",$nama);
							$view->assign("voucher_code", $voucher_code);
							$view->assign("url", $CONFIG['WebsiteURL']."promo.php?p=".$params."&ref=".$ref);
							$smtp->setMessage($view->toString("SITTI/email/promo_voucher.html"),$view->toString("SITTI/email/promo_voucher_text.html"));
							$smtp->send();

							$message = "Selamat! Anda berhak mendapat e-voucher perdana SITTI359 senilai Rp 100.000. E-voucher ini dapat Anda gunakan untuk beriklan dengan efektif menggunakan SITTI359.<br /><br />Kode e-voucher akan dikirimkan ke alamat email Anda.";
						}
						else
						{
							$message = "Mohon maaf, Anda belum berhasil mendapatkan e-voucher perdana SITTI359. E-voucher perdana SITTI359 telah habis.";
						}
					}
					else
					{
						$message = 'Mohon maaf, Anda belum berhasil mendapatkan e-voucher perdana SITTI359. Data diri yang Anda gunakan sudah terpakai.';
					}
				}
				else
				{
					$message = 'Anda tidak mengisi form dengan benar.';
				}
			}
			else
			{
				$message = 'Captcha anda salah';	
			}
			
			if ($message != '')
			{
				$view->assign("mainContent", $view->showMessage($message, "promo.php?ref=".$ref));
			}	
		}
		else
		{
			$promo->writeDBLog($_SERVER["REMOTE_ADDR"], 'promo.php');
			$view->assign("ref_id",$ref);
			$view->assign('captcha', $captcha->getHtml());
			$view->assign("mainContent", $view->toString("SITTI/promo.html"));
		}
	/*}
	else
	{
		$promo->writeDBLog($_SERVER["REMOTE_ADDR"], 'promo.php');
		$view->assign("mainContent", $view->toString("SITTI/index_promo.html"));
	}*/

	$view->assign("LOGIN_URL",$CONFIG['LOGIN_URL_2']);
	echo $view->toString("SITTI/content4.html");
}
	
?>