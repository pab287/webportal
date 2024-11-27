var search_val = "";
var advanced_search = {};
var query_builder = "";
var param_status = "";
var param_company = "";
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
if(typeof getUrlParameter('company') !== 'undefined'){
  param_company = getUrlParameter('company');
}

var tblAccountability = $("#table-accountability").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    //order: [ 1, "desc" ],
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/accountability/masterfile_list/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.advanced_search = advanced_search;
            d.query_builder = query_builder;
            d.status = param_status;
            d.company = param_company;
        }
    },
    searching: false,
    columns: [
        {
            data: "status", width: "14%", className: "text-center", render: function (data) {
                return renderStatusHtml(data)
            }
        },
        { data: "reference_no" },
        { data: "company" },
        {
            data: "firstname", render: function (data, type, row, meta) {
                return empName(row.display_name, row.contractor, row.is_contract)
            }
        },
        {
            data: "asset_name", render: function (data, type, row, meta) {
                return itemName(row.vehicle_name, row.asset_name, row.type)
            }
        },
        {
            data: "date_issued", render: function (data) {
                return formatCalendarDate(data)
            }
        },
        { data: null, width: "5%", className: "text-center" },
    ],
    columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
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
    ],
    createdRow: function (row, data, dataIndex) {
        var isUrgent = parseInt(data.is_urgent);
        var currentRow = $(row);

        if (isUrgent == 1) { currentRow.addClass("is_urgent"); }
    }
});

$("#ExportExcel").on("click", function (e) {
    e.preventDefault();
    tblAccountability.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log/1"),
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

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    tblAccountability.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log/2"),
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

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    tblAccountability.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/accountability/export_event_log/3"),
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

function empName(employee, contractor, isContract) {
    if (isContract == 0) {
        return employee;
    } else {
        return contractor;
    }
}

function renderStatusHtml(data) {
    switch (data) {
        case "Pending Accounting Notes":
            return '<div class="m-badge m-badge--wide alert alert-warning" role="alert"><strong>PENDING ACCOUNTING NOTES</strong></div>';
            break;
        case "Pending Payroll Notes":
            return '<div class="m-badge m-badge--wide alert alert-primary" role="alert"><strong>PENDING HR NOTES</strong></div>';
            break;
        case "Pending HR Notes":
                return '<div class="m-badge m-badge--wide alert alert-primary" role="alert"><strong>PENDING HR NOTES</strong></div>';
                break;    
        case "For Releasing":
            return '<div class="m-badge m-badge--wide alert alert-brand" role="alert"><strong>FOR RELEASING</strong></div>';
            break;
        case "Released":
            return '<div class="m-badge m-badge--wide alert alert-success" role="alert"><strong>RELEASED</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--wide alert alert-secondary" role="alert"><strong>CANCELLED</strong></div>';
            break;
    }
}

function itemName($vehicle, $asset, $type) {
    if ($type == 'Asset') {
        if ($asset) {
            return $asset;
        } else {
            return "No asset name";
        }
    } else if ($type == 'Vehicle') {
        if ($vehicle) {
            return $vehicle;
        } else {
            return "No asset name";
        }
    } else {
        return "No type";
    }
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    } else {
        return moment(data).format("MMM DD, YYYY");
    }
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<a href='javascript:void(0);' onclick='redirectTo(" + $id + ")' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></a>";
        return _actionButton;
    } else {
        return false;
    }
}

function reloadCurrentTable() {
    tblAccountability.ajax.reload(null, false);
}

function redirectTo(id) {
    if (id) {
        window.open(baseUrl('eforms/accountability/view_accountability?id=') + id, "_blank");
    } else {
        return false;
    }
}
//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblAccountability.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblAccountability.ajax.reload();
});

$("#issued_to").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: 'Select. .',
    width: '100%',
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
$("#status").select2({ placeholder: "SELECT STATUS", width: "100%", dropdownParent: $("#modal-advance-search") });
$("#company").select2({ placeholder: "SELECT COMPANY", width: "100%", dropdownParent: $("#modal-advance-search") });
/* END ADVANCE SEARCH MODAL*/

$('#advanced_search').on("click", function (callback) {
    advanced_search['status'] = $("#status").val();
    advanced_search['reference_no'] = $("#reference_no").val();
    advanced_search['company'] = $("#company").val();
    advanced_search['issued_to'] = $("#issued_to").val();
    advanced_search['description'] = $("#item").val();
    tblAccountability.ajax.reload();
    $("#modal-advance-search").modal("hide");
});


$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'a.id', label: 'ID #', type: 'integer' },
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
                        }, {
                            id: "Pending Payroll Notes",
                            text: "Pending Payroll Notes"
                        }, {
                            id: "For Releasing",
                            text: "For Releasing"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'reference_no', label: 'Reference #', type: 'string' },
            { id: 'a.company', label: 'File Under', type: 'string' },
            { id: 'firstname', label: 'Firstname', type: 'string' },
            { id: 'middlename', label: 'Middlename', type: 'string' },
            { id: 'lastname', label: 'Lastname', type: 'string' },
            { id: 'suffix', label: 'Suffix', type: 'string' },
            { id: 'asset_code', label: 'Asset Code', type: 'string', operators: ['contains', 'not_contains', 'begins_with', 'not_begins_with', 'is_empty', 'is_not_empty'] },
            { id: 'asset.name', label: 'Item', type: 'string', operators: ['contains', 'not_contains', 'begins_with', 'not_begins_with', 'is_empty', 'is_not_empty'] },
            {
                id: 'date_issued',
                label: 'Date Issued',
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
        tblAccountability.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblAccountability.ajax.reload();
}


