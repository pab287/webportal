var search_val = "";
var query_builder = "";
var tblOvertime = $("#table-overtime").DataTable({
    dom: '<"toolbar">frtlip',
	serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
		url: baseUrl("eforms/overtime/overtime_archive/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
			d.csrf_token = _csrf_hash,
            d.search['value'] = search_val,
            d.query_builder = query_builder
		}
    },
    searching: false,
    columns: [
        { data: "status", render: function (data) {return statusBg(data)}},
        { data: "reference_no"},
        { data: "firstname", render: function ( data, type, row, meta ) { return row.display_employee; }},
        { data: "company",  render: function ( data, type, row, meta ) { return row.display_details; }},
        { data: "purpose"},
        { data: "date_from", render: function (data) {return formatTime(data)}},
        { data: "date_to", render: function (data) {return formatTime(data)}},
        { data: null, width: "8%", className: "text-center"},
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
    tblOvertime.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log_archive/1"),
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
    tblOvertime.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log_archive/2"),
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
    tblOvertime.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/overtime/export_event_log_archive/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

function statusBg(status){
    switch(status){
        case "Pending":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
        break;
        case "Approved":
            return '<div class="m-badge m-badge--success text-white m-badge--wide" role="alert"><strong>Approved</strong></div>';
        break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger text-white m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
        break;
        default:
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
        break;
    }
}

function formatTime(time){
    return (time=="0000-00-00 00:00:00") ? "" : moment(time).format("LLL");
}

function itemDatatableActions($id){
	if($id){
		var _actionButton ="";
        _actionButton += "<a href='view_overtime?id="+$id+"&page=archive' target='__blank'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' data-toggle='m-tooltip' data-original-title='View Details' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-pencil-square'></i></button>";					
		return _actionButton;
	}else{ return false; }
}

//custom global search init
$('#generalSearch').donetyping(function(callback) {
	search_val = $(this).val();
    tblOvertime.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click",function(){
	tblOvertime.ajax.reload();
});

/*$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            {id: 'a.id', label: 'ID #', type: 'integer'},
            {id: 'firstname', label: 'Firstname', type: 'string'},
            {id: 'middlename', label: 'Middlename', type: 'string'},
            {id: 'lastname', label: 'Lastname', type: 'string'},
            {id: 'suffix', label: 'Suffix', type: 'string'},
            {id: 'reference_no', label: 'Reference #', type: 'string'},
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
                          id: "HR Recommendation Pending",
                          text: "Sup Recommendation"
                        },{
                            id: "HR Balance Pending",
                            text: "HR Balance Pending"
                        },{
                            id: "Accounting Balance",
                            text: "Accounting Balance Pending"
                        },{
                            id: "Awaiting Approval",
                            text: "Awaiting Approval"
                        },{
                            id: "Approved",
                            text: "Approved"
                        },{
                            id: "Disapproved",
                            text: "Disapproved"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'purpose', label: 'Purpose', type: 'string'},
            {
                id: 'created_dt',
                label: 'Date Applied',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },{
                id: 'approved_dt',
                label: 'Date Approved',
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
        tblOvertime.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblOvertime.ajax.reload();
}

*/
