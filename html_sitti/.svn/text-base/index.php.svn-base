<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SITTI Technology</title>
<link rel="stylesheet" href="css/SITTI_newface.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script> 
<script>

	$(document).ready(function(){
	    $.get("counter.php", function(result){
		      $(".counter").html(result);
	    });
	});

</script>
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="icon" type="image/gif" href="images/animated_favicon.gif">
</head>

<body>
	<?php include("header.php"); ?>
	
	<div id="main-content" class="margin-top-100">
    	<div class="content relative">
    		
        	<div class="head-text">Mempertemukan penjual dan pembeli<br />
            Tepat sasaran<br />
            Murah dan efektif
            </div>
            <a class="signup absolute" href="https://359.sitti.co.id/index.php?registration=1"></a>
            <div class="views absolute">
            	<span>Iklan yang telah dilihat:</span><br />
                <div class="round-5 counter">0</div>
            </div>
            <!-- <div class="btn-views absolute" href="#"></div> -->
            
            <div class="advertiser absolute round-5">
            	<ul>
                	<li>
                    	Impresi iklan gratis (iklan tidak diklik = tidak bayar) 
					</li><li>
                        Anggaran fleksibel, tidak ada biaya minimum
					</li><li>
                        Iklan dilihat oleh 500 juta pasang mata
					</li><li>
                        Geotargeting: Anda bebas menentukan di kota mana iklan akan ditayangkan
                    </li>
                </ul>
                <p style="padding: 2px;"><br /></p>
                <a class="adv-up" href="https://359.sitti.co.id/index.php?registration=1"></a>
                <a class="adv-why" href="advertiser.php"></a>
                
            </div>
                <div class="logo-adv absolute"></div>
                <div class="icon-adv absolute"></div>
                <div class="text-adv absolute">ADVERTISER</div>
            <div class="blogger absolute round-5">
            	<ul>
                	<li>
                    	Dapat penghasilan hanya dengan ngeblog pakai bahasa Indonesia
                    </li><li>
                       	Setiap bulan penghasilan akan ditransfer ke <br>rekening bank Anda
                    </li><li>    
                        Monitor penghasilan Anda setiap hari
                    </li><li>    
                        Iklan yang muncul sesuai dengan isi blog
                    </li>
                </ul>
                
                <a class="blog-up" href="https://sittizen.sitti.co.id/daftar.php"></a>
                <a class="blog-why" href="blogger.php"></a>
                
            </div>
                <div class="logo-blog absolute"></div>
                <div class="icon-blog absolute"></div>
                <div class="text-blog absolute">BLOGGER</div>
                
                <div id="feed-form" class="absolute round-5">
                	<div style="position:relative">
                    	<span id="close" style="color: #999999;right: 2px;top: -22px;width: 65px;position:absolute;font-size:10px;cursor:pointer;">CLOSE X</span>
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
