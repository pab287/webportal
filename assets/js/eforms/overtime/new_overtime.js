$("#employee").select2({
    placeholder: 'Select',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/overtime/get_employee"),
      dataType: "json",
      delay: 250,
      global: false,
      processResults: function (data) {
        return data;
      }
    }
});

$("#requested_by").select2({
    placeholder: 'Select',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
      url: baseUrl("eforms/overtime/get_employee"),
      dataType: "json",
      delay: 250,
      global: false,
      processResults: function (data) {
        return data;
      }
    }
});

$("#employee").on("select2:select", function() {
    $.ajax({
        type: "GET",
        data: { data: $("#employee option:selected").attr("value") },
        url: baseUrl("eforms/overtime/get_employee_detail"),
        dataType: "json",
        success: function(data) {
            if (typeof data.details !== "undefined") {
                $("#details").val(data.details);
                $("#company").val(data.company);
                $("#department").val(data.department);
                $("#position").val(data.position);
            }
        }
    });
});

$("#date_time").daterangepicker({
    timePicker: true,
    minDate: moment().subtract(2, 'years'),
    startDate: moment().startOf('hour'),
    endDate: moment().startOf('hour').add(32, 'hour'),
    locale: {
      format: 'M/DD hh:mm A'
    }
});

$('#date_time').on('apply.daterangepicker', function (ev, picker) {
    $("#date_from").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
    $("#date_to").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
    $("#date").val(picker.startDate.format('MM/DD/YYYY hh:mm a') + ' - ' + picker.endDate.format('MM/DD/YYYY hh:mm a'));
});

function save(){
  $.validate({
    form : '#frm_new',
    lang: 'en',
    onSuccess : function(form) {
            var disabled = $('#frm_new').find('textarea:disabled').removeAttr('disabled');
            $.ajax({
                url: baseUrl("eforms/overtime/save_overtime"),
                type: "POST",
                dataType: "json",
                data: $("#frm_new").find("input,select,textarea").serialize(),
                beforeSend: function(){
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function(data){
                    if(data.state){
                        window.location.href = baseUrl("eforms/overtime/masterfile", toastr.success(data.message, "Successfully saved!", 5000));
                        disabled.attr('disabled','disabled');
                    }else{
                        toastr.error(data.message, "Error!", 5000);
                        disabled.attr('disabled','disabled');
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        return false;
        },
    });
}

$.formUtils.addValidator({
    name: 'checkbox_group_min1',
    validatorFunction: function (value, $el, config, language, $form) {
        console.log(value);
        return parseInt(value) > 0;
    },
    errorMessage: 'Select at least 1 image option!',
    errorMessageKey: 'checkboxMinimumOne'
});

$.validate({
    form : '#frm_new',
    lang: 'en',
    validateHiddenInputs: true,
    onSuccess : function(form) {
        var disabled = $('#frm_new').find('textarea:disabled').removeAttr('disabled');
        $.ajax({
            url: baseUrl("eforms/overtime/save_overtime"),
            type: "POST",
            dataType: "json",
            data: $("#frm_new").find("input,select,textarea").serialize(),
            beforeSend: function(){
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(data){
                if(data.state){
                    window.location.href = baseUrl("eforms/overtime/masterfile", toastr.success(data.message, "Successfully saved!", 5000));
                    disabled.attr('disabled','disabled');
                }else{
                    toastr.error(data.message, "Error!", 5000);
                    disabled.attr('disabled','disabled');
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
    return false;
    },
});

jQuery(document).ready(function(){
    Init();
    approveModalFileUpload();
    getCurrentUploadFiles();
});

var CRLF   = 10;
var BULLET = String.fromCharCode(45);

function Init() {
    if (purpose.addEventListener) purpose.addEventListener("input", OnInput, false);
}

function OnInput(event) {
    char = event.target.value.substr(-1).charCodeAt(0);
    nowLen = purpose.value.length;
    if (nowLen > prevLen.value) {
        if (char == CRLF) purpose.value = purpose.value + BULLET + " ";
        if (nowLen == 1) purpose.value = BULLET + " " + purpose.value;
    }
    prevLen.value = nowLen;
}

var approveModalFileUpload = function () {
    var url = baseUrl("eforms/overtime/temp_upload_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var avatarImage = result.added_image;
                    var tempImage = result.temp_image;

                    toastr.success(result.toastr_msg, "Upload File", 5000);
                } else {
                    toastr.error(result.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_approve").show();
                var progress = parseInt((data.loaded / data.total) * 100, 10);
                var progressTotal = 0;

                var steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress_approve .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress_approve").hide();
                        getCurrentUploadFiles();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};

var getCurrentUploadFiles = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/get_current_uploaded_file"),
        dataType: "json",
        success: function (json) {
            if (json.response) {

                $("#temp_fileupload").attr('data-validation', '');
                
                if(json.count > 12){
                    $("#uploaded-attach").prop('data-max-height', '300');
                    $("#uploaded-attach").css('height', '300px').css('max-height', '300px');
                }
                
                var tempRows = Object.assign({}, json.rows.sort(SortByDate));
                var tempCount = json.count; 

                vmTempImages.rows = tempRows;
                vmTempImages.count = tempCount;
            }
        }
    });
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true
    });
}

var vmTempImages = new Vue({
    el: "#tempModalApproveImages",
    data: { count: 0, rows: {} },
    methods: {
        renderImageLabel: function (index) {
            var tempIndex = parseInt(index) + 1;
            return "Image " + tempIndex;
        },
        getCheckedCount: function () {
            var currentElement = this.$el;
            var checked = $(currentElement).find(".temp-attachment_image:checked");
            var checkedCounter = $(currentElement).find("#checked_count");
            checkedCounter.val(checked.length).validate();
        }
    }
});

function SortByDate(a, b){
    return new Date(b.created_date) - new Date(a.created_date);
}