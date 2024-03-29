/*
 * MojoZoom 0.1.4 - JavaScript Image Zoom
 * Copyright (c) 2008 Jacob Seidelin, jseidelin@nihilogic.dk, http://blog.nihilogic.dk/
 * Licensed under the MPL License [http://www.nihilogic.dk/licenses/mpl-license.txt]
 */

var MojoMagnify = (function() {

	var $ = function(id) {return document.getElementById(id);};
	var dc = function(tag) {return document.createElement(tag);};
	
	var preZoomObj = null;
	var preImgObj = null;
	var defaultWidth = 220;
	var defaultHeight = 220;

	function addEvent(element, ev, handler) 
	{
		var doHandler = function(e) {
			return handler(e||window.event);
		}
		if (element.addEventListener) { 
			element.addEventListener(ev, doHandler, false); 
		} else if (element.attachEvent) { 
			element.attachEvent("on" + ev, doHandler); 
		}
	}

	function getElementPos(element)
	{
		var x = element.offsetLeft;
		var y = element.offsetTop;
		var parent = element.offsetParent;
		while (parent) {
			x += parent.offsetLeft;
			y += parent.offsetTop;
			parent = parent.offsetParent;
		}
		return {
			x : x,
			y : y
		}
	}

	function getEventMousePos(element, e) {
		var scrollX = document.body.scrollLeft || document.documentElement.scrollLeft;
		var scrollY = document.body.scrollTop || document.documentElement.scrollTop;

		if (e.currentTarget) {
			var pos = getElementPos(element);
			return {
				x : e.clientX - pos.x + scrollX,
				y : e.clientY - pos.y + scrollY
			}
		}
		return {
			x : e.offsetX,
			y : e.offsetY
		}
	}

	function makeZoomable(img, zoomSrc, zoomImgCtr, zoomWidth, zoomHeight, alwaysShow) {
		// make sure the image is loaded, if not then add an onload event and return
		if (!img.complete && !img.__mojoZoomQueued) {
			addEvent(img, "load", function() {
				img.__mojoZoomQueued = true;
				setTimeout(function() {
				makeZoomable(img, zoomSrc, zoomImgCtr, zoomWidth, zoomHeight, alwaysShow);
				}, 1);
			});
			return;
		}

		img.__mojoZoomQueued = false;

		// Wrap everything in a timeout.
		// this fixes a problem where, if makeZoomable is called a second time after changing the src,
		// FF would not have figured out the new offsetHeight of the image yet. A small timeout helps though it's rather hackish.
		setTimeout(function(){

		// sorry
		var isIE = !!document.all && !!window.attachEvent && !window.opera;

		var w = img.offsetWidth;
		var h = img.offsetHeight;
		
		var oldParent = img.parentNode;
		if (oldParent.nodeName != "A") {
			var linkParent = dc("a");
			//linkParent.setAttribute("href", zoomSrc);
			oldParent.replaceChild(linkParent, img);
			linkParent.appendChild(img);
		} else {
			var linkParent = oldParent;
		}

		linkParent.style.position = "relative";
		linkParent.style.display = "block";
		linkParent.style.width = w+"px";
		linkParent.style.height = h+"px";
				
		var imgLeft = img.offsetLeft;
		var imgTop = img.offsetTop;

		var zoom = dc("div");
		zoom.className = "mojozoom_marker";

		var zoomImg = dc("img");
		zoomImg.className = "mojozoom_img";
		zoomImg.style.position = "absolute";
		zoomImg.style.left = "-9999px";
		zoomImg.style.top = "-9999px";

		document.body.appendChild(zoomImg);

		var parent = img.parentNode;

		var ctr = dc("div");
		with (ctr.style) {
			position = "absolute";
			left = imgLeft+"px";
			top = imgTop+"px";
			width = w+"px";
			height = h+"px";
			overflow = "hidden";
			display = "none";
		}
		ctr.appendChild(zoom);
		parent.appendChild(ctr);

		var zoomInput = parent;

		// clear old overlay
		if (img.__mojoZoomOverlay)
			parent.removeChild(img.__mojoZoomOverlay);
		img.__mojoZoomOverlay = ctr;

		// clear old high-res image
		if (img.__mojoZoomImage && img.__mojoZoomImage.parentNode)
			img.__mojoZoomImage.parentNode.removeChild(img.__mojoZoomImage);
		img.__mojoZoomImage = zoomImg;

		var useDefaultCtr = false;
		if (!zoomImgCtr) {
			zoomImgCtr = dc("div");
			zoomImgCtr.className = "mojozoom_imgctr";

			var imgPos = getElementPos(img);
			zoomImgCtr.style.left = w + imgPos.x + "px";
			zoomImgCtr.style.top = imgPos.y + "px";

			zoomImgCtr.style.width = (zoomWidth ? zoomWidth : defaultWidth) +"px";
			zoomImgCtr.style.height = (zoomHeight ? zoomHeight : defaultHeight) +"px";

			document.body.appendChild(zoomImgCtr);
			useDefaultCtr = true;
		}
		zoomImgCtr.style.overflow = "hidden";

		if (!alwaysShow) {
			zoomImgCtr.style.visibility = "hidden";
		}

		addEvent(zoomImg, "load", function() {

			// bail out if img has been removed from dom
			if (!zoomImg.parentNode) return;

			var zoomWidth = zoomImg.offsetWidth;
			var zoomHeight = zoomImg.offsetHeight;

			var ctrWidth = zoomImgCtr.offsetWidth;
			var ctrHeight = zoomImgCtr.offsetHeight;

			var ratioW = zoomWidth / w;
			var ratioH = zoomHeight / h;

			var markerWidth = Math.round(ctrWidth / ratioW);
			var markerHeight = Math.round(ctrHeight / ratioH);

			document.body.removeChild(zoomImg);
			// ie6 form element blocker
			zoomImgCtr.innerHTML = '<iframe scrolling="no" frameborder="0" class="selectBlocker" style="width:305px;height:290px;"></iframe>';
			zoomImgCtr.appendChild(zoomImg);

			var zoomFill = dc("div");
			zoomFill.className = "mojozoom_fill";
			zoom.appendChild(zoomFill);

			var zoomBorder = dc("div");
			zoomBorder.className = "mojozoom_border";
			zoom.appendChild(zoomBorder);

			zoom.style.width = markerWidth+"px";
			zoom.style.height = markerHeight+"px";


			if (alwaysShow) {
				zoom.style.left = "0px";
				zoom.style.top = "0px";
	
				zoomImg.style.left = "0px";
				zoomImg.style.top = "0px";
			}

			var isInImage = false;

			if (!alwaysShow) {
				addEvent(zoomInput, "mouseout", 
					function(e) {
						var target = e.target || e.srcElement;
						if (!target) return;
						if (target.nodeName != "DIV") return;
						var relTarget = e.relatedTarget || e.toElement;
						if (!relTarget) return;
						while (relTarget != target && relTarget.nodeName != "BODY" && relTarget.parentNode) {
							relTarget = relTarget.parentNode;
						}
						if (relTarget != target) {
							isInImage = false;
							ctr.style.display = "none";
							zoomImgCtr.style.visibility = "hidden";
						}
					}
				);
				// a bit of a hack, mouseout is sometimes not caught if moving mouse really fast
				addEvent(document.body, "mouseover", 
					function(e) {
						if (isInImage && !(e.toElement == zoomBorder || e.target == zoomBorder)) {
							ctr.style.display = "none";
							zoomImgCtr.style.visibility = "hidden";
							isInImage = false;
						}
					}
				);
			}

			addEvent(zoomInput, "mousemove", 
				function(e) {
					isInImage = true;

					var imgPos = getElementPos(img);

					if (useDefaultCtr) {
						zoomImgCtr.style.left = w + imgPos.x + "px";
						zoomImgCtr.style.top = imgPos.y + "px";
					}
					ctr.style.display = "block";
					zoomImgCtr.style.visibility = "visible";

					var pos = getEventMousePos(zoomInput, e);
					if (e.srcElement && isIE) {
						if (e.srcElement == zoom) return;
						if (e.srcElement != zoomInput) {
							var zoomImgPos = getElementPos(e.srcElement);
							pos.x -= (imgPos.x - zoomImgPos.x);
							pos.y -= (imgPos.y - zoomImgPos.y);
						}
					}
					var x = markerWidth/2;
					var y = markerHeight/2;

					if (!isIE) {
						pos.x -= imgLeft;
						pos.y -= imgTop;
					}

					if (pos.x < x) pos.x = x;
					if (pos.x > w-x) pos.x = w-x;
					if (pos.y < y) pos.y = y;
					if (pos.y > h-y) pos.y = h-y;

					var left = ((pos.x - x)|0);
					var top = ((pos.y - y)|0);
					
					// checks for zoom magnifier
					if (top >= 322) {
						top = 322;
					}
					if (left <= 2) {
						left = 2;
					} else if (left >= 186) {
						left = 186;
					}
					
					zoom.style.left = left + "px";
					zoom.style.top = top + "px";
					
					var zoomedLeft = -((pos.x*ratioW - ctrWidth/2)|0);
					var zoomedTop = -((pos.y*ratioH - ctrHeight/2)|0);
										
					// checks for zoomed image
					if (zoomedLeft >= -7) {
						zoomedLeft = -7;						
					} else if (zoomedLeft <= -867) {
						zoomedLeft = -867;	
					}
					
					zoomImg.style.left = zoomedLeft+"px";
					zoomImg.style.top = zoomedTop+"px";
				}
			);
		});

		// I've no idea. Simply setting the src will make IE screw it self into a 100% CPU fest. In a timeout, it's ok.
		setTimeout(function() { 
			zoomImg.src = zoomSrc;
		}, 1);

		}, 1);
	}

	function updateZoom(newSrc, sku) {
       
		// create object reference to detailmain image
		var img = $("imgl" + sku);
		// update zoom source
		var zoomSrc = newSrc.substring(0, newSrc.indexOf("$")) + "$zoom$";
        
		//var zoomSrc = newSrc.substring(0, newSrc.indexOf("$")) + "$testzoom$";
		// preload image
		preloadImages(newSrc, zoomSrc);
		// update thumbnail
		var formObj = document.forms["frml"+sku];
	    var imageEle = formObj["imgl"+sku];
	    if (getElement("imgl" + sku) == null)
			imageEle.src = preImgObj.src;
		getElement("imgl" + sku).src = preImgObj.src;	
		// zoomify
		makeZoomable(img, preZoomObj.src);
	}

	function preloadImages(imgSrc, zoomSrc) {
		preZoomObj = new Image();
		preZoomObj.src = zoomSrc;
        //alert(imgSrc);
		if (imgSrc != null) {
			preImgObj = new Image();
			preImgObj.src = imgSrc;
		}
	}

	function init() {

       

        
		var images = document.getElementsByTagName("img");
		for (var i=0;i<images.length;i++) {
			var img = images[i];
			if (img.name.indexOf("imgl") >= 0) {
				var zoomSrc = img.getAttribute("src").substring(0, img.getAttribute("src").indexOf("$")) + "$zoom$";
				preloadImages(null, zoomSrc);
                
				makeZoomable(img, preZoomObj.src, document.getElementById(img.getAttribute("id") + "_zoom"), null, null, img.getAttribute("data-zoomalwaysshow")=="true");
				break;
			}
		}
	}

	return {
		addEvent : addEvent,
		updateZoom : updateZoom,
		preloadImages : preloadImages, 
		init : init,
		makeZoomable : makeZoomable
	};
	
})();

MojoMagnify.addEvent(window, "load", MojoMagnify.init);