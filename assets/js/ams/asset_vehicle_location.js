let search_val = "";
let accountability_search = "";
let advanced_search = {};
let types = "";
const dropdown = $("#btn-export-fixed-assets > i");
let tblAssets;
let tblVehicle;
const loaderTemplate = `<div class="text-center">
                            <h3><i class="fa fa-spinner fa-spin mr-2" style="font-size: 22px;"></i>Loading, Please wait...</h3>
                        </div>`;

/*
initialize location dropdown
$.ajax({
    url: baseUrl("ams/assets/get_location"),
    dataType: "json",
    type: "get",
    success: function (data) {
        var newOption = new Option(data.results[0].text, data.results[0].id, true, true);
        $("#area").append(newOption);   

        if($("#type").val() == "asset"){
            advanced_search['area'] = $("#area").val();
            $("#table-fixed-asset").DataTable().ajax.reload();
        }else{
            advanced_search['area'] = $("#area").val();
            $("#table-vehicles").DataTable().ajax.reload();
        }
    }
});*/
var locationId = 0;
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
locationId = getUrlParameter('locationId') ? getUrlParameter('locationId') : 0;

function assetsTab(evt, tabName) {
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

    if (tabName == 'assets') {
        $("#loader").append(loaderTemplate);
        $(".table-assets-container").addClass("m--hide");
        loadTblAssets();
        $(".column-options-vehicles").addClass("m--hide");
        $(".column-options-assets").removeClass("m--hide");
        $("#type").val("asset");
        types = "Assets";
    } else {
        $("#loader").append(loaderTemplate);
        $(".table-vehicles-container").addClass("m--hide");
        tblVehicles();
        $(".column-options-vehicles").removeClass("m--hide");
        $(".column-options-assets").addClass("m--hide");
        $("#type").val("vehicle");
        types = "Vehicle";
    }
}

loadTblAssets();

function loadTblAssets() {
    tblAssets = $("#table-fixed-asset")
        .DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            autoWidth: false,
            ajax: {
                url: baseUrl("ams/Assetperlocation/asset_collection"),
                type: "post",
                global: false,
                dataType: "json",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                    d.search['value'] = search_val;
                    d.accountability_search = accountability_search;
                    d.advanced_search = advanced_search;
                    d.locationId = locationId;
                }
            },
            lengthMenu: [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
            searching: false,
            columns: [
                {
                    data: "image",
                    orderable: false,
                    width: "5%",
                    render: function (data, type, row) {
                        if (data) {
                            return '<a data-lightbox="roadtrip" data-title="' + row.imageFilename + '" href="' + data + '">' +
                                '       <span class="table-avatar" style="background-image: url(\'' + data + '\'); margin: 0 auto;"></span>' +
                                '   </a>';
                        } else {
                            return '<span class="table-avatar" title="No image available." style="margin: 0 auto;">' +
                                '       <i class="flaticon-open-box"></i>' +
                                '   </span>';
                        }
                    },
                    className: "text-center",
                },
                {
                    data: "assetacode",
                    width: "8%"
                },
                {data: "name"},
                {data: "description", visible: false, width: "30%"},
                {data: "area", width: "15%"},
                {data: "comp_name", visible: false},
                {data: "dep_name", visible: false},
                {data: "asset_category", width: "20%"},
                {data: "station", visible: false},
                {data: "brand", visible: false},
                {data: "serialno", visible: false},
                {data: "modelno", visible: false},
                {data: "stat_name", visible: false},
                {
                    data: "datepurchased",
                    visible: false
                },
                {data: "accounted_to", width: "15%", orderable: false},
                {data: "po_no", visible: false},
                {data: "purchaseprice", width: "8%", className: "text-right"},
                {data: null, width: "7%", className: "text-center actions"},
            ],
            columnDefs: [
                {
                    targets: 'no-sort',
                    orderable: false,
                },
                {
                    data: null,
                    defaultContent: "",
                    targets: -1,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return itemDatatableActions(row.id, row.is_borrowed);
                    },
                },
                {
                    targets: "_all",
                    defaultContent: "",
                }],
            buttons: [
                {
                    extend: 'excel',
                    text: 'EXCEL',
                    title: "Assets - " + moment().format('ll'),
                    customize: function (excel) {
                    },
                    exportOptions: {
                        columns: ':visible:not(:eq(0)):not(.actions)'
                    },
                    action: function (e, dt, node, config) {
                        const self = this;
                        const data = tblAssets.ajax.params();

                        $.ajax({
                            url: baseUrl("ams/Assetperlocation/asset_collection/1"),
                            type: "POST",
                            dataType: "JSON",
                            data,
                            success: function (response) {
                                dt.rows().remove();
                                dt.rows.add(response.data).draw();
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, node, config);

                                dropdown.removeClass();
                                dropdown.addClass("fa fa-external-link");
                            }
                        });
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: "Assets - " + moment().format('ll'),
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    customize: function (doc) {
                        // doc.content[1].table.widths = ['10%', '18%', '18%', '18%', '18%', '18%'];
                        doc.styles.tableHeader.alignment = 'left';
                    },
                    exportOptions: {
                        columns: ':visible:not(:eq(0)):not(.actions)'
                    },
                    action: function (e, dt, node, config) {
                        const self = this;
                        const data = tblAssets.ajax.params();
                        $.ajax({
                            url: baseUrl("ams/Assetperlocation/asset_collection/1"),
                            type: "POST",
                            dataType: "JSON",
                            data,
                            success: function (response) {
                                dt.rows().remove();
                                dt.rows.add(response.data).draw();
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, node, config);

                                dropdown.removeClass();
                                dropdown.addClass("fa fa-external-link");
                            }
                        });
                    },
                }
            ],
            initComplete: function (settings, json) {
                $("#loader").html(``);
                $(".table-assets-container").removeClass("m--hide");
            }
        });

    function itemDatatableActions($id, is_borrowed = 0) {
        if ($id) {
            var _actionButton = "";

            if (_currentActions.includes("edit")) {
                _actionButton += "" +
                    "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
                    "   data-toggle='modal' data-target='#modal-accountability-list'" +
                    "   onclick='accountabilityList(" + $id + ")'" +
                    "   data-toggle='m-tooltip'" +
                    "   title='Accountability History'" +
                    "   data-placement='bottom'" +
                    "   data-delay='{\"show\": 300}'>" +
                    "   <i class='la la-list'></i>" +
                    "</a>";
            }

            return _actionButton;
        } else {
            return false;
        }
    }
}

