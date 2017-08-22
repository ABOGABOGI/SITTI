<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:35
         compiled from SITTI/content.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strip_tags', 'SITTI/content.html', 224, false),array('modifier', 'number_format', 'SITTI/content.html', 269, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>SITTI - Platform Iklan Kontekstual Berbahasa Indonesia, Iklan Kontekstual, Bahasa Indonesia</title>
        <link href="css/sitti.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="css/overlay-basic.css">
         <link href="css/ui.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="js/iepngfix_tilebg.js"></script>
        <script type="text/javascript" language="javascript" src="js/drop_table.js"></script>
        <script type="text/javascript" src="js/jquery.js"></script>
        <link type="text/css" href="js/themes/hot-sneaks/ui.all.css" rel="stylesheet" />
		<script type="text/javascript" src="js/iepngfix_tilebg.js"></script>
        <script type="text/javascript" src="js/drop_table.js"></script>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/ui/ui.core.js"></script>
        <script type="text/javascript" src="js/ui/ui.datepicker.js"></script>
        <script type="text/javascript" src="js/ui/i18n/ui.datepicker-id.js"></script>
        <script type="text/javascript" src="js/flash.js"></script>
		<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
        <script type="text/javascript" src="js/jquery.metadata.js"></script> 
        <link rel="stylesheet" href="css/popup.css" type="text/css" media="screen" />
		<script src="js/popup.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery.validate.min.js"></script>
		<script src="js/jquery_swfobject.js" language="javascript1.1"></script>
        <script language="javascript" src="js/modal.popup.js"></script>
   		
        <?php echo '
        	
		<script type="text/javascript"> 
          
          </script>
		<script type="text/javascript">
		
          $(document).ready(function(){
            $(".closeDialog").click(function(){
				$("#kotakdialog").hide("slow");
			});
          });
		 
        </script>
		<script type="text/javascript">
            $(document).ready(function() {	
                //When page loads...
                $(".tab_content").hide(); //Hide all content
                $("ul.tabjs li:first").addClass("active").show(); //Activate first tab
                $(".tab_content:first").show(); //Show first tab content
				
				
            
            });
            $(document).ready(function(){
                $("#tanggalAwalBeranda,#tanggalAkhirBeranda,#tanggalAwalAkun,#tanggalAkhirAkun,#tanggalAwalPenempatan,#tanggalAkhirPenempatan,#tanggalAwalKatakunci,#tanggalAkhirKatakunci,#tanggalAwalTujuan,#tanggalAkhirTujuan,#tanggalAwalPeforma,#tanggalAkhirPeforma,#tanggalAwalGeografis,#tanggalAkhirGeografis,#tanggalAwalPenagihan,#tanggalAkhirPenagihan").datepicker();
            });
            function onPhraseClicked(obj){
				//alert(obj.value);
				$("input[name=keywords\\\\[\\\\]]").removeAttr("checked");
				$("input[name=keywords\\\\[\\\\]]").attr("disabled","true");
            }
			//This method hides the popup when the escape key is pressed
			$(document).keyup(function(e) {
				if (e.keyCode == 27) {
					closePopup(fadeOutTime);
				}
			});
        </script>
'; ?>

 <?php echo '
            <script>
			//script Popup
		function showOption(option) {
            
		//Change these values to style your modal popup
		var align = \'center\';									//Valid values; left, right, center
		var top = 100; 											//Use an integer (in pixels)
		var width = 500; 										//Use an integer (in pixels)
		var padding = 10;										//Use an integer (in pixels)
		var backgroundColor = \'#FFFFFF\'; 						//Use any hex code
		var source = \'popup_wizard.php?option=\'+option; 								//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
		var borderColor = \'#ffffff\'; 							//Use any hex code
		var borderWeight = 1; 									//Use an integer (in pixels)
		var borderRadius = 20; 									//Use an integer (in pixels)
		var fadeOutTime = 300; 									//Use any integer, 0 = no fade
		var disableColor = \'#666666\'; 							//Use any hex code
		var disableOpacity = 40; 								//Valid range 0-100
		var loadingImage = \'images/loading.gif\';		//Use relative path from this page
			
		//This method initialises the modal popup
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, source, loadingImage);
    }
			</script>
            <SCRIPT LANGUAGE="JavaScript">
			//radio link
