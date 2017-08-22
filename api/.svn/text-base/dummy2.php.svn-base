Onigi API<br />
<br />
<br />
<br />
Check Credential
<br />
<form action="" method="get">
	Email: <input type="text" name="email" /><br />
	Password: <input type="password" name="password" /><br />
	<input type="hidden" name="key" value="c01864402a311265679c843f76b94b02"/>
	<input type="submit" value="Go" />
</form>
<br />
<br />
Register Advertiser
<br />
<form action="" method="get">
	Email: <input type="text" name="email" /><br />
	Nama: <input type="text" name="nama" /><br />
	<input type="hidden" name="key" value="c01864402a311265679c843f76b94b02"/>
	<input type="submit" value="Go" />
</form>
<br />
<br />
Create Campaign and Ad
<br />
<form action="" method="get">
	Email: <input type="text" name="email" /><br />
	Nama Produk: <input type="text" name="nama" /><br />
	Deskripsi: <input type="text" name="deskripsi" /><br />
	Total Budget: <input type="text" name="budget" /><br />
	URL Link: <input type="text" name="url" /><br />
	Tanggal Mulai: <input type="text" name="tgl_mulai" /><br />
	Tanggal Berakhir: <input type="text" name="tgl_berakhir" /><br />
	OX Advertiser ID: <input type="text" name="ox_advertiser_id" /><br />
	<input type="hidden" name="key" value="c01864402a311265679c843f76b94b02"/>
	<input type="submit" value="Submit" />
</form>
<br />
<br />
Retrieve Report
<br />
<form action="" method="get">
	Email: <input type="text" name="email" /><br />
	Tipe: <select name='tipe'>
			<option value="1">Account</option>
			<option value="2">Kampanye</option>
			<option value="3">Iklan</option>
		  </select><br />
	Dari Tanggal: <input type="text" name="tgl_awal" />
	Sampai Tanggal: <input type="text" name="tgl_akhir" /><br />
	<input type="hidden" name="key" value="c01864402a311265679c843f76b94b02"/>
	<input type="submit" value="Submit" />
</form>
<br />
<br />
Get Current Saldo
<br />
<form action="" method="get">
	Email: <input type="text" name="email" /><br />
	<input type="hidden" name="key" value="c01864402a311265679c843f76b94b02"/>
	<input type="submit" value="Submit" />
</form>
<?php
include_once "../engines/functions.php";

$data = array(
	"email"=>"deto_1@sitti.co.id",
	"t"=>"".time()
);

$data = http_build_query($data, '', '&');
$data = urldecode($data);
$p = encrypt_parameter($data);

$data = array(
	"key"=>"c01864402a311265679c843f76b94b02",
	"p"=>$p
);

$data = http_build_query($data, '', '&');
?>
<br />
<br />
Link Login SITTI
<a href='../html/onigi-login.php?<?php echo $data?>'>Login SITTI</a>