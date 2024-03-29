<?php /* Smarty version 2.6.13, created on 2012-05-28 12:00:28
         compiled from SITTI/index.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'SITTI/index.html', 191, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>SITTI - Platform Iklan Kontekstual Berbahasa Indonesia, Iklan Kontekstual, Bahasa Indonesia</title>
        <link href="css/sitti.css" rel="stylesheet" type="text/css" />
		
        <script type="text/javascript" src="js/iepngfix_tilebg.js"></script>
        <script type="text/javascript" src="js/drop_table.js"></script>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/flash.js"></script>
        <script src="js/jcarousellite_1.0.1c4.js" type="text/javascript"></script> 
		<?php echo '
          
         
          
		<script type="text/javascript">
        
        /*** 
            Simple jQuery Slideshow Script
            Released by Jon Raasch (jonraasch.com) under FreeBSD license: free to use or modify, not responsible for anything, etc.  Please link out to me if you like it :)
        ***/
        
        function slideSwitch() {
            var $active = $(\'#slideshow IMG.active\');
        
            if ( $active.length == 0 ) $active = $(\'#slideshow IMG:last\');
        
            // use this to pull the images in the order they appear in the markup
            var $next =  $active.next().length ? $active.next()
                : $(\'#slideshow IMG:first\');
        
            // uncomment the 3 lines below to pull the images in random order
            
            // var $sibs  = $active.siblings();
            // var rndNum = Math.floor(Math.random() * $sibs.length );
            // var $next  = $( $sibs[ rndNum ] );
        
        
            $active.addClass(\'last-active\');
        
            $next.css({opacity: 0.0})
                .addClass(\'active\')
                .animate({opacity: 1.0}, 1000, function() {
                    $active.removeClass(\'active last-active\');
                });
        }
        
        $(function() {
            setInterval( "slideSwitch()", 5000 );
        });
        
        </script>
		<script type="text/javascript">
        $(function() {
            $(".newsticker-jcarousellite").jCarouselLite({
                vertical: true,
                hoverPause:true,
                visible: 1,
                auto:2000,
                speed:1000
            });
        });
        </script>
		<script type="text/javascript">
		
          $(document).ready(function(){
            $(".closeDialog").click(function(){
				$("#kotakdialog").hide("slow");
			});
          });
		 
        </script>
        '; ?>

        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="icon" type="image/gif" href="images/animated_favicon.gif">
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
        <div class="wrapperIndex">
            <div class="header">
                <a class="logo" href="beranda.php">&nbsp;</a>
                <a class="slogan" href="index.php">&nbsp;</a>
                            </div>
            
            <div class="top">
                <div>
                    <p>SITTI359 adalah sebuah platform iklan kontekstual berbahasa Indonesia yang mampu menjelajah situs/blog berbahasa Indonesia. Lewat SITTI359  iklan Anda akan ditempatkan di situs berbahasa Indonesia yang isinya sesuai dengan pesan iklan/produknya (relevan), sehingga iklan tersebut dapat dibaca oleh orang yang mengerti isi dan bahasa iklan Anda pada saat yang tepat (akurat), mudah diukur dan dapat dipertanggung jawabkan.
</p>
                </div>
            </div>
             <?php if ($this->_tpl_vars['msg']): ?>
            <div class="messageBoxLogin" align="right">
               
               
                <span class="sukses"><?php echo $this->_tpl_vars['msg']; ?>
</span> 
                <?php elseif ($this->_tpl_vars['er']): ?>
                
                <span class="error">Anda tidak berhasil masuk. Periksa nama akun dan kata sandi anda.</span>
                
               
            </div>
             <?php endif; ?>
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
            <div class="navigationBarHome">
                <ul class="navigation">
                    <li><a href="tentang_kami.php">Tentang</a></li>
                    <li><a href="uji_sitti.php">Simulasi</a></li>
                     <li><a href="career.php">Karir</a></li>
                    <li><a target="_blank" href="http://sitti.zendesk.com/anonymous_requests/new">Kontak Kami</a></li>
                </ul>
                    <form style="float:left;" class="regForm" action="<?php echo $this->_tpl_vars['LOGIN_URL']; ?>
" method="post" enctype="application/x-www-form-urlencoded">
                	<input name="username" type="text" value="E-mail Anda" onfocus="blank(this)" onblur="unblank(this)" autocomplete="off">
                    <input name="password" type="password" value="Kata Sandi" onfocus="blank(this)" onblur="unblank(this)" autocomplete="off">
                    <input type="submit" value="&nbsp;" />
                    <input name="role" type="hidden" value="1" />
               		 </form>
                    <a class="daftarSITTI" style="float:left;" href="index.php?registration=1">Daftar di SITTI</a>
               
            </div>
            <?php endif; ?>
            <div class="container">
                <div class="blockLeft">
                    <div class="block1">
                        <a href="index.php?registration=1"><h1 style="font-size:18px; margin:10px 0 5px 0">Beriklan Lewat SITTI359</h1></a>
                        <p style="margin:10px 0;">Berapapun anggaran belanja iklan Anda, Anda tetap dapat beriklan di Sitti dan jaringan periklanan kami karena Anda hanya membayar berdasarkan jumlah 'klik' yang dihasilkan oleh iklan Anda.
                        </p>
                        <a class="btnDaftarHome" href="index.php?registration=1">&nbsp;</a>
                    </div>
                    <div class="block2">
                        <a href="https://sittizen.sitti.co.id/daftar.php"><h1 style="font-size:18px;"> Uji Coba SITTIZEN Publisher Network</h1></a>
                        <p>Ingin mendapatkan penghasilan tambahan dari website atau blog Anda?</p>
                        <a class="btnDaftarSittizenHome" href="https://sittizen.sitti.co.id/daftar.php">&nbsp;</a>
                    </div>
                    <div class="block3">
                        <a href="index.php?registration=1"><h1 style="font-size:18px; margin:10px 0 5px 0">Bingung menentukan anggaran?</h1></a>
                        <p style="margin:10px 0;">
                            Gunakan penghitung kampanye SITTI!
                            Kini Anda dapat mensimulasikan kampanye 
                            dengan sarana perkiraan biaya iklan SITTI
                        </p>
                        <a class="btnKlikHome" href="uji_sitti.php">&nbsp;</a>
                    </div>
                </div>


                <div class="blockRight">
                    <div class="topRight">
                        <h1 style="margin-top:10px; font-size:20px;">Menjelajah 1 Juta Lebih Halaman Situs</h1>
                        <p>Bayangkan! Iklan Anda secara otomatis akan menjelajah <?php echo ((is_array($_tmp=$this->_tpl_vars['PAGE_SERVED'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 juta halaman situs dan blog berbahasa Indonesia, menggapai massa dan langsung berkomunikasi dengan sasaran pasar berbahasa Indonesia.</p>
                    </div>
                	<div class="video">
                    	<div id="slideshow">
                            <img src="images/new/slide/image2.jpg" alt="Slideshow Image 1" class="active" />
                            <img src="images/new/slide/image3.jpg" alt="Slideshow Image 2" />
                            <img src="images/new/slide/image4.jpg" alt="Slideshow Image 3" />
                            <img src="images/new/slide/image5.jpg" alt="Slideshow Image 4" />
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="socialBar">
              <a class="blogSITTI" href="http://blog.belajarsitti.com" target="_blank">SITTI Blog</a>
              <a class="bantuanSITTI" href="http://sitti.zendesk.com/anonymous_requests/new" target="_blank">Bantuan SITTI</a>
              <a class="twitter" href="http://twitter.com/sittiID" target="_blank">Ikuti kicauan kami di Twitter</a>
<!--              <a class="fblike">
            <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.sittibelajar.com%2F&amp;layout=button_count&amp;show_faces=true&amp;width=80&amp;action=like&amp;font=trebuchet+ms&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
            </a>-->
       		</div>
        </div>
        <div class="testimoni">
        	<div class="testiText">
                <div class="newsticker-jcarousellite">
                    <ul>
                        <li>
                            <p>Helo SITTI Juwariyah… <br />
aku baru pasang iklanmu di blogku loh.. jangan lupa kasih honor yang gede ya… hehehe…
                            </p>
                            <p class="testiName">
                            Ponco - Blogger
                            </p>
                        </li>
                        
                        <li>
                            <p>Betul Berjuang terus sitti,
Saya juga lagi siapin senjata buat perang lawan ALIBABA yang mengalir di SUNGAI AMAZON he…
Tetap semangat,dan pesan saya ndak usah head to Head lawan google, kita pakai taktik perang gerilya
Sukses buat sitti
                            </p>
                            <p class="testiName">
                            Any - Blogger
                            </p>
                        </li>
                        <li>
                            <p> “Thanks SITTI utk IPADnya yaaa :),, 
                            seneng deh *meski masih katrok pake’nya* hehehe, moga SITTI tampil paling depan dalam hal advertising
                            supaya kami sebagai advertiser juga publisher mendapatkan pelayanan yang memuaskan,, trussss…
                            SITTI yang rajin yaa bagi-bagi hadiah hehehe.”
                            </p>
                            <p class="testiName">
                            Dian - Blogger
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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
                    © 2010 SITTI
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
</html>