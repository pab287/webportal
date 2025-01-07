 var search_val = "";
var tblLoa = $("#table-loa").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/Loa/get_archive_request/"),
        type: "post",
        global: false,
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
            
        }
    },
    aaSorting: [],
    searching: true,
    columns: [
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "file"},
        { data: "firstname", render: function (data, type, row, meta) {return displayName(row.display_name)}},
        { data: "nature"},
        { data: "reason"},
        { data: "type",render: function (data) {return renderTypeHtml(data)}},
        { data: "type", sortable: false, render: function ( data, type, row, meta ) {return formatDifference(data,row)}},
        { data: "date_from", render: function ( data, type, row, meta ) {return formatCalendarDate(data,row)}},
        { data: "reference_no"},       
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        { targets: [4], width: "15%" },
        { targets: [3], width: "15%" },
        { targets: [0], className: "statusAlign" },
        { targets: [2], width: "10%" },
        { targets: [1], width: "10%" },
        { targets: [8], width: "5%" },
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
    tblLoa.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log_archive/1"),
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
    tblLoa.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log_archive/2"),
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
    tblLoa.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log_archive/3"),
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
        case "HR Noted":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}
function renderTypeHtml(data){
    switch(data){
        case "1":
            return 'Undertime';
        break;
        case "2":
            return 'Half Day';
        break;
        case "3":
            return 'Whole Day';
        break;
        default:
            return 'Others';
        break;
        
    }
}
function formatDifference(data,row){
    switch(data){
        case "1":
        var datefrom = new Date(row.date_from);
var dateto = new Date(row.date_to);
        var hours =Math.abs(dateto - datefrom)/36e5;
        var hours= (hours).toFixed(0);
            return hours +" HOURS";
        break;
        case "2":
            return '4 hours';
        break;
        case "3":
            return '8 hours';
        break;
        default:
             var datefrom = new Date(row.date_from);
var dateto = new Date(row.date_to);
        var hours = Math.abs(dateto - datefrom)/ 36e5;
        var temp=hours%24
         var temp2=Math.floor(hours/24);
if(temp=="0"){
 
  return temp2 +" Days ";
}else{
 return temp2 +" Days "+ temp +" HOURS";
}
            
        break;
        
    }
}
function formatCalendarDate(data,row){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{if(row.date_to=="0000-00-00 00:00:00"){
        return moment(data).format("MM/DD/YYYY");
    }
    else{
        return moment(data).format("MM/DD/YYYY hh:mm A")+" - "+moment(row.date_to).format("MM/DD/YYYY hh:mm A");
    }
    }
  
}

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
			_actionButton += " <a  class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/loa/view_loa?id=')+$id+"&page=archive' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}
//custom global search init
$('#generalSearch').donetyping(function(callback) {

    search_val = $(this).val();
    tblLoa.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
    tblLoa.ajax.reload();
});


