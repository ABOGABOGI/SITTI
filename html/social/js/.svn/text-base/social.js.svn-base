var max_tweet = 10;
var tweets = [];
var t;
var k;
var state = 0;

function RequestDataStream(cat_id,last_id_stream){
	if (!last_id_stream){
		tweets = [];
	}
	$.get('stream.php',{refresh:1, cat_id: cat_id, last_id_stream: last_id_stream, cache:false}, function(response) {
		var f1 = [];
		f1 = eval(response);
		last_id_stream = f1[0].record;
		var data = f1[1];
		var n = data.length;
		if (n>0){
			$("#mainFeed").html("");
			var temp = tweets;
			tweets = data;
			tweets.push.apply(tweets,temp);
			tweets = tweets.slice(0, max_tweet);
			var str = SetTableDataStream();
			//$("#mainFeed").fadeOut();
			$("#mainFeed").html(str);
			//$("#mainFeed").fadeIn();
		}
		t = setTimeout(function() {RequestDataStream(cat_id,last_id_stream);},2000);
	});
}

function SetTableDataStream(){
	var n = tweets.length;
	var str = "";
	var style = "";
	var j = 0;
	for (i=0;i<n;i++){
		j++;
		if(j%2==0&&j>0){
			style = "light";
		}else{
			style = "dark";
		}
		str+=AddRowDataStream(tweets[i].tweet,style);
	}
	return str;
}

function AddRowDataStream(tweet,style){
	var str='<div class="'+style+' height62">';
	str+='<div class="feedonly">';
	str+='<span>'+tweet+'</span>';
	str+='</div>';
	str+='</div>';
	return str;
}

