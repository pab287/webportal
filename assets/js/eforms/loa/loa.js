load_telegram_config();

var isExport = false;

$("#selectall").click(function () {
    $('#table-loa tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-loa")
    .on("click", "tbody input[type='checkbox']", function () {
        const allCheckboxes = $("#table-loa tbody input[type='checkbox']").length;
        const checkedCheckboxes = $("#table-loa tbody input[type='checkbox']:checked").length;
        const checked = allCheckboxes <= checkedCheckboxes;
        $('#selectall').prop('checked', checked);
    });

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

var tblLoa = $("#table-loa").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("eforms/Loa/get_datatable_request/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.query_builder = query_builder,
                d.status = param_status
        }
    },
    aaSorting: [],
    searching: true,
    columns: [
        {
            data: "id", orderable: false, render: function (data, type, row, meta) {
                return formatcheck(data, row)
            }
        },
        {
            data: "status", render: function (data) {
                return renderStatusHtml(data)
            }
        },
        { data: "file" },
        {
            data: "firstname", render: function (data, type, row, meta) {
                return displayName(row.display_name)
            }
        },
        { data: "nature" },
        {
            data: "reason", render: function (data) {
                return formatContent(data)
            }
        },
        {
            data: "type", render: function (data) {
                return renderTypeHtml(data)
            }
        },
        {
            data: "type", render: function (data, type, row, meta) {
                return formatDifference(data, row)
            }
        },
        {
            data: "date_from", render: function (data, type, row, meta) {
                return formatCalendarDate(data, row)
            }
        },
        { data: "reference_no" },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        {
            targets: [0], orderable: false,
            width: "2%",
            checkboxes: {
                selectRow: true
            }
        },
        { targets: [5], width: "15%" },
        { targets: [4], width: "15%" },
        { targets: [1], className: "statusAlign" },
        { targets: [3], width: "10%" },
        { targets: [2], width: "10%" },
        { targets: [9], width: "5%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
        },


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
    isExport = true;
    tblLoa.button('.buttons-excel').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log/1"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
    isExport=false;
});

$("#ExportCSV").on("click", function (e) {
    e.preventDefault();
    isExport = true;
    tblLoa.button('.buttons-csv').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log/2"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
    isExport=false;
});

$("#ExportPDF").on("click", function (e) {
    e.preventDefault();
    isExport = true;
    tblLoa.button('.buttons-pdf').trigger();
    $.ajax({
        url: baseUrl("eforms/Loa/export_event_log/3"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
           csrf_token : _csrf_hash,
            search : search_val,
            query_builder : query_builder
        }
    });
    isExport=false;
});


function displayName($displayName) {
    return $displayName;
}


function formatContent(data) {
    if (isExport) {
        return data;
    } else {
        if (data!== null && data.length > 20) {
            return data.substring(0, 20) + "...";
        } else {
            return "--";
        }
    }
}

function formatcheck(data, row) {
    if (data) {
        var isDisabled = (row.status.toLowerCase() == 'approved' || row.status.toLowerCase() == 'hr noted') ? 'disabled' : '';
        var isChecked = (row.status.toLowerCase() == 'approved' || row.status.toLowerCase() == 'hr noted') ? 'checked' : '';
        var _checkButton = "<input type='checkbox' class='call-checkbox' value=" + data + " "+isDisabled+" "+isChecked+">";
        return _checkButton;
    } else {
        return false;
    }
}

function renderStatusHtml(data) {
    switch (data) {
        case "Pending":
            return '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Pending</strong></div>';
            break;
        case "Approved":
            return '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
            break;
        case "Disapproved":
            return '<div class="m-badge m-badge--danger m-badge--wide" role="alert"><strong>Disapproved</strong></div>';
            break;
        case "HR Noted":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>HR Noted</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
            break;
    }
}

function renderTypeHtml(data) {
    switch (data) {
        case "1":
            return 'Undertime';
            break;
        case "2":
            return 'Half Day';
            break;
        case "3":
            return 'Whole Day';
            break;
        default:
            return 'Others';
            break;

    }
}

function formatDifference(data, row) {
    switch (data) {
        case "1":
            
            var datefrom = new Date(row.date_from);
            var dateto = new Date(row.date_to);
            
            // get total seconds between the times
            var delta = Math.abs(dateto - datefrom) / 1000;

            // calculate (and subtract) whole days
            var days = Math.floor(delta / 86400);
            delta -= days * 86400;

            // calculate (and subtract) whole hours
            var hours = Math.floor(delta / 3600) % 24;
            delta -= hours * 3600;

            // calculate (and subtract) whole minutes
            var minutes = Math.floor(delta / 60) % 60;
            delta -= minutes * 60;

            return hours + " HOURS " + minutes + " MINUTES";
            break;
        case "2":
            return '4 hours';
            break;
        case "3":
            return '8 hours';
            break;
        default:
            var datefrom = new Date(row.date_from);
            var dateto = new Date(row.date_to);
            // get total seconds between the times
            var delta = Math.abs(dateto - datefrom) / 1000;

            // calculate (and subtract) whole days
            var days = Math.floor(delta / 86400);
            delta -= days * 86400;

            // calculate (and subtract) whole hours
            var hours = Math.floor(delta / 3600) % 24;
            delta -= hours * 3600;

            // calculate (and subtract) whole minutes
            var minutes = Math.floor(delta / 60) % 60;
            delta -= minutes * 60;

            var display_minutes;
            var display_hours;
            var display_days;
            if(hours > 1){
                display_hours = hours + " HOURS ";
            }else if(hours == 1){
                display_hours = hours + " HOUR ";
            }else{
                display_hours = "";
            }
            if(minutes > 1){
                display_minutes = minutes + " MINUTES";
            }else if(minutes == 1){
                display_minutes = minutes + " MINUTE";
            }else{
                display_minutes = "";
            }
            
            if(days > 1){
                display_days = days + " DAYS ";
            }else{
                display_days = days + " DAY ";
            }
            
            return display_days + display_hours + display_minutes;
    }
}

function formatCalendarDate(data, row) {

    if (data == "0000-00-00 00:00:00") {
        return "";
    } else {
        if (row.date_to == "0000-00-00 00:00:00") {
            return moment(data).format("MM/DD/YYYY");
        } else {
            return moment(data).format("MM/DD/YYYY hh:mm A") + " - " + moment(row.date_to).format("MM/DD/YYYY hh:mm A");
        }
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if ($.inArray("edit", _currentActions) !== -1) {
            _actionButton += "<a href='" + baseUrl('eforms/loa/view_loa?id=') + $id + "' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditItem' target='__blank'><i class='la la-pencil-square'></i></a>";
        }
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblLoa.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblLoa.ajax.reload();
});

$("#choice").select2({
    width: '200px',
    placeholder: 'Select an Option'
}).on("select2:select", function (e) {
    var type = $("#choice option:selected").val();

    var rowcollection = tblLoa.$(".call-checkbox:checked", { "page": "all" });

    rowcollection.each(function (index, elem) {
        var checkbox_value = $(elem).val();
        if (type == 1) {
            $.ajax({
                url: baseUrl("eforms/loa/approve_loa/") + checkbox_value,
                type: "POST",
                dataType: "JSON",
                data: { csrf_token: _csrf_hash, approved_remarks: "" },
                success: function (data) {
                    // window.location.replace(baseUrl("eforms/loa"));
                    tblLoa.ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error: "ajax_approve"');
                }
            });
        }

        if (type == 2) {
            $.ajax({
                url: baseUrl("eforms/loa/disapprove_loa/") + checkbox_value,
                type: "POST",
                dataType: "JSON",
                data: { csrf_token: _csrf_hash, disapproved_remarks: "" },
                success: function (data) {
                    // window.location.replace(baseUrl("eforms/loa"));
                    tblLoa.ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error adding / update data');
                }
            });
        }
    });
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
                            id: "Pending",
                            text: "Pending"
                        }, {
                            id: "Approved",
                            text: "Approved"
                        }, {
                            id: "Disapproved",
                            text: "Disapproved"
                        }, {
                            id: "HR Noted",
                            text: "HR Noted"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'company', label: 'File Under', type: 'string' },
            { id: 'firstname', label: 'Firstname', type: 'string' },
            { id: 'middlename', label: 'Middlename', type: 'string' },
            { id: 'lastname', label: 'Lastname', type: 'string' },
            { id: 'suffix', label: 'Suffix', type: 'string' },
            {
                id: 'nature',
                label: 'Nature',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                            id: "Bereavement Leave",
                            text: "Bereavement Leave"
                        }, {
                            id: "Emergency Leave",
                            text: "Emergency Leave"
                        }, {
                            id: "Leave of Absence",
                            text: "Leave of Absence"
                        }, {
                            id: "Maternity Leave",
                            text: "Maternity Leave"
                        }, {
                            id: "Paternity Leave",
                            text: "Paternity Leave"
                        }, {
                            id: "Sick Leave",
                            text: "Sick Leave"
                        }, {
                            id: "Vacation Leave",
                            text: "Vacation Leave"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            { id: 'reason', label: 'Reason', type: 'string', operators: ['contains', 'not_contains', 'begins_with', 'not_begins_with', 'is_empty', 'is_not_empty'] },
            {
                id: 'type',
                label: 'Type',
                type: 'string',
                input: 'select',
                plugin: 'select2',
                plugin_config: {
                    placeholder: 'Select. .',
                    width: '110%',
                    data: [
                        {
                            id: "1",
                            text: "UNDERTIME"
                        }, {
                            id: "2",
                            text: "HALF DAY"
                        }, {
                            id: "3",
                            text: "WHOLE DAY"
                        }, {
                            id: "4",
                            text: "OTHER"
                        }
                    ]
                },
                operators: ['equal', 'not_equal']
            },
            {
                id: 'date_from',
                label: 'Date From',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }, {
                id: 'date_to',
                label: 'Date To',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            },
            { id: 'reference_no', label: 'Reference #', type: 'string' },
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblLoa.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblLoa.ajax.reload();
}

function save_telegram_config(){
    var chat_id = $("#chat_id").val();
    var telegram_bot_token = $("#telegram_bot_token").val();
    var module = $("#module").val();
    var id = $("#config_id").val();
    $.ajax({
        url : baseUrl("eforms/loa/add_telegram_config"),
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
    var module = "loa";
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



