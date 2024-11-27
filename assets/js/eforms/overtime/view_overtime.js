var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

param_id = getUrlParameter('id');

$(".btnPending").hide();
$(".btnApproved").hide();
$(".btnDisapproved").hide();

$.ajax({
    url: baseUrl("eforms/overtime/get_overtime_request_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        vmTab1.vm_tab1 = Object.assign({}, data);

        (data.updated_by != "N/A") ? $("#updated_at").text(" ON " + moment(data.updated_at).format('LLL')) : "";
        (data.requested_remarks == "") ? $("#requested_remarks").hide() : $("#requested_remarks").show();
        (data.cancelled_remarks == "") ? $("#cancelled_remarks").hide() : $("#cancelled_remarks").show();
        (data.status != "Approved") ? $("#approved_by").hide() : $("#approved_by").show();
        (data.status != "Disapproved") ? $("#disapproved_by").hide() : $("#disapproved_by").show();
        (data.status != "Cancelled") ? $("#cancelled_by").hide() : $("#cancelled_by").show();
        (data.actual_time_start == "0000-00-00 00:00:00") ? $("#actual_time").hide() : $("#actual_time").show();

        switch (data.status) {
            case "Pending":
                $("#status_state").addClass("alert alert-warning text-white");
                $(".btnPending").show();
                break;
            case "Approved":
                $("#status_state").addClass("alert alert-success");
                $(".btnApproved").show();
                break;
            case "Disapproved":
                $("#status_state").addClass("alert alert-danger");
                $(".btnDisapproved").show();
                break;
            default:
                $("#status_state").addClass("alert alert-metal text-white");
                break;
        }
    }
});

var vmTab1 = new Vue({
    el: "#form_overtime",
    data: { vm_tab1: {} },
});

function edit() {
    location.href = 'edit_overtime?id=' + param_id;
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
    form: '#approve_form',
    lang: 'en',
    validateHiddenInputs: true,
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/approve_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#approve_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#approve_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_approval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/undo_approve_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_approval_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#undo_approval_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#disapprove_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/disapprove_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#disapprove_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#disapprove_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_disapproval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/undo_disapprove_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_disapproval_form").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#undo_disapproval_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#cancel_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/overtime/cancel_overtime/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#cancel_form").find("input, textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.state) {
                    $('#cancel_modal').modal('hide');
                    toastr.success(data.message, "Updated successfully!", 5000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

function prints() {
    setTimeout(function(){
        window.open(baseUrl("eforms/overtime/print_overtime/") + param_id);
    }, 1500);
    
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

                    /*** uploadVM.left_pane = Object.assign({}, { display_avatar: avatarImage });
                    vmTab1.vm_tab1 = Object.assign({}, { pic_filename: tempImage }); ***/

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

jQuery(document).ready(function () {
    approveModalFileUpload();
    getCurrentUploadFiles();
});

function SortByDate(a, b){
    return new Date(b.created_date) - new Date(a.created_date);
}