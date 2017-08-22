/***************************************************************************************************************************************************
 	Author                   :      PA
	Version		             :	    1.0
	Date Created	         :  
	   
	Change history           
	
	Task/Bug 		        Date(mm-dd-yyyy)     Author 		     Change Description

    Trac 1127      		    07-23-2008	         PVeedu		         Code Modified for displaying 'Item Added to Your Wish List' 
																	 messgage and stay on  productdetails page when customer
																	 adds a product to their wish list on the product detail page.  
 
   Trac 1186                09-17-2008           PVeedu              Code modified for applying  a multi product template
   
   		
   Inventory status         09-24-2008         Pveedu              Inventory status message is not displayed for Family products  
   message is not 
   displayed for
   Family products                                 
  (Internal defect 221)
 
****************************************************************************************************************************************************/

//Array which holds the product ids mapped to the ProductSelector objects. 
var productSelectorArray = new Array();

/*
 * This class is used for sku selector functionality of multiple products.
 * The skuselector.js is used for skuselector functionality of single products.
 * This class acts as a wrapper class for skuselector.js and extends the skuselector
 * functionality for multiple products.  
 */
function ProductSelector(pProductId, isLeaderProduct){
    this.productId = pProductId;	
    this.skuSelector = new SkuSelector();
    this.skuList = new Array();
    this.sizeList = new Array();
    this.colorList = new Array();
    this.isLeaderProduct = isLeaderProduct;
    
    //Adds the given sku to the skuList Array
    this.addSku = function(pSku){
        this.skuList.push(pSku);
        this.addColor(pSku.colorCode, pSku.colorName);
    }
    
    //Adds the given size to the sizeList Array
    this.addSize = function(pSize){
		//alert("Sizelist:"+pSize);
        this.sizeList.push(pSize);
    }
    
        /*
         *Adds the given colorcode, colorname combination 
         * to the array if the color code does not exist in the colorlist already.
         */	
    this.addColor = function(pColorCode, pColorName){
        if(!this.containsColorCode(pColorCode)){
            this.colorList.push(new Color(pColorCode, pColorName));
        }
    }
    
        /*
         * Checks if the given color code exists in the color list.
         * Returns true if the given color code exists in the 
         * color list, otherwise returns false.
         */
    this.containsColorCode = function(pColorCode){
        for (var i = 0; i < this.colorList.length; i++) {
            if(this.colorList[i].colorCode == pColorCode){
                return true;
            }
        }
        return false;
    }
    
    //Returns the color name of the given color code
    this.getColorName = function(pColorCode){
        if(pColorCode != null){
            for (var i = 0; i < this.colorList.length; i++) {
                if(this.colorList[i].colorCode == pColorCode){
                    return this.colorList[i].colorName;
                }
            }
        }	
        return null;
    }
    
    this.initialize = function(){
		this.skuSelector.initialize(this.skuList, this.sizeList, "");	
    }	
    
    
    //Sets the given color as the currently selected color
    this.setSelectedColorCode = function(pSelectedColor) {
        if(isEmpty(pSelectedColor)){
            this.skuSelector.mSelectedColorCode =this.skuSelector.mAvailableColorCodes[0];
            this.skuSelector.initializeAvailableSizes(this.skuSelector.mSelectedColorCode, this.skuSelector.mSelectedSizeType);
            this.skuSelector.initializeDefaultSelectedSize();
        }
        else{
            this.skuSelector.mSelectedColorCode = pSelectedColor;
            this.skuSelector.initializeAvailableSizes(this.skuSelector.mSelectedColorCode, this.skuSelector.mSelectedSizeType);
            this.skuSelector.initializeDefaultSelectedSize();		
        }
    }
    
    //Sets the given size type as the currently selected size type 
    this.setSelectedSizeType = function(pSelectedSizeType) {
        this.skuSelector.mSelectedSizeType = pSelectedSizeType;
        this.skuSelector.initializeAvailableColors(this.skuSelector.mSelectedSizeType);
        this.skuSelector.initializeDefaultSelectedColor();
        this.skuSelector.initializeAvailableSizes(this.skuSelector.mSelectedColorCode, this.skuSelector.mSelectedSizeType);
        this.skuSelector.initializeDefaultSelectedSize();
        
    }
    
    //Sets the given size name as the currently selected size name
    this.setSelectedSizeName = function(pSelectedSizeName) {
        this.skuSelector.mSelectedSizeName = pSelectedSizeName;
        
    }
    
    //Repopulates the size types and highlights the currently selected size type
    this.repopulateSizeTypes = function(){		
        for(var i=0;i<this.skuSelector.mAvailableSizeTypes.length;i++){
            
            if(this.skuSelector.mAvailableSizeTypes[i] != ""){
                if(this.skuSelector.mAvailableSizeTypes[i] == this.skuSelector.mSelectedSizeType){	
                    if(getElement("sizetype"+this.productId+this.skuSelector.mAvailableSizeTypes[i])!=null){			
                        getElement("sizetype"+this.productId+this.skuSelector.mAvailableSizeTypes[i]).className="active";
                    }	
                    
                } else {
                    if(getElement("sizetype"+this.productId+this.skuSelector.mAvailableSizeTypes[i])!=null){
                        getElement("sizetype"+this.productId+this.skuSelector.mAvailableSizeTypes[i]).className="";
                    }	
                }
            }	
        }
    }
    
    
    //Repopulates the sizes  and highlights the currently selected size
    this.repopulateSizes = function(isLeaderProduct){
		//alert("into the loop");
        var modifiedProductId = this.productId;
        if(isLeaderProduct!=null & isLeaderProduct=="true"){
            modifiedProductId = "l"+this.productId;
        }
        if(getElement("size"+modifiedProductId) != null){
			//alert("it is null");
            var htmlStr="";
            if(this.skuSelector.mAllSizes.length > 0){
            if(this.skuSelector.mAvailableSizes.length==1){
				//alert("only one size");
            htmlStr+=this.skuSelector.mAvailableSizes[0];
            }
            else{
           //alert("having multiple sizes");
		   //alert("SizeID:"+modifiedProductId);
                htmlStr+= "<select id=\"size\" onchange=\"javascript:setSelectedSize('"+this.productId+"',this.options[this.selectedIndex].value,'"+isLeaderProduct+"')\" name=\"size\">";		
                if(this.productId == "UOGIFTCARD"){
					
					  htmlStr+="<option value=''>Select Amount</option>";
				}
				else
				{
					  htmlStr+="<option value=''>Select Size</option>";
				}
           
            for(var i=0;i<this.skuSelector.mAllSizes.length;i++){			
                var currentSku = this.skuSelector.getSku(this.skuSelector.mAllSizes[i]);			
                
                if(this.skuSelector.isAvailableSize(this.skuSelector.mAllSizes[i]) && currentSku != null){	
                    
                    if(this.skuSelector.mAllSizes[i] == this.skuSelector.mSelectedSizeName){						
                        htmlStr += "<option value='"+this.skuSelector.mAllSizes[i]+"' selected>"+this.skuSelector.mAllSizes[i]+"</option>";					
                    } else if(currentSku.availabilityStatus == this.skuSelector.AVAILABILITY_STATUS_BACKORDERABLE){				
                        htmlStr += "<option value='"+this.skuSelector.mAllSizes[i]+"'>"+this.skuSelector.mAllSizes[i]+"</option>";									
                    }
                    else if(currentSku.availabilityStatus == this.skuSelector.AVAILABILITY_STATUS_DISCONTINUED || currentSku.availabilityStatus == this.skuSelector.AVAILABILITY_STATUS_OUT_OF_STOCK){				
                    }
                    else {				
                        htmlStr += "<option value='"+this.skuSelector.mAllSizes[i]+"'>"+this.skuSelector.mAllSizes[i]+"</option>";					
                    }				
                } 
                else{
                }						
            }
             }
             }			
            htmlStr += "</select>";
            getElement("size"+modifiedProductId).innerHTML = htmlStr;
            
        }	
    }
    this.repopulateColors = function(isLeaderProduct){ 
        var modifiedProductId = this.productId;
        if(isLeaderProduct!=null & isLeaderProduct=="true"){
            modifiedProductId = "l"+this.productId;
        }
        
        var colorSelectBox = getElement(modifiedProductId+"color");
        
        if(colorSelectBox!=null && this.skuSelector.mSelectedColorCode!=null) {			
            var i=colorSelectBox.options.length;
            for (var j=0;j<i;j++){
                if(colorSelectBox.options[j].value==this.skuSelector.mSelectedColorCode){
                    colorSelectBox.options[j].selected=true;
                }
            }
        }
    }	
    
        /*
         * Wrapper function to repopulate size types, sizes.
         * Repopulating the size types will take care of showing the selected size type as
         * highlighted
         * Repopulating the sizes will take care of displaying the sizes according to their availability status.
         * Showing the selected color as highlighted is implemented as a seperate script named colorswatches.js
         * Displays the selected color, selected size in the page
         * Sets the value of CartFormModifierFormHandler.catalogRefIds value to the selected sku id.
         * Sets the name of the quantity field to the selected sku id.
         */
    this.repopulateAll = function(){
        var modifiedProductId = this.productId;
        if(this.isLeaderProduct!=null & this.isLeaderProduct=="true"){
            modifiedProductId = "l"+this.productId;
        }
        //Repopulates the size types.
        this.repopulateSizeTypes();
        
        //Repopulate Colors
        this.repopulateColors(this.isLeaderProduct);
        //Repopulates the sizes
        this.repopulateSizes(this.isLeaderProduct);
        var colorName = this.getColorName(this.skuSelector.mSelectedColorCode);
        if(colorName != null){
            //If there is selected color, displays the selected color in the page.
            if(getElement(modifiedProductId+"selectedColor") != null){				
                getElement(modifiedProductId+"selectedColor").innerHTML=colorName.toUpperCase();				
            }
            
            
            if(getElement(this.productId+"selColor") != null){
                getElement(this.productId+"selColor").innerHTML=colorName.toUpperCase();
            }	
        }	
        else {
            //If there is no selected color, displays selected color as empty in the page.
            if(getElement(modifiedProductId+"selectedColor") != null){	
                getElement(modifiedProductId+"selectedColor").innerHTML="";
            }
            
            if(getElement(this.productId+"selColor") != null){
                getElement(this.productId+"selColor").innerHTML="";
            }	
        }
        
        
        
        if(this.skuSelector.mSelectedSizeName != null){
            //If there is selected size, displays the selected size in the page.
            if(getElement(this.productId+"selectedSize") != null){
                getElement(this.productId+"selectedSize").innerHTML=this.skuSelector.mSelectedSizeName.toUpperCase();
            }	
            
            if(getElement(this.productId+"selSize")){
                getElement(this.productId+"selSize").innerHTML="SIZE"+" "+this.skuSelector.mSelectedSizeName.toUpperCase();
            }
        } else {
            //If there is no selected size, displays "NO SIZE SELECTED" in the page.
            if(getElement(this.productId+"selectedSize") != null){
                getElement(this.productId+"selectedSize").innerHTML="NO SIZE SELECTED";
            }	
            
            if(getElement(this.productId+"selSize")){
                getElement(this.productId+"selSize").innerHTML="NO SIZE SELECTED";
            }			
        }
        
        //If there is an element with id "<productid>selType", displays the selected size type in the page.
        if(getElement(this.productId+"selType") != null){
            if(this.skuSelector.mSelectedSizeType != null){
                if(this.skuSelector.mSelectedSizeName != null){
                    if(this.skuSelector.mAvailableSizeTypes.length>1){
                        //If there is selected size type, displays the selected size in the page.
                        getElement(this.productId+"selType").innerHTML=","+" "+this.skuSelector.mSelectedSizeType.toUpperCase();	
                        
                    }
                    else if(this.skuSelector.mAvailableSizeTypes.length==1){
                        if(this.skuSelector.mSelectedSizeType=="regular"){
                            getElement(this.productId+"selType").innerHTML="";
                        }
                        else{
                            getElement(this.productId+"selType").innerHTML=","+" "+this.skuSelector.mSelectedSizeType.toUpperCase();
                        }
                    }
                    else{
                        getElement(this.productId+"selType").innerHTML="";
                    }
                }
                else {
                    getElement(this.productId+"selType").innerHTML= "";
                }
            } else {
                //If there is no selected size type, displays the selected size type as in the page.
                getElement(this.productId+"selType").innerHTML= "";
            }
        }
        
        
                /*
                 * If there is a selected sku, then sets the value of CartFormModifierFormHandler.catalogRefIds, 
                 * name of the quantity field with selected sku id.
                 * Sku is considered as selected if a size is selected for a product
                 * if there is a select checkbox, Sku is considered as selected if both size and select checkbox is selected for a product.
                 */
        
        
        if(this.skuSelector.getSelectedSku() != null 
        && (getElement(modifiedProductId+"selectBox")==null 
        || getElement(modifiedProductId+"selectBox").checked)){
            
            var selectedSku = this.skuSelector.getSelectedSku();
            if(getElement(modifiedProductId+"catalogRefId") != null){
                getElement(modifiedProductId+"catalogRefId").value = selectedSku.skuId;
            }
            if(getElement(modifiedProductId+"qty") != null){
                getElement(modifiedProductId+"qty").name = selectedSku.skuId;			
            }
            
            var frmName = "frm"+this.productId;
            if(this.isLeaderProduct){
                frmName = "frml"+this.productId;
            }
            if(getElement(modifiedProductId+"addToBagBtn")!=null){
                getElement(modifiedProductId+"addToBagBtn").innerHTML="<input type=\"button\" name=\"AddtoCart\" value=\"Add to Cart\" class=\"add_tocart\" onclick=\"javascript:addItemToBag('"+frmName+"');\"></input>";
            }
            if(getElement(modifiedProductId+"addToWishlistBtn")!=null){
                getElement(modifiedProductId+"addToWishlistBtn").innerHTML="<input type=\"button\" name=\"AddtoWishlist\" value=\"Add to Wishlist\" class=\"add_wishlist\" onclick=\"javascript:addItemToWishlist('"+frmName+"');\"></input>";
            }
            
        } else {
            if(getElement(modifiedProductId+"catalogRefId") != null){
                getElement(modifiedProductId+"catalogRefId").value = "";
            }
            if(getElement(modifiedProductId+"qty") != null){
                getElement(modifiedProductId+"qty").name = "";
            }
            
            var spanIdName = this.productId+"colorsizenotavailable";
            var productIdName= this.productId;
            if(this.isLeaderProduct){
                spanIdName = "l"+spanIdName;
            }
            if(getElement(modifiedProductId+"addToBagBtn")!=null){
				
                getElement(modifiedProductId+"addToBagBtn").innerHTML="<input type=\"button\" name=\"AddtoCart\" value=\"Add to Cart\" class=\"add_tocart\" onclick=\"javascript:displayColorSizeNotAvailableMessage('"+spanIdName+"','"+productIdName+"');\"></input>";
            }
            if(getElement(modifiedProductId+"addToWishlistBtn")!=null){
                getElement(modifiedProductId+"addToWishlistBtn").innerHTML="<input type=\"button\" name=\"AddtoWishlist\" value=\"Add to Wishlist\" class=\"add_wishlist\" onclick=\"javascript:displayColorSizeNotAvailableMessage('"+spanIdName+"','"+productIdName+"');\"></input>";
            }
            
        }
    }
}	


