var pageLoaderHtml = '<div id="page-loader" class="animated-loader hidden">';
pageLoaderHtml +='<div id="nb-title" class="pulsate-loader">Loading</div>';
pageLoaderHtml +='<div class="nb-spinner"></div></div>';

var firstLoad = function(){ setTimeout(startPageLoader, 100); }

var checkProgressModal = function(){
	var _pModal = $("#modal_form_loading");
	if(typeof _pModal !== "undefined" && _pModal.length > 0){
		setTimeout(function(){
			if(_pModal.hasClass("in")){
				endPageLoader();
			}
		}, 1000);
	}
}

var startPageLoader = function(){
	var pageBody = $("body");
	if(typeof pageBody !== "undefined"){
		pageBody.append(pageLoaderHtml);
		var pageLoader = $("#page-loader");
		if(typeof pageLoader !== "undefined" && pageLoader.length > 0){
			if(pageLoader.hasClass("hidden")){
				pageLoader.removeClass("hidden");
				if(!pageLoader.hasClass("fadeIn-loader")){
					pageLoader.addClass("fadeIn-loader");					
					return true;
				}else{ return false; }
			}
		}else{ return false; }
	}else{ return "No Page Body"; }
}
var endPageLoader = function(){
	var pageBody = $("body");
	if(typeof pageBody !== "undefined"){
		var pageLoader = pageBody.find("#page-loader");
		if(typeof pageLoader !== "undefined" && pageLoader.length > 0){
			if(pageLoader.hasClass("fadeIn-loader")){
				pageLoader.removeClass("fadeIn-loader");
				if(!pageLoader.hasClass("fadeOut-loader")){
					pageLoader.addClass("fadeOut-loader");
					
					setTimeout(function(){ pageLoader.remove(); }, 200);
					return true;
				}else{ return false; }
			}
		}else{ return false; }
	}else{ return "No Page Body"; }
}
window.onload = firstLoad();
jQuery(document).ready(function($){
	setTimeout(endPageLoader, 200);
}).ajaxStart(function(){
	startPageLoader();
	checkProgressModal();
}).ajaxStop(function(){
	setTimeout(endPageLoader, 200);	
});