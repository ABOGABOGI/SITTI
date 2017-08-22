// JavaScript Document

$(document).ready(function(){

	$("#item01").hover(									
		function(){
			$("#img01").stop(true,true).attr("width","75px");
			$("#hov01").stop(true,true).delay().fadeIn();									
		},
		function(){		
			$("#hov01").stop(true,true).fadeOut("fast");
			$("#img01").stop(true,true).delay(10000).attr("width","132px");
		}
	);

});