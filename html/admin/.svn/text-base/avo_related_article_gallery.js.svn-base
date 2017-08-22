// JavaScript Document
var dsRA_gallery_start = 0;
var content_id = 0;

var dsRelatedArticleGallery = null;
var dsSearchArticleGalleryRelated = null;
function loadArticleRelatedGallery(){
    dsRelatedArticleGallery = new Spry.Data.XMLDataSet("index.php?s=acArticle&r=getRelatedArticleGallery&id="+articleID+"&start="+dsRA_gallery_start+"&total=20", "/data/item");
}
function prevRA_gallery(){
	dsRA_gallery_start-=20;
	if(dsRA_gallery_start<0){
		dsRA_gallery_start=0;
	}
	dsRelatedArticleGallery.setURL("index.php?s=acArticle&r=getRelatedArticleGallery&id="+articleID+"&start="+dsRA_gallery_start+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});
		var rgn = Spry.Data.getRegion("related_article_gallery_container");
	
	rgn.updateContent();
	
}
function nextRA_gallery(){
	
	dsRA_gallery_start+=20;
	dsRelatedArticleGallery.setURL("index.php?s=acArticle&r=getRelatedArticleGallery&id="+articleID+"&start="+dsRA_gallery_start+"&total=20",
							{method: "GET",
							headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	var rgn = Spry.Data.getRegion("related_article_gallery_container");

	rgn.updateContent();

}
var searchStartArticleGallery = 0;
var dsSearchArticleGalleryRelated = new Spry.Data.XMLDataSet("", "/data/item");
function arg_searchByTitle(strGalleryTitleTitle,start){
    //alert("okz");
    //alert(strGalleryTitleTitle);
	if(start==null){
		searchStartArticleGallery = 0;
	}else{
		searchStartArticleGallery = start;
	}
	if(searchStartArticleGallery<0){
		searchStartArticleGallery = 0;
	}

		dsSearchArticleGalleryRelated.setURL("index.php?s=acArticle&r=searchRelatedArticleGalleryByTitle&q="+strGalleryTitleTitle+"&start="+searchStartArticleGallery+"&total=20",
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_gallery_search");
	rgn.updateContent();
    //alert(id);
}
function arg_searchByTitleNext(strGalleryTitle){
	arg_searchByTitle(strGalleryTitle,searchStartArticleGallery+20);
}
function arg_searchByTitlePrevious(strGalleryTitle){
	arg_searchByTitle(strGalleryTitle,searchStartArticleGallery-20);
}
function arg_linkArticle(article_id, gallery_id){
    //alert(Gallery_id);
	//reset start jadi 0
	dsRA_gallery_start = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticleGallery.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=acArticle&r=updateArticleRelatedGallery&id="+article_id+"&gallery_id="+gallery_id,
			headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" ,"Pragma": "public","Cache-control": "private","Expires": "-1"}});
	var rgn = Spry.Data.getRegion("related_article_gallery_container");
	rgn.updateContent();
}
function arg_unlinkArticle(article_id,gallery_id){
    //alert(gallery_id);
	//reset start jadi 0
	dsRA_gallery_start = 0;
	//add related article dan update dataset. gunakan POST
	dsRelatedArticleGallery.setURL("index.php?RANDOM="+Math.random()*999999999,
			{method: "POST",postData: "s=acArticle&r=removeArticleRelatedGallery&id="+article_id+"&gallery_id="+gallery_id,
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8","Pragma": "public","Cache-control": "private","Expires": "-1" }});
	var rgn = Spry.Data.getRegion("related_article_gallery_container");
	rgn.updateContent();
}
function arg_getGallery(id){
    //alert(id);
	dsSearchArticleGalleryRelated.setURL("index.php?s=acArticle&r=searchArticleRelatedGallery&id="+id+"&RANDOM="+Math.random()*999999999,
			{method: "GET",
			headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});


var rgn = Spry.Data.getRegion("related_article_gallery_search");
rgn.updateContent();
}
function arg_last20(start){
	alert("yey");
	if(start==null){
		searchStartArticleGallery = 0;
	}else{
		searchStartArticleGallery = start;
	}
	if(searchStartArticleGallery<0){
		searchStartArticleGallery = 0;
	}

		dsSearchArticleGalleryRelated.setURL("index.php?s=acArticle&r=searchLast20Gallery&RANDOM="+Math.random()*999999999,
				{method: "GET",
				headers: {  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" }});

	
	var rgn = Spry.Data.getRegion("related_article_gallery_search");
	rgn.updateContent();
}