//Returns the productselector object based on the given product id.
function getProductSelector(pProductId, createNew, pIsLeaderProduct){
        /*
         * Loops through and returns the productselector object
         * from the product selector array for the given product id
         */
		 //alert("Size:"+productSelectorArray.length);
    for(var i=0;i<productSelectorArray.length;i++){		
        if(productSelectorArray[i].productId == pProductId && productSelectorArray[i].isLeaderProduct == pIsLeaderProduct){		
            //alert("Display:"+productSelectorArray[i]);
			return productSelectorArray[i];
        }
    }
    
    if(createNew){
		//alert("CreateNew:"+createNew);
        /* 
         * If the createNew boolean flag is set to true and 
         * product selector is not available for the given product id,
         * then creates a new productselector object for the given product id and
         * returns the newly created productselector object.
         */
        
        productSelectorArray.push(new ProductSelector(pProductId, pIsLeaderProduct));	
        return productSelectorArray[productSelectorArray.length - 1];
    } else {
                /*If the createNew boolean flag is not set to true and 
                 * product selector is not available for the given product id
                 * then return null
                 */
        return null;
    }	
    
}

/*
 * Adds the given sku to the product selector of the given product id.
 */
function addSku(pProductId, sku, pIsLeaderProduct){	
    //alert("addSku"+pProductId+pIsLeaderProduct);
    var productSelector = getProductSelector(pProductId, true, pIsLeaderProduct);
    productSelector.addSku(sku);
}

