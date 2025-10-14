toastr.options = { newestOnTop: true, positionClass: "toast-bottom-right" };

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
var notif = getUrlParameter('notif');
var ca_type = (getUrlParameter('type') == 'pyrll') ? "?type=pyrll" : (getUrlParameter('type') == 'acctg') ? "?type=acctg" : "?type=approval";
let globalLoadingContent = true;

var tblCashAdvance = $("#table-cash-advance-content").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    bPaginate: false,
    bInfo: false,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/cash_advance/get_cashadvance_content/") + param_id,
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash
        }
    },
    searching: false,
    columns: [
        { data: "reference_no", width: "15%"},
        { data: "purpose" },
        { data: "approved_dt", width: "25%", render: function (data) { return formatCalendarDate(data) } },
        { data: "activebal", width: "20%",
            render: function(data, type, row){
                if(row.reference != 'no reference no'){
                    return "₱ " + row.rembalance;
                }else{
                    return '<span class="m--font-danger">--</span>';
                }
            } 
        },
        {
            data: null, 
            render: function(data, type, row){
                let response;
                if(row.reference != 'no reference no'){
                    if(row.active == 1){
                        if(row.paid == 1 || row.is_paid || parseFloat(row.rembalance) <= 0){
                            response = '<a href="#"><span class="m-badge m-badge--wide m--font-bolder m-badge--success">Paid</span></a>';
                        }else{
                            response = '<a href="#"><span class="m-badge m-badge--wide m--font-bolder m-badge--info">Active</span></a>';
                        }
                    }else if(row.active == 0 && row.is_paid){
                        response = '<a href="#"><span class="m-badge m-badge--wide m--font-bolder m-badge--success">Paid</span></a>';
                    }else{
                        response = '<a href="#"><span class="m-badge m-badge--wide m--font-bolder m-badge--warning">Suspended</span></a>';
                    }
                }else{
                    response = '<span class="m--font-danger">--</span>';
                }
                return response;
            },
            orderable: false
        }
    ],
    columnDefs: [
        { targets: [2], width: "50%" },
    ]
});

$('#table-cash-advance-content tbody').on('click', 'tr', function () {
    var data = tblCashAdvance.row(this).data();
    if(data.reference != 'no reference no'){
        deductionStatus(data.reference_no, param_id);
    }
});

// function getCAStatus(data){
//     $.ajax({
//         url: baseUrl('eforms/cash_advance/get_ca_status/') + data,
//         type: "get",
//         dataType: "json",
//         success: function(response){
//             console.log( response);
//         }
//     });
// }

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "<span class='m--font-danger'>--</span>";
    }
    else {
        return moment(data).format("LL");
    }

}

$("#recommend_modal").hide();
$("#undo_recommend_modal").hide();

$("#approved_modal").hide();
$("#undo_approval_modal").hide();

$("#posting_modal").hide();
$("#undo_posting_modal").hide();
$("#posted_modal").hide();
$("#undo_posted_modal").hide();
$("#undo_awaiting_modal").hide();

$("#disapprove_modal").hide();
$("#undo_disapproval_modal").hide();

$("#cancel_modal").hide();

$("#set_hr_modal").hide();
$("#edit_set_hr_modal").hide();

$("#set_acctg_modal_details").hide();

$("#set_acctg_modal").hide();
$("#edit_set_acctg_modal").hide();

$("#undo_posting").hide();
$("#for_posting").hide();
$("#undo_posted").hide();
$("#posted").hide();

$("#final_approval_modal").hide();
$("#undo_for_final_modal").hide();

$("#released").hide();

$("#set_acctg_modal .test_charge").keypress( function() {
    console.log( "Handler for `keypress` called." );
  });

