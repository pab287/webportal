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

param_id = getUrlParameter('id');
var search_val = "";
var query_builder = "";
var tbl = $("#table-tickets").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ts/ticketing/get_masterfile/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            d.query_builder = query_builder;
        }
    },
    searching: false,
    order: [[4, 'desc']],
    columns: [
        {data: "id"},
        {data: "type"},
        {data: "issue"},
        {
            data: "status",
            width: "100px",
            className: "text-center", render: function (data) {
                return renderStatusHtml(data)
            }
        },
        {data: "need_dt", width: "110px"},
        {data: "requested_by"},
        {data: "performed_by_name", defaultContent: "<i>Not set</i>", width: "15%"},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            targets: [5], width: "15%",
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status);
            },
        }
    ], buttons: [
        {
            extend: 'csv',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    } else {
        return moment(data).format("MM/DD/YYYY");
    }

}

function itemDatatableActions($id, $status) {
    var _actionButton = "";
    if ($status == "Open") {
        _actionButton += " <a style='text-decoration: none;' " +
            "   href='" + baseUrl('ts/ticketing/edit_ticket?id=') + $id + "' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</a>";
        _actionButton += " <a style='text-decoration: none;' " +
            "   href='" + baseUrl('ts/ticketing/service?id=') + $id + "' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Service Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-wrench'></i>" +
            "</a>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   onclick='deleteR(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-trash'></i>" +
            "</button>"
        return _actionButton;
    } else if ($status == "Inprogress") {
        _actionButton += " " +
            "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   href='" + baseUrl('ts/ticketing/edit_ticket?id=') + $id + "'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</a>";
        _actionButton += " " +
            "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
            "   href='" + baseUrl('ts/ticketing/service?id=') + $id + "'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Service Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-wrench'></i>" +
            "</a>";
        _actionButton += " " +
            "<button" +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Ticket'" +
            "   data-skin='dark'" +
            "   onclick='deleteR(" + $id + ")'><i class='la la-trash'></i>" +
            "</button>"
        return _actionButton;
    } else if ($status == "Closed" || $status == "Onhold" || $status == "Completed") {
        _actionButton += " " +
            "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   href='" + baseUrl('ts/ticketing/confirm?id=') + $id + "'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Confirm Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-check'></i>" +
            "</a>";
        _actionButton += " " +
            "<a href='" + baseUrl('ts/ticketing/service?id=') + $id + "'" +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Service Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-wrench'></i>" +
            "</a>";
        _actionButton += " <button " +
            "   type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Ticket'" +
            "   data-skin='dark'" +
            "   onclick='deleteR(" + $id + ")'>" +
            "   <i class='la la-trash'></i>" +
            "</button>"
        return _actionButton;
    } else if ($status == "Confirmed") {
        _actionButton += " " +
            "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   href='" + baseUrl('ts/ticketing/view_ticket?id=') + $id + "'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='View Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='fa fa-eye'></i>" +
            "</a>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Ticket'" +
            "   data-skin='dark'" +
            "   onclick='deleteR(" + $id + ")'>" +
            "   <i class='la la-trash'></i>" +
            "</button>"
        return _actionButton;
    } else {
        _actionButton += " <button " +
            "   type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Delete Ticket'" +
            "   data-skin='dark'" +
            "   onclick='deleteR(" + $id + ")'>" +
            "   <i class='la la-trash'></i>" +
            "</button>"
        return _actionButton;
    }
}

function renderStatusHtml(data) {
    switch (data) {
        case "Inprogress":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Inprogress</strong></div>';
            break;
        case "Confirmed":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Resolved</strong></div>';
            break;
        case "Closed":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Completed</strong></div>';
            break;
        case "Open":
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Open</strong></div>';
            break;
        case "Onhold":
            return '<div class="m-badge m-badge--warning m-badge--wide" role="alert"><strong>Onhold</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tbl.ajax.reload();
    console.log(search_val);
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tbl.ajax.reload();
});

function deleteR($id) {
    $("#frm-remove-ticket").attr("action", baseUrl("ts/ticketing/delete_ticket/" + $id));
    $("#remove-ticket-confirmation-modal").modal("show");
}

function processRemoveTicket(formElement) {
    const form = $(formElement);
    const url = form.attr("action");
    $.ajax({
        url,
        type: 'GET',
        dataType: "json",
        success: function (response) {
            if (response) {
                $("#remove-ticket-confirmation-modal").modal("hide");
                toastr.success("Ticket was removed successfully.", "Ticket Removed.", 5000);
                tbl.ajax.reload();
            } else {
                toastr.error("An error occurred while removing ticket.", "Error", 5000);
            }
        },
    });
}

