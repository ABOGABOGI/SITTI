// JavaScript Document
var dsRA_start_media = 0;
var content_id = 0;

var dsRelatedArticleMedia = null;
var dsSearchArticleMediaRelated = null;
function loadArticleRelatedMedia(){
    //alert(articleID);
	dsRelatedArticleMedia = new Spry.Data.XMLDataSet("index.php?s=acArticle&r=getRelatedArticleMedia&id="+articleID+"&start="+dsRA_start_media+"&total=20", "/data/item");
}
function prevRA_media(){
	dsRA_start_media-=20;
	if(dsRA_start_media<0){
		dsRA_start_media=0;
	}
	dsRelatedArticleMedia.setURL("index.php?s=acArticle&r=getRelatedArticleMedia&id="+articleID+"&start="+dsRA_start_media+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});
		var rgn = Spry.Data.getRegion("related_article_media_container");
	
	rgn.updateContent();
	
}
function nextRA_media(){
	
	dsRA_start_media+=20;
	dsRelatedArticleMedia.setURL("index.php?s=acArticle&r=getRelatedArticleMedia&id="+articleID+"&start="+dsRA_start_media+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	var rgn = Spry.Data.getRegion("related_article_media_container");

	rgn.updateContent();

}
var searchStartArticleMedia = 0;
var dsSearchArticleMediaRelated = new Spry.Data.XMLDataSet("", "/data/item");
function arm_searchByTitle(strMediaTitleTitle,start){
    //alert("okz");
    //alert(strMediaTitleTitle);
	if(start==null){
		searchStartArticleMedia = 0;
	}else{
		searchStartArticleMedia = start;
	}
	if(searchStartArticleMedia<0){
		searchStartArticleMedia = 0;
	}

		dsSearchArticleMediaRelated.setURL("index.php?s=acArticle&r=searchRelatedArticleMediaByTitle&q="+strMediaTitleTitle+"&start="+searchStartArticleMedia+"&total=20",
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_media_search");
	rgn.updateContent();
    //alert(id);
}
function arm_searchByTitleNext(strMediaTitle){
	arm_searchByTitle(strMediaTitle,searchStartArticleMedia+20);
}
function arm_searchByTitlePrevious(strMediaTitle){
	arm_searchByTitle(strMediaTitle,searchStartArticleMedia-20);
}
function arm_linkArticle(article_id,media_id){
    //alert(media_id);
	//reset start jadi 0
	dsRA_start_media = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticleMedia.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=acArticle&r=updateArticleRelatedMedia&id="+article_id+"&media_id="+media_id,
			headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" ,"Pragma": "public","Cache-control": "private","Expires": "-1"}});
	var rgn = Spry.Data.getRegion("related_article_media_container");
	rgn.updateContent();
}
function arm_unlinkArticle(article_id,media_id){
    //alert(media_id);
	//reset start jadi 0
	dsRA_start_media = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticleMedia.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=acArticle&r=removeArticleRelatedMedia&id="+article_id+"&media_id="+media_id,
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8","Pragma": "public","Cache-control": "private","Expires": "-1" }});
	var rgn = Spry.Data.getRegion("related_article_media_container");
	rgn.updateContent();
}
function arm_getArticle(id){
    //alert(id);
	dsSearchArticleMediaRelated.setURL("index.php?s=acArticle&r=searchArticleRelatedMedia&id="+id+"&RANDOM="+Math.random()*999999999,
			{method: "GET",
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});


var rgn = Spry.Data.getRegion("related_article_media_search");
rgn.updateContent();
}
function arm_last20(start){
	alert("yey");
	if(start==null){
		searchStartArticleMedia = 0;
	}else{
		searchStartArticleMedia = start;
	}
	if(searchStartArticleMedia<0){
		searchStartArticleMedia = 0;
	}

		dsSearchArticleMediaRelated.setURL("index.php?s=acArticle&r=searchLast20&RANDOM="+Math.random()*999999999,
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_media_search");
	rgn.updateContent();
}