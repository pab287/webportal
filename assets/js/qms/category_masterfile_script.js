var tblCategory = $("#table-category");
var tblArchiveCategory = $("#table-archived-category");
var modalWindow = $("#modalTempContent");
var search_val = '';

if(typeof tblCategory != 'undefined'){
    var table = tblCategory.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("qms/masterfile/get_category_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "added_dt" },
            { data: "code", width: '10%',
                render: function(data){
                    return data ? data : ' --- ';
                }
            },
            { data: "name", width: '20%' },
            { data: "description", width: '*' },
            {data: null, width: "10%", className: "text-center"}
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false,
                searchable: false
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return categoryDataTableActions(row.id);
                }
            }
        ]
    });

    function categoryDataTableActions(id){
        if (id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditCategory' " +
                "   data-toggle='modal' data-target='#edit_modal' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Edit Category'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveCategory' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Archive'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-file-archive-o'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        table.ajax.reload();
    });
}

$(document).on("click", "#btnNewCategory", function () {
    $.ajax({
        url: baseUrl("qms/masterfile/get_category_modal_content/add"), 
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-add_category",
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
                                success: function(json){
                                    if (json.response) {
                                        toastr.success(json.toastr_msg, "Category", 5000);
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        table.ajax.reload();
                                    } else {
                                        toastr.error(json.toastr_msg, "Category!", 5000);
                                    }

                                    $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                            });

                            return false;
                        }
                    });
                }
            }
        }
    });
});

$(document).on("click", ".btnRemoveCategory", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveCategory");
    modalWindowRemove.find("input#categoryId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentCategory", function () {
    var dataId = $("#modalRemoveCategory").find("input#categoryId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("qms/masterfile/remove_current_category"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemoveCategory")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Archive Category", 5000);
                    table.ajax.reload();
                    $("#modalRemoveCategory").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Archive Category", 5000);
                }

                $("#modalRemoveCategory")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditCategory", function () {
    var _self = $(this);
    var dataId = _self.data("id");

    $.ajax({
        url: baseUrl("qms/masterfile/get_category_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function(json){
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-edit_category",
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
                                    $(currentForm).find(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                                },
                                success: function (json) {
                                    if (json.response) {
                                        toastr.success(json.toastr_msg, "Update Category", 5000);

                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        table.ajax.reload();
                                    } else {
                                        toastr.error(json.toastr_msg, "Update Category", 5000);
                                    }

                                    $(currentForm).find(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
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

if(typeof tblArchiveCategory != 'undefined'){
    var archiveTable = tblArchiveCategory.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("qms/masterfile/get_archived_category_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "added_dt" },
            { data: "code", width: '10%',
                render: function(data){
                    return data ? data : ' --- ';
                }
            },
            { data: "name", width: '20%' },
            { data: "description", width: '*' },
            {data: null, width: "10%", className: "text-center"}
        ],
        columnDefs: [
            {
                targets: 0,
                visible: false,
                searchable: false
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return categoryDataTableActions(row.id);
                }
            }
        ]
    });

    function categoryDataTableActions(id){
        if (id) {
            var _actionButton = "";

            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRestoreCategory' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Restore'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-reply'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        archiveTable.ajax.reload();
    });
}

$(document).on("click", ".btnRestoreCategory", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRestoreCategory");
    modalWindowRemove.find("input#categoryId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRestoreCurrentCategory", function () {
    var dataId = $("#modalRestoreCategory").find("input#categoryId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("qms/masterfile/restore_current_category"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRestoreCategory")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Restore Category", 5000);
                    archiveTable.ajax.reload();
                    $("#modalRestoreCategory").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Restore Category", 5000);
                }

                $("#modalRestoreCategory")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});