$(document).ready(function(){
	var msg = "";
	var d2 = [];
	var d3 = [];
	var d4 = [];
	//Loading Awal
	RequestDataStream();
	
	//Feed Menu
	$("#socFeed").click(function(){
		clearTimeout(t);
		if (state!=0){
			$("#mainFeed").html("");
		}
		state = 0;
		$(this).addClass("feedAct");$("#socSNA").removeClass("snaAct");$("#socWC").removeClass("cloudAct");$("#socST").removeClass("statAct");
		$("#mainFeed2").hide();$("#mainFeed3").hide();$("#subFeedCat").hide();$("#mainFeed4").hide();$("#radioSNA").hide();
		$("#feedGen").show();$("#feedCat").show();$("#mainFeed").show();$("#optionBrand").show();
		$("#feedGen").addClass("bg_ccc");$("#feedGen").removeClass("bg_fff");
		$("#feedCat").removeClass("bg_ccc");$("#feedCat").addClass("bg_fff");
		$(".wcNotif").fadeOut();
		$("#feedGen").addClass("genAct");
		$("#feedCat").removeClass("catAct");
		RequestDataStream()
	});
	//General Category
	$("#feedGen").click(function(){
		clearTimeout(t);
		if (state!=0){
			$("#mainFeed").html("");
		}
		state = 0;
		$(this).addClass("genAct");
		$("#feedCat").removeClass("catAct");
		$("#subFeedCat").hide();
		RequestDataStream();
	});
	//Specific Category
	$("#feedCat").click(function(){
		clearTimeout(t);
		if (state!=1){
			// $('#category option').eq(0).attr('selected', 'selected');
			$("#mainFeed").html("");
			if($('#category option:selected').val()!=0){
				RequestDataStream($('#category option:selected').val());
			}
		}
		state = 1;
		$(this).addClass("catAct");
		$("#feedGen").removeClass("genAct");
		$("#subFeedCat").show();$("#optionBrand").show();
		$("#radioSNA").hide();
		msg = "You want to know what they are saying about your brand?";
		$("#msgContact").html(msg);
	});
	//SNA
	$("#socSNA").click(function(){
		clearTimeout(t);
		// if (state!=2){
			// $('#category option').eq(0).attr('selected', 'selected');
			// $('input.group1').each(function() {
				// $(this).attr('disabled','true');
			// });
		// }
		if($('#category option:selected').val()==0){
			$('input.group1').each(function() {
				$(this).attr('disabled','true');
			});
		}
		state = 2;
		$(this).addClass("snaAct");$("#socWC").removeClass("cloudAct");$("#socFeed").removeClass("feedAct");$("#socST").removeClass("statAct");
		$("#subFeedCat").show();$("#mainFeed3").show();$("#radioSNA").show();
		$("#mainFeed").hide();$("#mainFeed2").hide();$("#feedGen").hide();$("#feedCat").hide();$("#mainFeed4").hide();$("#optionBrand").hide();
		$(".wcNotif").fadeOut();
		if (d2.length>0){
			var abc = SetSNA();
			$("#mainFeed3").html(abc);
			$(".loader").fadeIn();
			$("select#category").attr("disabled","true");
			$('input.group1').each(function() {
				$(this).attr('disabled','true');
			});
			init(d2);
			d2 = [];
		}
	});
	//WordCloud
	$("#socWC").click(function(){
		clearTimeout(t);
		// if (state!=3){
			// $('#category option').eq(0).attr('selected', 'selected');
		// }
		state = 3;
		$(this).addClass("cloudAct");$("#socSNA").removeClass("snaAct");$("#socFeed").removeClass("feedAct");$("#socST").removeClass("statAct");
		$("#subFeedCat").show();$("#mainFeed2").show();$("#optionBrand").show();
		$("#feedGen").hide();$("#feedCat").hide();$("#mainFeed3").hide();$("#mainFeed").hide();$("#mainFeed4").hide();$("#radioSNA").hide();
		msg = "You want to know what are the most relevant keywords to your brand?";
		$("#msgContact").html(msg);
		if (d3.length>0){
			$("#mainFeed2").html("");
			$(".loader").fadeIn();
			$("select#category").attr("disabled","true");
			$("#mainFeed2").jQCloud(d3, {
				callback: function() {
				// This code executes after the cloud is fully rendered
				$("select#category").removeAttr("disabled");
				$(".social-menu").removeAttr("disabled");
				$(".loader").fadeOut();
				$(".wcNotif").fadeIn();
			  }
			});
			d3 = [];
		} 
		var get = $("#category").val();
		if (get!=0){
			$(".wcNotif").fadeIn();
		}
	});
	//Stats
	$("#socST").click(function(){
		clearTimeout(t);
		// if (state!=4){
			// $('#category option').eq(0).attr('selected', 'selected');
		// }
		state = 4;
		$(this).addClass("statAct");$("#socSNA").removeClass("snaAct");$("#socFeed").removeClass("feedAct");$("#socWC").removeClass("cloudAct");
		$("#subFeedCat").show();$("#mainFeed4").show();$("#optionBrand").show();
		$("#feedGen").hide();$("#feedCat").hide();$("#mainFeed3").hide();$("#mainFeed").hide();$("#mainFeed2").hide();$("#radioSNA").hide();
		msg = "You want to know how popular your brand is?";
		$("#msgContact").html(msg);
		$(".wcNotif").fadeOut();
		if (d4.length>0){
			var totalUser = d4[0].total_users;
			var totalConversation = d4[0].total_conversations;
			var uDet = FormatNumber(totalUser);
			var cDet = FormatNumber(totalConversation);
			var totU = ConcisedNumber(totalUser);
			var totC = ConcisedNumber(totalConversation);
			$("#totU").html(totU);$("#totC").html(totC);
			$(".uDet").html(uDet);$(".cDet").html(cDet);
			$("#mainStatBox").fadeIn();
			totalUsers(d4[0].category,d4[0].brands,d4[0].users, d4[0].date);
			totalConvers(d4[0].category,d4[0].brands,d4[0].conversations, d4[0].date);
			d4 = [];
		}
	});
	
	//Hover Stats
	$(".stat_question").hover(
		function(){
			var no = $(this).attr('no');
			$("#statHov"+no).stop(true,true).fadeIn("fast");
		},
		function(){
			var no = $(this).attr('no');
			$("#statHov"+no).stop(true,true).fadeOut("fast");
		}
	);
	
	//Klik select brand
	$(".mask").click(function(){
		$("#bgContact").fadeIn("fast");
		$("#contact").fadeIn("fast");
		$("#contactSubtext").html("Contact us for the complete result");
		$("#formContact input[name=tipe]").val("brand");
		$("form #field").val("");
		$(".wcNotif").fadeOut();
	});
	$("#bgContact").click(function(){
		$(this).fadeOut("fast");
		$("#contact").fadeOut("fast");
		$("#nextBrand").fadeOut("fast");
		$("#contactUs").fadeOut("fast");
		$("#wcBridge").fadeOut("fast");
	});
	$("a#linkContact").click(function(){
		$("#contact").fadeOut("fast");
		$("#nextBrand").fadeIn("fast");
	});
	$(".exit_contact").click(function(){
		$("#bgContact").fadeOut("fast");
		$("#contact").fadeOut("fast");
		$("#nextBrand").fadeOut("fast");
		$("#contactUs").fadeOut("fast");
		$("#wcBridge").fadeOut("fast");
		$("#brandCat").html("Your brand is");
	});
	$("a#nextButton").click(function(){
		if ($("form #field").val()!=""){
			$("#nextBrand").fadeOut("fast");
			$("#contactUs").fadeIn("fast");
			$("#formContact input[name=field]").val($("form #field").val());
		}
	});
	$("#submitButton").click(function(){
		var tipe = $("#formContact input[name=tipe]").val();
		if (tipe=="brand"){
			$.post("brand_contact.php", { brand: $("#formContact input[name=field]").val(), name: $("#formContact input[name=name]").val(), email: $("#formContact input[name=email]").val(), telp: $("#formContact input[name=telp]").val(), message: $("#formContact textarea[name=message]").val() } );
		}else if (tipe=="help"){
			$.post("category_contact.php", { category: $("#formContact input[name=field]").val(), name: $("#formContact input[name=name]").val(), email: $("#formContact input[name=email]").val(), telp: $("#formContact input[name=telp]").val(), message: $("#formContact textarea[name=message]").val() } );
		}else if (tipe=="comment"){
			$.post("feedback.php", { name: $("#formContact input[name=name]").val(), email: $("#formContact input[name=email]").val(), telp: $("#formContact input[name=telp]").val(), feedback: $("#formContact textarea[name=message]").val() } );
		}
		$("#formContact input[name=field]").val("");
		$("#formContact input[name=name]").val($("#formContact input[name=name]").attr("no"));
		$("#formContact input[name=email]").val($("#formContact input[name=email]").attr("no"));
		$("#formContact input[name=telp]").val($("#formContact input[name=telp]").attr("no"));
		$("#formContact input[name=tipe]").val("");
		$("#formContact textarea[name=message]").val("");
		$("#formContact textarea[name=message]").html("Message");
		$("#bgContact").fadeOut("fast");
		$("#contactUs").fadeOut("fast");
	});
	$("input[type=text]").focus(function(){
		var att = $(this).attr("no");
		if ($(this).val()==att){
			$(this).val("");
		}
	});
	$("input[type=text]").blur(function(){
		var att = $(this).attr("no");
		if ($(this).val()==""){
			$(this).val(att);
		}
	});
	$("textarea").focus(function(){
		if ($(this).html()=="Message"){
			$(this).html("");
		}
	});
	$("textarea").blur(function(){
		if ($(this).html()==""){
			$(this).html("Message");
		}
	});
	$("input[name=group1]").change(function(){
		var method = $(this).val();
		var cat_id = $('#category option:selected').val();
		$.get('sna.php',{sna:1, cat_id: cat_id, method:method, cache:false}, function(response) {
			var f1 = [];
			f1 = eval(response);
			$("#mainFeed3").html("");
			if (f1[0]){
				d2 = f1;
				var abc = SetSNA();
				$("#mainFeed3").html(abc);
				$(".loader").fadeIn();
				$("select#category").attr("disabled","true");
				$('input.group1').each(function() {
					$(this).attr('disabled','true');
				});
				init(f1);
				d2 = [];
			}
		});
	});

	$(".bubble").click(function(){
		$("#bgContact").fadeIn("fast");
		$("#contactUs").fadeIn("fast");
		$("#contactSubtext").html("Help us improve with your comments");
		$("#formContact input[name=tipe]").val("comment");
		$(".wcNotif").fadeOut();
	});
	$("#helpButton").click(function(){
		$("#bgContact").fadeIn("fast");
		$("#nextBrand").fadeIn("fast");
		$("#wcBridge").fadeOut("fast");
		$("#brandCat").html("Your category is");
		$("#contactSubtext").html("Contact us to help you with your Ad Campaign");
		$("#formContact input[name=tipe]").val("help");
		$("form #field").val("");
		$(".wcNotif").fadeOut();
	});

	//Category Change
	$('#category').bind('change', function () {
		clearTimeout(t);
		var cat_id = $('#category option:selected').val();
		if(cat_id!=0){
			$('input.group1').each(function() {
			    $(this).removeAttr('disabled');
			});
		}else{
			$('input.group1').each(function() {
			    $(this).attr('disabled','true');
			});
		}
		if (cat_id != 0){
			if (state==1){
				RequestDataStream(cat_id);
			}
			// if (state==2){
				var method = $("input[@name=group1]:radio:checked").val();
				$.get('sna.php',{sna:1, cat_id: cat_id, method:method, cache:false}, function(response) {
					var f1 = [];
					f1 = eval(response);
					$("#mainFeed3").html("");
					if (f1[0]){
						d2 = f1;
						if (state==2){
							var abc = SetSNA();
							$("#mainFeed3").html(abc);
							$(".loader").fadeIn();
							$("select#category").attr("disabled","true");
							$('input.group1').each(function() {
								$(this).attr('disabled','true');
							});
							init(f1);
							d2 = [];
						}
					}
				});
			// }
			// if (state==3){
				$.get('wordcloud.php',{wordcloud:1, cat_id: cat_id, cache:false}, function(response) {
					var f1 = [];
					f1 = eval(response);
					d3 = f1;
					if (state==3){
						$("#mainFeed2").html("");
						$(".loader").fadeIn();
						$("select#category").attr("disabled","true");
						$("#mainFeed2").jQCloud(f1, {
							callback: function() {
							// This code executes after the cloud is fully rendered
							$("select#category").removeAttr("disabled");
							$(".social-menu").removeAttr("disabled");
							$(".loader").fadeOut();
							$(".wcNotif").fadeIn();
						  }
						});
						d3 = [];
					}
				});
			// }
			// if (state==4){
				$.get('stats.php',{stats:1, cat_id: cat_id, cache:false}, function(response) {
					var f1 = [];
					f1 = eval(response);
					d4 = f1;
					if (state==4){
						var totalUser = f1[0].total_users;
						var totalConversation = f1[0].total_conversations;
						var uDet = FormatNumber(totalUser);
						var cDet = FormatNumber(totalConversation);
						var totU = ConcisedNumber(totalUser);
						var totC = ConcisedNumber(totalConversation);
						$("#totU").html(totU);$("#totC").html(totC);
						$(".uDet").html(uDet);$(".cDet").html(cDet);
						$("#mainStatBox").fadeIn();
						totalUsers(f1[0].category,f1[0].brands,f1[0].users, f1[0].date);
						totalConvers(f1[0].category,f1[0].brands,f1[0].conversations, f1[0].date);
						d4 = [];
					}
				});
			// }
			$.get('topkeyword.php',{topkeyword:1, cat_id: cat_id, cache:false}, function(response) {
				var f1 = [];
				f1 = eval(response);
				$("#topkeyword").html("");
				if (f1){
					k = "";
					var str = SetTableTopKeyword(f1);
					$("#topkeyword").html(str);
				}
			});
			$.get('topuser.php',{topuser:1, cat_id: cat_id, cache:false}, function(response) {
				var f1 = [];
				f1 = eval(response);
				$("#topuser").html("");
				if (f1){
					var str = SetTableTopUser(f1);
					$("#topuser").html(str);
				}
			});
		}
	});
});

