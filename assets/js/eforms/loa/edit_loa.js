var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split("&"),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

var typingTimer0;
var doneTypingInterval0 = 500;

var search_val = "";
param_id = getUrlParameter("id");
var name = null;

$.ajax({
    url: baseUrl("eforms/loa/ajax_loa_details/") + param_id + '/edit',
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        tempName = data.name;
        $('[name="nature"]').val(data.data.nature).trigger("change");
        switch (data.data.type) {
            case "1":
                $('[name="type"]').val('1').trigger("change");
                dt = data.data.date_to;
                $('[name="under_from"]').val(data.data.date_from);
                $('[name="under_to"]').val(dt.substr(11, 8));

                $('#date-from-hidden').val(data.data.date_from);
                $('#date-to-hidden').val(data.data.date_to);
                break;
            case "2":
                $('[name="type"]').val('2').trigger("change");
                dt = data.data.date_from;
                if (dt.substr(11, 2) == "08") {
                    $('[name="half_from"]').val(dt.substr(0, 10));
                    $('[name="half"]').val('1').trigger("change");
                } else {
                    $('[name="half_from"]').val(dt.substr(0, 10));
                    $('[name="half"]').val('2').trigger("change");
                }
                break;
            case "3":
                $('[name="type"]').val('3').trigger("change");
                dt = data.data.date_from;
                $('[name="whole_date"]').val(dt.substr(0, 10));
                break;
            default:
                $('[name="type"]').val('4').trigger("change");
                $('[name="date_from"]').val(data.data.date_from);
                $('[name="date_to"]').val(data.data.date_to);

                $('#date-from-hidden').val(data.data.date_from);
                $('#date-to-hidden').val(data.data.date_to);
                break;

        }

        if (parseInt(data.data.type) === 1) {
            data.data.date_to = moment(data.data.date_to).format("HH:mm");
        }

        vmTab1.vm_tab1 = Object.assign({}, data.data);
        setTimeout(function () {
            var newOption = new Option(tempName, data.data.employee, true, true);
            $('#select2_employee').append(newOption).trigger('change');
            /*** onBlurReason(); ***/
        }, 400);

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

            $("#date-from-hidden").val(moment(e.date).format("YYYY/MM/DD HH:mm"));
            var toDate = $("#date-to-hidden").val();
            var fromDate = toDate == '' ? new Date(e.date) : new Date(e.date + ' ' + toDate);
            var val = moment(fromDate).format("YYYY/MM/DD HH:mm");
            $("#date-to-hidden").val(val);
        });

        $('#under_to').timepicker({
            minuteStep: 1,
            showMeridian: false,
            use24hours: false,
            defaultTime: null,
        }).on('change', function (e) {
            var fromDate = $("#date-from-hidden").val();
            fromDate = new Date(fromDate);
        
            fromDate = moment(fromDate).format("YYYY/MM/DD");
        
            var toDate = fromDate == 'Invalid date' ? e.target.value : new Date(fromDate + " " + e.target.value);
            var val = fromDate == 'Invalid date' ? toDate : moment(toDate).format("YYYY/MM/DD HH:mm");
            $("#date-to-hidden").val(val);
        });

        $('#half_from').datepicker({
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
            todayBtn: true,
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
            todayBtn: true,
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

            $("#date-from-hidden").val(moment(e.date).format("YYYY-MM-DD HH:mm"));
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

            $("#date-to-hidden").val(moment(e.date).format("YYYY-MM-DD HH:mm"));
        });

        $("#phone_on_leave").inputmask({
            mask: "(0\\9) 9999-99999",
            // removeMaskOnSubmit: true,
            alias: 'phonenumber'
        });
    },
    error: function (jqXHR, textStatus, errorThrown) {
        alert("Error get data from ajax");
    }
});


