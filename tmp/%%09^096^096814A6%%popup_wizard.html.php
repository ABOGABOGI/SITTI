<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:56
         compiled from SITTI/popup_wizard.html */ ?>
<html>
<head>
<title>Pop up Wizard</title>
<link href="css/sitti.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/overlay-basic.css">
<link href="css/ui.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="js/iepngfix_tilebg.js"></script>
<script type="text/javascript" language="javascript" src="js/drop_table.js"></script>
 <script type="text/javascript" src="js/jquery.js"></script>
<link type="text/css" href="js/themes/hot-sneaks/ui.all.css" rel="stylesheet" />
<?php echo '
<style>
#popup_wizard
{
	text-align:left;
}
.pilihan{
	margin-left:65px;
}
</style>
'; ?>

</head>
<body>
<h3 class="blue" style="margin-bottom:20px; font-size: 16px; margin-top: 20px;">Silahkan pilih mode pembuatan Iklan/ Kampanye Anda</h3>
<!--form>
  <input type="radio" name="radio" id="test" value="wizard" />
  <label for="test">Mode Wizard</label>
  <input type="radio" name="radio" id="test" value="advance" />
  <label for="test">Mode Advance</label>
</form-->
<div id="popup_wizard">
  <div class="pilihan">
<FORM>
<p style="font-size:15px;">
  <strong>
  <INPUT TYPE="radio" NAME="songs" VALUE="kampanye_wizard.php" onClick="setShemp(false)" checked>
  Simple Mode
  </strong></p>
<p style="color:#999; margin-left:26px; margin-top:-15px; font-size:11px;">Cara cepat dan mudah untuk memulai Kampanye/Iklan SITTI</p>
<p style="font-size:15px;">
<INPUT TYPE="radio" NAME="songs" VALUE="<?php if ($this->_tpl_vars['option'] == 'c'): ?>beranda.php?buat_campaign=1<?php endif;  if ($this->_tpl_vars['option'] == 'a'): ?>buat.php<?php endif; ?>" onClick="setShemp(false)">
<strong>Advance Mode</strong></p>
<p style="color: #999;; margin-left:26px; margin-top: -15px;font-size:11px;">Untuk Anda yang ingin lebih leluasa membuat Kampanye/Iklan Anda</p>

<p>
<input type="button" style="width:64px; height:28px; margin-left:20px; margin-top: 10px;" value="Lanjut" class="submitButton" onClick="fullName(this.form)">
</p>
</FORM>

  </div>
<div>
</body>
</html>