function tblVehicles() {
    tblVehicle = $("#table-vehicles").DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        order: [[0, "asc"]],
        destroy: true,
        ajax: {
            url: baseUrl("ams/Assetperlocation/vehicle_collection"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.accountability_search = accountability_search;
                d.advanced_search = advanced_search;
            }
        },
        searching: false,
        columns: [
            {
                data: "image",
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    if (data) {
                        return '<a data-lightbox="roadtrip" data-title="' + row.primary_pic + '" href="' + data + '">' +
                            '       <div class="table-avatar" style="background-image: url(\'' + data + '\'); margin: 0 auto;"></div>' +
                            '   </a>';
                    } else {
                        return '<div class="table-avatar" title="No image available." style="margin: auto;">' +
                            '       <i class="la la-truck"></i>' +
                            '   </div>';
                    }
                },
                className: "text-center"
            },
            {
                data: "assetacode",
                width: "8%"
            },
            {data: "name", width: "20%"},
            {data: "description", width: "24%", visible: false},
            {data: "area", width: "15%"},
            {data: "comp_name", visible: false},
            {data: "asset_category", width: "20%"},
            {data: "brand", visible: false},
            {data: "serialno", visible: false},
            {data: "model", visible: false},
            {data: "plateno", visible: false},
            {data: "status", visible: false},
            {data: "datepurchased", visible: false},
            {data: "accounted_to", width: "15%", orderable: false},
            {data: "purchaseprice", width: "10%", className: "text-right"},
            {data: null, width: "8%", className: "text-center actions"},
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.is_borrowed);
            },
        },
            {
                targets: "_all",
                defaultContent: "",
            }],
        buttons: [
            {
                extend: 'excel',
                text: 'EXCEL',
                title: "Vehicles - " + moment().format('ll'),
                customize: function (excel) {
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblVehicle.ajax.params();

                    $.ajax({
                        url: baseUrl("ams/Assetperlocation/vehicle_collection/1"),
                        type: "POST",
                        dataType: "JSON",
                        data,
                        success: function (response) {
                            dt.rows().remove();
                            dt.rows.add(response.data).draw();
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, node, config);

                            dropdown.removeClass();
                            dropdown.addClass("fa fa-external-link");
                        }
                    });
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                title: "Vehicles - " + moment().format('ll'),
                orientation: 'landscape',
                pageSize: 'LEGAL',
                customize: function (doc) {
                    doc.styles.tableHeader.alignment = 'left';
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblVehicle.ajax.params();
                    $.ajax({
                        url: baseUrl("ams/Assetperlocation/vehicle_collection/1"),
                        type: "POST",
                        dataType: "JSON",
                        data,
                        success: function (response) {
                            dt.rows().remove();
                            dt.rows.add(response.data).draw();
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, node, config);

                            dropdown.removeClass();
                            dropdown.addClass("fa fa-external-link");
                        }
                    });
                },
            }
        ],
        initComplete: function (settings, json) {
            $(".table-vehicles-container").removeClass("m--hide");
            $("#loader").html(``);
        }
    });

    function itemDatatableActions($id, is_borrowed = 0) {
        if ($id) {
            var _actionButton = "";
            if (_currentActions.includes("edit")) {
                _actionButton += "" +
                    "<a class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
                    "   data-toggle='modal' data-target='#modal-accountability-list'" +
                    "   onclick='accountabilityList(" + $id + ")'" +
                    "   data-toggle='m-tooltip' title='Accountability History'" +
                    "   data-placement='bottom' data-delay='{\"show\": 300}'>" +
                    "   <i class='la la-list'></i></a>";
            }

            return _actionButton;
        } else {
            return false;
        }
    }
}

