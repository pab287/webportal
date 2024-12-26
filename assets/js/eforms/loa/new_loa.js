$("#reason").on("blur", function (e) {
	var _this = $(e.target);
	var currentValue = e.target.value;
	currentValue = currentValue.trim();
	_this.val(currentValue);
});


// var tempEmployeeSelect2 = $("#select2_employee").select2({
// 	placeholder: 'SELECT AN OPTION',
// 	width: '100%',
// 	ajax: {
// 		url: baseUrl("eforms/Loa/get_employee_collection"),
// 		dataType: "json",
// 		delay: 250,
// 		global: false,
// 		processResults: function (data) {
// 			return data;
// 		}
// 	}
// });

$("#select2_employee").select2({
	placeholder: 'SELECT AN OPTION',
	width: '100%',
});

var sel = $("#select2_employee");
$.ajax({
	type: 'GET',
	url: baseUrl("eforms/Loa/get_user_emp_data"),
	dataType: 'JSON'
}).then(function (json) {
	if (json.response) {
		setTimeout(function () {
			var option = new Option(json.text, json.id, true, true);
			sel.append(option).trigger('change');
		}, 150);
	}
	if (typeof json.allow_search_employee !== "undefined" && json.allow_search_employee == true) {
		sel.select2({
			placeholder: 'SELECT AN OPTION',
			width: '100%',
			minimumInputLength: 3,
			ajax: {
				url: baseUrl("eforms/Loa/get_employee_collection"),
				dataType: "json",
				delay: 250,
				global: false,
				processResults: function (data) {
					return data;
				}
			}
		});
	}
});

sel.on("change", function (e) {
	var currentObject = $(e.target);
	currentObject.validate();
});

$('#under_from').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	todayBtn: true,
	format: 'yyyy/mm/dd hh:ii',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd hh:ii tt");
    var self = $(e.target);
    self.validate();
});

$('#under_to').timepicker({
	minuteStep: 1,
	showMeridian: false,
	use24hours: false,
	defaultTime: null,
});

$('#half_from').datepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	showButtonPanel: true,
	todayBtn: 'linked',
	format: 'yyyy/mm/dd',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd");
    var self = $(e.target);
    self.validate();
});

$('#whole_date').datepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	showButtonPanel: true,
	todayBtn: 'linked',
	format: 'yyyy/mm/dd',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd");
    var self = $(e.target);
    self.validate();
});

$('#date_from').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	todayBtn: true,
	format: 'yyyy/mm/dd hh:ii',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd hh:ii");
    var self = $(e.target);
    self.validate();
});

$('#date_to').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	todayBtn: true,
	format: 'yyyy/mm/dd hh:ii',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd hh:ii");
    var self = $(e.target);
    self.validate();
});

function emp_details() {
	var emp = $('[name="employee"]').val();
	$.ajax({
		url: baseUrl('eforms/Loa/ajax_emp_details/') + emp,
		type: "POST",
		dataType: "JSON",
		data: { csrf_token: _csrf_hash },
		success: function (json) {
			if (json.response) {
				var tempRow = Object.assign({}, json.row);
				var _tempHtml = "";
				var tempCompany = (typeof tempRow.company_id !== "undefined") ? tempRow.company_id : "No assigned company";
				var tempDepartment = (typeof tempRow.department_id !== "undefined") ? tempRow.department_id : "No assigned department";
				var tempPosition = (typeof tempRow.position !== "undefined") ? tempRow.position : "No assigned position";
				_tempHtml += tempCompany + '\n';
				_tempHtml += tempDepartment + '\n';
				_tempHtml += tempPosition;
				$('[name="company"]').val(_tempHtml);
			} else {
				$('[name="company"]').val("");
			}

		}, error: function (jqXHR, textStatus, errorThrown) {
			console.log('Error: "ajax_emp_details"');
		}
	});
}

