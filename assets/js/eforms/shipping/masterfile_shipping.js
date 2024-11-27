var param_status = "";
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

if(typeof getUrlParameter('status') !== 'undefined'){
    param_status = getUrlParameter('status');
}

var shipping_entries;

$("#selectall").click(function () {
    var shipping_id = [];
    $('#table-shipping tbody input[type="checkbox"]').prop('checked', this.checked);
    
    $("input:checkbox[name=shipping_id]:checked").each(function(){
        shipping_id.push($(this).val());
    });
    shipping_entries = shipping_id;
    if(shipping_entries.length > 0){
        $("#mass_actions").removeClass("m--hide");
    }else{
        $("#mass_actions").addClass("m--hide");
    }  
});


$("#table-shipping")
    .on("click", "tbody input[type='checkbox']", function () {
        var shipping_id = [];
        const allCheckboxes = $("#table-shipping tbody input[type='checkbox']").length;
        const checkedCheckboxes = $("#table-shipping tbody input[type='checkbox']:checked").length;
        const checked = allCheckboxes <= checkedCheckboxes;
        $('#selectall').prop('checked', checked);
        
        $("input:checkbox[name=shipping_id]:checked").each(function(){
            shipping_id.push($(this).val());
        });
        shipping_entries = shipping_id;
        if(shipping_entries.length > 0){
            $("#mass_actions").removeClass("m--hide");
        }else{
            $("#mass_actions").addClass("m--hide");
        }  
    });
 
  

param_id = getUrlParameter('id');
var search_val = "";
var query_builder = "";
var tblShipping = $("#table-shipping").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    "lengthMenu": [10, 25, 50, 100],
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/shipping/get_datatable_request/"),
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
    searching: false,
    columns: [
        {
            data: "id", orderable: false, render: function (data, type, row, meta) {
                return formatcheck(data, row)
            }
        },
        { data: "status",render: function (data) {return renderStatusHtml(data)}},
        { data: "priority"},
        { data: "reference_no" },
        { data: "file_under"},
        { data: "firstname", render: function (data, type, row, meta){return renderShipTo(row.display_name, row.company_to, row.department_to)}},
        { data: "description"},
        { data: "ship_date", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "8%", className: "text-center"},
    ],
    columnDefs: [
        {
            targets: [0], orderable: false,
            width: "2%",
            checkboxes: {
                selectRow: true
            }
        },
        { targets: [1], className: "statusAlign", width: "5%" },
        { targets: [2], width: "5%" },
        { targets: [3], width: "12%" },
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
        url: baseUrl("eforms/shipping/export_event_log/1"),
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
    tblShipping.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/shipping/export_event_log/2"),
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
    tblShipping.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/shipping/export_event_log/3"),
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

function formatcheck(data, row) {
    if (data) {
        var _checkButton = "<input type='checkbox' name='shipping_id'  value=" + data + ">";


        return _checkButton;
    } else {
        return false;
    }
}

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

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            {id: 'a.id', label: 'ID #', type: 'integer'},
            {
                id: 'a.cat',
                label: 'Type',
                type: 'string',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                          id: "in",
                          text: "Internal"
                        },{
                            id: "ex",
                            text: "External"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },{
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
                            id: "Received",
                            text: "Received"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },{
                id: 'priority', 
                label: 'Priority', 
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                            id: "Normal",
                            text: "Normal"
                        },{
                            id: "Important",
                            text: "Important"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'reference_no', label: 'Advice #', type: 'string'},
            {id: 'c.description', label: 'File Under', type: 'string'},
            {
                id: 'ship_to', 
                label: 'Ship To (Internal)', 
                type: 'integer',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select . . .',
                    width: '110%',
                    dropdownParent: $("#modal-query-builder"),
                    ajax: {
                        url: baseUrl("eforms/shipping/get_requested_by"),
                        delay: 250,
                        dataType: "json",
                        global: false,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'a.ship_to', label: 'Ship To (External)', type: 'string', operators: ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},   
            {id: 'b.description', label: 'Item', type: 'string'},
            {
                id: 'ship_date',
                label: 'Ship Date',
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
        tblShipping.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblShipping.ajax.reload();
}

function approve_all_shipping(action){
    
    $.ajax({
        type: "POST",
        data: { data: shipping_entries, csrf_token : _csrf_hash, action : action },
        //url: url,
        url: baseUrl("eforms/shipping/mass_action_shipping"),
        dataType: "json",
        success: function(data) {
            if(data){
                $("#mass_actions").addClass("m--hide");
                toastr.success(data.message);
                tblShipping.ajax.reload();
                $('#selectall').prop('checked', false);
                shipping_entries = [];
            }
                
               
                
        }
      });
    
}

function save_telegram_config(){
    var chat_id = $("#chat_id").val();
    var telegram_bot_token = $("#telegram_bot_token").val();
    var module = $("#module").val();
    var id = $("#config_id").val();
    $.ajax({
        url : baseUrl("eforms/shipping/add_telegram_config"),
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
            $("#modal_telegram_config_sa").modal("hide");
        }
    });
};

function load_telegram_config(){
    var module = "shipping_advice";
    $.ajax({
        url : baseUrl("eforms/shipping/load_telegram_config"),
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

$(document).ready(function(){
    load_telegram_config();
});