$("#emp_name").select2({
    dropdownParent: $("#modal-accountability-search"),
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_employee_name"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#location").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("ams/Assetperlocation/get_location"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#area").select2({
    dropdownParent: $("#modal-location-search"),
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("ams/Assetperlocation/get_location"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#cat").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: 'Select. .',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_category"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    if ($("#type").val() == "asset") {
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $("#table-vehicles").DataTable().ajax.reload();
    }
});

$("#reload_dtTbl").on("click", function () {
    if ($("#type").val() == "asset") {
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $("#table-vehicles").DataTable().ajax.reload();
    }
});

function accountabilitySearch() {
    accountability_search = $("#emp_name").val();
    if ($("#type").val() == "asset") {
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $("#table-vehicles").DataTable().ajax.reload();
    }
    $("#modal-accountability-search").modal("hide");
}

function advancedSearch() {
    if ($("#type").val() == "asset") {
        advanced_search['assetacode'] = $("#asset_code").val();
        advanced_search['name'] = $("#asset_name").val();
        advanced_search['assetname'] = $("#asset_desc").val();
        advanced_search['area'] = $("#location").val();
        advanced_search['description'] = $("#cat").val();
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        advanced_search['assetacode'] = $("#asset_code").val();
        advanced_search['name'] = $("#asset_name").val();
        advanced_search['assetname'] = $("#asset_desc").val();
        advanced_search['area'] = $("#location").val();
        advanced_search['description'] = $("#cat").val();
        $("#table-vehicles").DataTable().ajax.reload();
    }
    $("#modal-advance-search").modal("hide");
}


function locationSearch() {
    if ($("#type").val() == "asset") {
        advanced_search['area'] = $("#area").val();
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        advanced_search['area'] = $("#area").val();
        $("#table-vehicles").DataTable().ajax.reload();
    }
    $("#modal-location-search").modal("hide");
}

function clearSearch() {
    search_val = "";
    $("#generalSearch").val("");
    $("#table-fixed-asset").DataTable().ajax.reload();
    $("#table-vehicles").DataTable().ajax.reload();
}

function clear_acc_search() {
    $('#frm-accountability-search')[0].reset();
    $("#emp_name").val("").trigger('change');

    accountability_search = "";
    if ($("#type").val() == "asset") {
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $("#table-vehicles").DataTable().ajax.reload();
    }
}


function clear_adv_search() {
    if ($("#type").val() == "asset") {
        $('#frm-advance-search')[0].reset();
        $("#location").val("").trigger('change');
        $("#cat").val("").trigger('change');
        $("#station").val("").trigger('change');

        advanced_search['assetacode'] = "";
        advanced_search['name'] = "";
        advanced_search['assetname'] = "";
        advanced_search['area'] = "";
        advanced_search['description'] = "";
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $('#frm-advance-search')[0].reset();
        $("#location").val("").trigger('change');
        $("#cat").val("").trigger('change');
        $("#station").val("").trigger('change');

        advanced_search['assetacode'] = "";
        advanced_search['name'] = "";
        advanced_search['assetname'] = "";
        advanced_search['area'] = "";
        advanced_search['description'] = "";
        $("#table-vehicles").DataTable().ajax.reload();
    }
}

