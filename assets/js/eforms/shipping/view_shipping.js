var getUrlParameter = function getUrlParameter(sParam){
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
        for (i = 0; i < sURLVariables.length; i++){
                sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam){
                    return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
};

param_id = getUrlParameter('id');
var tblShipping = $("#table-shipping-content").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    bPaginate: false,
    bInfo: false,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/shipping/shipping_content_table/")  + param_id,
		type: "post",
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash
		}
    },
    searching: false,
    columns: [
        { data: "stock_code"},
        { data: "quantity", render: function(data, type, row, meta){return row.quantity+" "+row.uom; }},
        { data: "description"},
        { data: "item_purpose" },
    ],
});

var vmTab1 = new Vue({
    el: "#shipping_renderer",
    data: { vm_tab1: {} }
});

$("#approve_modal").hide();
$("#undo_approve_modal").hide();
$("#disapprove_modal").hide();
$("#undo_disapprove_modal").hide();
$("#cancel_modal").hide();
$("#undo_cancel_modal").hide();
$("#receive_modal").hide();
$("#undo_receive_modal").hide();

$.ajax({
    url: baseUrl('eforms/shipping/view_shipping_details/')+ param_id,
    type: "GET",
    dataType: "JSON",
    success: function(data){
        if(data.shipping_main[0].cat=="in"){
            $("#ship_type").text("Internal");
            $("#internal_det").show();
            $("#external_det").hide();
            $("#waybill").hide();
        }else{
            $("#ship_type").text("External");
            $("#external_det").show();
            $("#internal_det").hide();
            $("#waybill").show();
        }
        if(data.shipping_main[0].is_service==1){
            $("#service").show();
            $("#others").hide();
        }else{
            $("#service").hide();
            $("#others").show();
        }
        if(data.shipping_main[0].last_edited_by && data.shipping_main[0].last_edited_dt && data.shipping_main[0].last_edited_by != "N/A"){
            $("#last_edited_by").append("ON  <b>"+moment(data.shipping_main[0].last_edited_dt).format('LLL')+"</b>");
        }
        console.log(data.shipping_main[0].status)
        switch(data.shipping_main[0].status){
            case "Pending":
                $("#status_color").addClass("alert alert-warning");
                $("#buttons").append("<button type='button' onclick='approve("+param_id+")' data-toggle='modal' data-target='#approve_modal' class='btn btn-success btnApprove_action m-btn m-btn--custom m-btn--air m-btn--box'>Approve</button>");
                $("#buttons").append("<button type='button' onclick='disapprove("+param_id+")' data-toggle='modal' data-target='#disapprove_modal' class='btn btn-danger btnDisapprove_action m-btn m-btn--custom m-btn--air m-btn--box'>Disapprove</button>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/edit_shipping?id=')+param_id+"'><button type='button' class='btn btn-warning text-white btnEdit m-btn m-btn--custom m-btn--air m-btn--box'><span><span>Edit</span></span></button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/edit_shipping?id=')+param_id+"' class='btn btn-warning text-white btnEdit m-btn m-btn--custom m-btn--air m-btn--box'><span><span>EDIT</span></span></a>");
                // $("#buttons").append("<a><button type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>Print Report</button></a>");
                $("#buttons").append("<a href='javascript:void(0)' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>PRINT REPORT</a>");
                $("#buttons").append("<button type='button' onclick='cancel("+param_id+")' class='btn btn-danger btnCancel_action m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#cancel_modal'>Cancel</button>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"'><button type='button' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Back</button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>BACK</a>");
                $("#disapproved_by").hide();
                $("#receive_by").hide();
                $("#remarks").hide();   
                $("#approve_by").hide();   
                $("#cancelled").hide();  
            break;
            case "Approved":
                $("#status_color").addClass("alert alert-success");
                $("#buttons").append("<button type='button' id='receive' onclick='recieve("+param_id+")' class='btn btn-success btnReceive m-btn m-btn--custom m-btn--air m-btn--box' data-toggle='modal' data-target='#receive_modal'>Receive</button>");
                $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_approve_modal' onclick='undoApprove("+param_id+")' class='btn btn-danger btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Undo Approval</button>");
                // $("#buttons").append("<a><button type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>Print Report</button></a>");
                $("#buttons").append("<a href='javascript:void(0)' type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>PRINT REPORT</a>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"'><button type='button' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Back</button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>BACK</a>");
                $("#disapproved_by").hide();
                $("#receive_by").hide();
                $("#remarks").hide();
                $("#cancelled").hide();  
            break;
            case "Disapproved":
                $("#status_color").addClass("alert alert-danger");
                $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_disapprove_modal' onclick='undoDisapprove("+param_id+")' class='btn btn-danger btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Undo Disapproval</button>");
                // $("#buttons").append("<a><button type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>Print Report</button></a>");
                $("#buttons").append("<a href='javascript:void(0)' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>PRINT REPORT</a>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"'><button type='button' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Back</button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>BACK</a>");
                $("#approve_by").hide();
                $("#receive_by").hide();
                $("#remarks").hide();
                $("#cancelled").hide();  
            break;
            case "Received":
                $("#status_color").addClass("alert alert-info");
                //$("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_approve_modal' onclick='undoApprove("+param_id+")' class='btn btn-danger btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Undo Approval</button>");
                $("#buttons").append("<button type='button' onclick='undoReceive("+param_id+")' data-toggle='modal' data-target='#undo_receive_modal' class='btn btn-danger btnUndo_receive m-btn m-btn--custom m-btn--air m-btn--box'>Undo Receive</button>");
                // $("#buttons").append("<a><button type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>Print Report</button></a>");
                $("#buttons").append("<a href='javascript:void(0)' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>PRINT REPORT</a>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"'><button type='button' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Back</button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>BACK</a>");
                $("#disapproved_by").hide();
                $("#cancelled").hide();  
            break;
            default:
                $("#status_color").addClass("alert alert-metal text-white");
                $("#buttons").append("<button type='button' data-toggle='modal' data-target='#undo_cancel_modal' onclick='undoCancel("+param_id+")' class='btn btn-danger btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Undo Cancel</button>");
                // $("#buttons").append("<a><button type='button' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>Print Report</button></a>");
                $("#buttons").append("<a  href='javascript:void(0)' onclick='printArea("+param_id+")' class='btn btn-accent btnNew m-btn m-btn--custom m-btn--air m-btn--box printBtn'>PRINT REPORT</a>");
                // $("#buttons").append("<a href='"+baseUrl('eforms/shipping/archive_shipping')+"'><button type='button' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>Back</button></a>");
                $("#buttons").append("<a href='"+baseUrl('eforms/shipping/masterfile')+"' class='btn btn-metal text-white btnNew m-btn m-btn--custom m-btn--air m-btn--box'>BACK</a>");
                $("#disapproved_by").hide();
                $("#receive_by").hide();
                $("#remarks").hide();   
                $("#approve_by").hide();   
            break;
        }
        
        vmTab1.vm_tab1 = Object.assign({}, data.shipping_main[0]);
        vmPrint.vm_tab1 = Object.assign({}, data.shipping_main[0]);
        vmPrint.vm_content = Object.assign({}, data.shipping_content.data);
    }
});

function approve($id){
    $.validate({
        form : '#approve_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/approve_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#approve_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#approve_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Shipping approved!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function undoApprove($id){
    $.validate({
        form : '#undo_approve_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/undo_approve_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#undo_approve_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#undo_approve_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function disapprove($id){
    $.validate({
        form : '#disapprove_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/disapprove_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#disapprove_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#disapprove_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Shipping disapproved!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function undoDisapprove($id){
    $.validate({
        form : '#undo_disapprove_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/undo_disapprove_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#undo_disapprove_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#undo_disapprove_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function cancel($id){
    $.validate({
        form : '#cancel_modal_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/cancel_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#cancel_modal_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#cancel_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Shipping cancelled!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function undoCancel($id){
    $.validate({
        form : '#undo_cancel_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/undo_cancel_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#undo_cancel_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#undo_cancel_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Shipping cancelled!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function recieve($id){
    $.validate({
        form : '#receieve_modal_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/receive_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#receieve_modal_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#receive_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Shipping received!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

function undoReceive($id){
    $.validate({
        form : '#undo_receive_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/shipping/undo_receive_shipping/") + $id,
                    type: "POST",
                    dataType: "json",
                    data: $("#undo_receive_form").find("input,textarea,select,text").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#undo_receive_modal').modal('hide');
                            location.reload();
                            toastr.success(data.toastr_msg, "Updated successfully!", 5000);
                        }else{
                            toastr.error(data.toastr_msg, "Error!", 5000);    
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            return false;
        },
    });
}

var vmPrint = new Vue({
    el: "#print_shipping",
    data: { vm_tab1: {}, vm_content: {} },
    methods: {
        generateQR:function(){
            const _this=this;
            const temp_data=_this.vm_tab1;
            let url="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl="+temp_data.reference_no+"&choe=UTF-8";
            return url;
        }
    }
});  

function printArea(id){
    win = window.open();
    var divToPrint = document.getElementById("printableArea");
    win.document.write(divToPrint.outerHTML);
    win.focus();
    win.print();
    win.close();
}