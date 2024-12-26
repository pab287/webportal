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

var search_val = "";
// var tblBody = $("#tblbody")Table({
//     // dom: '<"toolbar">frtlip',
//     serverSide: true,
//     processing: true,
//     destroy: true,
//     bPaginate: false,
//     // bInfo: true,
//     //order: [ 1, "desc" ],
//     aaSorting: [],
//     ajax: {
//         url: baseUrl("eforms/accountability/content_body_detail/") + param_id,
//         type: "post",
//         dataType: "json",
//         data: function (d) {
//             d.csrf_token = _csrf_hash,
//                 d.search['value'] = search_val
//         }
//     },
//     footerCallback: function () {
//         $("#tblbody tfoot tr").text("");
//         var api = this.api();
//         $("#tblbody tfoot tr").append("<td></td><td></td><td class='text-right'><b>Total: </b></td><td class='text-right'>PHP " + (api.column(3, { page: 'current' })().sum()).toLocaleString("PHP", { minimumFractionDigits: 2 }) + "</td>");
//     },
//     searching: false,
//     columns: [
//         { data: "asset_code", width: "15%" },
//         { data: "description", width: "40%", render: function (data, type, row, meta) { return descriptionDetail(row.description, row.brand, row.modelno, row.serialno, row.plateno, row.engineno, row.chasisno, row.type, row.desc); } },
//         { data: "remarks" },
//         { data: "amount", width: "10%", className: "text-right" }
//     ], columnDefs: [{
//         defaultContent: "",
//         orderable: false
//     }],
// });

// document.getElementById('tblbody').createTFoot().insertRow(0);

function descriptionDetail($desc, $brand, $model, $serial, $plateno, $engineno, $chasisno, $type, $desc_det) {
    if ($brand == '' || $brand == null) { $brand = 'N/A'; }
    if ($model == '' || $model == null) { $model = 'N/A'; }
    if ($serial == '' || $serial == null) { $serial = 'N/A'; }
    if ($plateno == '' || $plateno == null) { $plateno = 'N/A'; }
    if ($engineno == '' || $engineno == null) { $engineno = 'N/A'; }
    if ($chasisno == '' || $chasisno == null) { $chasisno = 'N/A'; }
    if ($type == 'Asset') {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Brand: ' + $brand + '<br>Model: ' + $model + '<br>Serial: ' + $serial;
    } else {
        return '<b>' + $desc + '</b><br>Description: ' + $desc_det + '<br>Plate no.: ' + $plateno + '<br>Engine no.: ' + $engineno + '<br>Chasis no.: ' + $chasisno;
    }
}

var vmResendEmail = new Vue({
    el: "#modal-resend_action",
    data: { row: {} },
    methods: {
        resendEmail: function () {
            var _this = this;
            var currentRow = _this.row;
            if (typeof currentRow.id !== "undefined" && currentRow.id) {
                $.ajax({
                    url: siteUrl("eforms/accountability/resend_email/" + currentRow.id),
                    dataType: "json",
                    success: function (json) {
                        if (json.response) {
                            $("#resend_email_modal").modal("hide");
                            toastr.success(json.toastr_msg, "Re-send Accountability Email", { timeOut: 5000 });
                        } else {
                            toastr.success(json.toastr_msg, "Re-send Accountability Email", { timeOut: 5000 });
                        }
                    }
                });
            }
        }
    }
});

