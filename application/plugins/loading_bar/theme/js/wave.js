var LOADING = (function(){
	if(document.getElementById('loader')!=null) return;
	var _initHTML = function(){
		var div = document.createElement("div");
		div.setAttribute('id', 'loader');
		div.innerHTML =`<div class="main"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div><div class="rect6"></div><div class="rect7"></div></div>`;
		document.body.appendChild(div);
	};
	var _initCss = function(){
		var styles = `#loader{display:none;position:fixed;z-index:9999;top:0;width:100vw;height:100vh;background:#0000008a;text-align:center}#loader .main{width:60px;height:100px;font-size:10px;top:50%;position:fixed;margin-top:-50px;text-align:center;margin-left:-30px;left:50%}#loader .main>div{margin: 0 1px;background-color:#fff;height:100%;width:6px;display:inline-block;-webkit-animation:sk-stretchdelay 1.2s infinite ease-in-out;animation:sk-stretchdelay 1.2s infinite ease-in-out}#loader .main .rect2{-webkit-animation-delay:-1.1s;animation-delay:-1.1s}#loader .main .rect3{-webkit-animation-delay:-1s;animation-delay:-1s}#loader .main .rect4{-webkit-animation-delay:-.9s;animation-delay:-.9s}#loader .main .rect5{-webkit-animation-delay:-.8s;animation-delay:-.8s}#loader .main .rect6{-webkit-animation-delay:-.7s;animation-delay:-.7s}#loader .main .rect7{-webkit-animation-delay:-.6s;animation-delay:-.6s}@-webkit-keyframes sk-stretchdelay{0%,100%,40%{-webkit-transform:scaleY(.4)}20%{-webkit-transform:scaleY(1)}}@keyframes sk-stretchdelay{0%,100%,40%{transform:scaleY(.4);-webkit-transform:scaleY(.4)}20%{transform:scaleY(1);-webkit-transform:scaleY(1)}}`;
		var styleSheet = document.createElement("style")
		styleSheet.type = "text/css"
		styleSheet.innerText = styles
		document.head.appendChild(styleSheet)
	};
	var _initAjax = function(){
		$(document).ajaxStart(function() {
			$('#loader').fadeIn(100);
		});
		$(document).ajaxComplete(function(event, xhr, settings) {
			$('#loader').delay(300).fadeOut(500); 
		});
	};
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