var vmTab1 = new Vue({
    el: "#form_loa",
    data: { vm_tab1: {} },
    methods: {
        updateFields: function (field, value) {
            var _this = this;
            value = value.trim();
            _this.$set(_this.vm_tab1, [field], value);

            /*** var tempRow = { [field]: value }; ***/
            /*** var tempElement = $(_this.$el).find("[name=" + field + "]");
            var tempId = tempElement.attr("id");
            setTimeout(function () {
                document.getElementById(tempId).focus();
                document.getElementById(tempId).value = '';
                document.getElementById(tempId).value = value;
                $(tempElement).validate();
            }, 100); ***/
        },
        getCurrentValue: function (e) {
            var _this = this;
            /*** $(e.target).on('keyup', function (e) {
                var tempName = $(e.target).attr("name");
                var tempValue = $(e.target).val();
                clearTimeout(typingTimer0);
                typingTimer0 = setTimeout(function () {
                    _this.updateFields(tempName, tempValue);
                }, doneTypingInterval0);
            }).on('keydown', function () {
                clearTimeout(typingTimer0);
            }); ***/
            $(e.target).on('blur', function (e) {
                var _this = $(e.target);
                var currentValue = e.target.value;
                currentValue = currentValue.trim();
                _this.val(currentValue);
            });
        }, isNumber: function (evt) {
            const char = String.fromCharCode(evt.which);
            if(!(/[0-9+]/.test(char)))
                evt.preventDefault();
        }
    },
    mounted: function () {
        var tempSelect2Employee = $("#select2_employee").select2({
            placeholder: 'SELECT AN OPTION',
            width: '100%',
            minimumInputLength: 3,
            ajax: {
                url: baseUrl("eforms/Loa/get_employee_collection"),
                dataType: 'json',
                delay: 250,
                global: false,
                processResults: function (data) {
                    return data;
                }
            }
        });

        tempSelect2Employee.on("change", function (e) {
            $(e.target).validate();
        });

        /*setTimeout(function () {
              var vmData = this.vmTab1.vm_tab1;
              var newOption = new Option(name, vmData.employee, true, true);
              $('#select2_employee').append(newOption).trigger('change');
          }, 400);*/
    }
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

        $("#under_from input").val('');
        $("#under_to").val('');
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

    if (type == 2 || type == 3) {
        $("#date-from-hidden").val('');
    	$("#date-to-hidden").val('');
    }
}

function view_back() {
    window.location.replace(baseUrl("eforms/loa/view_loa?id=") + param_id);
}

$.validate({
    form: '#form_loa',
    lang: 'en',
    onSuccess: function (form) {
        var currentForm = form[0];
        var formData = $(currentForm).serialize();


        //     if (parseInt($("#reason").val().length) < 30) {
        //         alert_function("Must have minimum of 30 characters.");
        //     }
        //     else if (parseInt($("#reason").val().length) > 255) {
        //         alert_function("Must have maximum of 255 characters.");
        //     }
        //     else {
        //         $.ajax({
        //         url: baseUrl("eforms/loa/check_reason"),
        //         type: "POST",
        //         dataType: "JSON",
        //         global: false,
        //         data: {reason:$(this).val(), csrf_token: _csrf_hash},
        //         success: function (data) {
        //             if (data.reps!=="error") {
        //                 alert_function("Invalid Reason!");
        //             }
        //             else {
        //                 $.ajax({
        //                     url: baseUrl("eforms/loa/update_loa/") + param_id,
        //                     type: "POST",
        //                     dataType: "JSON",
        //                     data: formData,
        //                     success: function (data) {
        //                         if (data.status) {
        //                             toastr.success("LOA successfully updated!");
        //                             window.location.replace(baseUrl("eforms/loa/view_loa?id=") + param_id);
        //                         } else {
        //                             toastr.danger("Error processing request!");
        //                         }
        //                     }
        //                 });
        //             }
        //         }
        //       })
        // }

        var phone = $("#phone_on_leave").val();
        var rawPhone = phone.replace(/\D/g, "");

        var type = $('#type').val();
		var hasTo = false;
		var message = "";

        if (type == 1 || type == 4) {
			var date_from = $("#date-from-hidden").val();
			var date_to = $("#date-to-hidden").val();

			date_from = new Date(date_from);
			date_to = new Date(date_to);

			date_from = moment(date_from);
			date_to = moment(date_to);

			var duration = moment.duration(date_to.diff(date_from));
			var minutes = duration.asMinutes();

			if (minutes < -1){ 
				hasTo = true;
				message = 'Invalid Date! `TO DATE` cannot be the less than the `FROM DATE`.';
			} else if (minutes == 0) {
				hasTo = true;
				message = 'Invalid Date! `TO DATE` cannot be the same as `FROM DATE`.';
			} else if (minutes < 30) {
				hasTo = true;
				message = "LOA Must have a minimum of 30 minutes.";
			} else {
				hasTo = false;
			}
		}

        if (!hasTo) {
            if (rawPhone.length < 11){
                toastr.error("Invalid phone number. Number must be 11 digits. (09———)", "Error!", 5000);
            } else {
                if (parseInt($("#reason").val().length) < 30) {
                    alert_function("Must have minimum of 30 characters.");
                }else{
                    $.ajax({
                        url: baseUrl("eforms/loa/check_reason"),
                        type: "POST",
                        dataType: "JSON",
                        global: false,
                        data: {reason:$("#reason").val(), csrf_token: _csrf_hash},
                        success: function (data) {
                            
                            if (data.reps!=="error") {
                                alert_function("Invalid Reason!");
                            } else {
        
                                $.ajax({
                                    url: baseUrl("eforms/loa/update_loa/") + param_id,
                                    type: "POST",
                                    dataType: "JSON",
                                    data: formData,
                                    success: function (data) {
                                        if (data.status) {
                                            toastr.success("LOA successfully updated!");
                                            window.location.replace(baseUrl("eforms/loa/view_loa?id=") + param_id);
                                        } else {
                                            toastr.error("Failed to update LOA. `FROM DATE` must be less than `TO DATE`.", "Error!", 5000);
                                            $("#btnSaveLoa").attr('disabled', false);
                                        }
                                    }
                                });
                            }
                        }
                    })
                }
            }
        } else {
            toastr.error(message, "Error!", 5000);
        }
        /*** $('#reason_v').empty();
        if ($('[name="reason"]').val().length < 30 && $('[name="reason"]').val().length > 255) {
            if ($('[name="reason"]').val().length < 30) {
                $('#reason_v').append('<p><font color="#FF0000">Must have minimum of 30 characters.</font></p>');
            }
            if ($('[name="reason"]').val().length > 255) {
                $('#reason_v').append('<p><font color="#FF0000">Must have maximum of 255 characters.</font></p>');
            }
        } else {
            
        } ***/
        return false;
    }
});

$(document).ready(function () {
    document.getElementById('under_date').style.display = 'none';
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

//select2 trigg
$("#nature").select2({
    width: '100%',
});

$("#type").select2({
    width: '100%',
});

var onBlurReason = function () {
    $("#reason").on("blur", function (e) {
        var _this = $(e.target);
        var currentValue = e.target.value;
        currentValue = currentValue.trim();
        _this.val(currentValue);
    });
}
function alert_function(msg){
        setTimeout(function(){
            $('#reason_v').html('<span class="help-block form-error" ><font color="#FF0000">'+msg+'</font></span>');
            $('#reason').attr("style","border-color: rgb(185, 74, 72)");    
        },100);
    }