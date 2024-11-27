let search_val = "";
let advanced_search = {};
let vehiclesComponents = [];
const dropdown = $("#btn-export-vehicle-components > i");

const tblVehicleComponentsMasterfile = $("#table-vehicle-components-master-file")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        order: [[0, "asc"]],
        autoWidth: false,
        ajax: {
            url: baseUrl("ams/vehicles/get_vehicle_components_collection"),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.advanced_search = advanced_search;
            }
        },
        searching: false,
        columns: [
            {
                data: 'id', orderable: false, render: function (data, type, row, meta) {
                    return formatcheck(data, row);
                }
            },
            {
                data: "image",
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    if (data) {
                        return '<a data-lightbox="roadtrip" data-title="' + row.primary_pic + '" href="' + data + '">' +
                            '       <div class="table-avatar" style="background-image: url(\'' + data + '\')"></div>' +
                            '   </a>';
                    } else {
                        return '<div class="table-avatar" title="No image available.">' +
                            '       <i class="la la-truck"></i>' +
                            '   </div>';
                    }
                },
                className: "d-flex justify-content-center"
            },
            {
                data: "assetacode",
                width: "8%"
            },
            {data: "name", width: "20%"},
            {data: "description", width: "24%"},
            {data: "area", width: "15%"},
            {data: "comp_name", visible: false},
            {data: "asset_category", width: "20%"},
            {data: "brand", visible: false},
            {data: "serialno", visible: false},
            {data: "po_no", visible: false},
            {data: "check_no"},
            {data: "model", visible: false},
            {data: "status", visible: false},
            {
                data: "datepurchased", visible: false,
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : 'N/A';
                }
            },
            {
                data: "dateCreated", visible: false,
                render: function (data) {
                    return data && data !== "0000-00-00" ? moment(data).format("YYYY-MM-DD") : 'N/A';
                }
            },
            {data: "accounted_to", visible: false},
            {data: "purchaseprice", visible: false, render: function (data) {
                return numberFormat(data);
            }},
            {data: "stock_code", visible: false},
            {
                data: null,
                width: "8%",
                className: "text-center actions",
                orderable: false,
                defaultContent: "",
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.is_borrowed);
                }
            },
        ],
        buttons: [
            {
                extend: 'excel',
                text: 'EXCEL',
                title: "Vehicle Components - " + moment().format('ll'),
                customize: function (excel) {
                },
                exportOptions: {
                    columns: ':visible:not(:eq(0)):not(.actions)'
                },
                action: function (e, dt, node, config) {
                    const self = this;
                    const data = tblVehicleComponentsMasterfile.ajax.params();

                    $.ajax({
                        url: baseUrl("ams/vehicles/get_vehicle_components_collection/0/1"),
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
                title: "Vehicle Components - " + moment().format('ll'),
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
                    const data = tblVehicleComponentsMasterfile.ajax.params();
                    $.ajax({
                        url: baseUrl("ams/vehicles/get_vehicle_components_collection/0/1"),
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
        pageLength: 20
    });

$("#generalSearch")
    .donetyping(function () {
        search_val = $(this).val();
        reloadTable();
    });


function reloadTable() {
    tblVehicleComponentsMasterfile.ajax.reload();
}

function formatcheck(data, row) {
    if (data) {
        var _checkButton = "<label class='m-checkbox m-checkbox--state-primary'><input type='checkbox' name='asset_id' class='text-gray'  value='"+ data +"'><span></span><label>";
        return _checkButton;
    } else {
        return false;
    }
}


function itemDatatableActions($id, is_borrowed) {
    if ($id) {
        let _actionButton = "";
        if (_currentActions.includes("edit")) {
            _actionButton += "" +
                "<a title='Edit Component' data-placement='bottom' " +
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem' " +
                "   href='" + baseUrl('ams/vehicles/edit_vehicle_component/') + $id + "'>" +
                "<i class='la la-edit'></i></a>";
        }

        if (_currentActions.includes("archive")) {
            _actionButton += " <button type='button' " +
                "data-placement='bottom' title='Archive Component'" +
                "class='btn btn-default m-btn m-btn--hover-warning " +
                "m-btn--icon m-btn--icon-only m-btn--pill btnCancel' data-id='" + $id + "' " +
                "onclick='archiveVehicleComponent(" + $id + "," + is_borrowed + ")'>" +
                "<i class='la la-file-archive-o'></i></button>";
        }

        return _actionButton;
    } else {
        return false;
    }
}

$("#selectall").click(function () {
    var asset_ids = [];
    $('#table-vehicle-components-master-file tbody input[type="checkbox"]').prop('checked', this.checked);
    
    $("input:checkbox[name=asset_id]:checked").each(function(){
        asset_ids.push($(this).val());
    });
    vehiclesComponents = asset_ids;
});

$("#table-vehicle-components-master-file").on("click", "tbody input[type='checkbox']", function () {
    var asset_id = [];
    const allCheckboxes = $("#table-vehicle-components-master-file tbody input[type='checkbox']").length;
    const checkedCheckboxes = $("#table-vehicle-components-master-file tbody input[type='checkbox']:checked").length;
    const checked = allCheckboxes <= checkedCheckboxes;
    $('#selectall').prop('checked', checked);
    
    $("input:checkbox[name=asset_id]:checked").each(function(){
        asset_id.push($(this).val());
    });
    vehiclesComponents = asset_id;
});

function openAlertModal(title, message, function_name, action, color) {
    $.ajax({
        url: baseUrl("ams/assets/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {title, message, action, color},
            path: "alert_dialog",
            function_name
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function archiveVehicleComponent(id, is_borrowed) {
    checkIfAssetIsBorrowedOrAccounted(id)

        .then((done) => {
            const accountability = done.accountability;
            const borrowing_history = done.borrowing_history;
            const has_mother_asset = done.has_mother_asset;
            const mother_asset_accountability = done.mother_asset_accountability;

            if (accountability && (borrowing_history || mother_asset_accountability)) {
                const modalAlert = $(".cant-archive-alert-dialog");
                const modalBody = modalAlert.find(".modal-body");
                let el = "";
                if (accountability && mother_asset_accountability) {
                    if (has_mother_asset && mother_asset_accountability) {
                        el = "" +
                            "<div class='normal-case m--regular-font-size-lg2'>" +
                            "  <p>" +
                            "       Asset, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.asset_name + "</span> cannot be archived." +
                            "       Mother asset of this component with asset name, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" +
                            "   " + mother_asset_accountability.asset_name + "</span> is still accounted to " +
                            "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + mother_asset_accountability.issued_to + "</span>." +
                            "  </p>" +
                            "</div>" +
                            "</br>";
                    } else {
                        el = "" +
                            "<div class='normal-case m--regular-font-size-lg2'>" +
                            "  <p>Asset, <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.asset_name + "</span> cannot be archived." +
                            "       This asset is still accounted to " +
                            "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + accountability.issued_to + "</span>." +
                            "  </p>" +
                            "</div>" +
                            "</br>";
                    }
                }

                if (borrowing_history) {
                    el += "" +
                        "<div class='normal-case m--regular-font-size-lg2'>" +
                        "   <p class='mb-1 m--font-bold'>Borrower:</p>" +
                        "   <p>This asset is still in the possession of" +
                        "       <span class='m--font-bold text-primary' style='border-bottom: 1px dotted #5867dd;'>" + borrowing_history.borrower_name + "</span>." +
                        "   </p>" +
                        "</div>";
                }

                modalBody.html("").append(el);
                modalAlert.modal("show");
            } else {
                const _modal = $(".archive-remarks");
                const form = _modal.find("form");
                form.attr("action", baseUrl("ams/vehicles/archive_vehicle/1/?id=" + id));
                _modal.modal("show");
            }
        });
}

function checkIfAssetIsBorrowedOrAccounted(asset_id) {
    return new Promise((resolve, reject) => {
        return $.ajax({
            url: baseUrl("ams/vehicles/check_if_asset_is_borrowed_or_accounted"),
            dataType: "JSON",
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                asset_id,
                type: "vehicle",
                isComponent: 1
            },
            success: function (response) {
                resolve(response);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                reject(errorThrown);
            }
        });
    });
}

$(".m-content")
    .on("submit", "#confirmation-dialog",
        function (e) {
            e.preventDefault();
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");

            $.ajax({
                url: baseUrl(url),
                type: "GET",
                dataType: "JSON",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "", 5000);
                        tblVehicleComponentsMasterfile.ajax.reload();
                        closeModal();
                    } else {
                        toastr.error(response.message, "Error", 5000);
                    }
                }
            })
        })
    .on("submit", ".archive-remarks form", function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr("action");
        const formData = new FormData(this);

        if (form.isValid()) {
            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "Archive successful.", 10000);
                        form.resetForm();
                        tblVehicleComponentsMasterfile.ajax.reload();
                        $(".archive-remarks").modal("hide");
                    } else {
                        toastr.error(response.message, "Error", 10000);
                    }
                }
            })
        }
    });

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

