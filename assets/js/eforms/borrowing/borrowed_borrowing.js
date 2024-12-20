var stateFormatter = function (value, row, index) {

    return { disabled: false }


    return value;
}
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
var tblBorrowing = $("#table-borrowing").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/borrowing/get_borrowed_request/"),
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
        // {textAlign:"center",data: "id", render: function ( data, type, row, meta ) {return formatcheck(data,row)}},
        { data: "id", className: "mycheckbox", render: function (data, type, row, meta) { return formatcheck(data, row) } },
        { data: "reference_no",
            render: function(data, type, row, meta){
                let html = ``;
                html += `<p class="mb-0">${data}</p>`;

                switch(row.status){
                    case "Pending":
                        html += '<div class="m-badge text-white m-badge--warning m-badge--wide" role="alert"><strong>Pending</strong></div>';
                    break;
                    case "Approved":
                        html += '<div class="m-badge m-badge--success m-badge--wide" role="alert"><strong>Approved</strong></div>';
                    break;
                    case "Released":
                        html += '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Released</strong></div>';
                    break;
                    default:
                        html += '<div class="m-badge m-badge--metal text-white m-badge--wide" role="alert"><strong>Cancelled</strong></div>';
                    break;
                }

                return html;
            }
        },
        // { data: "firstname", render: function (data, type, row, meta) { return displayName(row.display_name) } },
        { data: "display_name", 
            render: function (data, type, row, meta) { 
                var html = ``;

                if(data){
                    html += `<b>${ data }</b>`;
                    html += `<p class="m-0">${ row.company }</p>`;
                    html += `<p class="m-0">${ row.department }</p>`;
                    html += `<p class="m-0">${ row.position }</p>`;
                }

                return html;
            } 
        },
        { data: "asset" },
        { data: "date_borrowed", render: function (data) { return formatCalendarDate(data) } },
        { data: "date_due", render: function (data) { return formatCalendarDateDue( data) } },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        { targets: [0], width: "5%", orderable: false },
        { targets: [6], width: "5%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) { return itemDatatableActions(row.id); },
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

$("#ExportExcel").on("click", function() {
    tblBorrowing.button( '.buttons-excel' ).trigger();
});

$("#ExportCSV").on("click", function() {
    tblBorrowing.button( '.buttons-csv' ).trigger();
});

$("#ExportPDF").on("click", function() {
    tblBorrowing.button( '.buttons-pdf' ).trigger();
});

function displayName($displayName) {
    return $displayName;
}


function formatcheck(data, row) {
    if (data) {
        var _checkButton = "<input type='checkbox' class='watchlist-checkbox' value=" + data + ">";


        return _checkButton;
    } else { return false; }
}


function formatCalendarDate(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        return moment(data).format("MM/DD/YYYY");
    }

}
function formatCalendarDateDue(data) {
    if (data == "0000-00-00 00:00:00") {
        return "";
    }
    else {
        var currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        var today = month + "-" + day + "-" + year;
        today = new Date(today);
        var due = new Date(data);

        if (today != due) {
            
            
            return moment(data).format("MM/DD/YYYY").fontcolor("red");
        } else {
            return moment(data).format("MM/DD/YYYY");
        }
        
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        
        if(jQuery.inArray('return', _currentActions) !== -1 || jQuery.inArray('extend', _currentActions) !== -1){
            if(jQuery.inArray('return', _currentActions) !== -1){
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem btnEdit' title='Return' onclick='open_return(" + $id + ")'><i class='la la-mail-reply'></i></button>";
            }
    
            if(jQuery.inArray('extend', _currentActions) !== -1){
                _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditItem btnEdit' title='Extend' onclick='open_extend(" + $id + ")'><i class='la la-history'></i></button>";
            }
        }else{
            _actionButton += ' --- ';
        }

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
    format: 'yyyy/mm/dd hh:ii:ss',
});
$('#new_due').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'yyyy/mm/dd hh:ii:ss',
});
$.ajax({
    url: baseUrl('eforms/borrowing/overdue_count/'),
    type: "POST",
    dataType: "JSON",
    data: { csrf_token: _csrf_hash },
    success: function (data) {

        $('#overdue').append(data);

    }, error: function (jqXHR, textStatus, errorThrown) {
        alert('Error: "count"');
    }
});
function mass_open() {
    document.getElementById('btnSave').style.display = 'none';
    document.getElementById('btnSave2').style.removeProperty('display');
    $('#modal_form_return').modal('show'); // show bootstrap modal
    $('.modal-title').text('Return Item'); // Set Title to Bootstrap modal title
}
function open_return($id) {
    document.getElementById('btnSave').style.removeProperty('display');
    document.getElementById('btnSave2').style.display = 'none';
    $('[name="id_return"]').val($id);
    $('#modal_form_return').modal('show'); // show bootstrap modal
    $('.modal-title').text('Return Item'); // Set Title to Bootstrap modal title
}
function open_extend($id) {
    $('[name="id_extend"]').val($id);
    $('#modal_form_extend').modal('show'); // show bootstrap modal
    $('.modal-title').text('Extend Due'); // Set Title to Bootstrap modal title
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
                        $('#modal_form_return').modal("hide");
                        // $('#form_return input').val("");
                        // $('#form_return textarea').val("");
                        $('#form_return').trigger('reset');
                        toastr.success("Success", "Item returned!", 5000);
                    } else {
                        toastr.error("Failed", "Failed returning item!", 5000);
                    }

                }
            });
            return false;
        },
    });
}
function mass_return_item() {
    let checked = [];

    $.validate({
        form: '#form_return',
        lang: 'en',
        onSuccess: function (form) {
            $("#table-borrowing tbody input[type='checkbox']:checked").each(function () {
                checked.push($(this).val());
            });

            let data = $('#form_return').serialize();
            data += "&checked=" + JSON.stringify(checked);

            $.ajax({
                url: baseUrl("eforms/borrowing/mass_return_item/"),
                type: "POST",
                data: data,
                dataType: "JSON",
                success: function (data) {
                    if (data.status) {

                        tblBorrowing.ajax.reload();
                        $('#modal_form_return').modal("hide");
                        $("#form_return").trigger('reset');
                    } else {
                        alert('Error get data from ajax');
                    }

                }
            });
            return false;
        },
    });
}
function extend() {

    $.validate({
        form: '#form_extend',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("eforms/borrowing/extend/"),
                type: "POST",
                data: $('#form_extend').serialize(),
                dataType: "JSON",

                success: function (data) {
                    if (data.status) {

                        tblBorrowing.ajax.reload();
                        $('#modal_form_extend').modal("hide");
                        // $('#form_extend input').val("");
                        // $('#form_extend textarea').val("");
                        $('#form_return').trigger('reset');
                        toastr.success("Success", "Item returned!", 5000);
                    } else {
                        toastr.error("Failed", "Failed returning item!", 5000);
                    }

                }
            });
            return false;
        },
    });
}

