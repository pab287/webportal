jQuery(document).ready(function($){
	toastrInit();
	inputSetCursorPosition();
});

var toastrInit = function(){
	if(typeof toastr !== "undefined"){
		toastr.options = {
		  "closeButton": false,
		  "debug": true,
		  "newestOnTop": true,
		  "progressBar": true,
		  "positionClass": "toast-top-right",
		  "preventDuplicates": true,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
		return toastr.options;
	}else{
		return false;
	}
}

var inputSetCursorPosition = function(){
	$.fn.setCursorPosition = function (pos) {
		this.each(function (index, elem) {
			if (elem.setSelectionRange) {
				elem.setSelectionRange(pos, pos);
			} else if (elem.createTextRange) {
				var range = elem.createTextRange();
				range.collapse(true);
				range.moveEnd('character', pos);
				range.moveStart('character', pos);
				range.select();
			}
		});
		return this;
	};	
}

var numberFormat = function(numbers){
	if(numbers){
		var _num = numbers.toString().replace(/,/g, "");
		_num = parseFloat(_num).toFixed(2);
		
		var components = _num.toString().split(".");
		components [0] = components [0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		
		return components.join(".");		
	}else{
		return "0.00";
	}
}

var toNumber = function(numbers){
	if(numbers){
		var _num = numbers.toString().replace(/,/g, "");
		_num = parseFloat(_num).toFixed(2);
		return _num;
	}else{
		return "0.00";
	}
}

jQuery.fn.extend({
	doneTyping: function(callback){
		var _this = $(this);
		var x_timer;    
		_this.keyup(function (){
			clearTimeout(x_timer);
			x_timer = setTimeout(clear_timer, 1000);
		}); 

		function clear_timer(){
			clearTimeout(x_timer);
			callback.call(_this);
		}
	}
});