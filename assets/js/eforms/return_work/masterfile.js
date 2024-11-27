var search_val = "";
var query_builder = "";
var filteredIds = [];
var enabledFilter = false;

var tblTemp = $("#table-rtw").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/return_to_work/get_rtw_datatable_request"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.ids = filteredIds;
            d.filtered = enabledFilter;
        }
    },
    searching: false,
    columns: [
        { data: "status", width: "10%", render: function (data) { return statusBg(data) } },
        { data: "reference_no", width: "10%" },
        { data: "employee_name" },
        { data: "return_type", width: "18%", orderable: false, render: function (data) { return returnType(data) } },
        { data: "reason", width: "18%", orderable: false },
        { data: "created_at", width: "14%", orderable: false, className: "text-center", render: function (data) { return formatTime(data) } },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
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

$("#ExportExcel").on("click", function (e) {
    e.preventDefault();
    tblTemp.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/return_to_work/export_event_log/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    tblTemp.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/return_to_work/export_event_log/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    tblTemp.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/return_to_work/export_event_log/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
        }
    });
});

function statusBg(status) {
    switch (status) {
        case "0":
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
            break;
        case "1":
            return '<div class="m-badge m-badge--success text-white m-badge--wide" role="alert"><strong>Approved</strong></div>';
            break;
        case "2":
            return '<div class="m-badge m-badge--danger text-white m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
            break;
        case "3":
            return '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--warning text-white m-badge--wide" role="alert"><strong>Pending</strong></div>';
            break;
    }
}

function formatTime(time) {
    tempTime = (time == "0000-00-00 00:00:00") ? "" : moment(time).format("LLL")
    return time;
}

function returnType(type = 0) {
    var tempLabel = "Unauthorized Absence";
    if (type == 1) {
        tempLabel = "Recalled";
    } else if (type == 2) {
        tempLabel = "Request to extend days of work";
    }
    return tempLabel;
}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<a href='" + baseUrl("eforms/return_to_work/view_return_to_work" + "/" + $id) + "' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' data-toggle='m-tooltip' data-original-title='View Details' data-placement='bottom' data-delay='{\"show\": 300}'><i class='la la-pencil-square'></i></a>";
        return _actionButton;
    } else { return false; }
}

$(document).on("click", "#addNewRtw", function () {
    window.location.replace(baseUrl("eforms/return_to_work/add_new"));
});

function reloadCurrentTable() {
    tblTemp.ajax.reload(null, false);
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTemp.ajax.reload();
});

$("#frm-advance-search").on("submit", function (e) {
    e.preventDefault();
    var tempUrl = e.target.action;
    var tempType = e.target.method;

    $.ajax({
        url: tempUrl,
        type: tempType,
        dataType: "json",
        data: $(e.target).serialize(),
        success: function (json) {
            if (json.response) {
                filteredIds = json.ids;
                enabledFilter = true;
                tblTemp.ajax.reload(function () {
                    enabledFilter = false;
                    filteredIds = [];
                }, false);

            }
            if (json.has_error) { toastr.error(json.toastr_msg, "Filtered Search", { timeOut: 5000 }); }
        }
    });
});

$("#employee").select2({
    placeholder: 'Select an option',
    width: '100%',
    dropdownParent: $("#modal-advance-search"),
    allowClear: true,
    ajax: {
        url: baseUrl("eforms/return_to_work/get_employee_select2_data"),
        dataType: "json",
        delay: 250,
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$("#status").select2({
    placeholder: 'Select an option',
    width: '100%',
    dropdownParent: $("#modal-advance-search"),
    allowClear: true,
});

$("#date_time").daterangepicker({
    minDate: moment().subtract(2, 'years'),
    locale: {
        format: 'MM/DD/YYYY'
    }
});

$(document).ready(function () {
    $("#date_time").val("");
});