function SetTableTopKeyword(keywords){
	var n = keywords.length;
	var str = "";
	var style = "";
	var j = 0;
	for (i=0;i<n;i++){
		j++;
		if(j%2==0&&j>0){
			style = "light";
		}else{
			style = "dark";
		}
		if (k!=""){
			k+="\n";
		}
		k += keywords[i].keyword;
		str+=AddRowTopKeyword(i+1,keywords[i].keyword,style);
	}
	return str;
}

function AddRowTopKeyword(no,keyword,style){
	var str='<div class="w-key w-'+style+'">';
	str+='<div class="w-no">'+no+'</div>';
	str+='<div class="w-keyword"><a href="javascript:JumpToSimulationPage()">'+keyword+'</a></div>';
	str+='</div>';
	return str;
}

function JumpToSimulationPage(){
	var myForm = document.createElement("form");
	myForm.method = "post";
	myForm.action = "https://359.sitti.co.id/uji_sitti.php";
	myForm.target = "_blank";
	
	var myInput = document.createElement("input");
	myInput.setAttribute("name", "q");
	myInput.setAttribute("value", escape(k));
	myForm.appendChild(myInput);
	
	document.body.appendChild(myForm);
	myForm.submit();
	document.body.removeChild(myForm);
}

function SetTableTopUser(users){
	var n = users.length;
	var str = "";
	var style = "";
	var j = 0;
	for (i=0;i<n;i++){
		j++;
		if(j%2==0&&j>0){
			style = "light";
		}else{
			style = "dark";
		}
		if (users[i].avatar){
			str+=AddRowTopUser(users[i].tweep,users[i].avatar,style);
		}else{
			str+=AddRowTopUser(users[i].tweep,"images/user/default.png",style);
		}
	}
	return str;
}

