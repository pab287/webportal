let search_val = "";
const type = $("#archived_assets_type").val();

const tblArchivedAssets = $("#table-archived-assets")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        ajax: {
            url: baseUrl("ams/assets/get_archive_collections/?type=" + type),
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.filter = $("#filterAssetsByType").val();
            }
        },
        searching: false,
        buttons: [
            {
                extend: 'excel',
                text: 'EXCEL',
                title: "Archived Assets - " + moment().format('ll'),
                // exportOptions: {
                //     columns: ':visible:not(:eq(0)):not(.actions)'
                // },
                // action: function (e, dt, node, config) {
                //     const self = this;
                //     const data = tblArchivedAssets.ajax.params();
                //     $.ajax({
                //         url: baseUrl("ams/assets/get_archive_collections/1"),
                //         type: "POST",
                //         dataType: "JSON",
                //         data,
                //         success: function (response) {
                //             alert(data);
                //             dt.rows().remove();
                //             dt.rows.add(response.data).draw();
                //             $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, node, config);
                //         }
                //     });
                // }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                title: "Archived Assets - " + moment().format('ll'),
                // orientation: 'landscape',
                // pageSize: 'LEGAL',
                // customize: function (doc) {
                //     //doc.content[1].table.widths = ['10%', '18%', '18%', '18%', '18%', '18%'];
                //     doc.styles.tableHeader.alignment = 'left';
                // },
                // exportOptions: {
                //     columns: ':visible:not(:eq(0)):not(.actions)'
                // },
                // action: function (e, dt, node, config) {
                //     const self = this;
                //     const data = tblArchivedAssets.ajax.params();
                //     $.ajax({
                //         url: baseUrl("ams/assets/get_archive_collections/1"),
                //         type: "POST",
                //         dataType: "JSON",
                //         data,
                //         success: function (response) {
                //             dt.rows().remove();
                //             dt.rows.add(response.data).draw();
                //             $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, node, config);
                //         }
                //     });
                // },
            }
        ],
        columns: [
            {
                data: null,
                defaultContent: '-',
                orderable: false,
                render: function (data, type, row) {
                    // if (row.status.toLowerCase() === "archived") { //changed to lost status
                    if (row.status.toLowerCase() === "lost") {
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
                data: "assetacode",
                width: '8%',
                defaultContent: "-"
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
                width: (type === "mother" || type === "component") ? "18%" : "13%",
                render: function (data) {
                    return data ? data : "-"
                }
            },
            {
                data: "area",
                width: '10%',
                defaultContent: "-"
            },
            {
                data: "asset_category",
                width: '10%',
                defaultContent: "-"
            },
            {
                data: "station",
                width: '12%',
                defaultContent: "-"
            },
            {
                data: "asset_type",
                width: '10%'
            },
            {
                data: null, width: "8%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row) {
                    let actions = "";

                    // if (_currentActions.includes("restore") && (row.status.toLowerCase() === 'archived' || row.status.toLowerCase() === 'junk' || row.status.toLowerCase() === 'destructed' || row.status.toLowerCase() === 'sold' || row.status.toLowerCase() === 'lost' || row.status.toLowerCase() === 'others' )) { // requested by the warehouse that the lost status is only the restorable item
                    if (_currentActions.includes("restore") && row.status.toLowerCase() === 'lost') {
                        actions += " <button type='button' class='btn btn-sm btn-default m-btn m-btn--hover-primary m-btn--icon " +
                            "m-btn--icon-only m-btn--pill btnRestore' title='Restore'" +
                            "data-placement='bottom'>" +
                            "<i class='la la-reply' onclick='restoreAsset(" + row.id + ")'></i>" +
                            "</button> ";
                    }

                    if (_currentActions.includes("view")) {
                        actions += ` <a class='btn btn-sm btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnView' 
                            title='View Details' data-placement='bottom'
                            href="${baseUrl('ams/assets/view_asset/' + row.id + '/' + (row.asset_type === 'Component' ? 1 : 0))}">
                            <i class='la la-eye'></i>
                        </a>`;
                    }

                    // if (_currentActions.includes("delete") && (row.status.toLowerCase() === 'archived' || row.status.toLowerCase() === 'junk' || row.status.toLowerCase() === 'destructed' || row.status.toLowerCase() === 'sold' || row.status.toLowerCase() === 'lost' || row.status.toLowerCase() === 'others' )) {
                    if (_currentActions.includes("delete") && (row.status.toLowerCase() === 'archived' || row.status.toLowerCase() === 'junk' || row.status.toLowerCase() === 'destructed' || row.status.toLowerCase() === 'sold' || row.status.toLowerCase() === 'others' )) {
                        actions += " <button type='button' class='btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon " +
                            "m-btn--icon-only m-btn--pill btnDelete' title='Delete Permanently'" +
                            "data-placement='bottom'>" +
                            "<i class='la la-trash-o' onclick='removeAsset(" + row.id + ")'></i>" +
                            "</button>";
                    }

                    return actions;
                }
            },
        ],
        order: [[1, "asc"]]
    });

