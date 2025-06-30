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
let search_val = "";
let query_builder = "";

let tbl = $("#table-tickets").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ticket/ticket/ticket_masterfile"),
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
    order: [[0, 'desc']],
    columns: [
        {data: "id", visible: false},
        {data: "reference_no"},
        {data: "category"},
        {data: "sub_category",
            render: function (data, type, row) {
                return row.sub_category ? row.sub_category : 'NOT SET';
        }},
        {data: "priority",
            render: function (data, type, row) {
                if (!row.priority) return "<span class='m-badge m-badge--secondary m-badge--wide text-white'><strong>NOT SET</strong></span>";
                
                let badgeClass = '';
                
                switch(row.priority.toLowerCase()) {
                    case 'low':
                        badgeClass = 'm-badge--info';
                        break;
                    case 'medium':
                        badgeClass = 'm-badge--warning';
                        break;
                    default:
                        badgeClass = 'm-badge--danger';
                        break;
                }
                
                return `<span class='m-badge ${badgeClass} m-badge--wide text-white'><strong>${row.priority}</strong></span>`;
            }
        },
        {data: "status",
            render: function (data, type, row) {
                if (!row.status) return "<span class='m-badge m-badge--metal m-badge--wide text-white'><strong>NOT SET</strong></span>";
                
                let badgeClass = '';
                
                switch(row.status.toLowerCase()) {
                    case 'completed':
                        badgeClass = 'm-badge--success';
                        break;
                    case 'open':
                        badgeClass = 'm-badge--brand';
                        break;
                    case 'cancelled':
                        badgeClass = 'm-badge--danger';
                        break;
                    case 'in progress':
                        badgeClass = 'm-badge--accent';
                        break;
                    default:
                        badgeClass = 'm-badge--metal';
                        break;
                }
                
                return `<span class='m-badge ${badgeClass} m-badge--wide text-white'><strong>${row.status}</strong></span>`;
            }
        },        
        {data: "requested_date",
            render: function (data, type, row) {
                return moment(row.requested_date).format('MMM D, YYYY hh:mm A');
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                if(row.status == 'completed' || row.status.toLowerCase() == 'resolved') {
                    return 'Ticket Completed';
                }
                if(row.status.toLowerCase() == 'cancelled') {
                    return 'Ticket Cancelled';
                }
                if (!row.requested_date) return '---';
                
                const today = new Date();
                const requestDate = new Date(row.requested_date);
                
                // Return empty if invalid date
                if (isNaN(requestDate.getTime())) return '';
                
                // Calculate difference in days
                const diffTime = today - requestDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays <= 0) return 'Not Overdue';
                
                return `${diffDays.toLocaleString()} ${diffDays === 1 ? 'Day' : 'Days'}`;
            }
        },        
        {data: "requestor"},
        {data: "performed_by"},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            targets: [6], width: "15%",
        },
        {
            targets: [7],
            orderable: false
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
            title: "Ticket System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            title: "Ticket System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            title: "Ticket System Report",
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
    let _actionButton = "";
        _actionButton += " <a style='text-decoration: none;' " +
            "   href='" + baseUrl('ticket/ticket/edit_ticket?id=') + $id + "' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Edit Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-pencil-square'></i>" +
            "</a>";
        _actionButton += " <a style='text-decoration: none;' " +
            "   href='" + baseUrl('ticket/ticket/view_ticket?id=') + $id + "' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='View Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-eye'></i>" +
            "</a>";
        _actionButton += " <button " +
            "   type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnArchive' " +
            "   onclick='deleteR(" + $id + ")'" +
            "   data-toggle='m-tooltip' data-placement='bottom' title='' data-delay='{\"show\": 200, \"hide\": 0}'" +
            "   data-original-title='Archive Ticket'" +
            "   data-skin='dark'>" +
            "   <i class='la la-file-archive-o'></i>" +
            "</button>"
        return _actionButton;
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
    $("#frm-remove-ticket").attr("action", baseUrl("ticket/ticket/delete_ticket/" + $id));
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

$("select#category").select2({
    width: "100%",
    placeholder: "Select an option",
    ajax: {
        url: baseUrl("ticket/ticket/get_category_collection/") + 'category',
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$(document).ready(function () {
    $("form").attr('autocomplete','off');
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            // { id: 'dept.description', label: 'Department', type: 'string', operators: ['contains', 'equal', 'not_equal'] },
            {
                id: 'a.requested_date',
                label: 'Date Requested',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
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
                    ajax: {
                        url: baseUrl("ticket/ticket/get_category_collection/") + 'status',
                        dataType: "json",
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'cat.name',
                label: 'Category',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    ajax: {
                        url: baseUrl("ticket/ticket/get_category_collection/") + 'category',
                        dataType: "json",
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'sub.name',
                label: 'Sub Category',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    ajax: {
                        url: baseUrl("ticket/ticket/get_category_collection/") + 'sub-category',
                        dataType: "json",
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'a.reference_no', label: 'Reference No', type: 'string', operators: ['contains','equal', 'not_equal'] },
            { id: 'requestor', field: 'CONCAT(b.firstname, " ",b.lastname) ', label: 'Requested By', type: 'string', operators: ['contains'] },
            { id: 'performed_by', field: 'CONCAT(c.firstname, " ", c.lastname) ', label: 'Performed By', type: 'string', operators: ['contains'] },
            { id: 'Priority', label: 'Priority', type: 'string',
                input: 'select',
                plugin: 'select2',
                operators: ['equal', 'not_equal'],
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '200%',
                    data: [{id: '', text: ''},{id: 'low', text: 'Low'}, {id: 'medium', text: 'Medium'}, {id: 'high', text: 'High'}],
                 }
                },
            { id: 'message', label: 'Issue', type: 'string', operators: ['contains'] },
        ]
    });
    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');
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