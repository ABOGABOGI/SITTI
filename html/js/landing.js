/**
 *
 *landing js
 * 
 */	
 		   var isIE=/*@cc_on!@*/false;//IE detector
		  if (isIE){
			  
			  }else{
        $(document).ready(function(){
          
           $("#tampilanPanel,#metaPanel,#imagePanel,#formulirpanel, .tampilanContent, .metaContent, .imageContent,.formulirContent").hide(); //hides the panel and content from the user
          
				$("a.arrow").click(function () {
					 $('.tampilanContent, .metaContent, .imageContent,.formulirContent').fadeOut('slow', function() { //fade out the content 
					  $('#tampilanPanel,#metaPanel,#imagePanel,#formulirpanel').stop().animate({width:"0"}, 200); //slide the #panel back to a width of 0
					  });
		
				});

		  
        });
        $(document).ready(function(){
          
           $("#tampilanPanel, .tampilanContent").hide(); //hides the panel and content from the user
           
           $('#tampilan').toggle(function(){ //adding a toggle function to the #tab
        	   hideAllPanel();
              $('#tampilanPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
              $('.tampilanContent').fadeIn('slow'); //slides the content into view.
              });  
           },
           function(){ //when the #tab is next cliked
        	   hideAllPanel();
               $('#tampilanPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
               $('.tampilanContent').fadeIn('slow'); //slides the content into view.
               });
        	   /*
        	   $('.tampilanContent').fadeOut('slow', function() { //fade out the content 
              $('#tampilanPanel').stop().animate({width:"0"}, 200); //slide the #panel back to a width of 0
              
              });
              */
           });
        });
		//meta
        $(document).ready(function(){
           $("#metaPanel, .metaContent").hide(); //hides the panel and content from the user
           
           $('#metaBtn').toggle(function(){ //adding a toggle function to the #tab
        	   hideAllPanel();
              $('#metaPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
              $('.metaContent').fadeIn('slow'); //slides the content into view.
              });  
           },
           function(){ //when the #tab is next cliked
        	   hideAllPanel();
               $('#metaPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
               $('.metaContent').fadeIn('slow'); //slides the content into view.
               });  
        	   /*
        	   $(' .metaContent').fadeOut('slow', function() { //fade out the content 
              $('#metaPanel').stop().animate({width:"0"}, 200); //slide the #panel back to a width of 0
              });*/
           });
        });
		//image
        $(document).ready(function(){
           $("#imagePanel, .imageContent").hide(); //hides the panel and content from the user
           
           $('#image').toggle(function(){ //adding a toggle function to the #tab
        	   hideAllPanel();
              $('#imagePanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
              $('.imageContent').fadeIn('slow'); //slides the content into view.
              });  
           },
           function(){ //when the #tab is next cliked
        	   /*
           $(' .imageContent').fadeOut('slow', function() { //fade out the content 
              $('#imagePanel').stop().animate({width:"0"}, 200); //slide the #panel back to a width of 0
              });*/
        	   hideAllPanel();
               $('#imagePanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
               $('.imageContent').fadeIn('slow'); //slides the content into view.
               });  
           });
        });
		//form
        $(document).ready(function(){
           $("#formulirPanel, .formulirContent").hide(); //hides the panel and content from the user
           
           $('#formulir').toggle(function(){ //adding a toggle function to the #tab
        	   hideAllPanel();
              $('#formulirPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
              $('.formulirContent').fadeIn('slow'); //slides the content into view.
              });  
           },
           function(){ //when the #tab is next cliked
        	   /*
           $('.formulirContent').fadeOut('slow', function() { //fade out the content 
              $('#formulirpanel').stop().animate({width:"0"}, 200); //slide the #panel back to a width of 0
              });*/
        	   hideAllPanel();
               $('#formulirPanel').stop().animate({width:"480px"}, 200, function() {//sliding the #panel to 400px
               $('.formulirContent').fadeIn('slow'); //slides the content into view.
               });  
           });
        });
	}
function hideAllPanel(){
	$("#tampilanPanel, .tampilanContent").hide(); 
	 $("#imagePanel, .imageContent").hide();
	$("#metaPanel, .metaContent").hide(); 
	$("#formulirPanel, .formulirContent").hide();
}