/*
 * Adds the given size to the product selector of the given product id.
 */
function addSize(pProductId, size, pIsLeaderProduct){
    //alert("addSize"+pProductId+pIsLeaderProduct);
    var productSelector = getProductSelector(pProductId, true, pIsLeaderProduct);
    productSelector.addSize(size);
}


/*
 * Initializes the product selector of the given product id.
 * Internally initializes the skuselector wrapped by product selector.
 * Sets the given color code as the selected color.
 * Sku selector initializes the available sizetypes, available colors, available colors,
 * default selected size type and default selected color based on the skuList and sizeList
 * properties of product selector.
 */
function initializeProduct(productId, colorCode, pIsLeaderProduct){
    //alert("initializeProduct"+productId+pIsLeaderProduct);
    var productSelector = getProductSelector(productId, true, pIsLeaderProduct);
    productSelector.skuSelector.mSelectedColorCode = colorCode;
    productSelector.initialize();
}


/*
 * Sets the given color code as selected and reinitializes all products.
 * Also takes the extra parameters to show different swatches for selected and
 * unselected image.
 * It calls the imgClicked function from the colorswatches.js. This function takes
 * care of showing the current image as selected and rest of the images below to this
 * color swatch as unselected.
 */
function setSelectedColor(imgElem, pProductId, pColorCode, prodImg, pSkuId, pColorName, isLeaderProduct){
	//update zoom
	MojoMagnify.updateZoom(prodImg, pProductId);
	var colorSelector = getColorSelector(pColorName,false);
	var altImageList = new Array();
	altImageList = colorSelector.getImageList();
	if(getElement(pProductId+"altViews1")!=null && getElement(pProductId+"altViews1")!=""){
	var str="";
	/* begin multiproduct detail fix for extra thumbnails */
	var total = altImageList.length;
	if (total > 5) {
		// only five thumbnails should ever be displayed on the product detail page
		total = 5;
	}
	/* end multiproduct detail fix for extra thumbnails */
	for(var i=0;i<total;i++){
str =str+"<dl><dd>"+altImageList[i]+"</dd></dl>";
}
getElement(pProductId+"altViews1").innerHTML=str;
}
    var productSelector = getProductSelector(pProductId, false, isLeaderProduct);
    
    if(productSelector != null){
        
        productSelector.setSelectedColorCode(pColorCode);
        initializeAllProducts(isLeaderProduct);
    }
    changeColor(pProductId, pColorCode, prodImg, pSkuId, pColorName, isLeaderProduct);	
    //imgClicked(imgElem.id, imgElem);
}

