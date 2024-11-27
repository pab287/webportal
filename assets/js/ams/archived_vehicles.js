let search_val = "";
const type = $("#archived-vehicle-type").val();

const tblArchivedVehicles = $("#table-archived-vehicles")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        ajax: {
            url: baseUrl("ams/vehicles/get_archive_collection/?type=" + type),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.filter = $("#filterByVehicleType").val();
            }
        },
        searching: false,
        buttons: [
            {
                extend: 'excel',
                text: 'EXCEL',
                title: "Archived Vehicle and Components - " + moment().format('ll'),
            },
            {
                extend: 'pdf',
                text: 'PDF',
                title: "Archived Vehicle and Components - " + moment().format('ll'),
            }
        ],
        columns: [
            {
                data: null,
                defaultContent: '-',
                orderable: false,
                render: function (data, type, row) {
                    if (row.status.toLowerCase() === "archived") {
                        return "<label class='m-checkbox'>" +
                            "<input type='checkbox' class='form-control' value='" + row.id + "'>" +
                            "<span></span>" +
                            "</label>";
                    } else {
                        return "---";
                    }
                },
                className: "d-flex justify-content-center"
            },
            {
                data: "status",
                render: function (data) {
                    return `<span class="m--font-boldest">${data}</span>`;
                }
            },
            {
                data: "assetacode", width: "8%"
            },
            {
                data: "name",
                width: (type === "mother" || type === "component") ? "25%" : "20%",
                render: function (data, type, row) {
                    if (data) {
                        return "<p class='mb-0'>" + data + "</p>" +
                            "   <p class='mb-0 text-muted m--regular-font-size-sm1'>" + row.description + "</p>";
                    } else {
                        return "<p class='mb-0'>" + row.description + "</p>";
                    }
                }
            },
            {
                data: "archive_remark",
                width: (type === "mother" || type === "component") ? "20%" : "15%",
                render: function (data) {
                    return data ? data : "-"
                }
            },
            {data: "area", width: "10%"},
            {data: "asset_category", width: "20%"},
            {data: "type", width: "10%"},
            {
                data: null, width: "8%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row) {
                    let actions = "";

                    if (_currentActions.includes("restore") && (row.status.toLowerCase() === 'archived' || row.status.toLowerCase() === 'junk' || row.status.toLowerCase() === 'destructed' || row.status.toLowerCase() === 'sold' || row.status.toLowerCase() === 'lost' || row.status.toLowerCase() === 'others' )) {
                        actions += "<button type='button' class='btn btn-sm btn-default m-btn m-btn--hover-primary m-btn--icon " +
                            "m-btn--icon-only m-btn--pill btnRestore' title='Restore' " +
                            "   data-placement='bottom'>" +
                            "<i class='la la-reply' onclick='restoreVehicle(" + row.id + ")'></i>" +
                            "</button> ";
                    }

                    if (_currentActions.includes("view")) {
                        actions += ` <a class='btn btn-sm btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView' 
                                                              title='View Details' data-placement='bottom'
                                                              href="${baseUrl('ams/vehicles/view_vehicle/' + row.id + '/' + (row.type === 'Component' ? 1 : 0))}">
                                           <i class='la la-eye'></i>
                                        </a>`;
                    }

                    if (_currentActions.includes("delete") && (row.status.toLowerCase() === 'archived' || row.status.toLowerCase() === 'junk' || row.status.toLowerCase() === 'destructed' || row.status.toLowerCase() === 'sold' || row.status.toLowerCase() === 'lost' || row.status.toLowerCase() === 'others' )) {
                        actions += " <button type='button' class='btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon " +
                            "m-btn--icon-only m-btn--pill btnDelete' title='Delete Permanently' " +
                            "   data-placement='bottom'>" +
                            "<i class='la la-trash-o' onclick='removeVehicle(" + row.id + ")'></i>" +
                            "</button>";
                    }

                    return actions;
                }
            },
        ],
        order: [[1, "asc"]]
    });

tblArchivedVehicles.on("xhr.dt", function (e) {
    $("#select-all-archived-vehicle-page").prop("checked", false);
});

tblArchivedVehicles.column(7).visible(!(type === "mother" || type === "component"));

function filterArchivedVehicles() {
    tblArchivedVehicles.ajax.reload();
}