$.ajax({
    url: baseUrl('eforms/accountability/content_detail/') + param_id,
    type: "GET",
    success: function (data) {
        vmResendEmail.row = Object.assign({}, data[0]);
        vmTab1.vm_tab1 = Object.assign({}, data[0]);
        vmTab1.vm_tab_body = Object.assign({}, data.data_body);
        vmTab1.vm_tab_total_amount = Object.assign({}, data.data_body_total_amount);
        vmFormTab.vm_main = Object.assign({}, data[0]);
        vmFormTab.vmFormData_body = Object.assign({}, data.data_body);
        vmFormTab.isLoading = false;
        if (data[0].is_contract == 0) {
            $("#issued_to").append("<b>" + data[0].display_name + "</b>");
        } else {
            $("#issued_to").append("<b>" + data[0].contractor + "</b>");
        }
        $("#company").text(data[0].company);
        $("#reference_no").text(data[0].reference_no);
        $("#department").text(data[0].department);
        $("#issued_dt").append("on <b>" + moment(data[0].date_issued).format("MMMM DD, YYYY") + "</b>");
        console.log(_currentActions);
        switch (data[0].status) {
            case "Pending Accounting Notes":
                $("#status").append('<div class="m-badge m-badge--wide alert alert-warning" role="alert"><strong>Pending Accounting Notes</strong></div>');
                if (_currentActions.includes('resend')) {
                    $("#buttons").append("<button type='button' class='btn btn-primary btnResend m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#resend_email_modal'><span>Re-send Email</span></button>");
                }
                if (_currentActions.includes('acct_note')) {
                    $("#buttons").append("<button type='button' class='btn btn-success btnAcct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#acctg_note_modal'><span>Note</span></button>");
                }
                if (_currentActions.includes('edit')) {
                    // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/edit_accountability?id=') + data[0].id + "'><button type='button' class='btn btn-warning btnEdit text-white m-btn m-btn--custom m-btn--air m-btn--box'><span>Edit</span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl('eforms/accountability/edit_accountability?id=') + data[0].id + "' class='btn btn-warning btnEdit text-white m-btn m-btn--custom m-btn--air m-btn--box'><span>EDIT</span></a>");
                }
                if (_currentActions.includes('cancel')) {
                    $("#buttons").append("<button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span>Cancel</span></button>");
                }
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                break;
            case "Pending Payroll Notes":
                $("#acctg_note").show();
                $("#status").append('<div class="m-badge m-badge--wide alert alert-primary" role="alert"><strong>Pending HR Notes</strong></div>');
                if (_currentActions.includes('resend') ) {
                    $("#buttons").append("<button type='button' class='btn btn-primary btnResend m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#resend_email_modal'><span>Re-send Email</span></button>");
                }
                if (_currentActions.includes('hr_note') ) {
                    $("#buttons").append("<button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#hr_note_modal'><span>Note</span></button>");
                }
                if (_currentActions.includes('undo_acct_note') ) {
                    $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_acct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_acctg_modal'><span>Undo Acctg Notes</span></button>");
                }
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                break;
            case "Pending HR Notes":
                    $("#acctg_note").show();
                    $("#status").append('<div class="m-badge m-badge--wide alert alert-primary" role="alert"><strong>Pending HR Notes</strong></div>');
                    if (_currentActions.includes('resend') ) {
                        $("#buttons").append("<button type='button' class='btn btn-primary btnResend m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#resend_email_modal'><span>Re-send Email</span></button>");
                    }
                    if (_currentActions.includes('hr_note') ) {
                        $("#buttons").append("<button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#hr_note_modal'><span>Note</span></button>");
                    }
                    if (_currentActions.includes('undo_acct_note') ) {
                        $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_acct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_acctg_modal'><span>Undo Acctg Notes</span></button>");
                    }
                    $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                    // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                    break;    
            case "For Releasing":
                $("#acctg_note").show();

                if (data[0].is_contract == 0) {
                    $("#hr_note").show();
                }

                $("#status").append('<div class="m-badge m-badge--wide alert alert-brand" role="alert"><strong>For Releasing</strong></div>');
                if (_currentActions.includes('acct_release') ) {
                    $("#buttons").append("<button type='button' class='btn btn-success btnAcct_release m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#release_note_modal'><span>Release</span></button>");
                }

                if (data[0].is_contract == 0) {
                    if (_currentActions.includes('undo_hr_note') ) {
                        $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_hr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_hr_modal'><span>Undo Payroll Notes</span></button>");
                    }
                } else {
                    if (_currentActions.includes('undo_acct_note') ) {
                        $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_acct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_acctg_modal'><span>Undo Acctg Notes</span></button>");
                    }
                }

                if (_currentActions.includes('cancel') ) {
                    $("#buttons").append("<button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span>Cancel</span></button>");
                }
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                break;
            case "Released":
                $("#acctg_note").show();

                if (data[0].is_contract == 0) {
                    $("#hr_note").show();
                }
                
                $("#release_note").show();
                $("#status").append('<div class="m-badge m-badge--wide alert alert-success" role="alert"><strong>Released</strong></div>');
                if (_currentActions.includes('undo_release') ) {
                    $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_release m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_release_modal'><span>Undo Release</span></button>");
                }
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                break;
            case "Cancelled":
                $("#status").append('<div class="m-badge m-badge--wide alert alert-metal text-white" role="alert"><strong>Cancelled</strong></div>');
                $("#cancelled").show();
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/archive') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                break;
            default:
                $("#status").append('<div class="m-badge m-badge--wide alert alert-metal text-white" role="alert"><strong>Unreturned</strong></div>');
                $("#cancelled").show();
                $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span>Print Report</span></button>");
                // $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "'><button type='button' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></button></a>");
                $("#buttons").append("<a href='" + baseUrl('eforms/accountability/masterfile') + "' class='btn btn-metal btnBack m-btn m-btn--air m-btn--custom m-btn--box text-white'><span>Back</span></a>");
                break;
        }

        var isUrgent = parseInt(data[0].is_urgent);
        if (isUrgent == 1) {
            $("#accountability_form").find("#status").append('<div class="m-badge m-badge--wide alert alert-danger m--font-bold m--margin-left-5" role="alert"><strong>URGENT</strong></div>');
        }

        if (data[0].created_by) {
            $("#created_by").append("<b>" + data[0].created_by + "</b>");
            $("#created_dt").append(' ON <b>' + moment(data[0].created_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");
        } else {
            $("#created_by").text('N/A');
        }

        if (data[0].last_edited_by) {
            $("#last_edited_by").text(data[0].last_edited_by);
            $("#last_edited_dt").text(' ON ' + moment(data[0].last_edited_dt).format("MMMM DD, YYYY hh:mm a"));
        } else {
            $("#last_edited_by").text('N/A');
        }

        $("#acctg_noted_remarks").append(data[0].acctg_noted_remarks);
        $("#acctg_noted_by").append("by <b>" + data[0].acctg_noted_by + "</b>");
        $("#acctg_noted_dt").append("on <b>" + moment(data[0].acctg_noted_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");

        $("#hr_noted_remarks").append(data[0].hr_noted_remarks);
        $("#hr_noted_by").append("by <b>" + data[0].hr_noted_by + "</b>");
        $("#hr_noted_dt").append("on <b>" + moment(data[0].hr_noted_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");

        $("#release_remarks").append(data[0].release_remarks);
        $("#released_by").append("by <b>" + data[0].released_by + "</b>");
        $("#released_dt").append("on <b>" + moment(data[0].released_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");

        $("#cancelled_reason").append(data[0].cancelled_remarks);
        $("#cancelled_by").append("<b>" + data[0].cancelled_by + "</b>");
        $("#cancelled_dt").append("on <b>" + moment(data[0].cancelled_dt).format("MMMM DD, YYYY hh:mm a") + "</b>");
    }
});

$("#cancel_modal").hide();

$("#acctg_note_modal").hide();
$("#undo_acctg_modal").hide();

$("#hr_note_modal").hide();
$("#undo_hr_modal").hide();

$("#release_note_modal").hide();
$("#undo_release_modal").hide();

$("#cancelled").hide();
$("#acctg_note").hide();
$("#hr_note").hide();
$("#release_note").hide();

$.validate({
    form: '#cancel_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/cancel_accountability/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#cancel_modal_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#cancel_modal').modal('hide');
                    $('#cancel_modal_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Form Cancelled!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#acctg_note_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/set_acct_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#acctg_note_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#acctg_note_modal').modal('hide');
                    $('#acctg_note_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_acctg_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/undo_acctg_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_acctg_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_acctg_modal').modal('hide');
                    $('#undo_acctg_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#hr_note_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/set_hr_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#hr_note_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#hr_note_modal').modal('hide');
                    $('#hr_note_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_hr_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/undo_hr_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_hr_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_hr_modal').modal('hide');
                    $('#undo_hr_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Undo changes successfully!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#release_note_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/set_release_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#release_note_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#release_note_modal').modal('hide');
                    $('#release_note_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

$.validate({
    form: '#undo_release_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/accountability/undo_release_note/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_release_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_release_modal').modal('hide');
                    $('#undo_release_form')[0].reset();
                    location.reload(toastr.success(data.toastr_msg, "Undo changes successfully!", 5000));
                } else {
                    toastr.error(data.toastr_msg, "Notification: Error", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});
var vmTab1 = new Vue({
    el: "#print_accountability",
    data: { vm_tab1: {}, vm_tab_body : {}, vm_tab_total_amount: {} },
});

var vmFormTab = new Vue({
    el: "#accountability_form",
    data: { vmFormData_body : {}, vm_main: {}, isLoading: true},
})

function printArea() {
    win = window.open();
    var divToPrint = document.getElementById("printableArea");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}

function model($brand, $model) {
    return $brand + " / " + $model;
}