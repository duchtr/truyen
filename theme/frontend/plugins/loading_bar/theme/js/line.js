var LOADING = (function(){
	var _initHTML = function(){
		if(document.getElementById('loader')!=null) return;
		var div = document.createElement("div");
		div.setAttribute('id', 'loader');
		div.innerHTML =`<div class="bar"></div>`;
		document.body.appendChild(div);
	}
	var _initCss = function(){
		var styles = `@keyframes loader-animation{0%{width:0%}49%{width:100%;left:0}50%{left:100%}100%{left:0;width:100%}}#loader{display:none;height:3px;width:100%;height:5px;width:100%;position:fixed;top:0;z-index:99999;left:0}#loader .bar{position:relative;height:3px;background-color:#1e90ff;animation-name:loader-animation;animation-duration:3s;animation-iteration-count:infinite;animation-timing-function:ease-in-out}`;
		var styleSheet = document.createElement("style")
		styleSheet.type = "text/css"
		styleSheet.innerText = styles
		document.head.appendChild(styleSheet)
	}
	var _initAjax = function(){
		$(document).ajaxStart(function() {
			$('#loader').fadeIn(100);
		});
		$(document).ajaxComplete(function(event, xhr, settings) {
			$('#loader').delay(300).fadeOut(500); 
		});
	}
	return {
		_:function(){
			_initHTML();
			_initCss();
			_initAjax();
		}
	}
})();
$(function() {
	LOADING._();
});