function exportAs(type) {
    setTimeout(() => {
        switch (type) {
            case "excel":
                tblArchivedVehicles.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblArchivedVehicles.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}

function restoreVehicle(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Confirm Restore",
                message: "Are you sure to restore this item?",
                action: "ams/vehicles/restore_archived_vehicle/" + id,
                color: "btn-primary"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function removeVehicle(id) {
    $.ajax({
        url: baseUrl("ams/vehicles/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Confirm Permanent Delete",
                message: "Are you sure to permanently delete this record?",
                action: "ams/vehicles/delete_vehicles/" + id,
                color: "btn-danger"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

$("#search-archived-vehicles")
    .donetyping(function () {
        search_val = $(this).val();
        reloadTable();
    });

function reloadTable() {
    tblArchivedVehicles.ajax.reload();
}

function clearSearch() {
    const search = $("#search-archived-vehicles");
    search.val("");
    search_val = "";
    reloadTable();
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
                        search_val = "";
                        $("#search-archived-vehicles").val(search_val);
                        reloadTable();
                        closeModal();
                    } else {
                        toastr.error(response.message, "Error", 5000);
                    }
                }
            })
        });

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

$("#select-all-archived-vehicle-page")
    .on("click", function (e) {
        const c_boxes = $("#table-archived-vehicles tbody").find("input[type='checkbox']");
        const check = this.checked;
        c_boxes.prop('checked', check);
        enableButtons();
    });

$("#table-archived-vehicles tbody")
    .on("click", "input[type='checkbox']", function () {
        const tbody = $("#table-archived-vehicles tbody");
        const c_boxesNotChecked = tbody.find("input[type='checkbox']").not(':checked');
        $("#select-all-archived-vehicle-page").prop('checked', (c_boxesNotChecked.length <= 0));
        enableButtons();
    });

function enableButtons() {
    const ids = getCheckboxSelections();
    $(".btn-restore-multiple").attr("disabled", !(!!ids));
    $(".btn-delete-multiple").attr("disabled", !(!!ids));
}

function getCheckboxSelections() {
    const checked = $("#table-archived-vehicles tbody").find("input[type='checkbox']:checked");
    let selected = [];
    $.each(checked, function (key, el) {
        selected.push($(el).val());
    });

    const selectedIds = selected.join(',');
    return selectedIds;
}

function confirmRestoreSelections() {
    const ids = getCheckboxSelections();
    $("#frm-confirm-restore-vehicles-multiple .multiple_id").val(ids);
    $("#confirm-restore-vehicles-multiple").modal("show");
}

function confirmDeleteSelections() {
    const ids = getCheckboxSelections();
    $("#frm-confirm-delete-archived-vehicles .multiple_id").val(ids);
    $("#confirm-delete-archived-vehicles").modal("show");
}

$("#frm-confirm-restore-vehicles-multiple")
    .on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Successfully restored.", 5000);
                    search_val = "";
                    $("#search-archived-assets").val(search_val);
                    $("#confirm-restore-vehicles-multiple").modal("hide");
                    form.resetForm();
                    enableButtons();
                    $(".btn-restore-multiple").attr("disabled", true);
                    $(".btn-delete-multiple").attr("disabled", true);
                    tblArchivedVehicles.ajax.reload();
                } else {
                    toastr.error(response.message, "Error", 5000);
                }
            }
        })
    });


$("#frm-confirm-delete-archived-vehicles")
    .on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = new FormData(this);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Successfully removed.", 5000);
                    search_val = "";
                    $("#search-archived-assets").val(search_val);
                    $("#confirm-delete-archived-vehicles").modal("hide");
                    form.resetForm();
                    enableButtons();
                    $(".btn-restore-multiple").attr("disabled", true);
                    $(".btn-delete-multiple").attr("disabled", true);
                    tblArchivedVehicles.ajax.reload();
                } else {
                    toastr.error(response.message, "Error", 5000);
                }
            }
        })
    });

$('body, .modal-body')
    .tooltip({
        selector: '[title]',
        skin: "dark",
        delay: {
            show: 300
        }
    });

$("#filterByVehicleType")
    .select2({width: '100%'})
    .on('select2:select', function () {
        filterArchivedVehicles();
    });