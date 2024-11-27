var search_val = "";
var query_builder = "";
var tbleventlogs = $("#table-eventlogs").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_event_logs/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
           d.csrf_token = _csrf_hash,
           d.search['value'] = search_val,
           d.query_builder = query_builder
       }
   },
   searching: true,
   columns: [
       { data: "user", width: "6%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
       { data: "log_message", width: "25%"},
       { data: "user_action", className: "text-center", width: "4%"},
       { data: "type", className: "text-center", width: "4%", render: function (data) {
                return renderStatus(data)
            }
       },
       { data: "created_at", className: "text-center", width: "5%"},
   ],
   select: {
    style:    'os',
    selector: 'td:first-child'
   },
   buttons: [
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

function renderStatus(data) {
    switch (data) {
        case "success":
            return '<div class="m-badge text-white m-badge--accent m-badge--wide" role="alert"><strong>Success</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Error</strong></div>';
            break;
    }
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tbleventlogs.ajax.reload();
});

$("#ExportExcel").on("click", function() {
    tbleventlogs.button( '.buttons-excel' ).trigger();
    saveExportLogs('Event Logs - Export Excel');
});

$("#ExportCSV").on("click", function() {
    tbleventlogs.button( '.buttons-csv' ).trigger();
    saveExportLogs('Event Logs - Export CSV');
});

$("#ExportPDF").on("click", function() {
    tbleventlogs.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Event Logs - Export PDF');
});

function saveExportLogs(export_){
    $.ajax({
        url: baseUrl("eforms/billing/save_export_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, export_: export_ },
        success: function (data) {
            
        }
    });
}