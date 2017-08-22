// JavaScript Document
var dsRA_start = 0;
var content_id = 0;

var dsRelatedArticle = null;
var dsSearchRelated = null;
function loadRelatedArticle(){
    //alert(articleID);
	dsRelatedArticle = new Spry.Data.XMLDataSet("index.php?s=acArticle&r=getRelatedArticles&id="+articleID+"&start="+dsRA_start+"&total=20", "/data/item");
}
function prevRA(){
	dsRA_start-=20;
	if(dsRA_start<0){
		dsRA_start=0;	
	}
	dsRelatedArticle.setURL("index.php?s=acArticle&r=getRelatedArticles&id="+articleID+"&start="+dsRA_start+"&total=20", 
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});
		var rgn = Spry.Data.getRegion("related_article_container");
	
	rgn.updateContent();
	
}
function nextRA(){
	
	dsRA_start+=20;
	dsRelatedArticle.setURL("index.php?s=acArticle&r=getRelatedArticles&id="+articleID+"&start="+dsRA_start+"&total=20", 
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	var rgn = Spry.Data.getRegion("related_article_container");

	rgn.updateContent();

}
var searchStart = 0;
var dsSearchRelated = new Spry.Data.XMLDataSet("", "/data/item");
function ar_searchByTitle(str,start){
    //alert(str);
	if(start==null){
		searchStart = 0;
	}else{
		searchStart = start;
	}
	if(searchStart<0){
		searchStart = 0;
	}

		dsSearchRelated.setURL("index.php?s=acArticle&r=searchRelatedArticleByTitle&q="+str+"&start="+searchStart+"&total=20",
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_search");
	rgn.updateContent();
}
function ar_searchByTitleNext(str){
	ar_searchByTitle(str,searchStart+20);
}
function ar_searchByTitlePrevious(str){
	ar_searchByTitle(str,searchStart-20);
}
function ar_linkArticle(article_id,related_id){
	//reset start jadi 0
	dsRA_start = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticle.setURL("index.php?RANDOM="+Math.random()*999999999, 
			{method: "POST",postData: "s=acArticle&r=updateRelatedArticle&id="+article_id+"&related="+related_id,
			headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" ,"Pragma": "public","Cache-control": "private","Expires": "-1"}});
	var rgn = Spry.Data.getRegion("related_article_container");
	rgn.updateContent();
}
function ar_unlinkArticle(article_id,related_id){
	//reset start jadi 0
	dsRA_start = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticle.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=acArticle&r=removeRelatedArticle&id="+article_id+"&related="+related_id,
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8","Pragma": "public","Cache-control": "private","Expires": "-1" }});
	var rgn = Spry.Data.getRegion("related_article_container");
	rgn.updateContent();
}
function ar_getArticle(id){
	dsSearchRelated.setURL("index.php?s=acArticle&r=searchArticle&id="+id+"&RANDOM="+Math.random()*999999999,
			{method: "GET",
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});


var rgn = Spry.Data.getRegion("related_article_search");
rgn.updateContent();
}
function ar_last20(start){
	alert("yey");
	if(start==null){
		searchStart = 0;
	}else{
		searchStart = start;
	}
	if(searchStart<0){
		searchStart = 0;
	}

		dsSearchRelated.setURL("index.php?s=acArticle&r=searchLast20&RANDOM="+Math.random()*999999999,
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_search");
	rgn.updateContent();
}