tblArchivedAssets.on("xhr.dt", function (e) {
    $("#select-all-archived-page").prop("checked", false);
});

tblArchivedAssets.column(8).visible(!(type === "mother" || type === "component"));

$("#search-archived-assets")
    .donetyping(function () {
        search_val = $(this).val();
        tblArchivedAssets.ajax.reload();
    });

function removeAsset(id) {
    $.ajax({
        url: baseUrl("ams/assets/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Confirm Permanent Delete",
                message: "Are you sure to permanently delete this item?",
                action: "ams/assets/delete_asset/" + id,
                color: "btn-danger"
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");

            $(".m-content").on("submit", "#confirmation-dialog", function (e) {
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
                            $("#search-archived-assets").val(search_val);
                            filterArchivedAssets();
                            closeModal();
                        } else {
                            toastr.error(response.message, "Error", 5000);
                        }
                    }
                })
            });
        }
    });
}

function clearFilter() {
    const searchField = $("#search-archived-assets");
    searchField.val("");
    search_val = searchField.val();
    tblArchivedAssets.ajax.reload();
}

function filterArchivedAssets() {
    tblArchivedAssets.ajax.reload();
}

function restoreAsset(id) {
    $.ajax({
        url: baseUrl("ams/assets/open_modal"),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: "Confirm Restore",
                message: "Are you sure to restore this item?",
                action: "ams/assets/restore_archived_asset/" + id,
                color: "btn-primary",
                type: 'archive'
            },
            path: "confirmation_dialog",
            function_name: "pass_data_to_dialog"
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");

            $("#restore-status").select2({
                width: '100%',
                placeholder:'Select a Status',
                dropdownParent: $('.document-modal-container')
            });

            $.validate({
                form: '#confirmation-dialog',
                lang: 'en',
                onSuccess: function(form){
                    var currentForm = form[0];
                    var formData = $(currentForm).serialize();

                    $.ajax({
                        url: baseUrl('ams/assets/restore_archived_asset/') + id,
                        type: "post",
                        dataType: "json",
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message, "", 5000);
                                search_val = "";
                                $("#search-archived-assets").val(search_val);
                                filterArchivedAssets();
                                closeModal();
                            } else {
                                toastr.error(response.message, "Error", 5000);
                            }
                        }
                    })

                    return false;
                }
            });
        }
    });
}

// $(".m-content")
//     .on("submit", "#confirmation-dialog",
//         function (e) {
//             e.preventDefault();
//             e.preventDefault();
//             const form = $(this);
//             const url = form.attr("action");

//             $.ajax({
//                 url: baseUrl(url),
//                 type: "GET",
//                 dataType: "JSON",
//                 success: function (response) {
//                     if (response.success) {
//                         toastr.success(response.message, "", 5000);
//                         search_val = "";
//                         $("#search-archived-assets").val(search_val);
//                         filterArchivedAssets();
//                         closeModal();
//                     } else {
//                         toastr.error(response.message, "Error", 5000);
//                     }
//                 }
//             })
//         });

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

