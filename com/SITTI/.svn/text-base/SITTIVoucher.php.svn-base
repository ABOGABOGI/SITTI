<?php
/****
 * Implementasi sistem redeem kode voucher di frontend
 * ada 2 system disini :  
 * 1. yang hanya redeem kode voucher
 * 2. user topup + redeem kode voucher
 * 
 * class ini juga memiliki method untuk menggenerate kode voucher otomatis.
 * kode voucher harus unique.. dan bersifat 1x pakai.
 * kode2 voucher lama akan disimpan sebagai archive.
 */
include_once "SITTIApp.php";
include_once "model/SITTIVoucherHelper.php";
class SITTIVoucher extends SITTIApp{
	var $helper;
	function SITTIVoucher($req,$account){
		parent::SITTIApp($req, $account);
		$this->helper = new SITTIVoucherHelper();
	}
	
	//frontend dispatcher
	function main(){
		
	}
	function generate($type){
		return $this->helper->generateCode($type);
	}
	function validate($kode){
		return $this->helper->ValidateCode($kode,2);
	}
	//admin dispatcher method
	function admin(){
		
	}
	
}
?>