$("#delete_modal").hide();

$(document).ready(function () {
    $("form").attr('autocomplete','off');
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'dept.description', label: 'Department', type: 'string', operators: ['contains', 'equal', 'not_equal'] },
            {
                id: 'a.need_dt',
                label: 'Date Needed',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy/mm/dd'},
                operators: ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            },
            {
                id: 'a.created_dt',
                label: 'Date Requested',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd'},
                operators: ['equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }, 
            {
                id: 'a.status',
                label: 'Status',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    data: [
                        {
                            id: "Open",
                            text: "Open"
                        }, {
                            id: "Cancelled",
                            text: "Cancelled"
                        }, {
                            id: "Closed",
                            text: "Completed"
                        }, {
                            id: "Confirmed",
                            text: "Resolved"
                        }, {
                            id: "Inprogress",
                            text: "Inprogress"
                        }, {
                            id: "Onhold",
                            text: "ON HOLD"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'a.type',
                label: 'Type',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    data: [
                        {
                            id: "HARDWARE",
                            text: "HARDWARE"
                        }, {
                            id: "SOFTWARE",
                            text: "SOFTWARE"
                        }, {
                            id: "OUTLOOK",
                            text: "OUTLOOK"
                        }, {
                            id: "WEBPORTAL",
                            text: "WEBPORTAL"
                        }, {
                            id: "WEBSITE",
                            text: "WEBSITE"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'a.module',
                label: 'Module',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    data: [
                        {
                            id: "FORMS",
                            text: "FORMS"
                        }, {
                            id: "ASSETS",
                            text: "ASSETS"
                        }, {
                            id: "ASSET MANAGEMENT SYSTEM (AMS)",
                            text: "AMS"
                        }, {
                            id: "HUMAN RESOURCE INFORMATION SYSTEM (HRIS)",
                            text: "HRIS"
                        }, {
                            id: "LEAVE OF ABSENCE (LOA)",
                            text: "LOA"
                        }, {
                            id: "WEBSITE",
                            text: "WEBSITE"
                        }, {
                            id: "MATERIAL REQUISITION SYSTEM (MRS)",
                            text: "MRS"
                        }, {
                            id: "TRANSMITTAL REPORT (TR)",
                            text: "TRANSMITTAL"
                        }, {
                            id: "BORROWING FORM (BF)",
                            text: "BORROWING"
                        }, {
                            id: "ACCOUNTABILITY FORM (AF)",
                            text: "ACCOUNTABILITY"
                        }, {
                            id: "TICKETING SYSTEM (TS)",
                            text: "TICKETING"
                        }, {
                            id: "BORROWING FORM (BF)",
                            text: "BORROWING"
                        }, {
                            id: "TRAINING AND ORIENTATION SYSTEM (TOS)",
                            text: "TRAINING AND ORIENTATION SYSTEM"
                        }, {
                            id: "DATA ARCHIVING SYSTEM (DAS)",
                            text: "DATA ARCHIVING"
                        }, {
                            id: "COMPANY RECRUITMENT SYSTEM (CRS)",
                            text: "CRS"
                        }, {
                            id: "CORPORATE SMS GATEWAY SERVICE (SMS)",
                            text: "SMS"
                        }, {
                            id: "SHIPPING ADVICE (SA)",
                            text: "SHIPPING"
                        }, {
                            id: "CASH ADVANCE (CA)",
                            text: "CA"
                        }, {
                            id: "TRAVEL ORDER (TO)",
                            text: "TO"
                        }, {
                            id: "PAYROLL SYSTEM (PS)",
                            text: "PAYROLL"
                        }
                        
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'issue', label: 'Issue', type: 'string', operators: ['contains','equal', 'not_equal'] },
            { id: 'display_name', label: 'Requested By', type: 'string', operators: ['contains','equal', 'not_equal'] },
            { id: 'performed_by_name', label: 'Performed By', type: 'string', operators: ['contains','equal', 'not_equal'] },
        ]
    });
    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');
    console.log(result);
    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tbl.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tbl.ajax.reload();
}

$("#ExportExcel").on("click", function (e) {
    e.preventDefault();
    tbl.button('.buttons-excel').trigger();
});

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    tbl.button('.buttons-csv').trigger();
});

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    tbl.button('.buttons-pdf').trigger();
});