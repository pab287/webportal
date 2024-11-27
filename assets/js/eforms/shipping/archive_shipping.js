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
var tblShipping = $("#table-shipping").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/shipping/archive_datatable_request/"),
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
        { data: "priority"},
        { data: "reference_no" },
        { data: "file_under"},
        { data: "firstname", render: function (data, type, row, meta){return renderShipTo(row.display_name, row.company_to, row.department_to)}},
        { data: "description", width: "20%"},
        { data: "ship_date", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [0], className: "statusAlign", width: "5%" },
        { targets: [1], width: "5%" },
        { targets: [2], width: "12%" },
        { targets: [5], width: "15%" },
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

$("#ExportExcel").on("click", function(e) {
    e.preventDefault();
    tblShipping.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/shipping/export_event_log_archived/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblShipping.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/shipping/export_event_log_archived/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblShipping.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/shipping/export_event_log_archived/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

function renderShipTo($name, $company, $department){
    return "<b>"+$name+"</b><br>"+$company+"<br>"+$department;
}

function renderStatusHtml(data){
    switch(data){
        case "Pending":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
        break;
        case "Approved":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
        break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
        break;
        case "Received":
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Received</strong></div>';
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
			_actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/shipping/view_shipping?id=')+$id+"' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblShipping.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblShipping.ajax.reload();
});