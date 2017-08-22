/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopupKonversi(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popupKonversi").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopupKonversi2(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popupKonversi2").fadeIn("slow");
		popupStatus = 1;
	}
}

function loadPopup160x600(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup160x600").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup250x250(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup250x250").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup300x160(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup300x160").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup300x250(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup300x250").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup336x280(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup336x280").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup468x60(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup468x60").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup520x70(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup520x70").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup610x60(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup610x60").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup728x90(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup728x90").fadeIn("slow");
		popupStatus = 1;
	}
}
function loadPopup940x70(){
	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#backgroundPopup").fadeIn("slow");
		$("#popup940x70").fadeIn("slow");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#backgroundPopup").fadeOut("slow");
		$(".popupBox").fadeOut("slow");
		popupStatus = 0;
	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $(".popupBox").height();
	var popupWidth = $(".popupBox").width();
	//centering
	$(".popupBox").css({
		"position": "absolute",
		"top": 80,
		"left": windowWidth/2-popupWidth/2 
	});
	//only need force for IE6
	
	$("#backgroundPopup").css({
		"height": windowHeight
	});
	
}


//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	
	//LOADING POPUP
	//Click the button event!
	
	$("#buttonKonversi, #linkKonversi").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopupKonversi();
	});

	$("#buttonKonversi2").click(function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopupKonversi2();
	});
	$("#160x600").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup160x600();
	});
	$("#250x250").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup250x250();
	});
	$("#300x160").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup300x160();
	});
	$("#300x250").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup300x250();
	});
	$("#336x280").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup336x280();
	});
	$("#468x60").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup468x60();
	});
	$("#520x70").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup520x70();
	});
	$("#610x60").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup610x60();
	});
	$("#728x90").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup728x90();
	});
	$("#940x70").live('click', function(){
		//centering with css
		centerPopup();
		//load popup
		loadPopup940x70();
	});
				
	//CLOSING POPUP
	//Click the x event!
	$(".popupClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup();
	});
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});