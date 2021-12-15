var LOADING = (function(){
	if(document.getElementById('loader')!=null) return;
	var _initHTML = function(){
		var div = document.createElement("div");
		div.setAttribute('id', 'loader');
		div.innerHTML =`<div class="sk-cube-grid"><div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>`;
		document.body.appendChild(div);
	}
	var _initCss = function(){
		var styles = `#loader{display:none;position:fixed;z-index:9999;top:0;width:100vw;height:100vh;background:#0000008a;text-align:center}#loader .sk-cube-grid{width:80px;height:80px;margin-top:-40px;top:50%;position:fixed;left:50%;margin-left:-40px}#loader .sk-cube-grid .sk-cube{width:33%;height:33%;background-color:#fff;float:left;-webkit-animation:sk-cubeGridScaleDelay 1.3s infinite ease-in-out;animation:sk-cubeGridScaleDelay 1.3s infinite ease-in-out}#loader .sk-cube-grid .sk-cube1{-webkit-animation-delay:.2s;animation-delay:.2s}#loader .sk-cube-grid .sk-cube2{-webkit-animation-delay:.3s;animation-delay:.3s}#loader .sk-cube-grid .sk-cube3{-webkit-animation-delay:.4s;animation-delay:.4s}#loader .sk-cube-grid .sk-cube4{-webkit-animation-delay:.1s;animation-delay:.1s}#loader .sk-cube-grid .sk-cube5{-webkit-animation-delay:.2s;animation-delay:.2s}#loader .sk-cube-grid .sk-cube6{-webkit-animation-delay:.3s;animation-delay:.3s}#loader .sk-cube-grid .sk-cube7{-webkit-animation-delay:0s;animation-delay:0s}#loader .sk-cube-grid .sk-cube8{-webkit-animation-delay:.1s;animation-delay:.1s}#loader .sk-cube-grid .sk-cube9{-webkit-animation-delay:.2s;animation-delay:.2s}@-webkit-keyframes sk-cubeGridScaleDelay{0%,100%,70%{-webkit-transform:scale3D(1,1,1);transform:scale3D(1,1,1)}35%{-webkit-transform:scale3D(0,0,1);transform:scale3D(0,0,1)}}@keyframes sk-cubeGridScaleDelay{0%,100%,70%{-webkit-transform:scale3D(1,1,1);transform:scale3D(1,1,1)}35%{-webkit-transform:scale3D(0,0,1);transform:scale3D(0,0,1)}}`;
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