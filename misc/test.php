<html>
<head>
<title>Test</title>
<script src="js/jquery.js" language="javascript1.1"></script>
<script src="js/jquery_swfobject.js" language="javascript1.1"></script>
</head>
<body>
<script>
$(document).ready(
	function () {
		// #myFlashVars is the selector
		$('#banner1').flash(
			{
				// test_flashvars.swf is the flash document
				swf: 'uploader.swf',
				width: 350,
				height: 50,
				// these arguments will be passed into the flash document
				flashvars: {
					fileID: 'foo1'
				}
			}
		);
		$('#banner2').flash(
			{
				// test_flashvars.swf is the flash document
				swf: 'uploader.swf',
				width: 350,
				height: 50,
				// these arguments will be passed into the flash document
				flashvars: {
					fileID: 'foo2'
				}
			}
		);
		$('#banner3').flash(
			{
				// test_flashvars.swf is the flash document
				swf: 'uploader.swf',
				width: 350,
				height: 50,
				// these arguments will be passed into the flash document
				flashvars: {
					fileID: 'foo3'
				}
			}
		);
	}
);
</script>

<div id="banner1"></div>
<div id="banner2"></div>
<div id="banner3"></div>
<script>
var n_file = 0;
function upload_notify(f_id){
	n_file+=1;	
	
	if(n_file==3){
		alert("done");
	}
	return false;
}
</script>
</body>
</html>