function AddRowTopUser(tweep,avatar,style){
	var str='<div class="w-user w-'+style+'">';
	str+='<div class="w-pic"><img width="48" src="'+avatar+'" /></div>';
	str+='<div class="w-id"><strong>'+tweep+'</strong></div>';
	str+='</div>';
	return str;
}

function SetSNA(){
	var str='<div id="center-container"><div id="infovis"></div></div>';
    str+='<div id="right-container" style="display:none;"><div id="inner-details"></div></div>';
    str+='<div id="log"></div>';
	return str;
}

function FormatNumber(str) {
	var amount = new String(str);
	amount = amount.split("").reverse();

	var output = "";
	for ( var i = 0; i <= amount.length-1; i++ ){
		output = amount[i] + output;
		if ((i+1) % 3 == 0 && (amount.length-1) !== i)output = ',' + output;
	}
	return output;
}

function ConcisedNumber(str){
	var n = parseFloat(str);
	var s = "";
	if(n>1000000000){
		s = Math.round(n/1000000000)+"B";
	}else if(n>1000000){
		s = Math.round(n/1000000)+"M";
	}else if(n>1000){
		s = Math.round(n/1000)+"K";
	}else{
		s = n;
	}
	return s;
}

var color = ['rgba(241,90,34,0.8)','rgba(38,181,76,0.8)','rgba(94,45,143,0.8)','rgba(255,198,10,0.8)','rgba(0,172,240,0.5)','rgba(255,0,0,0.8)'];