var myOption = false
function initValue() {
    myOption = document.forms[0].songs[3].checked
}
 function fullName(form) {
	var link
    for (var i = 0; i < form.songs.length; i++) {
        if (form.songs[i].checked) {
			link = form.songs[i].value;
            break
        }
    }
    window.location = link;
}
function setShemp(setting) {
    myOption = setting
}
function exitMsg() {
    if (myOption) {
        alert("You like My Option?")
    }
}
</SCRIPT>
            '; ?>

        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="icon" type="image/gif" href="images/animated_favicon.gif">
		<link rel="stylesheet" type="text/css" href="js/passwordStrengthMeter.css" />
    </head>

    <body onload="init()" style="background:url(images/bg_main.png) repeat-x #ECECEC;">
      <?php if ($this->_tpl_vars['takeOut']): ?><div class="cLabel"><a href="http://www.sitti.co.id/UtakAtik/index.html"></a></div><?php endif; ?>
	 <?php if ($this->_tpl_vars['broadcast']): ?>
	
     <div id="kotakdialog" title="Pesan Hari Ini">
     	<div class="isiDialog">
       <?php echo $this->_tpl_vars['broadcast']; ?>

        </div>
	<a class="closeDialog" onmouseover="tooltip.show('Tutup Kotak Dialog');" onmouseout="tooltip.hide();"  href="#">&nbsp;</a>
    </div>
	<?php endif; ?>
    <table  border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
        <div class="wrapper">
			
            <div class="header">
             <?php if ($this->_tpl_vars['isLogin']): ?>
                <a class="logo" href="beranda.php">&nbsp;</a>
                <a class="slogan" href="beranda.php">&nbsp;</a>
                             <?php else: ?>
                <a class="logo" href="beranda.php">&nbsp;</a>
                <a class="slogan" href="beranda.php">&nbsp;</a>
                <a class="bahasa" href="?lang=en">>> Can't speak Bahasa? Click here for English!</a>
             <?php endif; ?>
            </div>
<!--              <?php if ($this->_tpl_vars['msg']): ?>
            <div class="messageBoxLogin" align="right">
              
               
                <span class="sukses"><?php echo $this->_tpl_vars['msg']; ?>
</span> 
                <?php elseif ($this->_tpl_vars['er']): ?>
                
                <span class="error">Anda tidak berhasil masuk. Periksa nama akun dan kata sandi anda.</span>
                
               
            </div>
             <?php endif; ?>-->
            <?php if ($this->_tpl_vars['isLogin']): ?>
			<div class="navigationBarAllLogin">
			<div class="navigationRight" style="position:relative;">
                <ul class="menu navigation" id="menu">
                    <li  style="margin:0 29px 0 0"><a class="dropmenu" href="profile.php">Profile Saya</a>
                    	<ul>
                            <li><a href="profile.php?modify=1">Ganti Profil</a>
    <!--                            <ul>
                                    <li><a href="profile.php?bank=1">Daftar Rekening Bank</a></li>
                                    <li><a href="profile.php?cc=1">Daftar Kartu Kredit</a></li>
                                </ul>-->
                            </li>
                            <li><a href="refer.php">Refer Teman</a></li>
                  			<li><a href="pembayaran.php">Pembayaran</a></li>
                  			<li><a href="pembayaran.php?mutasi=true">Mutasi Rekening</a></li>
                            <li><a href="refer_report.php">Laporan Referral</a></li>
                        </ul>
                    </li>
                    <li  style="margin:0 29px 0 0"><a href="beranda.php">Laporan</a></li>
                    <li  style="margin:0 29px 0 0"><a href="pembayaran.php">Top Up</a></li>
                    <li  style="margin:0 29px 0 0"><a target="_blank" href="http://sitti.zendesk.com/anonymous_requests/new">Kontak</a></li>
                </ul>
                <script type="text/javascript">var menu=new menu.dd("menu");menu.init("menu","menuhover");</script>
             
             <a class="btnKeluar" href="index.php?logout=1"></a>
             <a target="_blank" href="http://twitter.com/sittiID" class="twitterAtas" alt="http://twitter.com/sittiID"></a>
             <a target="_blank" href="http://www.facebook.com/SITTI.ID" class="fbAtas" alt="http://www.facebook.com/SITTI.ID"></a>
            
             </div>
			
            </div>
            <?php else: ?>
            
            <div class="navigationBarAll">
              <div class="navigationRight">
                <ul class="navigation">
                    <li><a href="tentang_kami.php">Tentang</a></li>
                    <li><a href="uji_sitti.php">Simulasi</a></li>
                     <li><a href="career.php">Karir</a></li>
                    <li><a target="_blank" href="http://sitti.zendesk.com/anonymous_requests/new">Kontak Kami</a></li>
                </ul>
                   <a class="daftarSITTI" href="index.php?registration=1">Daftar di SITTI</a>
                    <form class="regForm" action="<?php echo $this->_tpl_vars['LOGIN_URL']; ?>
