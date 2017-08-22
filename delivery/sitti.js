var webName = document.location.href;

var myurl = 'http://www.sittibelajar.com/dev/delivery/ad.php';
//var myurl = 'http://localhost/sitti3/svn/sitti/src/delivery/ad.php';
var mtrack_sapiers_rand = parseInt(Math.random()*999999999999999);
var mtrack_sapiers_modurl = myurl+"?rand="+mtrack_sapiers_rand+'&sitti_pub_id='+sitti_pub_id+'&sitti_zone_id='+sitti_zone_id+'&type='+sitti_ad_type;
var mtrack_sapiers_show_ads = '<iframe src="'+mtrack_sapiers_modurl+'" width="'+sitti_ad_width+'" height="'+sitti_ad_height+'" class="column" scrolling="no" frameborder="0">\n<p>Your browser does not support iframes.</p>\n</iframe>';
document.write(mtrack_sapiers_show_ads);