$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': {delay: 100},
        filters: [
            {id: 'a.id', label: 'ID #', type: 'integer'},
            {id: 'c.reference_no', label: 'Reference #', type: 'string'},
            {id: 'firstname', label: 'Firstname', type: 'string'},
            {id: 'middlename', label: 'Middlename', type: 'string'},
            {id: 'lastname', label: 'Lastname', type: 'string'},
            {id: 'suffix', label: 'Suffix', type: 'string'},
            {id: 'asset_name', label: 'Item', type: 'string', operators : ['contains', 'not_contains', 'begins_with','not_begins_with', 'is_empty', 'is_not_empty']},
            {
                id: 'date_borrowed',
                label: 'Date Borrowed',
                type: 'date',
                plugin: 'datepicker',
                plugin_config: {format: 'yyyy-mm-dd'},
                operators: ['contains', 'less', 'less_or_equal', 'greater', 'greater_or_equal','between', 'not_between']
            },
            {
                id: 'date_due',
                label: 'Date Due',
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
        tblBorrowing.ajax.reload();
        $("#modal-query-builder").modal("hide");
    }
});

function clear_query_builder(){
    $('#query-builder').queryBuilder('reset');
    query_builder = null;
    tblBorrowing.ajax.reload();
}

$("#selectall").click(function () {
    $('#table-borrowing tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-borrowing")
    .on("click", "tbody input[type='checkbox']", function () {
        const allCheckboxes = $("#table-borrowing tbody input[type='checkbox']").length;
        const checkedCheckboxes = $("#table-borrowing tbody input[type='checkbox']:checked").length;
        const checked = allCheckboxes <= checkedCheckboxes;
        $('#selectall').prop('checked', checked);
    });
