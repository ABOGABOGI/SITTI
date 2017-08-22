/**
 * @author Hapsoro Renaldy <hapsoro.renaldy@kamisitti.com>
 */

 
 
var webName = document.location.href;

var myurl = 'http://www.sittibelajar.com/dev/delivery/ad.php';
//var myurl = 'http://localhost/sitti3/svn/sitti/src/delivery/ad.php';
var mtrack_sapiers_rand = parseInt(Math.random()*999999999999999);
var mtrack_sapiers_modurl = myurl+"?rand="+mtrack_sapiers_rand+'&sitti_pub_id='+sitti_pub_id+'&sitti_zone_id='+sitti_zone_id+'&type='+sitti_ad_type;
var mtrack_script_resize = document.getElementsByTagName('iframe');
		function iResize()
		{
			// Iterate through all iframes in the page.
			for (var i = 0, j = iFrames.length; i < j; i++)
			{
				// Set inline style to equal the body height of the iframed content.
				iFrames[i].style.height = iFrames[i].contentWindow.document.body.offsetHeight + 'px';
			}
		}

		// Check if browser is Safari or Opera.
		if ($.browser.safari || $.browser.opera)
		{
			// Start timer when loaded.
			$('iframe').load(function()
				{
					setTimeout(iResize, 0);
				}
			);

			// Safari and Opera need a kick-start.
			for (var i = 0, j = iFrames.length; i < j; i++)
			{
				var iSource = iFrames[i].src;
				iFrames[i].src = '';
				iFrames[i].src = iSource;
			}
		}
		else
		{
			// For other good browsers.
			$('iframe').load(function()
				{
					// Set inline style to equal the body height of the iframed content.
					this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
				}
			);
		}

var mtrack_sapiers_show_ads = '<iframe src="'+mtrack_sapiers_modurl+'" width="'+sitti_ad_width+'" height="'+sitti_ad_height+'" class="column" scrolling="no" frameborder="0">\n<p>Your browser does not support iframes.</p>\n</iframe>';
document.write(mtrack_script_resize+mtrack_sapiers_show_ads);