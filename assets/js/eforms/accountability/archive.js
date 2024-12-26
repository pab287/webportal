var search_val = "";
var advanced_search = {};
var query_builder = "";
var tblAccountability = $("#table-accountability").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    ajax: {
		url: baseUrl("eforms/accountability/archive_list/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
            d.search['value'] = search_val
            d.advanced_search = advanced_search;
            d.query_builder = query_builder
		}
    },
    order: [[ 1, "desc" ]],
    searching: false,
    columns: [
        { data: "status",  width: "14%", className: "text-center", render: function (data) {return renderStatusHtml(data)}},
        { data: "reference_no"},
        { data: "company"},
        // { data: "firstname", render: function (data, type, row, meta) {return empName(row.display_name, row.contractor, row.is_contract)}},
        // { data: "asset_name", render: function (data, type, row, meta) {return itemName(row.vehicle_name, row.asset_name, row.type)}},
        { data: "firstname",
            render: function(data, type, row, meta){
                return row.display_name && row.display_name !== ' ' ? row.display_name : 'No Employee Name';
            }
        },
        { data: "asset_name", orderable: false,
            render: function(data, type, row, meta){
                var html = ``;

                if(row.type == 'Asset'){
                    html = data ? data : 'No Asset Name';
                }else{
                    html = data ? data : 'No Vehicle Name';
                }

                return html;
            }
        },
        { data: "date_issued", render: function (data) {return formatCalendarDate(data)}},
        { data: null, width: "5%", className: "text-center"},
    ],
    columnDefs: [
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
    tblAccountability.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log_archive/1"),
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
    tblAccountability.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log_archive/2"),
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
    tblAccountability.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log_archive/3"),
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

function empName(employee, contractor, isContract){
    if(isContract==0){
        return employee;
    }else{
        return contractor;
    }
}

function renderStatusHtml(data){
    switch(data){
        case "Pending Accounting Notes":
            return '<div class="m-badge m-badge--wide alert alert-warning" role="alert"><strong>Pending Accounting Notes</strong></div>';
        break;
        case "Pending HR Notes":
            return '<div class="m-badge m-badge--wide alert alert-primary" role="alert"><strong>Pending HR Notes</strong></div>';
        break;
        case "For Releasing":
            return '<div class="m-badge m-badge--wide alert alert-brand" role="alert"><strong>For Releasing</strong></div>';
        break;
        case "Released":
            return '<div class="m-badge m-badge--wide alert alert-success" role="alert"><strong>Released</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--wide alert alert-metal text-white" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

function itemName($vehicle, $asset, $type){
    if($type=='Asset'){
        if($asset){
            return $asset;
        }else{
            return "No asset name";
        }
    }else if($type=='Vehicle'){
        if($vehicle){
            return $vehicle;
        }else{
            return "No asset name";
        }
    }else{
        return "No type";
    }
}

function formatCalendarDate(data){
    if(data=="0000-00-00 00:00:00"){
        return "";
    }
    else{
        return moment(data).format("MMM DD, YYYY");
    }
  
}

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
			_actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='"+baseUrl('eforms/accountability/view_accountability?id=')+$id+"&page=archive' target='__blank'><i class='la la-pencil-square'></i></a>";				
		return _actionButton;
	}else{ return false; }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblAccountability.ajax.reload();
}, 1000, 3);

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblAccountability.ajax.reload();
});

$("#issued_to").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: 'Select. .',
    width: '100%',
    minimumInputLength: 3,
    ajax: {
        url: baseUrl("eforms/accountability/issued_to_lookup"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

/* ADVANCE SEARCH MODAL */
// $("#status").select2({placeholder: "SELECT STATUS", width: "100%", dropdownParent: $("#modal-advance-search")});
$("#company").select2({placeholder: "SELECT COMPANY", width: "100%", dropdownParent: $("#modal-advance-search")});
/* END ADVANCE SEARCH MODAL*/

$('#advanced_search').on("click", function (callback) {
    // advanced_search['status'] = $("#status").val();
    advanced_search['reference_no'] = $("#reference_no").val();
    advanced_search['company'] = $("#company").val();
    advanced_search['issued_to'] = $("#issued_to").val();
    advanced_search['description'] = $("#item").val();
    tblAccountability.ajax.reload();
    $("#modal-advance-search").modal("hide");
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
                          id: "Pending Accounting Notes",
                          text: "Pending Accounting Notes"
                        },{
                            id: "Pending HR Notes",
                            text: "Pending HR Notes"
                        },{
                            id: "For Releasing",
                            text: "For Releasing"
                        },{
                            id: "Cancelled",
                            text: "Cancelled"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'reference_no', label: 'Reference #', type: 'string'},
            {id: 'a.company', label: 'File Under', type: 'string'},
            {id: 'firstname', label: 'Firstname', type: 'string'},
            {id: 'middlename', label: 'Middlename', type: 'string'},
            {id: 'lastname', label: 'Lastname', type: 'string'},
            {id: 'suffix', label: 'Suffix', type: 'string'},
            {id: 'asset_code', label: 'Asset Code', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {id: 'asset.name', label: 'Item', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},   
            {
                id: 'date_issued',
                label: 'Date Issued',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },

        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function() {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblAccountability.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblAccountability.ajax.reload();
}

$("#refresh").on('click', function () {
    $("#frm-advance-search").trigger('reset');
    $("#company").val('').trigger('change');
    $("#issued_to").val('').trigger('change');

    advanced_search = {};

    tblAccountability.ajax.reload();
});