// Sets the given size type as selected and reinitializes all products.
function setSelectedType(pProductId, pSizeType, isLeaderProduct){
    var productSelector = getProductSelector(pProductId, false, isLeaderProduct);
    if(productSelector != null){
        productSelector.setSelectedSizeType(pSizeType);
        initializeAllProducts(isLeaderProduct);
    }	
    
}

// Sets the given size as selected and reinitializes all products.
function setSelectedSize(pProductId, pSizeName, isLeaderProduct){
	//alert("hi");
    //alert("setSelectedSize "+pSizeName);
       //code  modified for the  Trac 1127 
      ///Checks whether the given getElement is addedtoWish.
     var addedtoWishVar = getElement("addedtoWish");
	if(addedtoWishVar != undefined || addedtoWishVar != null){
	  getElement("addedtoWish").innerHTML="";
	}
	//end of code modified for the Trac 1127
	 //code  modified for the  Trac 1186 
      var addedtocartVar = getElement("addedtocart");
	if(addedtocartVar != undefined || addedtocartVar != null){
	  getElement("addedtocart").innerHTML="";
	}
    //end of code modified for the Trac 1186
    
     //code  modified for the  Internal defect 221
    var addedtocartmess = getElement("addedtomess");
	if(addedtocartmess != undefined || addedtocartmess != null){
	  getElement("addedtomess").innerHTML="";
	}
	 //end of code modified for the Internal defect 221
    var productSelector = getProductSelector(pProductId, false, isLeaderProduct);
    if(productSelector != null){
        productSelector.setSelectedSizeName(pSizeName);
        initializeAllProducts(isLeaderProduct);	
    }
    var spanIdName = pProductId+"colorsizenotavailable";
            if(isLeaderProduct){
                spanIdName = "l"+spanIdName;
            }
	
	getElement(spanIdName).innerHTML="";
    
}