" method="post" enctype="application/x-www-form-urlencoded">
                  <input name="username" type="text" value="E-mail Anda" onfocus="blank(this)" onblur="unblank(this)" autocomplete="off">
                    <input name="password" type="password" value="Kata Sandi" onfocus="blank(this)" onblur="unblank(this)" autocomplete="off">
                    <input name="role" type="hidden" value="1" />
                    <input type="submit" value="&nbsp;" />
                   </form>
                    
               </div>
            </div>
            
           
            <?php endif; ?>
            <?php if ($this->_tpl_vars['isLogin']): ?>
         <div class="topUserMenu">
         	<div class="topUserMenu-right">
         	
         	<div class="tum_info">
         	<span class="namaPemilik">Nama Pemilik Akun</span>
         	<span class="namaUser"><?php echo ((is_array($_tmp=$this->_tpl_vars['info']['name'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</span>
         	<span class="namaUser"><?php echo $this->_tpl_vars['info']['sittiID']; ?>
</span>
         	</div>  	
         	<?php if ($this->_tpl_vars['is_ppa'] || ! $this->_tpl_vars['is_cpm']): ?><a class="tum_bantuan" href="faq.php"></a><?php endif; ?>
            <a class="tum_bugdet" href="uji_sitti2.php"></a>
         	<a class="tum_buat" href="javascript:showOption('a')"></a>
            <a class="tum_kampanye" href="javascript:showOption('c')"></a>
         	<a class="tum_notifikasi" href="<?php if ($this->_tpl_vars['is_cpm']): ?>beranda.php?buat_campaign=1<?php else: ?>beranda.php?notifikasi=true<?php endif; ?>"></a>
            
         	
         	</div>
         </div>
         <?php endif; ?>   
         <div class="blockContent">   
            <div class="content">
     <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">
                <div class="colLeft">
                    <?php echo $this->_tpl_vars['mainContent']; ?>

                </div>
                <?php if ($this->_tpl_vars['contentID'] == '1' || $this->_tpl_vars['contentID'] == '2' || $this->_tpl_vars['contentID'] == '3'): ?>
                <div class="w242">
                    <ul class="navUser">
                        <li><a href="index.php?id=1">Latar Belakang</a></li>
                        <li><a href="index.php?id=2">Visi dan Misi</a></li>
                        <li><a href="index.php?id=3">Video Presentasi</a></li>
                    </ul>
                </div>
                <?php endif; ?>
				<!--  colom kanan beranda -->
		</td>
		<?php if ($this->_tpl_vars['takeOut']): ?>
          <td valign="top">
                <?php if ($this->_tpl_vars['isLogin']): ?>
                <div class="w242">
                    <div class="userSitti">
                        <span class="namaPemilik">Nama Pemilik Akun</span>
                        <span class="namaUser"><?php echo ((is_array($_tmp=$this->_tpl_vars['info']['name'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)); ?>
</span>
                    </div>
                    <div class="idUser">
                      <span class="f14">SITTI ID :</span><span> <?php echo $this->_tpl_vars['info']['sittiID']; ?>
</span>
                    </div>
                    <div class="saldoAnda">
					  <span>Saldo Anda</span>
                      <span class="f18 green bold">Rp. <?php echo ((is_array($_tmp=$this->_tpl_vars['SALDO'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
,-</span>
                    </div>
                    <?php if ($this->_tpl_vars['isFree']): ?>
                    <div class="saldoAnda">
                      <span class="f18 red bold">PROMO GRATIS SITTI</span>
                    </div>
                    <?php endif; ?>
                    <a class="campaignBaru" href="beranda.php?buat_campaign=1">&nbsp;</a>
                    <a class="iklanBaru" href="buat.php<?php if ($this->_tpl_vars['is_ppa']): ?>?ad_ppa=true<?php endif;  if ($this->_tpl_vars['is_cpm']): ?>?ad_banner=true<?php endif; ?>">&nbsp;</a>
                    <a class="manajemenIklan" href="beranda.php?PerformaIklan">&nbsp;</a>
                    <a class="lihatLaporan" href="beranda.php">&nbsp;</a>
                 <a class="pembayaran" href="beranda.php?InformasiPenagihan">&nbsp;</a>
                    <a class="editProfil" href="profile.php?modify=1">&nbsp;</a>
                    <a class="newsletter" href="subscribe.php">&nbsp;</a>
                    <div class="sideBox">
               		    <h3> Bingung Menggunakan Sitti?</h3>
                        Kirimkan pertanyaan, komentar, masukan atau masalah yang Anda hadapi pada proses pendaftaran melalui surat elektronik di bawah ini :<a href="mailto:support@sitti.zendesk.com">support@sitti.zendesk.com</a>   
					</div>
                 <!--   <div class="help">
                   <h3>	Bantuan SITTI</h3>
                        <ul>
                         <li>Saat ini belum ada pertanyaan</li>
                        </ul>
                      <ul>
                         <li><a href="#">Bagaimanakah cara mengetahui berapa penghasilan saya?</a></li>
                        <li><a href="#">Saya mempunyai blog wordpress, apakah SITTI memiliki plugin khusus?</a></li>
                        <li><a href="#">Apa keuntungan yang saya dapat  dengan refer teman?</a></li>
                        <li><a href="#">Bagaimana cara merubah data rekening saya? </a></li>
                        </ul>
                    </div>-->
                </div>
                <!--  colom kanan beranda end -->
               <?php else: ?>
               <!--  colom kanan -->
				<div class="w242">
                	<a class="newsletter" href="#">&nbsp;</a>
                    <div class="sideBox">
               		    <h3> Bingung Menggunakan Sitti?</h3>
                        Kirimkan pertanyaan, komentar, masukan atau masalah yang Anda hadapi pada proses pendaftaran melalui surat elektronik di bawah ini :<a href="mailto:support@sitti.zendesk.com">support@sitti.zendesk.com</a>           
					</div>
                </div>
                <?php endif; ?>
                </td>
        <?php endif; ?>
        </tr>
      </table>
                 <div class="bottomContent"></div>
            </div>
        </div>    
        </div>
   </td>
  </tr>
</table>
        <div class="footer">
            <div class="wrapper">
                <ul class="navFoot">
                    <li><a href="tentang_kami.php">Tentang Kami</a>|</li>
                    <li><a href="term.html" target="_blank"> Syarat dan Ketentuan</a>|</li>
                    <li><a href="privasi.html" target="_blank">Kebijakan Privasi</a>|</li>
                    <li><a href="http://blog.belajarsitti.com/" target="_blank">Blog</a>|</li>
                    <li><a href="http://sittizen.sitti.co.id/" target="_blank"> Jadi SITTIZEN</a>|</li>
                    <li><a href="http://sitti.zendesk.com/anonymous_requests/new" target="_blank">Kontak Kami</a></li>
                </ul>
                <p class="copyRight">
                    Email: beriklan@sitti.zendesk.com<br />
                    © 2012 SITTI
                </p>
            </div>
        </div>
		        <?php echo '
		 <script type="text/javascript">
        function blank(a) { if(a.value == a.defaultValue) a.value = ""; }
        function unblank(a) { if(a.value == "") a.value = a.defaultValue; }
        </script> 

        <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-16501037-1\']);
  _gaq.push([\'_setDomainName\', \'sitti.co.id\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' :
\'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0];
s.parentNode.insertBefore(ga, s);
  })();

</script>

'; ?>

    </body>