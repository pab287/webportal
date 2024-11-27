let modalWindow = $("#modalTempContent");
let search_val = "";
var tbl = $("#table-category").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ticket/ticket/category_masterfile"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    searching: false,
    columns: [
        {data: "id"},
        {data: "name"},
        {data: "type"},
        {data: "status", render: function(data){
            let status = "";
            if(data == 0){
                status = '<div class="m-badge m-badge--success m-badge--wide m--margin-top-5" role="alert"><strong>Active</strong></div>';
            }else{
                status = '<div class="m-badge m-badge--danger m-badge--wide m--margin-top-5" role="alert"><strong>Inactive</strong></div>';
            }
            return status;
        }},
        {data: null, width: "10%", className: "text-center"},
    ],
    columnDefs: [
        {
            targets: [4], width: "15%",
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.is_archived);
            },
        }
    ], buttons: [
        {
            extend: 'csv',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            title: "Ticketing System Report",
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

$.validate({
	form: '#form_category',
	lang: 'en',
	onSuccess: function (form) {
		var currentForm = form[0];
		var formData = $(currentForm).serialize();
        $.ajax({
            url: baseUrl("ticket/ticket/add_category"),
            type: "POST",
            dataType: "JSON",
            data: formData,
            success: function (data) {
                if (data) {
                    currentForm.reset();
                    tbl.ajax.reload();
                    $("#add_category").modal('hide');
                    toastr.success(data.toastr_msg, "Successfully saved!", 5000);
                } else {
                    toastr.error("Error", "Failed adding data!", 5000);
                }
            }
        });
        return false;
	}
});

$("#type").select2({
	placeholder: 'SELECT AN OPTION',
	width: '100%',
});

$("#status").select2({
	placeholder: 'SELECT AN OPTION',
	width: '100%',
});

function itemDatatableActions($id, is_archived) {
    if ($id) {
        let _actionButton = "";
        if ($.inArray("edit", _currentActions) !== -1) {
            _actionButton += " <button type='button' " +
            "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditCategory' " +
            "   data-toggle='m-tooltip'" +
            "   data-placement='bottom'" +
            "   data-original-title='Edit Category'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-id='" +
            $id +
            "'><i class='la la-edit'></i></button>";
            if(is_archived == 1){
                _actionButton += " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill brnRestoreCategory' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-original-title='Restore Category'" +
                "   onclick=restoreCategory("+$id+")" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-id='" +
                $id +
                "'><i class='la la-reply'></i></button>";
            }
            if(is_archived == 0){
                _actionButton += " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveCategory' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-original-title='Archive Category'" +
                "   onclick=deleteCategory("+$id+")" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";
            }
        }
        return _actionButton;
    } else {
        return false;
    }
}

function restoreCategory($id) {
    $("#rst-remove-category").attr("action", baseUrl("ticket/ticket/restore_category/" + $id));
    $("#restore-category-confirmation-modal").modal("show");
}

function deleteCategory($id) {
    $("#frm-remove-category").attr("action", baseUrl("ticket/ticket/delete_category/" + $id));
    $("#remove-category-confirmation-modal").modal("show");
}

function processRemoveCategory(formElement) {
    const form = $(formElement);
    const url = form.attr("action");
    $.ajax({
        url,
        type: 'GET',
        dataType: "json",
        success: function (response) {
            if (response) {
                $("#remove-category-confirmation-modal").modal("hide");
                toastr.success("Category was removed successfully.", "Category Removed.", 5000);
                tbl.ajax.reload();
            } else {
                toastr.error("An error occurred while removing category.", "Error", 5000);
            }
        },
    });
}
function processRestoreCategory(formElement) {
    const form = $(formElement);
    const url = form.attr("action");
    $.ajax({
        url,
        type: 'GET',
        dataType: "json",
        success: function (response) {
            if (response) {
                $("#restore-category-confirmation-modal").modal("hide");
                toastr.success("Category restored successfully.", "Category Removed.", 5000);
                tbl.ajax.reload();
            } else {
                toastr.error("An error occurred while restoring category.", "Error", 5000);
            }
        },
    });
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tbl.ajax.reload();
});


$(document).on("click", ".btnEditCategory", function () {
    let _self = $(this);
    let dataId = _self.data("id");
    $.ajax({
        url: baseUrl("ticket/ticket/get_category_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    let tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    let select2Data = modalWindow.find("select#type").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    if (typeof json.data !== "undefined") {
                        let tempData = json.data;
                        select2Data.val(tempData.type).trigger("change");
                    }
                    select2Data.on("change", function (e) {
                        let self2 = $(e.target);
                        
                        self2.validate();
                    });

                    let selectStatus = modalWindow.find("select#status").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    if (typeof json.data !== "undefined") {
                        let tempData = json.data;
                        selectStatus.val(tempData.status).trigger("change");
                    }
                    selectStatus.on("change", function (e) {
                        let self1 = $(e.target);
                        self1.validate();
                    });

                    modalWindow.modal("show");
                   
                    $.validate({
                        form: "#form_category",
                        lang: "en",
                        onSuccess: function (form) {
                            var currentForm = form[0];
                            var formUrl = currentForm.action;
                            var formData = $(currentForm).serialize();

                            $.ajax({
                                url: formUrl,
                                type: "post",
                                dataType: "json",
                                data: formData,
                                beforeSend: function () {
                                    $(currentForm)
                                        .find(".btn-submit")
                                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(
                                            json.toastr_msg,
                                            "Employee position details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        tbl.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating position details!",
                                            5000
                                        );
                                    }

                                    $(currentForm)
                                        .find(".btn-submit")
                                        .removeClass(
                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                        );
                                }
                            });
                            return false;
                        }
                    });
                }
            }
        }
    });
});