function clearSearch() {
    const search = $("#generalSearch");
    search.val("");
    search_val = search.val();

    tblVehicleComponentsMasterfile.ajax.reload();
}

$('body, .modal-body')
    .tooltip({
        selector: '[title]',
        skin: "dark",
        delay: {
            show: 300
        }
    });


$("#location").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: "SELECT AN OPTION",
    width: '100%',
    ajax: {
        url: baseUrl("ams/vehicles/get_location"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$("#cat").select2({
    dropdownParent: $("#modal-advance-search"),
    placeholder: "SELECT AN OPTION",
    width: '100%',
    ajax: {
        url: baseUrl("ams/vehicles/get_category"),
        dataType: "json",
        delay: 500,
        processResults: function (data) {
            return data;
        }
    }
});

$('#advanced_search').on("click", function (callback) {
    advanced_search['gen_code'] = $("#asset_code").val();
    advanced_search['name'] = $("#asset_name").val();
    advanced_search['description'] = $("#asset_desc").val();
    advanced_search['serialno'] = $("#serialno").val();
    advanced_search['po_no'] = $("#po_no").val();
    advanced_search['dateCreated_from'] = $("#date_created_from").val();
    advanced_search['dateCreated_to'] = $("#date_created_to").val();
    advanced_search['location'] = $("#location").val();
    advanced_search['asset_category'] = $("#cat").val();
    tblVehicleComponentsMasterfile.ajax.reload();
    $("#modal-advance-search").modal("hide");
});

$(".dt-picker").datepicker({
    format: "yyyy-mm-dd",
    todayHighlight: true,
    todayBtn: "linked",
    clearBtn: true,
});

function clear_adv_search() {
    $('#frm-advance-search')[0].reset();
    $("#location").val("").trigger('change');
    $("#cat").val("").trigger('change');
    $("#station").val("").trigger('change');

    advanced_search = {};
    tblVehicleComponentsMasterfile.ajax.reload();
}

function exportAs(type) {
    dropdown.removeClass();
    dropdown.addClass("fa fa-spinner fa-spin");

    setTimeout(() => {
        switch (type) {
            case "excel":
                tblVehicleComponentsMasterfile.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblVehicleComponentsMasterfile.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

function showOrHideColumn(index, el) {
    const column = tblVehicleComponentsMasterfile.column(index);
    column.visible($(el)[0].checked);
}

$("#column-options.dropdown-menu")
    .click(function (e) {
        e.stopPropagation();
    });
    
$("#select2-status").select2({
    placeholder: 'Select Status',
    width: '100%'
});      

$("#modal-print-barcode").on("shown.bs.modal", function () {
    let name;
    const jsonResponse = tblVehicleComponentsMasterfile.ajax.json();
    const data = jsonResponse.data;
    const barcodeContainer = $("#barcodes div.row");
    barcodeContainer.empty();
    console.log(vehiclesComponents);
    if(vehiclesComponents.length > 0){
        data.forEach((item, i) => {
            if(vehiclesComponents.includes(item.id)){
                if(item.name){
                    name = item.name;
                }else{
                    name = 'NO ASSIGNED NAME';
                }
                barcodeContainer.append(`<div class="col-6 text-center mb-1">
                    <p class="barcode-label" style="margin-bottom: 17px;">${name}</p>
                    <p class="barcode-code" 
                        style="font-size:1.2rem; font-family: barcode, sans-serif;">*${item.assetacode}*</p>
                    </div>`);
            }
        });
    }else{
        barcodeContainer.append(`<div class="col-1"></div><div class="col-10 text-center bold"><h5>NO ASSET SELECTED. PLEASE CHECK AT LEAST ONE ASSET.</h5></div><div class="col-1"></div>`);
        
    }
});