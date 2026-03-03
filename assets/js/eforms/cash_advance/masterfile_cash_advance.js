var search_val = "";
var query_builder = "";
var param_status = "";
var blacklistedSearch = "";
let emp = [];

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

var tblCashAdvance = $("#table-cash-advance").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/cash_advance/get_datatable_request/"),
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
    searching: false,
    columns: [
        { data: "status", render: function (data) { return renderStatusHtml(data) } },
        { data: "reference_no" },
        { data: "firstname", render: function (data, type, row, meta) { return displayName(row.display_name, row.position) } },
        { data: "company" },
        { data: "amt_applied" },
        { data: "purpose" },
        { data: "amt_approved" },
        { data: "created_dt", render: function (data) { return formatCalendarDate(data) } },
        { data: "approved_dt", render: function (data) { return formatCalendarDate(data) } },
        { data: null, width: "8%", className: "text-center" },
    ],
    columnDefs: [
        { targets: [3, 6], className: "columnAlign" },
        { targets: [0], className: "statusAlign" },
        { targets: [2], width: "15%" },
        { targets: [1], width: "10%" },
        { targets: [7, 8], width: "5%" },
        { targets: [5], width: "25%" },
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
        case "For Posting":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>For Posting</strong></div>';
            break;
        case "Posted":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>Posted</strong></div>';
            break;
        case "For Final Approval":
            return '<div class="m-badge m-badge--accent m-badge--wide" role="alert"><strong>For Final Approval</strong></div>';
            break;
        case 'Released': 
            return '<div class="m-badge m-badge--focus m-badge--wide" role="alert"><strong>Released</strong></div>';
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
            _actionButton += "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' href='" + baseUrl('eforms/cash_advance/view_cash_advance?id=') + $id + "' target='__blank'><i class='la la-pencil-square'></i></a>";
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
                        }, {
                            id: "For Posting",
                            text: "For Posting"
                        }, {
                            id: "Posted",
                            text: "Posted"
                        }, {
                            id: "For Final Approval",
                            text: "For Final Approval"
                        }, {
                            id: 'Released',
                            text: 'Released'
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

$('#blacklistSearch').donetyping(function (callback) {
    blacklistedSearch = $(this).val();
    tblBlacklisted.ajax.reload();
});

var tblBlacklisted = $("#blacklisted-employee-table").DataTable({
    dom: '<"toolbar">rtlip',
    searchable: false,
    serverSide: true,
    processing: true,
    ajax: {
        url: siteUrl("eforms/cash_advance/get_blacklisted_employee"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = blacklistedSearch;
            return d;
        },
    },
    order: [[0, 'desc']],
    columns: [
        { data: "added_dt", visible: false },
        { data: "employee_name", width: "25%" },
        { data: "remarks", width: "45%", orderable: false },
        { 
            data: "added_by", width: "25%",
            render: function(data, type, row, meta){
                var html = '';
                html += data;
                html += '<p style="margin: 0"><small><b>Added Date: </b>' + moment(row.added_dt).format('LLL') + '</small></p>';
                return html;
            }
        },
        {
            data: null,
            width: "5%",
            className: "text-center",
            orderable: false,
            render: function(data, type, row, meta){
                var html = "";

                html += `<a href="javascript:void(0);" class="btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveTemp" onClick="removeBLacklist(`+row.id+`, '`+row.employee_name+`')"><i class="fa fa-trash"></i></a>`;

                return html;
            }
        },
    ]
});

$("#addBlacklist").on('click', function(){
    
    var html = '';
    html += '<div class="form-group" style="text-align: left !important">' +
            '<label>Employee</label>' +
            '<select id="blacklist-emps" class="form-control select2-hidden-accessible" name="test[]" multiple><option>Select an Option</option></select>' +
            '</div>';

    html += '<div class="form-group" style="text-align: left !important">' +
            '<label>Remarks</label>' +
            '<textarea id="blacklist-remarks" class="form-control" rows="5"></textarea>' +
            '</div>';

    Swal.fire({
        title: 'Blacklist Employee',
        html: html,
        icon: 'question',
        focusConfirm: false,
        showCancelButton: true,
        cancelButtonText: "Cancel",
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        showConfirmButton: true,
        target: "#modal_blacklisted_employee",
        allowOutsideClick: false,
        confirmButtonText: "Confirm",
        didOpen: () => {
            $("#blacklist-emps").select2({
                placeholder: 'Select',
                width: '100%',
                dropdownParent: $(".swal2-modal"),
                tokenSeparators: [',', ' '],
                allowClear: true,
                ajax: {
                    url: baseUrl("eforms/cash_advance/get_for_blacklist_employee"),
                    dataType: "json",
                    delay: 250,
                    global: false,
                    processResults: function (data) {
                        return data;
                    }
                }
            }).on("select2:select", function (e) {
                var data = e.params.data;

                console.log(data);
                let newData = [];
                const tempData = $(this).select2("data");
                tempData.forEach((value) => { newData.push({ id: value.id }); });

                emp = newData;
            }).on("select2:clear", function(e){
                emp = [];
            }).on('select2:unselect', function(e){
                let newData = [];
                const tempData = $(this).select2("data");
                tempData.forEach((value) => { newData.push({ id: value.id }); });

                emp = newData;
            });
        },
        preConfirm: (result) => {
            var remark = document.getElementById('blacklist-remarks').value;

            if(!jQuery.isEmptyObject(emp) && remark){
                $.ajax({
                    url: baseUrl('eforms/cash_advance/blacklist_employee'),
                    data: {
                        csrf_token: _csrf_hash,
                        emp: emp,
                        remark: remark
                    },
                    dataType: "json",
                    type: "POST",
                    success: function(response){
                        if(response.state){
                            toastr.success('Blacklist Employee', response.message, 5000);
                            Swal.close();
                            tblBlacklisted.ajax.reload();
                        }else{
                            toastr.error('Blacklist Employee', response.message, 5000);
                        }
                    }
                });
            }else{
                toastr.error('Blacklist Employee', 'Input Fields is empty!', 5000);
            }
            return false;
        }
    });
});

function removeBLacklist(id, emp){
    var html = "";

    html += '<p>Are you sure to remove <b>`' + emp.toUpperCase() + '`</b> from Cash Advance Blacklisted lists?</p>';

    html += '<div class="form-group" style="text-align: left !important">' +
            '<label>Remarks</label>' +
            '<textarea id="blacklist-remove-remarks" class="form-control" rows="5"></textarea>' +
            '</div>';

    Swal.fire({
        title: "Remove Blacklisted Employee",
        html: html,
        icon: 'question',
        focusConfirm: false,
        showCancelButton: true,
        cancelButtonText: "Cancel",
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        showConfirmButton: true,
        target: "#modal_blacklisted_employee",
        allowOutsideClick: false,
        confirmButtonText: "Confirm",
        preConfirm: (result) => {
            var remark = document.getElementById('blacklist-remove-remarks').value;

            if(remark){
                $.ajax({
                    url: baseUrl('eforms/cash_advance/remove_blacklist_employee'),
                    data: {
                        csrf_token: _csrf_hash,
                        id: id,
                        remark: remark
                    },
                    dataType: "json",
                    type: "POST",
                    success: function(response){
                        if(response.state){
                            toastr.success('Remove Blacklisted Employee', response.message, 5000);
                            Swal.close();
                            tblBlacklisted.ajax.reload();
                        }else{
                            toastr.error('Remove Blacklisted Employee', response.message, 5000);
                        }
                    }
                });
            }else{
                toastr.error('Remove Blacklisted Employee', 'Input Field is empty!', 5000);
            }

            return false;
        }
    })
}
