var FORM = (function(){
	var validate = function(frm){
		var res = frm.querySelectorAll('[crequired]');
		for (var i = 0; i < res.length; i++) {
			var item = res[i];
			if(item.value.trim() ==''){
				item.focus();
			}
		}
	};
	var submitForm = function (){
		$(document).on('submit', '.ajaxform', function(event) {
			event.preventDefault();
			var item = this;
		});
	};
	return {
		_:function(){
			submitForm();
		}
	}
})();