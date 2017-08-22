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

<body style="background:url(images/bg_main2.png) repeat-x scroll 0 0 #fff;">
	<?php include("header.php"); ?>
	<div id="main-content" class="margin-top-130">
    	<div id="voucher" class="content relative round-5 bg_white" style="padding: 60px 80px 30px;width: 800px; color:#333333; text-align:left;">
        	
            <div class="logo-voucher absolute page-voucher"></div>
            <img src="images/voucher/paket_voucher.png" width="271" class="paketVoucher" />
            <hr style="border: 1px dotted #CCCCCC;margin-top: 45px;" />
            <div class="voucherColom">
            	<img class="voucher" src="images/voucher/200.png" width="241" />
                <a class="v1" href="#">Paket Rp.200.000</a>	
            </div>
            <div class="voucherColom">
            	<img class="voucher2" src="images/voucher/300.png" width="241" />
                <a class="v2" href="#">Paket Rp.300.000</a>		
            </div>
            <div class="voucherColom fix">
            	<img class="voucher" src="images/voucher/500.png" width="241" />
                <a class="v3" href="#">Paket Rp.500.000</a>		
            </div>
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
