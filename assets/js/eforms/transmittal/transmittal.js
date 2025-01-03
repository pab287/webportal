var search_val = "";
var advanceSearch = false;
var advanceSearchData = {
    priority: null,
    reference_no: null,
    company_from: null,
    delivered_to: null,
    description: null,
    ship_date: null,
    created_by: null,
    created_dt: null,
};
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

const modalAdvanceSearch = $("#modal-advance-search");

var tblTransmittal = $("#table-transmittal")
    .DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        aaSorting: [],
        ajax: {
            url: baseUrl("eforms/transmittal/get_datatable_request/"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.advanceSearch = advanceSearch;
                d.advanceSearchData = advanceSearchData;
                d.query_builder = query_builder;
                d.status = param_status
            }
        },
        searching: false,
        columns: [
            {
                data: "status", render: function (data) {
                    return renderStatusHtml(data)
                }
            },
            {data: "priority"},
            {data: "reference_no"},
            {data: "company_from"},
            {
                data: "firstname", render: function (data, type, row, meta) {
                    return displayName(row.display_name)
                }
            },
            {
                data: "trans_desc", render: function (data) {
                    return formatContent(data)
                }
            },
            {
                data: "trans_desc", visible: false,
            },
            {
                data: "ship_date", render: function (data) {
                    return formatCalendarDate(data)
                }
            },
            {data: "created_by"},
            {
                data: "created_dt", render: function (data) {
                    return formatCalendarDate(data)
                }
            },
            {data: null, width: "8%", className: "text-center"},
        ],
        columnDefs: [
            {targets: [4], width: "15%"},
            {targets: [0], className: "statusAlign"},
            {targets: [2], width: "5%"},
            {targets: [5], width: "15%"},
            {targets: [1], width: "5%"},
            {targets: [7], width: "12%"},
            {targets: [9], width: "5%"},

            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,

                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id);
                },
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
                    // columns: "thead th:not(.notExport)"
                    columns: [2, 3, 4, 6, 7] 
                }
            }
        ]
    });

$("#ExportExcel").on("click", function(e) {
    e.preventDefault();
    tblTransmittal.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log/1"),
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
    tblTransmittal.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log/2"),
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
    tblTransmittal.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/transmittal/export_event_log/3"),
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

function displayName($displayName) {
    return $displayName;
}

function formatContent(data) {
    var shortname = data;
    if (data != null) {
        if (data.length > 50) {
            var shortname = data.substring(0, 50) + " ...";
        }
    } else {
        var shortname = "--";
    }

    return shortname;
}

function renderStatusHtml(data) {
    switch (data) {
        case "Pending":
            return '<div class="m-badge m-badge--warning m-badge--wide" role="alert" style="color: #FFFFFF;"><strong>Pending</strong></div>';
            break;
        case "Approved":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert" style="color: #FFFFFF;"><strong>Approved</strong></div>';
            break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert" style="color: #FFFFFF;"><strong>Disapproved</strong></div>';
            break;
        case "Received":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert" style="color: #FFFFFF;"><strong>Received</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--info m-badge--wide" role="alert" style="color: #FFFFFF;"><strong>Cancelled</strong></div>';
            break;
    }
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    } else {
        return moment(data).format("MM/DD/YYYY");
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='" + baseUrl('eforms/transmittal/view_transmittal?id=') + $id + "' target='__blank'><i class='la la-pencil-square'></i></a>";
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblTransmittal.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblTransmittal.ajax.reload();
});

modalAdvanceSearch
    .on("show.bs.modal", function () {
        $("[name='priority']")
            .select2({
                placeholder: "Select Priority",
                width: "100%",
                dropdownParent: $(this),
                allowClear: true
            });


        $("[name='status']")
            .select2({
                placeholder: "Select Status",
                width: "100%",
                dropdownParent: $(this),
                allowClear: true
            });

        $("[name='company_from']")
            .select2({
                placeholder: "Select File Under",
                width: "100%",
                dropdownParent: $(this),
                allowClear: true,
                ajax: {
                    url: baseUrl("eforms/transmittal/get_company_collection"),
                    dataType: "JSON",
                    delay: 500
                }
            });

        $("[name='created_by']")
            .select2({
                placeholder: "Select Creator",
                width: "100%",
                dropdownParent: $(this),
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: baseUrl("eforms/transmittal/get_transmittal_creators"),
                    dataType: "JSON",
                    delay: 500
                }
            });

        $("#ship_date_container, #created_dt_container")
            .datepicker({
                todayHighlight: true,
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                autoclose: true
            });

        $("#frm-advance-search")
            .on("submit", function (e) {
                e.preventDefault();

                advanceSearchData = {
                    priority: $("[name='priority']").val(),
                    reference_no: $("[name='reference_no']").val(),
                    company_from: $("[name='company_from']").val(),
                    delivered_to: $("[name='delivered_to']").val(),
                    description: $("#advance-search-description").val(),
                    ship_date: $("[name='ship_date']").val(),
                    created_by: $("[name='created_by']").val(),
                    created_dt: $("[name='created_dt']").val(),
                    status: $("[name='status']").val(),
                };

                advanceSearch = true;

                tblTransmittal.ajax.reload();
                modalAdvanceSearch.modal("hide");
            });
    });

function clearAdvanceSearch() {
    const form = $("#frm-advance-search");
    form.resetForm();
    form.find(".s2").val('').trigger('change');
    form.find('select[name="company_from"]').val('').trigger('change')
    advanceSearch = false;
    advanceSearchData = {
        priority: null,
        reference_no: null,
        company_from: null,
        delivered_to: null,
        description: null,
        ship_date: null,
        created_by: null,
        created_dt: null,
    };
    tblTransmittal.ajax.reload();
    modalAdvanceSearch.modal("hide");
}

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            // {id: 'a.id', label: 'ID #', type: 'integer'},
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
            },
            {
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
                            id: "Received",
                            text: "Received"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'reference_no', label: 'Reference #', type: 'string'},
            {id: 'a.company_to', label: 'File Under', type: 'string'},
            {
                id: 'ship_to', 
                label: 'Deliver To (Internal)', 
                type: 'integer',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select . . .',
                    width: '110%',
                    dropdownParent: $("#modal-query-builder"),
                    ajax: {
                        url: baseUrl("eforms/transmittal/get_request_collection"),
                        global: false,
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            {id: 'a.ship_to', label: 'Deliver To (External)', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {id: 'c.description', label: 'Contents', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},   
            {
                id: 'ship_date',
                label: 'Delivery Date',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },
            {
                id: 'created_by', 
                label: 'Created by', 
                type: 'integer',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select . . .',
                    width: '110%',
                    dropdownParent: $("#modal-query-builder"),
                    ajax: {
                        url: baseUrl("eforms/transmittal/get_request_collection"),
                        global: false,
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'created_dt',
                label: 'Date Created',
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
        tblTransmittal.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblTransmittal.ajax.reload();
}


