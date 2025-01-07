 var search_val = "";
var tblTransmittal = $("#table-transmittal").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/transmittal/get_archive_request/"),
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
        { data: "reference_no"},
        { data: "company_from"},
        { data: "firstname", orderable: false, render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "trans_desc", render: function(data) {return formatContent(data)}},
        { data: "ship_date", render: function (data) {return formatCalendarDate(data)}},
        { data: "created_by"},       
        { data: "created_dt", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [4], width: "15%" },
        { targets: [0], className: "statusAlign" },
        { targets: [2], width: "5%" },
        { targets: [1], width: "5%" },
        { targets: [7], width: "12%" },
        { targets: [9], width: "5%" },
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
    tblTransmittal.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log_archived/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblTransmittal.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log_archived/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblTransmittal.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log_archived/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val
        }
    });
});

function displayName($displayName){
    return $displayName;
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
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Received</strong></div>';
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
			_actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/transmittal/view_transmittal?id=')+$id+"' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}

function formatContent(data){
    var shortname = data;
    if (data != null) {
        if (data.length > 20) {
            var shortname = data.substring(0, 50) + " ...";
        }
    }else{
        var shortname = "--";
    }

    return shortname;
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblTransmittal.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblTransmittal.ajax.reload();
});
