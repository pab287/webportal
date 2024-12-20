load_telegram_config();
 var search_val = "";
 var query_builder = "";
 var param_status = "";

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

if(typeof getUrlParameter('status') !== 'undefined'){
    param_status = getUrlParameter('status');
}

var tblBorrowing = $("#table-borrowing").DataTable({
    dom: '<"toolbar">rtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/borrowing/get_datatable_request/"),
        type: "post",
        global: false,
        dataType: "json",
       data: function(d){
            d.csrf_token = _csrf_hash,
            d.search['value'] = search_val,
            d.query_builder = query_builder,
            d.status = param_status
        }
    },
    searching: true,
    columns: [
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "reference_no"},
        { data: "company", width: "10%"},
        { data: "display_name", // data: firstname
            render: function (data, type, row, meta) {
                // return displayName(row.display_name)
                var html = ``;

                if(data){
                    html += `<b>${data}</b>`;
                    html += `<p class="m-0">${ row.position }</p>`;
                }

                return html;
            }
        },
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
        url: baseUrl("eforms/borrowing/export_event_log/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportCSV").on("click", function(e) {
    e.preventDefault();
    tblBorrowing.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/borrowing/export_event_log/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

$("#ExportPDF").on("click", function(e) {
    e.preventDefault();
    tblBorrowing.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/borrowing/export_event_log/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
});

function displayName($displayName){
    return $displayName;
}

function renderStatusHtml(data){
    switch(data){
        case "Pending":
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Pending</strong></div>';
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
    url: baseUrl('eforms/borrowing/overdue_count/'),
    type: "POST",
    dataType: "JSON",
    data:  { csrf_token: _csrf_hash },
    success: function(data){
        $('#overdue').append(data);
    }, error: function (jqXHR, textStatus, errorThrown){
        alert('Error: "count"');
    }
});

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            // {id: 'a.id', label: 'ID #', type: 'integer'},
            {
                id: 'a.status', 
                label: 'Status', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                          id: "Pending",
                          text: "Pending"
                        },{
                            id: "Approved",
                            text: "Approved"
                        },{
                            id: "Disapproved",
                            text: "Disapproved"
                        },{
                            id: "HR Noted",
                            text: "HR Noted"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'a.reference_no', label: 'Reference #', type: 'string'},
            // {id: 'company', label: 'File Under', type: 'string'},
            {id: 'comp.code', label: 'File Under', type: 'string'},
            {id: 'b.firstname', label: 'Firstname', type: 'string'},
            {id: 'b.middlename', label: 'Middlename', type: 'string'},
            {id: 'b.lastname', label: 'Lastname', type: 'string'},
            {id: 'b.suffix', label: 'Suffix', type: 'string'},
            {id: 'c.asset_name', label: 'Item', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {
                id: 'a.date_trans',
                label: 'Transaction Date',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            }
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function() {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblBorrowing.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblBorrowing.ajax.reload();
}

function save_telegram_config(){
    var chat_id = $("#chat_id").val();
    var telegram_bot_token = $("#telegram_bot_token").val();
    var module = $("#module").val();
    var id = $("#config_id").val();
    $.ajax({
        url : baseUrl("eforms/borrowing/add_telegram_config"),
        type: "POST",
        dataType: "JSON",
        data:  { 
            csrf_token : _csrf_hash, 
            telegram_bot_token : telegram_bot_token,
            chat_id : chat_id,
            module : module,
            id : id
        },
        success: function(data){
            toastr.success(data.toastr_msg, "Telegram Configuration Updated!", 5000);
            $("#modal_telegram_config_loa").modal("hide");
        }
    });
};

function load_telegram_config(){
    var module = "borrowing";
    $.ajax({
        url : baseUrl("eforms/loa/load_telegram_config"),
        type: "POST",
        dataType: "JSON",
        data:  { 
            csrf_token : _csrf_hash,
            module : module
        },
        success: function(data){
            $("#config_id").val(data.id);
            $("#telegram_bot_token").val(data.telegram_bot_token);
            $("#chat_id").val(data.chat_id);
        }
    })
}