//Total user
function totalUsers(category,brands,users,date){
	var max = 0;
	var brands_chart = brands;
	var brands_pie = brands;
	var users_chart = users;
	var users_pie = users;
	if (brands.length==6){
		for (i=0;i<brands.length-1;i++){
			if (users[i]>max){
				max = users[i];
			}
		}
		if (users[brands.length-1]>2*max){
			brands_chart = brands.slice(0,brands.length-1);
			users_chart = users.slice(0,users.length-1);
		}
	}
	
	var _chart = [];
	var _pie = [];
	
	for(var i in users_chart){
		var temp = {'y':users_chart[i], 'color': color[i]};
		_chart.push(temp);
	}
	for(var i in users_pie){
		var temp = {'name':brands_pie[i], 'y':users_pie[i], 'color': color[i]};
		_pie.push(temp);
	}
	var chart;
	$(document).ready(function() {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'totalUser',
				width: 594,
				height: 312,
				marginTop: 20,
				backgroundColor: false
			},
			xAxis: {
				categories: brands_chart,
				labels : {
					style : {
						color : '#ffffff'
					}
				}
			},
			yAxis: {
				title: {
					text: 'TOTAL USERS',
					style : {
						color : '#ffffff',
						fontSize: '9px'
					}
				},
				labels : {
					style : {
						color : '#ffffff'
					}
				}
			},
			tooltip: {
				formatter: function() {
					var s;
					if (this.point.name) { // the pie chart
						s = ''+
							this.point.name +': '+ this.y +' users';
					} else {
						s = ''+
							this.x  +': '+ this.y;
					}
					return s;
				}
			},
			credits : false,
			title : {
				text : date,
				align : 'right',
				y : 6,
				style : {
					color : '#ffffff',
					fontSize: '12px'
				}
			},
			labels: {
				items: [{
					html: 'POPULAR BRANDS',
					style: {
						left: '0px',
						top: '-15px',
						color : '#ffffff'
					}
				}]
			},
			series: [{
				type: 'column',
				name: category,
				showInLegend: false,
				borderWidth: 2,
				data: _chart
			},  {
				type: 'pie',
				name: category,
				borderWidth: 1,
				data: _pie,
				center: [50, 60],
				size: 100,
				showInLegend: false,
				dataLabels: {
					enabled: false
				}
			}]
		});
	});
}

