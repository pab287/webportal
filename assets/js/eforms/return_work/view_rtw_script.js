var returnContent = $("#return_to_work-content");
var formDisapprove = $("#disapprove_form");
var formCancel = $("#cancel_form");
var formUndoApproval = $("#undo_approval_form");
var formUndoDisapproval = $("#undo_disapproval_form");

var tempId = _tempContentData.id;

var vmRTWPrint = new Vue({
    el: "#printableRTW",
    data: { vmData: {} },
});

if (typeof returnContent !== "undefined") {
    var vmReturnContent = new Vue({
        el: "#return_to_work-content",
        data: { row: {} },
        methods: {
            getClassStatus: function (id) {
                var tempState = "alert-warning";
                switch (id) {
                    case "1": tempState = "alert-success"; break;
                    case "2": tempState = "alert-danger"; break;
                    case "3": tempState = "alert-metal"; break;
                    default: tempState = "alert-warning"; break;
                }
                return tempState;
            }, setTempDataBy: function (id, by, date) {
                var tempDesc = "---";
                if (id !== "0" && by) {
                    var tempDate = (date) ? date : "---";
                    tempDesc = "<strong>" + by + "</strong> on <strong>" + tempDate + "</strong>";
                }

                return tempDesc;
            }, editRtwRequest: function (id) {
                window.location.replace(siteUrl("eforms/return_to_work/edit_return_to_work/" + id));
            }
        }
    });

    $.ajax({
        url: siteUrl("eforms/return_to_work/get_rtw_data/" + tempId),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmReturnContent.row = Object.assign({}, json.row);
                vmRTWPrint.vmData = Object.assign({}, json.row);
            }
        }
    });

    var redirectMasterfile = function () {
        window.location.replace(siteUrl("eforms/return_to_work/masterfile"));
    }
}

if (typeof formUndoApproval !== "undefined") {
    formUndoApproval.on("submit", function (e) {
        e.preventDefault();
        var vmRow = vmReturnContent.row;
        if (typeof vmRow.allow_undo_cancel !== "undefined" && vmRow.allow_undo_cancel == true) {
            var tempUrl = e.target.action;
            tempUrl += "/" + _tempContentData.id;
            var formType = e.target.method;

            $.ajax({
                url: tempUrl,
                type: formType,
                dataType: "json",
                data: $(e.target).serialize(),
                beforeSend: function () {
                    $(e.target).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.success("Undo Approval Return to Work", "Success", { timeOut: 5000 });
                        $("#undo_approval_modal").modal("hide");
                        setTimeout(function () { window.location.reload(); }, 1000);
                    } else {
                        toastr.error("Undo Approval Return to Work", "Failed", { timeOut: 5000 });
                    }
                    $(e.target).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        }
    });
}

if (typeof formUndoDisapproval !== "undefined") {
    formUndoDisapproval.on("submit", function (e) {
        e.preventDefault();
        var vmRow = vmReturnContent.row;
        if (typeof vmRow.allow_undo_cancel !== "undefined" && vmRow.allow_undo_cancel == true) {
            var tempUrl = e.target.action;
            tempUrl += "/" + _tempContentData.id;
            var formType = e.target.method;

            $.ajax({
                url: tempUrl,
                type: formType,
                dataType: "json",
                data: $(e.target).serialize(),
                beforeSend: function () {
                    $(e.target).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.success("Undo Disapproval Return to work", "Success", { timeOut: 5000 });
                        $("#undo_disapproval_modal").modal("hide");
                        setTimeout(function () { window.location.reload(); }, 1000);
                    } else {
                        toastr.error("Undo Disapproval Return to work", "Failed", { timeOut: 5000 });
                    }
                    $(e.target).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        }
    });
}

if (typeof formDisapprove !== "undefined") {
    formDisapprove.on("submit", function (e) {
        e.preventDefault();
        var vmRow = vmReturnContent.row;
        if (typeof vmRow.allow_approval !== "undefined" && vmRow.allow_approval == true) {
            var tempUrl = e.target.action;
            tempUrl += "/" + _tempContentData.id;
            var formType = e.target.method;

            $.ajax({
                url: tempUrl,
                type: formType,
                dataType: "json",
                data: $(e.target).serialize(),
                beforeSend: function () {
                    $(e.target).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.success("Disapproved Return to Work", "Success", { timeOut: 5000 });
                        $("#disapprove_modal").modal("hide");
                        setTimeout(function () { window.location.reload(); }, 1000);
                    } else {
                        toastr.error("Disapproved Return to Work", "Failed", { timeOut: 5000 });
                    }
                    $(e.target).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        }
    });
}

if (typeof formCancel !== "undefined") {
    formCancel.on("submit", function (e) {
        e.preventDefault();
        var vmRow = vmReturnContent.row;
        if ((typeof vmRow.allow_undo_cancel !== "undefined" && vmRow.allow_undo_cancel == true)
            || (typeof vmRow.allow_approval !== "undefined" && vmRow.allow_approval == true)) {
            var tempUrl = e.target.action;
            tempUrl += "/" + _tempContentData.id;
            var formType = e.target.method;

            $.ajax({
                url: tempUrl,
                type: formType,
                dataType: "json",
                data: $(e.target).serialize(),
                beforeSend: function () {
                    $(e.target).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.success("Cancelled Return to Work", "Success", { timeOut: 5000 });
                        $("#cancel_modal").modal("hide");
                        setTimeout(function () { window.location.reload(); }, 1000);
                    } else {
                        toastr.error("Cancelled Return to Work", "Failed", { timeOut: 5000 });
                    }
                    $(e.target).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
        }
    });
}

var approveModalFileUpload = function () {
    var url = baseUrl("eforms/return_to_work/temp_upload_file");
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
            }, progressall: function (e, data) {
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
        url: baseUrl("eforms/return_to_work/get_current_uploaded_file"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var tempRows = Object.assign({}, json.rows);
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
        }
    }
});


$.validate({
    form: '#approve_form',
    lang: 'en',
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formType = currentForm.method;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: formUrl + "/" + tempId,
            type: formType,
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    $('#approve_modal').modal('hide');
                    toastr.success(json.toastr_msg, "Updated successfully!", { timeOut: 5000 });
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(json.toastr_msg, "Error!", { timeOut: 5000 });
                }
                $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

jQuery(document).ready(function () {
    approveModalFileUpload();
    getCurrentUploadFiles();
});

function printArea() {

//     const prtHtml = document.getElementById('printableRTW').innerHTML;

// // Get all stylesheets HTML
// let stylesHtml = baseUrl("assets/demo/demo3/base/style.bundle.css");
// // for (const node of [...document.querySelectorAll('link[rel="stylesheet"], style')]) {
// //   stylesHtml += node.outerHTML;
// // }

// // Open the print window
// const WinPrint = window.open();

// WinPrint.document.write(`<!DOCTYPE html>
// <html>
//   <head>
//     ${stylesHtml}
//   </head>
//   <body>
//     ${prtHtml}
//   </body>
// </html>`);

// WinPrint.document.close();
// WinPrint.focus();
// WinPrint.print();
// WinPrint.close();
    win = window.open();
    var divToPrint = document.getElementById("printableRTW");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}