$("#deduction_status_modal").hide();
$.ajax({
    url: baseUrl("eforms/cash_advance/get_cash_advance_details/") + param_id,
    type: "GET",
    dataType: "JSON",
    success: function (data) {
        $('#approved_modal input[name=amt_approved').change(function () {
            var amt_approve = parseInt((($('#approved_modal input[name=amt_approved').val()).replace(',', "")).replace('₱ ', ""));
            var amt_applied = parseInt((data.amt_applied.replace(',', "")).replace('₱ ', ""));
            if (amt_approve > amt_applied) {
                $("#approve_message").text("");
                $("#approve_message").text("Amount approved must be less than or equal to amount applied");
                $(".btnSave").prop("disabled", true);             
            } else {
                $("#approve_message").text("");
                $(".btnSave").prop("disabled", false);
            }
        });

        $('#approved_modal input[name=amt_approved').inputmask({ alias: "pesos", removeMaskOnSubmit: true });

        $("#created_by").append("ON <b>" + moment(data.created_dt).format('LLL') + "</b>");
        if (data.last_edited_by && data.last_edited_dt && data.last_edited_by != "N/A") {
            $("#last_edited_by").append("ON <b>" + moment(data.last_edited_dt).format('LLL') + "</b>");
        }
        if (data.deduct_type == "percentage") {
            $("#percent_sign").append("%");
        } else {
            $("#percent_sign").append("");
        }

        switch (data.status) {
            case "Sup Recommendation":
                $("#hr_bal").append("<b>N/A</b>");
                $("#status_detail").addClass("alert alert-warning");
                if (jQuery.inArray("recommend", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' onclick='recommend_details(" + param_id + ")' class='btn btn-success m-btn m-btn--custom m-btn--air m-btn--box btnRecommend m-1' data-toggle='modal' data-target='#recommend_modal'>Recommend</button>");
                }
                if (jQuery.inArray("disapprove_action", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box m-1' data-toggle='modal' data-target='#disapprove_modal'><span><span>Disapprove</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box m-1 text-white' data-toggle='modal' data-target='#disapprove_modal'><span><span>DISAPPROVE</span></span></a>");
                }
                if (jQuery.inArray("edit", _currentActions) !== -1) {
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/edit_cash_advance?id=') + param_id + "' class='text-white btn btn-warning btnEdit m-btn m-btn--custom m-btn--air m-btn--box'><span><span>EDIT</span></span></a>");
                }
                if (jQuery.inArray("cancel", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span><span>Cancel</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#cancel_modal'><span><span>CANCEL</span></span></a>");
                }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#reason").hide();
                $("#recommend_by").hide();
                $("#amount_approved").hide();
                $("#allowable").hide();
                $("#approved_remarks").hide();
                $("#remarks").hide();
                $("#cancelled_by").hide();
                break;
            case "HR Balance Pending":
                if(data.hr_bal_by !== ""){
                    $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                    $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                    $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                    $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b>from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                    $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                    $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                }else{
                    $("#hr_bal").append("<b>N/A</b>");
                }
                $("#status_detail").addClass("alert alert-info");
                if (jQuery.inArray("hr_note", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_hr_modal'><span><span>Set Payroll Balance</span></span></button>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_hr_modal'><span><span>Set Payroll Balance</span></span></button></a>");
                }
                if (jQuery.inArray("cancel", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span><span>Cancel</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#cancel_modal'><span><span>CANCE;</span></span></a>");
                }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    // $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#reason").hide();
                $("#amount_approved").hide();
                $("#allowable").hide();
                $("#approved_remarks").hide();
                $("#cancelled_by").hide();
                break;
            case "Payroll Balance Pending":
                    if(data.hr_bal_by !== ""){
                        $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                        $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                        $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                        $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b>from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                        $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                        $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                    }else{
                        $("#hr_bal").append("<b>N/A</b>");
                    }
                    $("#status_detail").addClass("alert alert-info");
                    if (jQuery.inArray("hr_note", _currentActions) !== -1) {
                        $("#buttons").append("<button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_hr_modal'><span><span>Set Payroll Balance</span></span></button>");
                        // $("#buttons").append("<a><button type='button' class='btn btn-success btnHr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_hr_modal'><span><span>Set Payroll Balance</span></span></button></a>");
                    }
                    if (jQuery.inArray("undo_recommend", _currentActions) !== -1) {
                        $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_recommend_modal' class='btn btn-danger btnUndo_recommend m-btn m-btn--custom m-btn--air m-btn--box'>Undo Recommend</button>");
                    }
                    if (jQuery.inArray("cancel", _currentActions) !== -1) {
                        // $("#buttons").append("<a><button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span><span>Cancel</span></span></button></a>");
                        $("#buttons").append("<a class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#cancel_modal'><span><span>CANCEL</span></span></a>");
                    }
                    if (jQuery.inArray("back", _currentActions) !== -1) {

                        const temp_url = (notif == 'true') ? 'eforms/cash_advance/pending_balance' + ca_type :'eforms/cash_advance/masterfile';
                        // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                        $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                    }
                    $("#disapproved_by").hide();
                    $("#approved_by").hide();
                    $("#reason").hide();
                    $("#amount_approved").hide();
                    $("#allowable").hide();
                    $("#approved_remarks").hide();
                    $("#cancelled_by").hide();
                    break;    
            case "Accounting Balance Pending":
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");

                if(data.acctg_bal_by !== ""){
                // $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_by + "</b>" + " on " + "<b>" +  moment(data.acctg_bal_dt).format('LLL') +"</b>");
                // $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_dt").append("<b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                }
                $("#status_detail").addClass("alert alert-primary");

                if (jQuery.inArray("undo_recommend", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_recommend_modal' class='btn btn-danger btnUndo_recommend m-btn m-btn--custom m-btn--air m-btn--box'>Undo Recommend</button>");
                }
                if (jQuery.inArray("acct_note", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' class='btn btn-brand btnAcct_note m-btn m-btn--custom m-btn--air m-btn--box' onClick='setCaInterestPercentage()'><span><span>Set Interest Percentage</span></span></button>");
                    $("#buttons").append("<button type='button' class='btn btn-success btnAcct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_acctg_modal'><span><span>Set Accounting Balance</span></span></button>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-brand btnAcct_note m-btn m-btn--custom m-btn--air m-btn--box' onClick='setCaInterestPercentage()'><span><span>Set Interest Percentage</span></span></button></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnAcct_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_acctg_modal'><span><span>Set Accounting Balance</span></span></button></a>");
                }
                if (jQuery.inArray("edit_set_acc_bal", _currentActions) !== -1) {
                    $("#buttons").append("<a href='javascript:void(0)' onclick ='edit_set_acc_bal("+ param_id + ")' class='btn btn-success btnedit_set_acc_bal m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_acctg_modal'><span><span>Set Accounting Balance</span></span></a>");
                    // $("#buttons").append("<a><button type='button' onclick ='edit_set_acc_bal("+ param_id + ")' class='btn btn-success btnedit_set_acc_bal m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#set_acctg_modal'><span><span>Set Accounting Balance</span></span></button></a>");
                }
                if (jQuery.inArray("edit_hr_note", _currentActions) !== -1) {
                   // $("#buttons").append("<a><button type='button' onclick='edit_hr(" + param_id + ")' class='btn text-white btn-warning btnEdit_hr_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#edit_set_hr_modal'><span><span>Edit PAYROLL Balance</span></span></button></a>");
                }
                if (jQuery.inArray("cancel", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span><span>Cancel</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#cancel_modal'><span><span>CANCEL</span></span></a>");
                }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = (notif == 'true') ? 'eforms/cash_advance/pending_balance' +  ca_type :'eforms/cash_advance/masterfile';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#reason").hide();
                $("#approved_remarks").hide();
                $("#cancelled_by").hide();
                break;
            case "Awaiting Approval":
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                $("#status_detail").addClass("alert alert-info");


                // if (jQuery.inArray("edit_acctg_note", _currentActions) !== -1) {
                //     $("#buttons").append("<a><button type='button' onclick='edit_acctg(" + param_id + ")' class='btn btn-accent btnEdit_acctg_note m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#edit_set_acctg_modal'><span><span>Edit Accounting Balance</span></span></button></a>");
                // }

                if (jQuery.inArray("undo_awaiting_approval", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnUndo_awaiting_approval m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_awaiting_modal'><span><span>Undo Awaiting Approval</span></span></button></a>");
                    $("#buttons").append("<button type='button' class='btn btn-danger btnUndo_awaiting_approval m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_awaiting_modal'><span><span>Undo Awaiting Approval</span></span></a>");
                }

                if(jQuery.inArray("posting_action", _currentActions) !== -1){
                    // $("#buttons").append("<a><button type='button' onclick='for_posting()' class='btn btn-success btnPosting_action m-btn m-btn--custom m-btn--air m-btn--box'><span><span>For Posting</span></span></button></a>");
                    $("#buttons").append("<button type='button' onclick='for_posting()' class='btn btn-success btnPosting_action m-btn m-btn--custom m-btn--air m-btn--box'><span><span>For Posting</span></span></button>");
                }
                if (jQuery.inArray("final_approval_action", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnApprove_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#approved_modal'><span><span>Approve</span></span></button></a>"); -> original source code for approve of CA
                    $("#buttons").append("<button type='button' onclick='for_final_approval()' class='btn btn-success btnFinal_approval_action m-btn m-btn--custom m-btn--air m-btn--box'><span><span>For Final Approval</span></span></button>");
                }
                // if (jQuery.inArray("disapprove_action", _currentActions) !== -1) {
                //     $("#buttons").append("<a><button type='button' class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#disapprove_modal'><span><span>Disapprove</span></span></button></a>");
                // }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = (notif == 'true') ? 'eforms/cash_advance/pending_balance' +  ca_type :'eforms/cash_advance/masterfile';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#approved_remarks").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();

                if(data.for_posting_by && data.for_posting_by != '0000-00-00 00:00:00'){
                    $("#undo_posting").show();
                }
                break;
            case "Approved":
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                $("#status_detail").addClass("alert alert-success");

                if (jQuery.inArray("ca_released", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' data-toggle='modal' data-target='#released' class='btn btn-success btnUndo_approval m-btn m-btn--custom m-btn--air m-btn--box'>Released</buttons>");
                }

                if (jQuery.inArray("undo_approval", _currentActions) !== -1 && !data.has_payment) {
                    $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_approval_modal' class='btn btn-danger btnUndo_approval m-btn m-btn--custom m-btn--air m-btn--box'>Undo Approval</button>");
                }
                if (jQuery.inArray("cancel", _currentActions) !== -1 && !data.has_payment) {
                    // $("#buttons").append("<a><button type='button' id='cancel' class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'><span><span>Cancel</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnCancel m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#cancel_modal'><span><span>CANCEL</span></span></a>");
                }
                if (jQuery.inArray("print", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Print Report</span></span></button>");
                }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    // $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#disapproved_by").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();
                break;
            case "Disapproved":
                if (data.hr_bal_by) {
                    $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                    $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                    $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");

                    if (data.acctg_bal_by) {
                        $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                        $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                        $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                    }
                } else {
                    $("#hr_bal").append("<b>N/A</b>");
                }
                if (data.recommend_by) {
                    $("#recommend_by").show();
                } else {
                    $("#recommend_by").hide();
                }
                $("#status_detail").addClass("alert alert-danger");
                if (jQuery.inArray("undo_disapproval", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_disapproval_modal' class='btn btn-danger btnUndo_disapproval m-btn m-btn--custom m-btn--air m-btn--box'>Undo Disapproval</button>");
                }
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                $("#amount_approved").hide();
                $("#approved_remarks").hide();
                $("#remarks").hide();
                $("#cancelled_by").hide();
                $("#approved_by").hide();
                break;
            case "For Posting":
                $("#status_detail").addClass("alert alert-info");

                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#approved_remarks").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();

                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");

                if(jQuery.inArray("posted_action", _currentActions) !== -1){
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-success btnPosted_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#posted_modal'><span><span>POST</span></span></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnPosted_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#posted_modal'><span><span>Post</span></span></button></a>");
                }

                if(jQuery.inArray("undo_posting", _currentActions) !== -1){
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-danger btnUndo_posting m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_posting_modal'><span><span>UNDO POSTING</span></span></button></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnUndo_posting m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_posting_modal'><span><span>Undo Posting</span></span></button></a>");
                }

                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = 'eforms/cash_advance/masterfile';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }

                $("#for_posting").show();
                if(data.posted_by && data.posted_dt != '0000-00-00 00:00:00'){
                    $("#undo_posted").show();
                }

                break;
            case "Posted":
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                
                $("#status_detail").addClass("alert alert-info");

                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#approved_remarks").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();

                $("#posted").show();

                if (jQuery.inArray("final_approval_action", _currentActions) !== -1) {
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-success btnFinal_approval_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#final_approval_modal'><span><span>FOR FINAL APPROVAL</span></span></button></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnFinal_approval_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#final_approval_modal'><span><span>For Final Approval</span></span></button></a>");
                }

                if(jQuery.inArray("undo_posting", _currentActions) !== -1){
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-danger btnUndo_posting m-btn m-btn--custom m-btn--air m-btn--bo text-whitex' data-toggle='modal' data-target='#undo_posted_modal'><span><span>UNDO POSTED</span></span></button></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnUndo_posting m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_posted_modal'><span><span>Undo Posted</span></span></button></a>");
                }

                if (jQuery.inArray("disapprove_action", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#disapprove_modal'><span><span>Disapprove</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box m-1 text-white' data-toggle='modal' data-target='#disapprove_modal'><span><span>DISAPPROVE</span></span></a>");
                }

                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = 'eforms/cash_advance/masterfile';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                break;
            case 'For Final Approval':
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");

                $("#status_detail").addClass("alert alert-info");

                $("#disapproved_by").hide();
                $("#approved_by").hide();
                $("#approved_remarks").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();

                if (jQuery.inArray("undo_final", _currentActions) !== -1) {
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-warning text-white btnUndo_final m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#undo_for_final_modal'><span><span>UNDO FINAL APPROVAL</span></span></button></a>");
                    // $("#buttons").append("<a><button type='button' class='btn btn-warning text-white btnUndo_final m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#undo_for_final_modal'><span><span>Undo Final Approval</span></span></button></a>");
                }

                if (jQuery.inArray("approve_action", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-success btnApprove_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#approved_modal'><span><span>Approve</span></span></button></a>");
                    $("#buttons").append("<a href='javascript:void(0)' class='btn btn-success btnApprove_action m-btn m-btn--custom m-btn--air m-btn--box text-white' data-toggle='modal' data-target='#approved_modal'><span><span>APPROVE</span></span></button></a>");
                }

                if (jQuery.inArray("disapprove_action", _currentActions) !== -1) {
                    // $("#buttons").append("<a><button type='button' class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#disapprove_modal'><span><span>Disapprove</span></span></button></a>");
                    $("#buttons").append("<a class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box m-1 text-white' data-toggle='modal' data-target='#disapprove_modal'><span><span>DISAPPROVE</span></span></a>");
                }

                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = 'eforms/cash_advance/masterfile';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                break;
            case 'Released':
                $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                $("#status_detail").addClass("alert alert-focus");

                if (jQuery.inArray("print", _currentActions) !== -1) {
                    $("#buttons").append("<button type='button' onclick='printArea()' class='btn btn-accent btnPrint m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Print Report</span></span></button>");
                }

                if (jQuery.inArray("back", _currentActions) !== -1) {
                    $("#buttons").append("<a href='" + baseUrl('eforms/cash_advance/masterfile') + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }

                $("#disapproved_by").hide();
                $("#reason").hide();
                $("#cancelled_by").hide();

                break;
            default:
                if (data.hr_bal_by) {
                    $("#hr_bal").append("<b>" + data.hr_bal_remarks + "</b> from <b>PAYROLL</b> by <b>" + data.hr_bal_by + "</b>");
                    $("#hr_bal_dt").append("On <b>" + moment(data.hr_bal_dt).format('LLL') + "</b>");
                    $("#hr_bal_remarks").append("Remarks: <b>" + data.hr_remarks + "</b>");
                    $("#acctg_bal").append("<b>" + data.acctg_bal_remarks + "</b> from <b>Acctg</b> by <b>" + data.acctg_bal_by + "</b>");
                    $("#acctg_bal_dt").append("On <b>" + moment(data.acctg_bal_dt).format('LLL') + "</b>");
                    $("#acctg_bal_remarks").append("Remarks: <b>" + data.acctg_bal_remarks2 + "</b>");
                } else {
                    $("#hr_bal").append("<b>N/A</b>");
                }
                if (data.approved_remarks) {
                    $("#amount_approved").show();
                    $("#approved_remarks").show();
                    $("#approved_by").show();
                    $("#allowable").show();
                } else {
                    $("#amount_approved").hide();
                    $("#approved_remarks").hide();
                    $("#approved_by").hide();
                    $("#allowable").hide();
                }
                if (data.recommend_by) {
                    $("#recommend_by").show();
                } else {
                    $("#recommend_by").hide();
                }
                $("#disapproved_by").hide();
                $("#status_detail").addClass("alert alert-metal text-white");
                if (jQuery.inArray("back", _currentActions) !== -1) {
                    const temp_url = (notif == 'true') ? 'eforms/cash_advance/pending_balance' +  ca_type :'eforms/cash_advance/archive';
                    // $("#buttons").append("<a href='" + baseUrl(temp_url) + "'><button type='button' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Back</span></span></button></a>");
                    $("#buttons").append("<a href='" + baseUrl(temp_url) + "' title='Back to Masterfile' class='btn btn-metal text-white btnBack m-btn m-btn--custom m-btn--air m-btn--box'><span><span>BACK</span></span></a>");
                }
                break;
        }

        vmTab1.vm_tab1 = Object.assign({}, data);
        vmTab1.loading_content = false;
        setTimeout(function(){ $("#list-content").removeClass("m--hide"); }, 250);
        
        if(typeof data.ocharge != "undefined" && data.ocharge.length){
            vmTab1.vm_charge = Object.assign({}, data.ocharge);
            vmTab1.count = data.ocharge.length;
            
            vmCashAdvancePrint.vm_charge = Object.assign({}, data.ocharge);
            vmCashAdvancePrint.count=data.ocharge.length;

            setAccountingModal.vm_charge = Object.assign({}, data.ocharge);
            setAccountingModal.count=data.ocharge.length;
        }
        
        vmCashAdvancePrint.vm_tab1 = Object.assign({}, data);
        setAccountingModal.vm_tab1 = Object.assign({}, data);
    }

});

var vmTab1 = new Vue({
    el: "#cash_advance_renderer",
    data: { vm_tab1: {}, vm_charge: {}, count:0, loading_content: true },
});

var setAccountingModal = new Vue({
    el: ".set_acctg_modal_details",
    data: { vm_tab1: {}, vm_charge: {}, count:0,  },
});
Inputmask.extendAliases({
    pesos: {
        prefix: "₱ ",
        groupSeparator: ".",
        alias: "numeric",
        placeholder: "0",
        autoGroup: !0,
        digits: 2,
        digitsOptional: !1,
        clearMaskOnLostFocus: !1
    }
});

$('#set_acctg_modal_form input[name=cash_advance_pending]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('.set_acctg_modal_details input[name=cash_advance_pending]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

$('#set_acctg_modal_form input[name=cash_advance_interest]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal_form input[name=cash_advance_interest]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});
$('#set_acctg_modal_form input[name=sss_loan]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal_form input[name=sss_loan]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});
$('#set_acctg_modal_form input[name=hdmf_loan]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal_form input[name=hdmf_loan]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});
$('#set_acctg_modal_form input[name=outside_loan]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal_form input[name=outside_loan]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

// $('#set_acctg_modal_form #outside_loann').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
// $('#set_acctg_modal_form #outside_loann').keypress(function () {
//     return (/\d/.test(String.fromCharCode(event.which)));
// });

$("#recommend_modal #amt_applied").inputmask({ removeMaskOnSubmit: true });

$('#set_hr_modal input[name=recommend_remarks]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_hr_modal input[name=recommend_remarks]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

$('#set_hr_modal input[name=hr_bal_remarks]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_hr_modal input[name=hr_bal_remarks]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

$('#set_acctg_modal input[name=acctg_bal_remarks]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal input[name=acctg_bal_remarks]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

$('#set_acctg_modal input[name=display_acctg_bal_remarks]').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
$('#set_acctg_modal input[name=display_acctg_bal_remarks]').keypress(function () {
    return (/\d/.test(String.fromCharCode(event.which)));
});

function recommend_details($id) {
    $.ajax({
        url: baseUrl('eforms/cash_advance/get_cash_advance_details/') + param_id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            vmTab2.vm_tab2 = Object.assign({}, data);
            vmTab2.isLoading = false;
        }
    });

    var vmTab2 = new Vue({
        el: "#recommend_form",
        data: { vm_tab2: {}, isLoading: true }
    });

    $.validate({
        form: '#recommend_form',
        lang: 'en',
        onSuccess: function (form) {
            var disabled = $('#recommend_form').find('textarea:disabled').removeAttr('disabled');
            $.ajax({
                url: baseUrl("eforms/cash_advance/recommend_update/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#recommend_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#recommend_modal').modal('hide');
                        $('#recommend_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                        disabled.attr('disabled', 'disabled');
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                        disabled.attr('disabled', 'disabled');
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}

$.validate({
    form: '#disapprove_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/disapprove_update/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#disapprove_modal_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#disapprove_modal').modal('hide');
                    $('#disapprove_modal_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
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
    form: '#cancel_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/cancel_update/") + param_id,
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
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
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
    form: '#set_hr_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/set_hr_update/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#set_hr_modal_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#set_hr_modal').modal('hide');
                    $('#set_hr_modal_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                } else {
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

function edit_hr($id) {
    $.ajax({
        url: baseUrl("eforms/cash_advance/edit_hr_details/") + $id,
        type: "get",
        success: function (data) {
            vmTab3.vm_tab3 = Object.assign({}, data);
        }
    });

    var vmTab3 = new Vue({
        el: "#edit_set_hr_modal_form",
        data: { vm_tab3: {} }
    });

    $('#edit_set_hr_modal input[name=recommend_remarks').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
    $('#edit_set_hr_modal input[name=recommend_remarks').keypress(function () {
        return (/\d/.test(String.fromCharCode(event.which)));
    });

    $('#edit_set_hr_modal input[name=hr_bal_remarks').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
    $('#edit_set_hr_modal input[name=hr_bal_remarks').keypress(function () {
        return (/\d/.test(String.fromCharCode(event.which)));
    });

    $.validate({
        form: '#edit_set_hr_modal_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/cash_advance/set_hr_update/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#edit_set_hr_modal_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#edit_set_hr_modal').modal('hide');
                        $('#edit_set_hr_modal_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}


// edit modal acc details


function edit_acc_details($id) {
    $.ajax({
        url: baseUrl("eforms/cash_advance/edit_acc_details/") + $id,
        type: "get",
        success: function (data) {
            vmTab3.vm_tab3 = Object.assign({}, data);
        }
    });

    var vmTab3 = new Vue({
        el: "#set_acctg_modal_form_details",
        data: { vm_tab3: {} }
    });

    $('#edit_set_hr_modal input[name=recommend_remarks').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
    $('#edit_set_hr_modal input[name=recommend_remarks').keypress(function () {
        return (/\d/.test(String.fromCharCode(event.which)));
    });

    $('#edit_set_hr_modal input[name=hr_bal_remarks').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
    $('#edit_set_hr_modal input[name=hr_bal_remarks').keypress(function () {
        return (/\d/.test(String.fromCharCode(event.which)));
    });

    $.validate({
        form: '#set_acctg_modal_form_details',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/cash_advance/set_hr_update_details/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#set_acctg_modal_form_details").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#edit_set_hr_modal').modal('hide');
                        $('#set_acctg_modal_form_details')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}


// edit modal acc details

const setAcctUpdateForm = function(id=null, currentForm){
    if(id){
        $.ajax({
            url: baseUrl("eforms/cash_advance/set_acctg_update/") + id,
            type: "POST",
            dataType: "json",
            data: currentForm.serialize(),
            beforeSend: function () {
                if($("#set_acctg_status").val() == "HR Balance Pending" || $("#set_acctg_status").val() == "Payroll Balance Pending" 
                    || $("#set_acctg_status").val() == "Accounting Balance Pending"){
                    currentForm.find("#save").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }else{ currentForm.find("#for_approval").addClass("m-btn--custom m-loader m-loader--light m-loader--right"); }
            }, success: function (data) {
                if (data) {
                    $('#set_acctg_modal').modal('hide');
                    currentForm[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                } else { toastr.error(data.toastr_msg, "Error!", 5000); }
                currentForm.find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
    }else{
        return false;
    }
}

$.validate({
    form: '#set_acctg_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        const currentForm = $(form);
        const { acctg_ca_interest_percentage } = vmTab1.vm_tab1;

        if(typeof acctg_ca_interest_percentage != "undefined" && parseFloat(acctg_ca_interest_percentage) == 0){
            Swal.fire({
                title: 'Set Interest Percentage!',
                html: "<strong class='m--font-danger'>Interest Percentage</strong> was not set! are you sure you want to proceed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Proceed',
              }).then((result) => {
                if (result.isConfirmed) { setAcctUpdateForm(param_id, currentForm); }
              });
        }else{ setAcctUpdateForm(param_id, currentForm); } 

        return false;
    },
});

function edit_acctg($id) {
    $.ajax({
        url: baseUrl("eforms/cash_advance/edit_acctg_details/") + $id,
        type: "get",
        success: function (data) {
            vmTab4.vm_tab4 = Object.assign({}, data);
        }
    });

    var vmTab4 = new Vue({
        el: "#edit_set_acctg_modal_form",
        data: { vm_tab4: {} }
    });

    $('#edit_set_acctg_modal input[name=acctg_bal_remarks').inputmask({ alias: "pesos", removeMaskOnSubmit: true });
    $('#edit_set_acctg_modal input[name=acctg_bal_remarks').keypress(function () {
        return (/\d/.test(String.fromCharCode(event.which)));
    });

    $.validate({
        form: '#edit_set_acctg_modal_form',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/cash_advance/set_acctg_update/") + param_id,
                type: "POST",
                dataType: "json",
                data: $("#edit_set_acctg_modal_form").find("input,textarea").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data) {
                        $('#edit_set_acctg_modal').modal('hide');
                        $('#edit_set_acctg_modal_form')[0].reset();
                        location.reload();
                        toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Error!", 5000);
                    }
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });
            return false;
        },
    });
}


$.validate({
    form: '#approved_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/get_cash_advance_details/") + param_id,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var amt_approve = parseInt((($('#approved_modal input[name=amt_approved').val()).replace(',', "")).replace('₱a ', ""));
                var amt_applied = parseInt((data.amt_applied.replace(',', "")).replace('₱ ', ""));
                if (amt_approve > amt_applied) {
                    toastr.error(data.toastr_msg, "Amount approved must be less than or equal to amount applied!", 5000);
                } else {
                    $.ajax({
                        url: baseUrl("eforms/cash_advance/approve_update/") + param_id,
                        type: "POST",
                        dataType: "json",
                        data: $("#approved_modal_form").find("input,textarea").serialize(),
                        beforeSend: function () {
                            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        },
                        success: function (data) {
                            if (data) {
                                $('#approved_modal').modal('hide');
                                $('#approved_modal_form')[0].reset();
                                location.reload();
                                toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                            } else {
                                toastr.error(data.toastr_msg, "Error!", 5000);
                            }
                            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        }
                    });
                }
            }
        });

        return false;
    },
});

function for_posting(){
    $.ajax({
        url: baseUrl('eforms/cash_advance/for_posting/') + param_id,
        type: "POST",
        dataType: "json",
        data:{
            csrf_token: _csrf_hash
        },
        beforeSend: function () {
            $(".btnPosting_action").attr("disabled", true);
            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(response){
            if(response.toastr_status){
                location.reload();
                toastr.success(response.toastr_msg, "Updated successfully!", 5000);
            }else{
                toastr.error(data.toastr_msg, "Error!", 5000);
            }

            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$.validate({
    form: '#posted_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        const formData = new FormData($("#posted_modal_form")[0]);
        formData.append("csrf_token", _csrf_hash);
        $.ajax({
            url: baseUrl('eforms/cash_advance/for_posted/') + param_id,
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#posted_modal').modal('hide');
                    $('#posted_modal_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }

                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

$.validate({
    form: '#undo_posting_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        console.log($('#undo_posting_modal_form').find("input,textarea").serialize());
        $.ajax({
            url: baseUrl('eforms/cash_advance/undo_for_posting/') + param_id,
            type: "POST",
            dataType: "json",
            data: $('#undo_posting_modal_form').find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#undo_posting_modal').modal('hide');
                    $('#undo_posting_modal_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }

                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

$.validate({
    form: '#undo_posted_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        console.log($('#undo_posted_modal_form').find("input,textarea").serialize());
        $.ajax({
            url: baseUrl('eforms/cash_advance/undo_posted/') + param_id,
            type: "POST",
            dataType: "json",
            data: $('#undo_posted_modal_form').find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#undo_posted_modal').modal('hide');
                    $('#undo_posted_modal_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }

                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

function for_final_approval(){
    $.ajax({
        url: baseUrl('eforms/cash_advance/final_approval/') + param_id,
        type: "POST",
        dataType: "json",
        data:{
            csrf_token: _csrf_hash
        },
        beforeSend: function () {
            $(".btnPosting_action").attr("disabled", true);
            $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(response){
            if(response.toastr_status){
                location.reload();
                toastr.success(response.toastr_msg, "Updated successfully!", 5000);
            }else{
                toastr.error(data.toastr_msg, "Error!", 5000);
            }

            $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
        }
    });
}

$.validate({
    form: '#final_approval_form',
    lang: 'en',
    onSuccess: function (form) {
        const formData = new FormData($("#final_approval_form")[0]);
        formData.append("csrf_token", _csrf_hash);
        $.ajax({
            url: baseUrl('eforms/cash_advance/final_approval/') + param_id,
            type: "POST",
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#final_approval_modal').modal('hide');
                    $('#final_approval_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }

                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

$.validate({
    form: '#undo_recommend_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/undo_recommend_update/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_recommend_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_recommend_modal').modal('hide');
                    $('#undo_recommend_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
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
    form: '#undo_approval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/undo_approval_update/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_approval_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_approval_modal').modal('hide');
                    $('#undo_approval_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
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
    form: '#undo_disapproval_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("eforms/cash_advance/undo_disapproval_update/") + param_id,
            type: "POST",
            dataType: "json",
            data: $("#undo_disapproval_form").find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    $('#undo_disapproval_modal').modal('hide');
                    $('#undo_disapproval_form')[0].reset();
                    location.reload();
                    toastr.success(data.toastr_msg, "Updated successfully!", 5000);
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
    form: '#undo_awaiting_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        console.log($('#undo_awaiting_modal_form').find("input,textarea").serialize());
        $.ajax({
            url: baseUrl('eforms/cash_advance/undo_awaiting_approval/') + param_id,
            type: "POST",
            dataType: "json",
            data: $('#undo_awaiting_modal_form').find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#undo_awaiting_modal').modal('hide');
                    $('#undo_awaiting_modal_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }

                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });

        return false;
    },
});

$.validate({
    form: '#undo_for_final_modal_form',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl('eforms/cash_advance/undo_for_final/') + param_id,
            type: "POST",
            dataType: "json",
            data: $('#undo_for_final_modal_form').find("input,textarea").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function(response){
                if(response.toastr_status){
                    $('#undo_for_final_modal').modal('hide');
                    $('#undo_for_final_modal_form')[0].reset();
                    location.reload();
                    toastr.success(response.toastr_msg, "Updated successfully!", 5000);
                }else{
                    toastr.error(data.toastr_msg, "Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

var tblFile = $("#table-file-content").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    bPaginate: false,
    bInfo: false,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/cash_advance/get_file_content/") + param_id,
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash
        }
    },
    searching: false,
    columns: [
        { data: "", width: "5%", orderable: false, className: "text-center", render: function (_data, _type, row, _meta) {
            return fileName(row.filename, row.emp_id, row.ext, row.has_thumbnail); 
        } },
        { data: "filename" },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (_data, _type, row, _meta) { return itemDatatableActions(row.id, row.filename, row.emp_id, row.has_file); },
        }
    ]
});

function fileName($name, $id, $ext, $hasThumbnail = false) {
    if (($ext == "jpg" || $ext == "png" || $ext == "PNG" || $ext == "JPEG" || $ext == "JPG") && $hasThumbnail) {
        return "<img src=" + baseUrl("uploads/files/cash_advance/empcode_" + $id + "/thumbnails/" + $name) + " width='35' />";
    } else {
        return "<i class='flaticon-interface-1'></i>";
    }
}

function itemDatatableActions($id, $name, $employee, $has_file = false) {
    if ($id && $has_file) {
        $fileName = '"' + $name + '"';
        return "<button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' onclick='openFile(" + $employee + ", " + $fileName + ")'><i class='la la-eye'></i></button>";
    } else { return "<i class='la la-eye-slash'></i>"; }
}

function openFile($employeeId, $name) {
    window.open(baseUrl("uploads/files/cash_advance/empcode_" + $employeeId + "/" + $name));
}

var vmCashAdvancePrint = new Vue({
    el: "#print_cash_advance",
    data: { vm_tab1: {}, vm_charges: {}, count: 0 }
});

// function printArea() {
//     win = window.open();
//     var divToPrint = document.getElementById("printableArea");
//     win.document.write(divToPrint.outerHTML);
//     win.focus();
//     win.print();

//     // $.ajax({
//     //     url: baseUrl("eforms/cash_advance/get_cash_advance_details/") + param_id,
//     //     type: "GET",
//     //     dataType: "JSON",
//     //     success: function (data) {
//     //         if (data.deduct_type == "percentage") {
//     //             $("#amount_deduct1").text(data.amt_to_b_deducted + "%");
//     //             $("#amount_deduct2").text(data.amt_to_b_deducted + "%");
//     //         } else {
//     //             $("#amount_deduct1").text(data.amt_to_b_deducted);
//     //             $("#amount_deduct2").text(data.amt_to_b_deducted);
//     //         }
//     //         vmTab1.vm_tab1 = Object.assign({}, data);
//     //     }
//     // });

   
// }

function printArea() {
    setTimeout( function (){
        win = window.open();
        var divToPrint = document.getElementById("printableArea");
        win.document.write(divToPrint.outerHTML);
        win.focus();
        win.print();
        win.close();
    },500);
}

$("#set_acctg_modal").on("show.bs.modal", function(){
   $.ajax({
        url: baseUrl("eforms/cash_advance/getAccountingDetails/") + param_id,
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token : _csrf_hash
        },
        success: function(data){
            $("#set_acctg_modal input[name=acctg_bal_remarks]").val(data);
            $("#display_acctg_bal_remarks").val(data);
        }
   });
});

function deductionStatus(ref, id){
    $("#deduction_status_modal").modal('show');
    $.ajax({
        url : baseUrl('eforms/cash_advance/getCA_details/') + id,
        type: "POST",
        dataType : "JSON",
        data: {
            ref: ref,
            csrf_token : _csrf_hash
        },
        beforeSend: function(){
            $("#for_remarks").text('');
        },
        success: function(response){
            var remarks = (response.data.remarks == '') ? "N/A" : response.data.approved_remarks;
            $("#for_remarks").text(remarks);

            if(response.pay_count == 0){
                $("#for_history").addClass('hideTable');
            }else{
                loan_history(response.data.reference);
                $("#for_history").removeClass('hideTable');
            }
        }
    });
}

function loan_history(ref){
    $("#loan_history").DataTable({
        dom: "frtlp",
        serverSide: false,
        destroy: true,
        beforeSend: function( ){
            $(this).remove();
        },
        ajax: {
            url: baseUrl(`eforms/cash_advance/get_cashadvance_history`),
            type: "POST",
            dataType: "JSON",
            data: {
                ref: ref,
                csrf_token : _csrf_hash
            }
        },
        autoWidth: false,
        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    return `<span class="m--font-boldest">${moment(data.date_start).format("MMM. DD, YYYY")}</span>`
                        + " - " + `<span class="m--font-boldest">${moment(data.date_end).format("MMM. DD, YYYY")}</span>`;
                }
            },
            {
                width: "30%",
                data: null,
                render: function (data, type, row) {
                    return `<div class="m--font-bolder">${row.firstname} ${row.lastname}</div>
                            <div class="m--regular-font-size-sm1 text-muted">${moment(row.posted_at).format("lll")}</div>`;
                }
            },
            {
                width: "20%",
                data: "amount_due",
                className: "text-right",
                render: function (data, type, row) {
                    return `<span class="m--font-boldest">
                                ${parseFloat(data).toLocaleString("en-US", {maximumFractionDigits: 2})}
                            </span>`;
                }
            },
        ],
        footerCallback: function (row, data, start, end, display) {
            const api = this.api();
            const total = api
                .column(2)
                .data()
                .reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

            $(api.column(2).footer()).html(
                `<span class="m--font-boldest m--regular-font-size-lg1">
                    ${parseFloat(total).toLocaleString("en-US", {maximumFractionDigits: 2})}
                </span>`
            );
        }
    });
}

var dl = $('.meu-rows').length;
checkMeuCount(dl);

var count = 1;
        
$('#addScnt').click(function(){
    count = count + 1;
    var html_code = '<div id="row_' + count + '" class="meu-rows row align-item-center py-3 tempo">';
    html_code += '<div class="col-7 ps-0"> <span>Label</span> <input type="text" class="form-control resetafter form-control-solid meu-items" placeholder="Charge Label" id="otherChargersLabel" name="oc_label[]" required /> </div>';
    html_code += '<div class="col-4 ps-0"> <span>Amount</span> <input type="number" step=".01" class="form-control resetafter form-control-solid meu-items oc_amount" placeholder="₱ 0.00" id="otherChargersAmount" name="oc_amount[]" required /> </div>';
    html_code += '<div class="col-1 d-flex align-items-end justify-content-center p-1"><span class="remove-meu svg-icon svg-icon-danger svg-icon-2hx" id="remove" data-row="row_'+count+'"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"/><rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="currentColor"/><rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="currentColor"/></svg></span></div>';
    html_code += '</div>';
  
    $('#meu-wrapper').append(html_code); 

    var l = $('.meu-rows').length;
    checkMeuCount(l);
});

$(document).on('click','.remove-meu', function(){
    var delete_row = $(this).data("row");
    var counter = $(this).attr('id');
    var _counter = counter;

    if(counter == 0){
        _counter = counter + 1;
    }

    if($(".meu-rows").hasClass('tempo')){
        $('#'+delete_row).remove();
        $('#'+delete_row).removeClass('tempo');
    }else{
        $('#'+delete_row+'_'+counter).remove();
        $('#meu-wrapper .meu-rows:nth-child('+_counter+')').removeClass('tempo');
    }
    var nl = $('.meu-rows').length;
    checkMeuCount(nl);
});

function checkMeuCount(length) {
    if(length == 0) {
        $('#remove').removeClass('remove-meu');
    } else {
        $('#remove').addClass('remove-meu');
    }
}

var imagesPreview = function(input, placeToInsertImagePreview) {
    if (input.files) {
      var filesAmount = input.files.length;
      $(".gallery").html('');
      for (i = 0; i < filesAmount; i++) {
        var reader = new FileReader();
  
        reader.onload = function(event) {
          $($.parseHTML('<a href="'+event.target.result+'" class="col-lg-4 text-center" style="margin-bottom: 10px" data-lightbox="photos"><img class="img-fluid" src="'+event.target.result+'"><a/>')).appendTo(placeToInsertImagePreview);
        }
        reader.readAsDataURL(input.files[i]);
      }
    }
};
  
$('#final-fileupload').on('change', function() {
    imagesPreview(this, 'div.gallery');
});

$('#posted-fileupload').on('change', function() {
    imagesPreview(this, 'div.gallery');
});

function setCaInterestPercentage(){
    Swal.fire({
        title: 'Interest Percentage!',
        icon: 'warning',
        inputLabel: "Enter the Interest Percentage",
        input: "number",
        inputValue: 0,
        inputPlaceholder: "0",
        customClass: { input: 'form-control text-center' },
        inputValidator: (value) => {
            if (!value) { return 'You need set the minimum value of 0 and maximum value of 100!' }
            else if(parseFloat(value) < 0 || parseFloat(value) > 100){ return 'Set the minimum value of 0 and maximum value of 100!'; }
        },
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Set Interest Percentage'
      }).then((result) => {
        if (result.isConfirmed) {
            const tempValue = result.value;
            $.ajax({
                url: siteUrl("eforms/cash_advance/set_interest_percentage"),
                data: { [_csrf_token]: _csrf_hash, id: param_id, interest_percentage: tempValue },
                type: "post",
                dataType: "json",
                success: function(json){
                    if(json.response){
                        Swal.fire({
                            title: 'Success!',
                            html: "<strong class='m--font-danger'>Interest Percentage</strong> has been added/updated successfully.",
                            icon: 'success',
                        });
                        toastr.success(json.toastr_msg, "Set/Update Interest Percentage");
                        vmTab1.vm_tab1 = Object.assign({}, vmTab1.vm_tab1, { acctg_ca_interest_percentage: tempValue });
                    }else{
                        Swal.fire({
                            title: 'Failed!',
                            html: "Failed to set/update <strong class='m--font-danger'>Interest Percentage</strong>!",
                            icon: 'error',
                        });
                        toastr.error(json.toastr_msg, "Set/Update Interest Percentage");
                    }
                }
            });
            
        }
      });
}

$.validate({
    form: '#released-form',
    lang: 'en',
    onSuccess: function(form) {
        const currentForm = form[0];
		const formData = $(currentForm).serialize();

        Swal.fire({
            title: 'Released',
            icon: 'question',
            text: "Are you sure you wan't to released this cash advance now? You can't undo the changes afterwards.",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl("eforms/cash_advance/released/") + param_id,
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    beforeSend: function () {
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function (data) {
                        if (data.state) {
                            toastr.success(data.msg, "Updated successfully!", 5000);
                            $('#released').modal('hide');
                            $('#released-form').trigger('reset');
                            location.reload();
                        } else{
                            toastr.error(data.msg, "Error!", 5000);
                        }
        
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            }
        });


        return false;
    }
});

$("#released").on('hidden.bs.modal', function (e) {
    $("#released-form").trigger('reset');
});