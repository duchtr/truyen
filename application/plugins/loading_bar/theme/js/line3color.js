var LOADING = (function(){
	if(document.getElementById('loader')!=null) return;
	var _initHTML = function(){
		var div = document.createElement("div");
		div.setAttribute('id', 'loader');
		div.innerHTML =`<div class="load-bar"><div class="bar"></div><div class="bar"></div><div class="bar"></div></div>`;
		document.body.appendChild(div);
	}
	var _initCss = function(){
		var styles = `#loader{position:fixed;top:0;left:0;z-index:99999;width:100%;display:none;}#loader .load-bar{position:relative;width:100%;height:3px;background-color:#fdba2c} #loader .bar{content:"";display:inline;position:absolute;width:0;height:100%;left:50%;text-align:center}#loader .bar:nth-child(1){background-color:#00923f;animation:loading 3s linear infinite}#loader .bar:nth-child(2){background-color:#e96a0c;animation:loading 3s linear 1s infinite}#loader .bar:nth-child(3){background-color:#245af3;animation:loading 3s linear 2s infinite}@keyframes loading{from{left:50%;width:0;z-index:100}33.3333%{left:0;width:100%;z-index:10}to{left:0;width:100%}}`;
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