$("#select-all-archived-page")
    .on("click", function (e) {
        const c_boxes = $("#table-archived-assets tbody").find("input[type='checkbox']");
        const check = this.checked;
        c_boxes.prop('checked', check);
        enableButtons();
    });

$("#table-archived-assets tbody")
    .on("click", "input[type='checkbox']", function () {
        const tbody = $("#table-archived-assets tbody");
        const c_boxesNotChecked = tbody.find("input[type='checkbox']").not(':checked');
        $("#select-all-archived-page").prop('checked', (c_boxesNotChecked.length <= 0));
        enableButtons();
    });

function enableButtons() {
    const ids = getCheckboxSelections();
    $(".btn-restore-multiple").attr("disabled", !(!!ids));
    $(".btn-delete-multiple").attr("disabled", !(!!ids));
}

function confirmRestoreSelections() {
    const ids = getCheckboxSelections();
    $("#frm-confirm-restore-multiple .multiple_id").val(ids);
    $("#confirm-restore-multiple").modal("show");

    $("#restore-status").select2({
        width: '100%',
        placeholder:'Select a Status',
        dropdownParent: $('#confirm-restore-multiple')
    });

    $.validate({
        form: '#frm-confirm-restore-multiple',
        lang: 'en',
        onSuccess: function(form){
            var currentForm = form[0];
            var _url = currentForm.action;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: _url,
                type: "post",
                dataType: "json",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, "Successfully restored.", 5000);
                        search_val = "";
                        $("#search-archived-assets").val(search_val);
                        filterArchivedAssets();
                        $("#confirm-restore-multiple").modal("hide");
                        form.resetForm();
                        enableButtons();
                        $(".btn-restore-multiple").attr("disabled", true);
                    } else {
                        toastr.error(response.message, "Error", 5000);
                    }
                }
            })

            return false;
        }
    })
}

function confirmDeleteSelections() {
    const ids = getCheckboxSelections();
    $("#frm-confirm-delete-multiple .multiple_id").val(ids);
    $("#confirm-delete-multiple").modal("show");
}

function getCheckboxSelections() {
    const checked = $("#table-archived-assets tbody").find("input[type='checkbox']:checked");
    let selected = [];
    $.each(checked, function (key, el) {
        selected.push($(el).val());
    });

    const selectedIds = selected.join(',');
    return selectedIds;
}

// $("#frm-confirm-restore-multiple") //original source code for multiple restore of archived assets. commented to changed it to a validation form
//     .on("submit", function (e) {
//         e.preventDefault();
//         const form = $(this);
//         const formData = new FormData(this);

//         $.ajax({
//             url: form.attr("action"),
//             type: "POST",
//             dataType: "JSON",
//             contentType: false,
//             processData: false,
//             data: formData,
//             success: function (response) {
//                 if (response.success) {
//                     toastr.success(response.message, "Successfully restored.", 5000);
//                     search_val = "";
//                     $("#search-archived-assets").val(search_val);
//                     filterArchivedAssets();
//                     $("#confirm-restore-multiple").modal("hide");
//                     form.resetForm();
//                     enableButtons();
//                     $(".btn-restore-multiple").attr("disabled", true);
//                     $(".btn-delete-multiple").attr("disabled", true);
//                 } else {
//                     toastr.error(response.message, "Error", 5000);
//                 }
//             }
//         })
//     });


$("#frm-confirm-delete-multiple")
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
                    filterArchivedAssets();
                    $("#confirm-delete-multiple").modal("hide");
                    form.resetForm();
                    enableButtons()
                    $(".btn-restore-multiple").attr("disabled", true);
                    $(".btn-delete-multiple").attr("disabled", true);
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

$("#filterAssetsByType")
    .select2({width: "100%"})
    .on('select2:select', function () {
        filterArchivedAssets();
    });

function exportAs(type) {
    setTimeout(() => {
        switch (type) {
            case "excel":
                tblArchivedAssets.button(".buttons-excel").trigger();
                break;
            case "pdf":
                tblArchivedAssets.button(".buttons-pdf").trigger();
                break;
        }
    }, 150);
}