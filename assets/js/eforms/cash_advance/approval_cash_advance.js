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

var search_val = "";
var tblCashAdvance = $("#table-cash-advance").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/cash_advance/approval_list/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
			d.search['value'] = search_val
		}
    },
    searching: false,
    columns: [
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "reference_no"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "amt_applied"},
        { data: "purpose"},
        { data: "amt_approved"},
        { data: "created_dt", render: function (data) {return formatCalendarDate(data)}},
        { data: "approved_dt", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [3, 5], className: "columnAlign" },
        { targets: [0], className: "statusAlign" },
        { targets: [2], width: "15%" },
        { targets: [1], width: "10%" },
        { targets: [6,7], width: "5%" },
        { targets: [4], width: "25%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row.id); },
        }
    ]
});

function displayName($displayName){
    return $displayName;
}

function renderStatusHtml(data){
    switch(data){
        case "HR Recommendation Pending":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Sup Recommendation</strong></div>';
        break;
        case "Approved":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
        break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
        break;
        case "HR Balance Pending":
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>HR Balance Pending</strong></div>';
        break;
        case "Accounting Balance Pending":
            return '<div class="m-badge m-badge--primary m-badge--wide" role="alert"><strong>Accounting Balance</strong></div>';
        break;
        case "Awaiting Approval":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Awaiting Approval</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

function formatCalendarDate(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MM/DD/YYYY");
    }
  
}

function itemDatatableActions($id){
	if($id){
        var _actionButton ="";
            _actionButton += " <button type='button' onclick='approve("+$id+")' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-toggle='modal' data-target='#approved_modal'><i class='la la-check'></i></button>";
            _actionButton += " <button type='button' onclick='disapprove("+$id+")' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' data-toggle='modal' data-target='#disapprove_modal'><i class='la la-close'></i></button>";			
            _actionButton += " <a href='"+baseUrl('eforms/cash_advance/view_cash_advance?id=')+$id+"'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></button></a>";			
		return _actionButton;
	}else{ return false; }
}

$("#approved_modal").hide();
$("#disapprove_modal").hide();

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

$('#approved_modal input[name=amt_approved').inputmask({ alias : "pesos", removeMaskOnSubmit: true });
$('#approved_modal input[name=amt_approved').keypress(function() {
    return (/\d/.test(String.fromCharCode(event.which) ));
});

function approve(id){
    $.validate({
        form : '#approved_modal_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/cash_advance/approve_update/") + id,
                    type: "POST",
                    dataType: "json",
                    data: $("#approved_modal_form").find("input,textarea").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#approved_modal').modal('hide');
                            $('#approved_modal_form')[0].reset();
                            tblCashAdvance.ajax.reload();
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

function disapprove(id){
    $.validate({
        form : '#disapprove_modal_form',
        lang: 'en',
        onSuccess : function(form) {
                $.ajax({
                    url: baseUrl("eforms/cash_advance/disapprove_update/") + id,
                    type: "POST",
                    dataType: "json",
                    data: $("#disapprove_modal_form").find("input,textarea").serialize(),
                    beforeSend: function(){
                        $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    },
                    success: function(data){
                        if(data){
                            $('#disapprove_modal').modal('hide');
                            $('#disapprove_modal_form')[0].reset();
                            tblCashAdvance.ajax.reload();
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

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblCashAdvance.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblCashAdvance.ajax.reload();
});