/**
 * Initializes all the products by calling repopulateAll function of each productselector.
 * Enables the Add to bag and Add to Wishlist buttons if atleast one sku is selected for any one of the products.
 * Disables the Add to bag and Add to Wishlist buttons if no sku is selected for any product.
 * toolTip will be displayed with the specified messages on MouseOver on the disabled Add to bag and Add to Wishlist buttons.
 * Sku is considered as selected if a size is selected for a product
 * if there is a select checkbox, Sku is considered as selected if both size and select checkbox is selected for a product.
 */	
function initializeAllProducts(){
    //checks whether the sku is selected or not.
    var skuSelected = false;
    //ckecks whether the size is selected.
    var sizeSelect = false;
    //checks whether the checkbox is selected when size is not selected
    var boxSelected = false;
    //this variable is used along with the skuSelected to know whether the sku is selected or not
    var checkBox = true;
    
    
    
    for(var i=0;i<productSelectorArray.length;i++){
        productSelectorArray[i].repopulateAll();
        
        if(productSelectorArray[i].skuSelector.getSelectedSku() != null){			
            if(getElement(productSelectorArray[i].productId+"selectBox")!=null){
                if(getElement(productSelectorArray[i].productId+"selectBox").checked){
                    skuSelected = true;
                }
            } else {
                sizeSelect = true;
                skuSelected = true;
            }	
        } else{
            if(getElement(productSelectorArray[i].productId+"selectBox")!=null){
                if(getElement(productSelectorArray[i].productId+"selectBox").checked){
                    boxSelected = true;
                    checkBox = false;
                }
            } else{
                sizeSelect = true;
            }
        }
    }
    
/*  	if(skuSelected && checkBox){
                getElement("addToBagBtn").innerHTML="<a href=\"javascript:addItemToBag();\"><img src=\"../images/btn_addtobag.gif\"	alt=\"Add to Bag\"/></a>";
                getElement("addToWishlistBtn").innerHTML="<a href=\"javascript:addItemToWishlist();\"><img	src=\"../images/btn_addtowishlist.gif\" alt=\"Add to Wishlist\"/></a>";
        } else {	
        var message = "";
        if(sizeSelect){
                message = "PLEASE SELECT A SIZE";
                } else{
                        if(boxSelected){
                                 message = "PLEASE SELECT A SIZE";
                         }
                         else{
                                message = "PLEASE SELECT AN ITEM";
                         }
                }
 
                getElement("addToBagBtn").innerHTML="<img style=\"cursor:pointer\" src=\"../images/btn_addtobag_disabled.gif\"/ onMouseout=\"hideddrivetip()\" onMouseover=\"ddrivetip(\'<div id=tooltip>"+message+"</div>\',\'#FFFFFF\',\'#8B0000\',\'160\',true)\"  alt=\"Add to Bag Disabled\">";
                getElement("addToWishlistBtn").innerHTML="<img style=\"cursor:pointer\" src=\"../images/btn_addtowishlist_disabled.gif\"  onMouseout=\"hideddrivetip()\" onMouseover=\"ddrivetip(\'<div id=tooltip>"+message+"</div>\',\'#FFFFFF\',\'#8B0000\',\'160\',true)\" alt=\"Add to Wishlist Disabled\">";
        }	*/
}

