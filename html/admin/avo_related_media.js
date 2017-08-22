// JavaScript Document
var dsRA_start_related_media = 0;
var content_id = 0;

var dsRelatedMedia = null;
var dsSearchRelated = null;
function loadRelatedMedia(){
    //alert(mediaID);
	dsRelatedMedia = new Spry.Data.XMLDataSet("index.php?s=amManagerContent&r=getRelatedMedia&id="+mediaID+"&start="+dsRA_start_related_media+"&total=20", "/data/item");
}
function prevRA_related_media(){
	dsRA_start_related_media-=20;
	if(dsRA_start_related_media<0){
		dsRA_start_related_media=0;
	}
	dsRelatedMedia.setURL("index.php?s=amManagerContent&r=getRelatedMedia&id="+mediaID+"&start="+dsRA_start_related_media+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});
		var rgn = Spry.Data.getRegion("related_media_container");

	rgn.updateContent();

}
function nextRA_related_media(){

	dsRA_start_related_media+=20;
	dsRelatedMedia.setURL("index.php?s=amManagerContent&r=getRelatedMedia&id="+mediaID+"&start="+dsRA_start_related_media+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	var rgn = Spry.Data.getRegion("related_media_container");

	rgn.updateContent();

}
var searchStart = 0;
var dsSearchRelated = new Spry.Data.XMLDataSet("", "/data/item");
function md_searchByTitle(str,start){
	if(start==null){
		searchStart = 0;
	}else{
		searchStart = start;
	}
	if(searchStart<0){
		searchStart = 0;
	}
    
		dsSearchRelated.setURL("index.php?s=amManagerContent&r=searchRelatedMediaByTitle&q="+str+"&start="+searchStart+"&total=20",
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});
                

	var rgn = Spry.Data.getRegion("related_media_search");
	rgn.updateContent();
    
}
function md_searchByTitleNext(str){
	md_searchByTitle(str,searchStart+20);
}
function md_searchByTitlePrevious(str){
	md_searchByTitle(str,searchStart-20);
}
function md_linkMedia(mediaID,related_id){
	//reset start jadi 0
	dsRA_start_related_media = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedMedia.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=amManagerContent&r=updateRelatedMedia&id="+mediaID+"&related="+related_id,
			headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" ,"Pragma": "public","Cache-control": "private","Expires": "-1"}});
	var rgn = Spry.Data.getRegion("related_media_container");
	rgn.updateContent();
   // alert(related_id);
}
function md_unlinkMedia(mediaID,related_id){
    //alert(related_id);
	//reset start jadi 0
	dsRA_start_related_media = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedMedia.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=amManagerContent&r=removeRelatedMedia&id="+mediaID+"&related="+related_id,
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8","Pragma": "public","Cache-control": "private","Expires": "-1" }});
	var rgn = Spry.Data.getRegion("related_media_container");
	rgn.updateContent();
    
}
function md_getMedia(id){
	dsSearchRelated.setURL("index.php?s=amManagerContent&r=searchMedia&id="+id+"&RANDOM="+Math.random()*999999999,
			{method: "GET",
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});


var rgn = Spry.Data.getRegion("related_media_search");
rgn.updateContent();
}
function md_last20(start){
	alert("yey");
	if(start==null){
		searchStart = 0;
	}else{
		searchStart = start;
	}
	if(searchStart<0){
		searchStart = 0;
	}

		dsSearchRelated.setURL("index.php?s=amManagerContent&r=searchLast20&RANDOM="+Math.random()*999999999,
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});


	var rgn = Spry.Data.getRegion("related_media_search");
	rgn.updateContent();
}