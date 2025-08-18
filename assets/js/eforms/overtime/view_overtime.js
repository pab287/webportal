const getUrlParameter = function getUrlParameter(sParam) {
    const sPageURL = decodeURIComponent(window.location.search.substring(1));
    const sURLVariables = sPageURL.split('&');
    let sParameterName;
    let i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

const param_id = getUrlParameter('id');
const vmTab1 = new Vue({
    el: "#form_overtime",
    data: { vm_tab1: {}, loading_content: true },
    methods: {
        removeActionDuration(startDate) {
            if (startDate) {
                const currentDate = moment();
                const startTime = moment(startDate);
                const timeDifference = Math.abs(currentDate - startTime);
                const daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
                /*** hotfix for undo approvals ***/
                if(startTime >= currentDate){ return true; }
                /*** hotfix for undo approvals ***/
                return daysDifference <= 15;
            }
            return false;
        }
    }
});

$(".btnPending").hide();
$(".btnApproved").hide();
$(".btnDisapproved").hide();

$.ajax({
    url: baseUrl("eforms/overtime/get_overtime_request_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    global: false,
    success: function (data) {
        const { valid_ot_dates, status } = data;
        vmTab1.vm_tab1 = { ...data };
        vmTab1.loading_content = false;

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
        
        if (valid_ot_dates === false && status === "Pending") {
            setTimeout(() => {
                Swal.fire({
                    title: 'Invalid Overtime Request!',
                    text: 'The overtime request is invalid. Please check the dates and times.',
                    icon: 'warning',
                });
            }, 750);
        }

        /*** Swal.fire({
            title: 'Invalid Overtime Request?',
            html: "Testing",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, '+tempTitle+' it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: siteUrl("hris/masterfile/approval_updated_payroll_data/"+nType),
                    type: "post",
                    data: { csrf_token: _csrf_hash, id: id },
                    dataType: "json",
                    success: function(json){
                        if(json.response){ 
                            toastr.success(json.toastr_msg, "For Approval");
                            dtForApproval.clear().rows.add(json.data).draw();
                        }else{ toastr.error(json.toastr_msg, "For Approval"); }
                    }
                });
            }
        }); ***/
    }
});

function edit() {
    location.href = 'edit_overtime?id=' + param_id;
}

$.formUtils.addValidator({
    name: 'checkbox_group_min1',
    validatorFunction: function (value, $el, config, language, $form) {
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

        if (vmTempImages.count > 0) {
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
        } else {
            toastr.error('Please upload atleast 1 attachment.', 'Approve Overtime');
        }

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

const approveModalFileUpload = function () {
    const url = baseUrl("eforms/overtime/temp_upload_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: { csrf_token: _csrf_hash },
            done: function (e, data) {
                const result = data.result;
                if (result.response) {
                    toastr.success(result.toastr_msg, "Upload File", 5000);
                } else {
                    toastr.error(result.toastr_msg, "Upload File", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress_approve").show();
                let progress = parseInt((data.loaded / data.total) * 100, 10);
                let progressTotal = 0;

                const steps = setInterval(function () {
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

const getCurrentUploadFiles = function () {
    $.ajax({
        url: baseUrl("eforms/overtime/get_current_uploaded_file"),
        dataType: "json",
        global: false,
        success: function (json) {
            if (json.response) {
                const tempRows = json.rows.sort(SortByDate);
                vmTempImages.rows = { ...tempRows };
                vmTempImages.count = json.count;
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