function SkuSelector() {
    
    this.AVAILABILITY_STATUS_IN_STOCK = 1000;
    this.AVAILABILITY_STATUS_OUT_OF_STOCK = 1001;
    this.AVAILABILITY_STATUS_PREORDERABLE = 1002;
    this.AVAILABILITY_STATUS_BACKORDERABLE = 1003;
    this.AVAILABILITY_STATUS_DISCONTINUED = 1005;
    
    this.contextPath = "";
    this.skus = null; //variable to hold list of all product sku's
    
    this.mSelectedColorCode = null; //variable to hold currently selected color
    
    this.mSelectedSizeType = null; //variable to hold currently selected size type
    
    this.mSelectedSizeName = null; //variable to hold currently selected size name
    
    //variable to hold current list of available colors with respect to currently selected
    // size type and currently selected size name
    this.mAvailableColorCodes = null; 
    
    //variable to hold current list of available size types with respect to currently selected
    // color and currently selected size name
    this.mAvailableSizeTypes = null; 
    
    //variable to hold current list of available sizes with respect to currently selected
    // size type and currently selected color
    this.mAvailableSizes = null;
    
    this.mAllSizes = null;
    
    //Initializes the Available size types, colors and sizes with default values
    // from the given list of skus.	
    this.initialize = function(skuList, sizeList, path){		
		//alert("this is one executed");
        //alert("SkuSelector.initialize(skuList)");
        this.skus = skuList;
        this.mAllSizes = sizeList;
        this.contextPath = path;
        
        this.initializeAvailableSizeTypes();
        this.initializeDefaultSelectedSizeType();
        this.initializeAvailableColors(this.mSelectedSizeType);
        this.initializeDefaultSelectedColor();
        this.initializeAvailableSizes(this.mSelectedColorCode,
        this.mSelectedSizeType);
        this.initializeDefaultSelectedSize();
    }	
    
    
    //Initializes the list containing all the available size types.
    this.initializeAvailableSizeTypes =  function(){
        //alert("calling initializeAvailableSizeTypes");
        this.mAvailableSizeTypes = new Array();
        var index = 0;
        for (var i = 0; i < this.skus.length; i++) {
            var currentSku = this.skus[i];
            if (this.mAvailableSizeTypes,this.skus[i].type!="" && !contains(this.mAvailableSizeTypes,this.skus[i].type)) {
                this.mAvailableSizeTypes[index] = this.skus[i].type;
                index++;
            }
        }
        this.mAvailableSizeTypes.sort();
        this.sortSizeTypes(this.mAvailableSizeTypes);
    }
    
    this.sortSizeTypes = function(array){
        if(contains(array,"regular") && contains(array,"petite")){
            var temp = array[0];
            array[0] = array[1];
            array[1] = temp;
        }
    }
    
    //Sets the default currently selected size type.
    this.initializeDefaultSelectedSizeType = function () {
        //alert("calling initializeDefaultSelectedSizeType");
        
        if (isEmpty(this.mSelectedSizeType)) {
            // Get default size type from brand config
            this.mSelectedSizeType = this.mAvailableSizeTypes[0];
        } else {
            // Check if the current selected size type is actually available
            var currentSelectedSizeTypeIsAvaiable = false;
            for (var i = 0; i < this.mAvailableSizeTypes.length; i++) {
                var sizeType = this.mAvailableSizeTypes[i];
                if (sizeType == this.mSelectedSizeType) {
                    currentSelectedSizeTypeIsAvaiable = true;
                    break;
                }
            }
            if (!currentSelectedSizeTypeIsAvaiable) {
                // Set to the first available size type
                this.mSelectedSizeType = this.mAvailableSizeTypes[0];
            }
        }
    }		
    
    //Initializes the list containing all the available colors with respect
    //to the given size type.
    this.initializeAvailableColors = function (pSizeType) {
        //alert("calling initializeAvailableColors(pSizeType)");
        
        this.mAvailableColorCodes = new Array();
        var index = 0;
        for (var i = 0; i < this.skus.length; i++) {
            var currentSku = this.skus[i];
            //if (currentSku.type == pSizeType) {
            if (this.mAvailableColorCodes, currentSku.colorCode!="" && !contains(this.mAvailableColorCodes, currentSku.colorCode)) {
                this.mAvailableColorCodes[index] = currentSku.colorCode;
                index++;
            }
            
            //}
        }
    }
    
    //Sets the default currently selected color.
    this.initializeDefaultSelectedColor = function() {
        //alert("calling initializeDefaultSelectedColor");
        
        // If no color is selected, select the sorted first color
        if (isEmpty(this.mSelectedColorCode)) {
            this.mSelectedColorCode = this.mAvailableColorCodes[0];
        } else {
            var selectedColorIsPresentInAvailableColors = false;
            // Check if the previously selected color is actually present in the
            // available colors
            for (var i = 0; i < this.mAvailableColorCodes.length; i++) {
                var color = this.mAvailableColorCodes[i];
                if (color == this.mSelectedColorCode) {
                    selectedColorIsPresentInAvailableColors = true;
                    break;
                }
            }
            if (!selectedColorIsPresentInAvailableColors) {
                // If selected color code is not available now, initialize it to
                // the first one
                this.mSelectedColorCode = this.mAvailableColorCodes[0];
            }
        }
        
    }
    
    //Initializes the list containing all the available sizes with respect
    //to the given size type and color.
    this.initializeAvailableSizes = function(pSelectedColor, pSelectedSizeType) {
        //alert("calling initializeAvailableSizes("+pSelectedColor+", "+pSelectedSizeType+")");
        // Retrieve available sizes for this product			
        this.mAvailableSizes = new Array();
        var index = 0;
        for (var i = 0; i < this.skus.length; i++) {
            var currentSku = this.skus[i];
            //alert(currentSku.type+","+currentSku.colorCode);
            if ((currentSku.type == pSelectedSizeType) && (currentSku.colorCode == pSelectedColor)) {
                if (this.mAvailableSizes, currentSku.sizeName!="" && !contains(this.mAvailableSizes, currentSku.sizeName)) {
                    this.mAvailableSizes[index] = currentSku.sizeName;
                    index++;
                }
            }
        }		
        
    }
    
    //Sets the default currently selected size.
    this.initializeDefaultSelectedSize = function () {
		
			//alert("in default selected size");
        if(this.mAvailableSizes.length==1){
		        this.mSelectedSizeName=this.mAvailableSizes[0];
		        return;
        }
        else{
		        for (var i = 0; i < this.mAvailableSizes.length; i++) {
		            var sizeName = this.mAvailableSizes[i];
					//alert("Size:"+sizeName);
		            if (sizeName == this.mSelectedSizeName) {
		                if(this.getSku(this.mAvailableSizes[i]) != null && (this.getSku(this.mAvailableSizes[i]).availabilityStatus == this.AVAILABILITY_STATUS_IN_STOCK || this.getSku(this.mAvailableSizes[i]).availabilityStatus == this.AVAILABILITY_STATUS_BACKORDERABLE)){
		                    return;
		                }
		            }
		        }
        
        }
        this.mSelectedSizeName = null;
        
    }
    
    
    
    //Returns the skuid based on the currently selected size type, color and size name.
    this.getSelectedSku = function() {
        for (var i = 0; i < this.skus.length; i++) {
            var sku = this.skus[i];
            //alert(sku+","+sku.colorCode+","+sku.sizeName+","+sku.type+","+this.mSelectedColorCode+","+this.mSelectedSizeName+","+this.mSelectedSizeType);
            if(sku.colorCode!='' && sku.sizeName!=''){
                if ((sku.colorCode == this.mSelectedColorCode) 
                && (sku.sizeName == this.mSelectedSizeName)
                && (sku.type == this.mSelectedSizeType)) {
                    return sku;
                }
            } else if(sku.colorCode=='' && sku.sizeName!=''){
                if ((sku.sizeName == this.mSelectedSizeName)
                && (sku.type == this.mSelectedSizeType)) {
                    return sku;
                }
            } else if(sku.colorCode!='' && sku.sizeName==''){
                if (sku.colorCode == this.mSelectedColorCode) {
                    return sku;
                }
            }else {
                return sku;
            }
        }
        return null;
    }
    
    this.getSku = function(pSizeName) {
        
        for (var i = 0; i < this.skus.length; i++) {
            var sku = this.skus[i];
            if(this.mSelectedColorCode != null){
                if ((sku.colorCode == this.mSelectedColorCode) 
                && (sku.sizeName == pSizeName)
                && (sku.type == this.mSelectedSizeType)) {
                    
                    return sku;
                }
            } else {
                if ((sku.sizeName == pSizeName)
                && (sku.type == this.mSelectedSizeType)) {
                    
                    return sku;
                }	
            }
        }
        return null;
    }
    
    this.getColorName = function(pColorCode){
        for (var i = 0; i < this.skus.length; i++) {
            var sku = this.skus[i];
            if (sku.colorCode == pColorCode) {
                return sku.colorName;
            }
        }
        return null;
    }
    
    this.isAvailableSize = function(pSizeName){
        for (var i = 0; i < this.mAvailableSizes.length; i++) {			
            if (this.mAvailableSizes[i] == pSizeName) {
                return true;
            }
        }
        return false;
    }
}

