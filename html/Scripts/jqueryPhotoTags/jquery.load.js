var $x1, $y1, $x2, $y2, $w, $h;
function selectChange(img, selection) {
	$x1.text(selection.x1);
	$y1.text(selection.y1);
	$x2.text(selection.x2);
	$y2.text(selection.y2);
	$w.text(selection.width);
	$h.text(selection.height);
	$('input#x1').val(selection.x1);
	$('input#y1').val(selection.y1);
	$('input#x2').val(selection.x2);
	$('input#y2').val(selection.y2);
	$('input#w').val(selection.width);
	$('input#h').val(selection.height);
}

$(document).ready(function () {
	$x1 = $('#x1');
	$y1 = $('#y1');
	$x2 = $('#x2');
	$y2 = $('#y2');
	$w = $('#w');
	$h = $('#h');
});

$(window).load(function () {
	$('img#imageid').imgAreaSelect({ selectionOpacity: 0, onSelectChange: selectChange,
	outerOpacity: 0.4, handles: true });
	$('.imgareaselect-handle').css('opacity', 0.7);
});
//
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post("includes/rpc.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup

function fill(thisValue) {
	$('#inputString').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}