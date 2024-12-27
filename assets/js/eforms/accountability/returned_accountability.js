tblReleased();
var query_builder = "";
function tblReleased() {
    var search_val = "";
    var tblReleased = $("#table-released").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        destroy: true,
        aaSorting: [],
        ajax: {
            url: baseUrl("eforms/accountability/released_assets/"),
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
            { data: "status", width: "5%", className: "text-center", render: function () { return renderStatusHtml() } },
            { data: "company" },
            { data: "reference_no" },
            // { data: "firstname", render: function (data, type, row, meta) { return empName(row.display_name, row.contractor, row.is_contract) } },
            { data: "firstname",
                render: function (data, type, row, meta) {
                    return row.display_name && row.display_name !== ' ' ? row.display_name : 'No Employee Name';
                }
            },
            { data: "asset_code" },
            // { data: "asset_name", render: function (data, type, row, meta) { return itemName(row.vehicle_name, row.asset_name, row.type) } },
            { data: "asset_name", orderable: false },
            { data: "amount", className: "text-right" },
            { data: null, width: "5%", className: "text-center" },
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) { return itemDatatableActionsR(row.acc_id); },
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
        tblReleased.button('.buttons-excel').trigger();
    });

    $("#ExportCSV").on("click", function () {
        tblReleased.button('.buttons-csv').trigger();
    });

    $("#ExportPDF").on("click", function () {
        tblReleased.button('.buttons-pdf').trigger();
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        tblReleased.ajax.reload();
    }, 1000, 3);

    $('#generalSearch').on('keyup', function(e) {
        if ($(this).val() == 0) {
            search_val = "";
            tblReleased.ajax.reload();
        }
    });

    $("#reload_dtTbl").on("click", function () {
        tblReleased.ajax.reload();
    });

    $('#query-builder-btn').on('click', function () {
        var result = $('#query-builder').queryBuilder('getSQL');

        if (!$.isEmptyObject(result)) {
            query_builder = result;
            tblReleased.ajax.reload();
            $("#modal-query-builder").modal("hide");
        }
    });

    $('#clear_query_builder').on('click', function () {
        $('#query-builder').queryBuilder('reset');
        query_builder = null;
        tblReleased.ajax.reload();
    });
}

function assetsTab(evt, tabName) {
    var search_val = "";
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tab-pane ");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("nav-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

    if (tabName == 'released') {
        tblReleased();
    } else {
        var tblReturned = $("#table-returned").DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            aaSorting: [],
            ajax: {
                url: baseUrl("eforms/accountability/returned_assets/"),
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
                { data: "reference_no" },
                { data: "company" },
                // { data: "firstname", render: function (data, type, row, meta) { return empName(row.display_name, row.contractor, row.is_contract) } },
                { data: "firstname",
                    render: function (data, type, row, meta) {
                        return row.display_name && row.display_name !== ' ' ? row.display_name : 'No Employee Name';
                    }
                },
                { data: "asset_code" },
                // { data: "asset_name", render: function (data, type, row, meta) { return itemName(row.vehicle_name, row.asset_name, row.type) } },
                { data: "asset_name", orderable: false },
                { data: "amount", className: "text-right" },
                { data: null, width: "5%", className: "text-center" },
            ],
            columnDefs: [
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) { return itemDatatableActions(row.acc_id); },
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

        $("#ExportExcel2").on("click", function () {
            tblReturned.button('.buttons-excel').trigger();
        });

        $("#ExportCSV2").on("click", function () {
            tblReturned.button('.buttons-csv').trigger();
        });

        $("#ExportPDF2").on("click", function () {
            tblReturned.button('.buttons-pdf').trigger();
        });

        $('#generalSearch2').donetyping(function (callback) {
            search_val = $(this).val();
            tblReturned.ajax.reload();
        }, 1000, 3);

        $('#generalSearch2').on('keyup', function(e) {
            if ($(this).val() == 0) {
                search_val = "";
                tblReturned.ajax.reload();
            }
        });

        $("#reload_dtTbl").on("click", function () {
            tblReturned.ajax.reload();
        });

        $('#query-builder-btn').on('click', function () {
            var result = $('#query-builder').queryBuilder('getSQL');

            if (!$.isEmptyObject(result)) {
                query_builder = result;
                tblReturned.ajax.reload();
                $("#modal-query-builder").modal("hide");
            }
        });

        $('#clear_query_builder').on('click', function () {
            $('#query-builder').queryBuilder('reset');
            query_builder = null;
            tblReturned.ajax.reload();
        });
    }
}

function empName(employee, contractor, isContract) {
    if (isContract == 0) {
        return employee;
    } else {
        return contractor;
    }
}

function renderStatusHtml() {
    return '<div class="m-badge m-badge--wide alert alert-warning" role="alert"><strong>Unreturned</strong></div>';
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
    }
    else {
        return moment(data).format("MMM DD, YYYY");
    }

}

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<a href='" + baseUrl('eforms/accountability/view_returned_accountability?id=') + $id + "' target='__blank'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></button></a>";
        return _actionButton;
    } else { return false; }
}

function itemDatatableActionsR($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<a href='" + baseUrl('eforms/accountability/view_released_accountability?id=') + $id + "' target='__blank'><button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-pencil-square'></i></button></a>";
        return _actionButton;
    } else { return false; }
}


$(document).ready(function () {
    $('#query-builder').queryBuilder({
        'bt-tooltip-errors': { delay: 100 },
        filters: [
            // { id: 'a.id', label: 'ID #', type: 'integer' },
            { id: 'a.company', label: 'File Under', type: 'string' },
            { id: 'd.company', label: 'Contractor Company', type: 'string' },
            { id: 'reference_no', label: 'Reference #', type: 'string' },
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



