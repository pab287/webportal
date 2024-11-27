var url_string = window.location.href;
var url = new URL(url_string);
var notif_data = url.searchParams.get("data");
let search_val
if(notif_data != ""){
    search_val = notif_data;
}else{
    search_val = "";
}
var query_builder = "";
// console.log("working");
var tblBorrowing = $("#table-borrowing").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/borrowing/get_overdue_request/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.query_builder = query_builder
        }
    },
    searching: true,
    columns: [
        { data: "firstname", render: function (data, type, row, meta) { return displayName(row.display_name) } },
        { data: "reference_no" },
        { data: "company" },
        { data: "asset_code" },
        { data: "asset_name" },
        { data: "date_borrowed", render: function (data) { return formatCalendarDate(data) } },
        { data: "date_due", render: function (data) { return formatCalendarDateDue(data) } },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        { targets: [0], width: "15%" },
        { targets: [7], width: "5%" },
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
    tblBorrowing.button('.buttons-excel').trigger();
});

$("#ExportCSV").on("click", function () {
    tblBorrowing.button('.buttons-csv').trigger();
});

$("#ExportPDF").on("click", function () {
    tblBorrowing.button('.buttons-pdf').trigger();
});

function displayName($displayName) {
    return $displayName;
}

function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        return moment(data).format("MM/DD/YYYY").fontcolor("red");
    }

}
function formatCalendarDateDue(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        return moment(data).format("MM/DD/YYYY").fontcolor("red");

    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditItem' title='Return' onclick='open_return(" + $id + ")'><i class='la la-mail-reply'></i></button>";
        return _actionButton;
    } else { return false; }
}
//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblBorrowing.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblBorrowing.ajax.reload();
});
$('#date_returned').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    maxView: 4,
    minView: 2,
    format: 'mm/dd/yyyy',
});

function open_return($id) {
    $('[name="id_return"]').val($id);
    $('#modal_form_return').modal('show'); // show bootstrap modal
    $('.modal-title').text('Return Item'); // Set Title to Bootstrap modal title
}

function return_item() {
    $.validate({
        form: '#form_return',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/borrowing/return_item/"),
                type: "POST",
                data: $('#form_return').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {
                        tblBorrowing.ajax.reload();
                        $("#date_returned input").val('');
                        $("#form_return textarea").val('');
                        $('#modal_form_return').modal("hide");
                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });
}


$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            { id: 'a.id', label: 'ID #', type: 'integer' },
            { id: 'c.reference_no', label: 'Reference #', type: 'string' },
            { id: 'firstname', label: 'Firstname', type: 'string' },
            { id: 'middlename', label: 'Middlename', type: 'string' },
            { id: 'lastname', label: 'Lastname', type: 'string' },
            { id: 'suffix', label: 'Suffix', type: 'string' },
            { id: 'company', label: 'Company', type: 'string' },
            { id: 'asset_code', label: 'Asset Code', type: 'string', operators: ['contains', 'not_contains', 'begins_with', 'not_begins_with', 'is_empty', 'is_not_empty'] },
            { id: 'asset_name', label: 'Asset Name', type: 'string', operators: ['contains', 'not_contains', 'begins_with', 'not_begins_with', 'is_empty', 'is_not_empty'] },
            {
                id: 'date_borrowed',
                label: 'Date Borrowed',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            },
            {
                id: 'date_due',
                label: 'Date Due',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: { format: 'yyyy-mm-dd' },
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between']
            }
        ],
    });

    $("#query-builder_group_0").addClass("col-12");
});

$('#query-builder-btn').on('click', function () {
    var result = $('#query-builder').queryBuilder('getSQL');

    if (!$.isEmptyObject(result)) {
        query_builder = result;
        tblBorrowing.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder() {
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblBorrowing.ajax.reload();
}