function Sku(pSkuId, pSizeName, pType, pColorCode, pColorName, pAvailabilityStatus, pAvailabilityDate, pImagePath) {
    this.skuId = pSkuId;
    this.sizeName = pSizeName;		
    this.type = pType;
    this.colorCode = pColorCode;
    this.colorName = pColorName;		
    this.availabilityStatus = pAvailabilityStatus;
    this.availabilityDate = pAvailabilityDate;
    this.imagePath = pImagePath;
}


//Returns true if the given string is empty.
function isEmpty(pValue) {
    if ((pValue == null) || (trim(pValue) == "")) {
        return true;
    } else {
        return false;
    }
}
function trim(str) {
    return str.replace(/^\s*|\s*$/g,"");
}


//Checks whether the given array contains the given value
//Returns true if the given array contains the given value, otherwise false.
function contains(array, value){
    for(var i=0;i<array.length;i++){
        if(array[i] == value){
            return true;
        }
    }
    return false;
}


// Returns the element by using the given id with respect to browser compatibility.	
function getElement(elementId){
    return document.all ? document.all[elementId] : document.getElementById(elementId);
}

function displayColorSizeNotAvailableMessage(spanElementId,productElementId){
     
      //code  modified for the  Trac 1127 
      ///Checks whether the given getElement is addedtoWish.
     var addedtoWishVar = getElement("addedtoWish");
	if(addedtoWishVar != undefined || addedtoWishVar != null){
	  getElement("addedtoWish").innerHTML="";
	}
	
	//end of code modified for the Trac 1127
	//code  modified for the  Trac 1186 
	    var addedtocartVar = getElement("addedtocart");
	if(addedtocartVar != undefined || addedtocartVar != null){
	  getElement("addedtocart").innerHTML="";
	}
	//End of code  modified for the  Trac 1186 
	
	 //code  modified for the  Internal defect 221.
	var addedtocartmess  = getElement("addedtomess");
	if(addedtocartmess  != undefined || addedtocartmess  != null){
	  getElement("addedtomess").innerHTML="";
	}
	 //End of code  modified for the  Internal defect 221.
	 
	//getElement(spanElementId).innerHTML="<span class=\"red\"><p>We are sorry but your selected size and color combination is not available.</p></span>";
    if(productElementId == "UOGIFTCARD"){
					
		 getElement(spanElementId).innerHTML="<div id=\"added_tocart\"><em>Please select a Amount</em></div>";
	}
	else
	{
		 getElement(spanElementId).innerHTML="<div id=\"added_tocart\"><em>Please select a size</em></div>";
	}
    
}