function type_change() {
	var type = $('[name="type"]').val();
	if (type == "1") {
		document.getElementById('under_date').style.removeProperty('display');
		document.getElementById('half_date').style.display = 'none';
		document.getElementById('whole').style.display = 'none';
		document.getElementById('other_date').style.display = 'none';
	}
	if (type == "2") {
		document.getElementById('under_date').style.display = 'none';
		document.getElementById('half_date').style.removeProperty('display');
		document.getElementById('whole').style.display = 'none';
		document.getElementById('other_date').style.display = 'none';
	}
	if (type == "3") {
		document.getElementById('under_date').style.display = 'none';
		document.getElementById('half_date').style.display = 'none';
		document.getElementById('whole').style.removeProperty('display');
		document.getElementById('other_date').style.display = 'none';
	}
	if (type == "4") {
		document.getElementById('under_date').style.display = 'none';
		document.getElementById('half_date').style.display = 'none';
		document.getElementById('whole').style.display = 'none';
		document.getElementById('other_date').style.removeProperty('display');
	}
}
$.validate({
	form: '#form_loa',
	lang: 'en',
	onSuccess: function (form) {
		var currentForm = form[0];
		var formData = $(currentForm).serialize();
		//var disabled = $('#form_loa').find('textarea:disabled').removeAttr('disabled');
			if (parseInt($("#reason").val().length) < 30) {
				alert_function("Must have minimum of 30 characters.");
			}else{
				$("#btnSaveLoa").attr('disabled', true);
				$.ajax({
					url: baseUrl("eforms/loa/check_reason"),
					type: "POST",
					dataType: "JSON",
					global: false,
					data: {reason:$("#reason").val(), csrf_token: _csrf_hash},
					success: function (data) {
						console.log(data);
						if (data.reps!=="error") {
							alert_function("Invalid Reason!");
						} else {
							$.ajax({
								url: baseUrl("eforms/loa/add_loa"),
								type: "POST",
								dataType: "JSON",
								data: formData,
								success: function (data) {
									if (data.status) {
										//disabled.attr('disabled','disabled');
										toastr.success(data.toastr_msg, "Successfully saved!", 5000)
										window.location.replace(baseUrl("eforms/loa/masterfile"));
									} else {
										toastr.error("Failed to save LOA. `FROM DATE` must be less than `TO DATE`.", "Error!", 5000);
										$("#btnSaveLoa").attr('disabled', false);
									}
								}
							});
						}
					}
				});
			}
							


			//$('#reason_v').empty();
		 
			
			// else if (parseInt($("#reason").val().length) > 255) {
			// 	alert_function("Must have maximum of 255 characters.");
			// }

			//  else {
			 				
				
			// }
		return false;
	}
});

$(document).ready(function () {
	document.getElementById('half_date').style.display = 'none';
	document.getElementById('whole').style.display = 'none';
	document.getElementById('other_date').style.display = 'none';


	$(document).on("keypress","#reason",function(e){
		var reason = $(this).val();
		if (/(.)\1{3,}/.test(reason)) {
		    console.log('Not possible');
		    alert_function("Repeated characters are not allowed. Delete the repeated charachters to proceed.");
		    return false;
		} else {  
			console.log('possible'); 
			char_check();
		}
	})
	$(document).on("change","#reason",function(e){
		e.preventDefault();
		char_check(); 
		//check_duplicate();
			$.ajax({
				url: baseUrl("eforms/loa/check_reason"),
				type: "POST",
				dataType: "JSON",
				global: false,
				data: {reason:$(this).val(), csrf_token: _csrf_hash},
				success: function (data) {
					if (data.reps!=="error") {
						alert_function("Invalid Reason!");
					} else {
						//$('#reason_v').html('');
					}
				}
			});
	})
	function alert_function(msg){
		setTimeout(function(){
			$('#reason_v').html('<span class="help-block form-error" ><font color="#FF0000">'+msg+'</font></span>');
			$('#reason').attr("style","border-color: rgb(185, 74, 72)");	
		},100);
	}
	function char_check(){
		if (parseInt($("#reason").val().length) < 30) { alert_function("Must have minimum of 30 characters."); }
		if (parseInt($("#reason").val().length) > 30){ $('#reason_v').html(''); }
		if (parseInt($("#reason").val().length) > 255) { alert_function("Must have maximum of 255 characters."); }
	}
	function check_duplicate(){
			 //const str = $('#reason').val(),
			const str = "big black bug bit a big black dog on a his big black nose";
			const findDuplicateWords = str => {
			   const strArr = str.split(" ");
			   const res = [];
			   for(let i = 0; i < strArr.length; i++){
			      if(strArr.indexOf(strArr[i]) !== strArr.lastIndexOf(strArr[i])){
			         if(!res.includes(strArr[i])){
			            res.push(strArr[i]);
			         };
			      };
			   };
			   return res.join(" ");
			};
			console.log(findDuplicateWords(str));
           //            words = text.split(' '),
           //            sortedWords =words.slice(0).sort(),
           //            duplicateWords = []
           //  highlighted = [];
           //  for (var i = 0; i < sortedWords.length - 1; i++) {
           //     if (sortedWords[i + 1] == sortedWords[i]) {
           //         duplicateWords.push(sortedWords[i]);
           //      }
           //  }
           // duplicateWords =$.unique(duplicateWords);
           // for (var j = 0, m = []; j < words.length; j++) {
           //     m.push($.inArray(words[j],duplicateWords) > -1);
           //     if (!m[j] && m[j - 1])
           //          highlighted.push('</span>');
           //      else if (m[j] && !m[j - 1])
           //          highlighted.push('<span class="duplicate">');
           //      highlighted.push(words[j]);
           //  }
           //  $('#reason').html(highlighted.join(''));
	}
});

function alert_function(msg){
		setTimeout(function(){
			$('#reason_v').html('<span class="help-block form-error" ><font color="#FF0000">'+msg+'</font></span>');
			$('#reason').attr("style","border-color: rgb(185, 74, 72)");	
		},100);
	}