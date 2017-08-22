Nurbaya API<br />
<br />
<br />
<br />
Register Advertiser
<br />
<form action="nurbaya-register.php" method="get">
	Email: <input type="text" name="email" /><br />
	Nama: <input type="text" name="nama" /><br />
	<input type="hidden" name="key" value="8b4423c11538eae42d0a55d562e24c74"/>
	<input type="submit" value="Go" />
</form>
<br />
<br />
Create Campaign and Ad
<br />
<form action="nurbaya-createad.php" method="get">
	Email: <input type="text" name="email" /><br />
	Nama Produk: <input type="text" name="nama" /><br />
	Deskripsi: <input type="text" name="deskripsi" /><br />
	Total Budget: <input type="text" name="budget" /><br />
	URL Link: <input type="text" name="url" /><br />
	Tanggal Mulai: <input type="text" name="tgl_mulai" /><br />
	Tanggal Berakhir: <input type="text" name="tgl_berakhir" /><br />
	OX Advertiser ID: <input type="text" name="ox_advertiser_id" /><br />
	<input type="hidden" name="key" value="8b4423c11538eae42d0a55d562e24c74"/>
	<input type="submit" value="Submit" />
</form>
<br />
<br />
Retrieve Report
<br />
<form action="nurbaya-report.php" method="get">
	Email: <input type="text" name="email" /><br />
	Tipe: <select name='tipe'>
			<option value="1">Account</option>
			<option value="2">Kampanye</option>
			<option value="3">Iklan</option>
		  </select><br />
	Dari Tanggal: <input type="text" name="tgl_awal" />
	Sampai Tanggal: <input type="text" name="tgl_akhir" /><br />
	<input type="hidden" name="key" value="8b4423c11538eae42d0a55d562e24c74"/>
	<input type="submit" value="Submit" />
</form>
<br />
<br />
Get Current Saldo
<br />
<form action="nurbaya-saldo.php" method="get">
	Email: <input type="text" name="email" /><br />
	<input type="hidden" name="key" value="8b4423c11538eae42d0a55d562e24c74"/>
	<input type="submit" value="Submit" />
</form>