var colorSelectorArray = new Array();
function ColorSelector(pColorId){

this.colorId = pColorId;

this.imageList = new Array();

	this.addImageList=function(pImageList){
	
	for(var i=0;i<pImageList.length;i++){
	
	this.imageList.push(pImageList[i]);
	
		}

	}
	this.getImageList=function(){
	return this.imageList;
	}
	}

function getColorSelector(pColorId, createNew){

/*

* Loops through and returns the productselector object

* from the product selector array for the given product id

*/

for(var i=0;i<colorSelectorArray.length;i++){ 

if(colorSelectorArray[i].colorId == pColorId){ 

return colorSelectorArray[i];

}

}


if(createNew){

/* 

* If the createNew boolean flag is set to true and 

* product selector is not available for the given product id,

* then creates a new productselector object for the given product id and

* returns the newly created productselector object.

*/


colorSelectorArray.push(new ColorSelector(pColorId)); 

return colorSelectorArray[colorSelectorArray.length - 1];

} else {

/*If the createNew boolean flag is not set to true and 

* product selector is not available for the given product id

* then return null

*/

return null;

} 


}
function addImageViews(colorId,imageViews){
	//alert(colorId);
var colorSelector = getColorSelector(colorId, true);

colorSelector.addImageList(imageViews);
}