//Total Conversation
function totalConvers(category,brands,conversations,date){
	var max = 0;
	var brands_chart = brands;
	var brands_pie = brands;
	var conversations_chart = conversations;
	var conversations_pie = conversations;
	if (brands.length==6){
		for (i=0;i<brands.length-1;i++){
			if (conversations[i]>max){
				max = conversations[i];
			}
		}
		if (conversations[brands.length-1]>2*max){
			brands_chart = brands.slice(0,brands.length-1);
			conversations_chart = conversations.slice(0,conversations.length-1);
		}
	}
	
	var _chart = [];
	var _pie = [];
	for(var i in conversations_chart){
		 var temp = {'y':conversations_chart[i], 'color': color[i]};
		_chart.push(temp);
	}
	for(var i in conversations_pie){
		var temp = {'name':brands_pie[i], 'y':conversations_pie[i], 'color': color[i]};
		_pie.push(temp);
	}
	var chart;
	$(document).ready(function() {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'totalConver',
				width: 594,
				height: 312,
				marginTop: 20,
				backgroundColor: false
			},
			xAxis: {
				categories: brands_chart,
				labels : {
					style : {
						color : '#ffffff'
					}
				}
			},
			yAxis: {
				title: {
					text: 'TOTAL CONVERSATION',
					style : {
						color : '#ffffff',
						fontSize: '9px'
					}
				},
				labels : {
					style : {
						color : '#ffffff'
					}
				}
			},
			tooltip: {
				formatter: function() {
					var s;
					if (this.point.name) { // the pie chart
						s = ''+
							this.point.name +': '+ this.y +' conversation';
					} else {
						s = ''+
							this.x  +': '+ this.y;
					}
					return s;
				}
			},
			credits : false,
			title : {
				text : date,
				align : 'right',
				y : 6,
				style : {
					color : '#ffffff',
					fontSize: '12px'
				}
			},
			labels: {
				items: [{
					html: 'TOP KEYWORDS',
					style: {
						left: '0px',
						top: '-15px',
						color : '#ffffff'
					}
				}]
			},
			series: [{
				type: 'column',
				name: category,
				showInLegend: false,
				borderWidth: 2,
				data: _chart
			},  {
				type: 'pie',
				name: category,
				borderWidth: 1,
				data: _pie,
				center: [50, 60],
				size: 100,
				showInLegend: false,
				dataLabels: {
					enabled: false
				}
			}]
		});
	});
}

function wcLink(){
	$("#bgContact").fadeIn("fast");
	$("#wcBridge").fadeIn("fast");
}