function clear_loc_search() {
    if ($("#type").val() == "asset") {
        $('#frm-location-search')[0].reset();
        $("#area").val("").trigger('change');
        advanced_search['area'] = "";
        $("#table-fixed-asset").DataTable().ajax.reload();
    } else {
        $('#frm-location-search')[0].reset();
        $("#area").val("").trigger('change');
        advanced_search['area'] = "";
        $("#table-vehicles").DataTable().ajax.reload();
    }
}

function accountabilityList(id) {
    if ($("#type").val() == "asset") {
        tbl = $("#table-accountability-list").DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            order: [[0, "desc"]],
            ajax: {
                url: baseUrl("ams/Assetperlocation/get_asset_accountability/" + id),
                type: "post",
                global: false,
                dataType: "json",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                }
            },
            searching: false,
            columns: [
                {
                    data: "date_issued",
                    width: "15%",
                    render: function (data) {
                        return dateFormatter(new Date(data));
                    }
                },
                {
                    data: "reference_no",
                    width: "20%"
                },
                {
                    data: "employee",
                    render: function (data, type, row) {
                        return "" +
                            " <p class='mb-0'>" + data + "</p>" +
                            " <p class='text-muted mb-0'>" + row.company + "</p>";
                    }
                },
                {
                    data: "status",
                    width: "30%",
                    render: function (data, type, row) {
                        if (row.acct_status === "Cancelled") {
                            return "Cancelled";
                        } else {
                            switch (parseInt(data)) {
                                case 1:
                                    return "Returned";
                                    break;
                                case 2:
                                    return "Partially Returned";
                                    break;
                                default:
                                    return "Current";
                                    break;
                            }
                        }
                    }
                }
            ]
        });
    } else {
        tbl = $("#table-accountability-list").DataTable({
            dom: '<"toolbar">frtlip',
            serverSide: true,
            processing: true,
            destroy: true,
            order: [[0, "desc"]],
            ajax: {
                url: baseUrl("ams/Assetperlocation/get_vehicle_accountability/" + id),
                type: "post",
                global: false,
                dataType: "json",
                data: function (d) {
                    d.csrf_token = _csrf_hash;
                }
            },
            searching: false,
            columns: [
                {
                    data: "date_issued",
                    width: "15%",
                    render: function (data) {
                        return dateFormatter(new Date(data));
                    }
                },
                {
                    data: "reference_no",
                    width: "20%"
                },
                {
                    data: "employee",
                    render: function (data, type, row) {
                        return "" +
                            " <p class='mb-0'>" + data + "</p>" +
                            " <p class='text-muted mb-0'>" + row.company + "</p>";
                    }
                },
                {
                    data: "status",
                    width: "30%",
                    render: function (data, type, row) {
                        if (row.acct_status === "Cancelled") {
                            return row.acct_status;
                        }

                        switch (parseInt(data)) {
                            case 1:
                                return "Returned";
                                break;
                            case 2:
                                return "Partially Returned";
                                break;
                            default:
                                return "Current";
                                break;
                        }
                    }
                }
            ]
        });
    }

    function dateFormatter(date) {
        const month_names = ["Jan", "Feb", "Mar",
            "Apr", "May", "Jun",
            "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec"];

        const day = date.getDate();
        const month_index = date.getMonth();
        const year = date.getFullYear();

        return month_names[month_index] + " " + day + ", " + year;
    }
}

function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    if (types == "Vehicle") {
        table = $("#table-vehicles").DataTable();
    } else {
        table = $("#table-fixed-asset").DataTable();
    }

    setTimeout(() => {
        switch (type) {
            case "excel":
                table.button(".buttons-excel").trigger();
                break;
            case "pdf":
                table.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

function showOrHideColumn(index, el, table) {
    const column = eval(table).column(index);
    column.visible($(el)[0].checked);
}

$("#column-options.dropdown-menu")
    .click(function (e) {
        e.stopPropagation();
    });

    $('body, .modal-body')
    .tooltip({
    selector: '[title]',
    skin: "dark",
    delay: {
        show: 300
    }
    });    