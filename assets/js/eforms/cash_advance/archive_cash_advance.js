var search_val = "";
var tblCashAdvance = $("#table-cash-advance").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/cash_advance/archive_list/"),
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
        { data: null, width: "8%", className: "text-center"},
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
    ],buttons: [
        { 
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, { 
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, { 
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

$("#ExportExcel").on("click", function() {
    tblCashAdvance.button( '.buttons-excel' ).trigger();
});

$("#ExportCSV").on("click", function() {
    tblCashAdvance.button( '.buttons-csv' ).trigger();
});

$("#ExportPDF").on("click", function() {
    tblCashAdvance.button( '.buttons-pdf' ).trigger();
});

function displayName($displayName){
    return $displayName;
}

function renderStatusHtml(data){
    switch(data){
        case "HR Recommendation Pending":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Sup Recommendation</strong></div>';
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
        case "Payroll Balance Pending":
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Payroll Balance Pending</strong></div>';
        break;   
        case "Awaiting Approval":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Awaiting Approval</strong></div>';
        break;
        case "For Posting":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>For Posting</strong></div>';
            break;
        case "Posted":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Posted</strong></div>';
            break;
        case "For Final Approval":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>For Final Approval</strong></div>';
            break;
        case 'Released': 
            return '<div class="m-badge m-badge--focus m-badge--wide" role="alert"><strong>Released</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
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
			_actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/cash_advance/view_cash_advance?id=')+$id+"' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblCashAdvance.ajax.reload();
    console.log(search_val);
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblCashAdvance.ajax.reload();
});



