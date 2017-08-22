<div id="header">
    	<div class="content relative" style="z-index:2;">
    		<a class="absolute" href="http://socwave.sitti.co.id" style="left: 370px;top: 150px;">
        		<img width="315" src="images/socwave/socwaveLink.png" />
        	</a>  		
        	<img class="logo absolute" src="images/logo.png" width="330" height="176" />
            <div class="control absolute">
            	<div class="login">
                <form id="sittiType" name="sittiTipe" action="https://359.sitti.co.id/login.php" method="post" enctype="application/x-www-form-urlencoded">
                    <input class="round-5 border-no margin-right-15" onfocus="blank(this)" onblur="unblank(this)" type="text" name="username" value="username" maxlength="40" autocomplete="off" />
                    <input class="round-5 border-no margin-right-15" onfocus="blank(this)" onblur="unblank(this)" type="password" name="password" value="password" maxlength="40" autocomplete="off" />                                  
                    <select class="round-5 border-no round-5 margin-right-15" name="sittiT" id="sitti-type" onchange="sitti();">
                          <option value="https://359.sitti.co.id/login.php">Advertiser</option>
                          <option value="https://sittizen.sitti.co.id/login.php">Blogger</option>
                    </select>
                    <!-- <div class="select_style round-5 border-no margin-right-15">
                        <select class="round-5 border-no" name="sittiT" id="sitti-type" onchange="sitti();">
                          <option value="https://359.sitti.co.id/login.php">Advertiser</option>
                          <option value="https://sittizen.sitti.co.id/login.php">Blogger</option>
                        </select>
                    </div>  -->
                    
                    <input name="login" type="hidden" value="1" />
                     <input name="role" type="hidden" value="1" />
                    <input class="round-20 border-no" type="submit" value="Login" />                   
            	</form>
                </div>
        	</div>
            <a class="home absolute" href="http://www.sitti.co.id"></a>
            <div class="menu-top absolute">
            	<a class="dot-vertical" href="tentang-sitti.php">TENTANG SITTI</a>
                <a class="dot-vertical" href="advertiser.php">ADVERTISER</a>
                <a class="dot-vertical" href="blogger.php">BLOGGER</a>
                <a id="faq" class="dot-vertical" href="#">FAQ</a>
                <a class="dot-vertical" href="http://blog.sitti.co.id" target="_blank">BLOG SITTI</a>
                <a class="dot-vertical" href="https://359.sitti.co.id/career.php">KARIR</a>
                <a href="http://sitti.zendesk.com/anonymous_requests/new" target="_blank">HUBUNGI KAMI</a>
            </div>
            <div id="faq-choice" class="absolute round-5">
             	<a class="faq-items" href="faqAdvertiser.php">Advertiser</a> /
             	<a class="faq-items" href="faqBlogger.php">Blogger</a>
    		</div>
        	<div class="social absolute" style="right:0; top:130px;">
            	<span>Join us on social media: </span>
            	<a class="fb" href="http://www.facebook.com/SITTI.ID" target="_blank"></a>
                <a class="twit" href="http://twitter.com/sittiID" target="_blank"></a>
            </div>
            <div class="absolute" style="right:200px; top:128px;">
        		<a class="pdf" href="download/sitti_vs_google.pdf">SITTI359 vs Google Adwords<sup>TM</sup></a>
    		</div>
        </div>
        <div class="relative" style="z-index:1;">
        	<div class="absolute" style="background: #1E6275;width: 100%;height: 90px;top: 121px;left:0;"></div>
        </div>
    </div>