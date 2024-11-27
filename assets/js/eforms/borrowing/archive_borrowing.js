 var search_val = "";
var tblBorrowing = $("#table-borrowing").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/borrowing/get_archive_request/"),
        type: "post",
        global: false,
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
        }
    },
    searching: true,
    columns: [
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "reference_no"},
        { data: "company", width: "10%"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "asset_name", width: "30%"},
        { data: "date_trans", width: "12%", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [0], className: "statusAlign" },       
        { targets: [6], width: "5%" },
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
    tblBorrowing.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/borrowing/export_event_log_archive/1"),
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
    tblBorrowing.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/borrowing/export_event_log_archive/2"),
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
    tblBorrowing.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/borrowing/export_event_log_archive/3"),
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
        case "Released":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Released</strong></div>';
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
        return moment(data).format("lll");
    }
  
}

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
			_actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/borrowing/view_borrowing?id=')+$id+"' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}
//custom global search init
$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblBorrowing.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblBorrowing.ajax.reload();
});
$.ajax({
    url: baseUrl('eforms/borrowing/overdue_count/') ,
    type: "POST",
    dataType: "JSON",
    data:  { csrf_token: _csrf_hash },
    success: function(data){

        $('#overdue').append(data);
      
    }, error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "count"');
    }
});

