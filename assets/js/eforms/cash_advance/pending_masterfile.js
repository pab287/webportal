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

var type = getUrlParameter('type');

var search_val = "";
var query_builder = "";
var tblCashAdvance = $("#table-cash-advance").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/cash_advance/get_pending_datatable_request/") + type,
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.query_builder = query_builder
        }
    },
    searching: false,
    columns: [
        { data: "status", render: function (data) { return renderStatusHtml(data) } },
        { data: "reference_no" },
        { data: "firstname", render: function (data, type, row, meta) { return displayName(row.display_name, row.position) } },
        { data: "amt_applied" },
        { data: "purpose" },
        { data: "amt_approved" },
        { data: "created_dt", render: function (data) { return formatCalendarDate(data) } },
        { data: "approved_dt", render: function (data) { return formatCalendarDate(data) } },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        { targets: [3, 5], className: "columnAlign" },
        { targets: [0], className: "statusAlign" },
        { targets: [2], width: "15%" },
        { targets: [1], width: "10%" },
        { targets: [6, 7], width: "5%" },
        { targets: [4], width: "25%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
        }
    ], buttons: [
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

$("#ExportExcel").on("click", function () {
    tblCashAdvance.button('.buttons-excel').trigger();
});

$("#ExportCSV").on("click", function () {
    tblCashAdvance.button('.buttons-csv').trigger();
});

$("#ExportPDF").on("click", function () {
    tblCashAdvance.button('.buttons-pdf').trigger();
});

function displayName($displayName, position) {
    var emp = '';
    emp += '<p class="mb-0">'+$displayName+'</p>';
    emp += '<small>'+position+'</small>';

    return emp;

}

function renderStatusHtml(data) {
    switch (data) {
        case "HR Recommendation Pending":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Sup Recommendation</strong></div>';
            break;
        case "Approved":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
            break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
            break;
        case "HR Balance Pending":
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Payroll Balance Pending</strong></div>';
            break;
        case "Payroll Balance Pending":
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Payroll Balance Pending</strong></div>';
            break;    
        case "Accounting Balance Pending":
            return '<div class="m-badge m-badge--primary m-badge--wide" role="alert"><strong>Accounting Balance</strong></div>';
            break;
        case "Awaiting Approval":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Awaiting Approval</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
    }
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        return moment(data).format("MM/DD/YYYY");
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if ($.inArray("edit", _currentActions) !== -1) {
            if(type == 'pyrll'){
                $ca_type =  "&type=pyrll";
            }else if(type == 'acctg'){
                $ca_type =  "&type=acctg";
            }else{
                $ca_type =  "&type=approval";
            }
            _actionButton += "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='" + baseUrl('eforms/cash_advance/view_cash_advance?id=') + $id + "&notif=true" + $ca_type + "' target='__blank'><i class='la la-pencil-square'></i></a>";
        }
        _actionButton = _actionButton ? _actionButton : "---";
        return _actionButton;
    } else { return false; }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblCashAdvance.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblCashAdvance.ajax.reload();
});

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'a.id', label: 'ID #', type: 'integer' },
            { id: 'firstname', label: 'Firstname', type: 'string' },
            { id: 'middlename', label: 'Middlename', type: 'string' },
            { id: 'lastname', label: 'Lastname', type: 'string' },
            { id: 'suffix', label: 'Suffix', type: 'string' },
            { id: 'reference_no', label: 'Reference #', type: 'string' },
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
                        }, {
                            id: "HR Balance Pending",
                            text: "HR Balance Pending"
                        },{
                            id: "Payroll Balance Pending",
                            text: "Payroll Balance Pending"
                        },{
                            id: "Accounting Balance Pending",
                            text: "Accounting Balance Pending"
                        }, {
                            id: "Awaiting Approval",
                            text: "Awaiting Approval"
                        }, {
                            id: "Approved",
                            text: "Approved"
                        }, {
                            id: "Disapproved",
                            text: "Disapproved"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'purpose', label: 'Purpose', type: 'string' },
            {
                id: 'created_dt',
                label: 'Date Applied',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }, {
                id: 'approved_dt',
                label: 'Date Approved',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            },
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblCashAdvance.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblCashAdvance.ajax.reload();
}