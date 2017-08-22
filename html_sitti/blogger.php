<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SITTI Technology</title>
<link rel="stylesheet" href="css/SITTI_newface.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script> 
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="icon" type="image/gif" href="images/animated_favicon.gif">
</head>

<body>
	 <?php include("header.php"); ?>
	<div id="main-content" class="margin-top-185">
    	<div class="content relative round-5 bg_white" style="padding: 130px 80px 0;width: 800px;">
        	<a class="signup absolute signup2" href="https://sittizen.sitti.co.id/daftar.php" style="left:785px"></a>
            <div class="logo-blog absolute page-blog"></div>
        	<div class="icon-blog absolute page-blog2"></div>
            <div class="text-blog absolute page-blog3">BLOGGER</div>
            <div class="comment">
            	"Sebelum ada SITTI, saya bingung mau meletakkan iklan apa di ribuan website saya, karena rata-rata PPC lokal tidak relevan dan sangat tidak nyambung dengan konten. Itu bisa mengurangi nilai website saya. Sedangkan kalau pakai PPC luar, rawan di-ban. Dengan SITTI, saya bisa lebih mengoptimalkan penghasilan website saya karena nilai kliknya lebih besar dan lebih nyam dari pada PPC lokal lain. Hihihi nyam nyam."
            </div>
            <div class="author-comment">
                    Adryan Fitra<br />
            	<span style="color: #CCCCCC;font-size: 12px;">
                    Adryan.org dan bisnisinter.net 
                </span>
            </div>
            
            <h1>Ngeblog menghasilkan uang</h1>
            <p class="blue" style="font-size:16px;">
            	Hobi nulis dan ngeblog? Dapatkan penghasilan bulanan dari blogmu! Apa yang lebih menyenangkan dibanding melakukan sesuatu yang kita suka dan dibayar? 
            </p>
            
            <table cellpadding="0" cellspacing="0" border="0">
            	<tr valign="top">
                	<td width="45%">
                		<h3>1.Tulisan apa saja akan dibayar* </h3>
                        <p>Mulai dari curhatan galau sampai tulisan berkualitas, Anda bisa dapat penghasilan dari topik cerita apa saja.
							*bila ada iklan yang sesuai</p>
                	</td>
                    <td width="10%"></td>
                	<td width="45%">
                    	<h3>2.Bebas berekspresi dengan bahasa Indonesia</h3>
                        <p>Biasa menulis dengan gaya formal? Atau  lebih suka campur dengan 
bahasa gaul? Bebaskan ekspresimu. 
SITTI akan memaknai tulisanmu dan mencarikan iklan yang sesuai 
agar halaman itu menghasilkan uang.</p>
                    </td>
             	</tr>
                <tr valign="top">
                	<td>
                		<h3>3.Iklan yang muncul sesuai dengan isi blog</h3>
                        <p>Nggak ada lagi yang namanya iklan nggak nyambung. 
Iklan disesuaikan dengan isi halaman maka lebih enak untuk 
dibaca. Iklan juga bisa disetting sesuai nuansa blog Anda.</p>
                	</td>
                    <td></td>
                	<td>
                    	<h3>4.Dapat penghasilan bulanan</h3>
                        <p>Tulis cerita sesuka Anda dan sebanyak yang Anda mau. 
Setiap bulan penghasilan akan ditransfer ke rekening bank Anda.</p>
                    </td>
             	</tr>
            </table>
            <a href="https://sittizen.sitti.co.id/daftar.php" style="background:#8ec231; color: white; text-decoration:none; width:800px; padding: 10px 0; display:block; font-size: 18px;" class="round-5">Tambah Penghasilan Sekarang Juga!</a>
        <div id="feed-form" class="absolute round-5">
                	<div style="position:relative">
                    	<span id="close" style="color: #999999;
    right: 2px;
    top: -22px;
    width: 65px;
    position:absolute;
    font-size:10px;
    cursor:pointer;
}
">CLOSE X</span>
                    </div>
    			 <form action="feedback.php" method="post" enctype="application/x-www-form-urlencoded"> 
    			<input class="round-5 border-no margin-right-15" onfocus="blank(this)" onfocus="unblank(this)" type="text" name="name" value="NAMA" />
                <input class="round-5 border-no margin-right-15" onfocus="blank(this)" onfocus="unblank(this)" type="text" name="email" value="E-MAIL" />
                <textarea class="round-5 border-no margin-right-15" name="saran" cols="30" rows="5"></textarea>
                <input type="submit" value="SEND" name="send" class="round-20 border-no">
                <input type="reset" value="RESET" name="reset" class="round-20 border-no">
                </form>
    		</div>
        </div>
        
    </div>
     <?php include("footer.php"); ?>
    <script>
			$("#feeds").click(function(){
				$("#feed-form").fadeToggle("slow");						  
			});
			$("#close").click(function(){
				$("#feed-form").fadeOut("slow");						  
			});
			
			$("#toc").hover(
				function(){
					$("#toc-choice").stop(true, true).fadeIn("fast");},
				function(){
					$("#toc-choice").stop(true, true).fadeOut("slow");	
				}
			);
			
			$("a.toc-items").hover(
				function(){
					$("#toc-choice").stop(true, true).fadeIn("fast");},
				function(){
					$("#toc-choice").stop(true, true).fadeOut("slow");	
				}
			);
			
			function blank(a) { if(a.value == a.defaultValue) a.value = ""; }
        	function unblank(a) { if(a.value == "") a.value = a.defaultValue; }
			function sitti(){
					document.getElementById("sittiType").action = document.sittiTipe.sittiT.value;
					return;
				}
			$("#faq").hover(
				function(){
					$("#faq-choice").stop(true, true).fadeIn("fast");},
				function(){
					$("#faq-choice").stop(true, true).fadeOut("slow");	
				}
			);
			
			$("a.faq-items").hover(
				function(){
					$("#faq-choice").stop(true, true).fadeIn("fast");},
				function(){
					$("#faq-choice").stop(true, true).fadeOut("slow");	
				}
			);
	</script>
	<script type="text/javascript">

			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-16501037-1']);
			  _gaq.push(['_setDomainName', 'sitti.co.id']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga, s);
			  })();

	</script>	
	
</body>
</html>
