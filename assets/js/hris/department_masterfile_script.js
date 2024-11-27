var modalWindow = $("#modalTempContent");
var tableDepartmentList = $("#table-department");
var tableDepartmentArchivedList = $("#table-archived-department");
var search_val = "";

if (typeof tableDepartmentList !== "undefined") {
    var search_val = "";
    var dtDepartment = tableDepartmentList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_department_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {data: "code", width: "20%"},
            {data: "description", width: "35%"},
            {data: "head", width: "37%"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return employeeDataTableActions(row.id);
            }
        },
            {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });

    function employeeDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditDepartment' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Edit Department'" +
                "   data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveDepartment' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Archive Department'" +
                "   data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtDepartment.ajax.reload();
    });
}

$(document).on("click", ".btnRemoveDepartment", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveDepartment");
    modalWindowRemove.find("input#departmentId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentDepartment", function () {
    var dataId = $("#modalRemoveDepartment").find("input#departmentId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_department"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemoveDepartment")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove Department", 5000);
                    dtDepartment.ajax.reload();
                    $("#modalRemoveDepartment").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove Department", 5000);
                }

                $("#modalRemoveDepartment")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditDepartment", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_department_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#head_id").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    if (typeof json.data !== "undefined") {
                        var tempData = json.data;
                        select2Data.val(tempData.head_id).trigger("change");
                    }
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-edit_department",
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
                                            "Employee department details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtDepartment.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating department details!",
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

$(document).on("click", ".btnNewDepartment", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_department_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");
                    var select2Data = modalWindow.find("select#head_id").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });

                    $.validate({
                        form: "#form-add_department",
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
                                            "Employee department details has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtDepartment.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding department details!",
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

if(typeof tableDepartmentArchivedList !== 'undefined'){
    var dtDepartmentArchived = tableDepartmentArchivedList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_department_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.is_archived = 1
            }
        },
        columns: [
            {data: "code", width: "20%"},
            {data: "description", width: "35%"},
            {data: "head", width: "37%"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return employeeArchivedDataTableActions(row.id);
            }
        },
            {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });

    function employeeArchivedDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestoreDepartment' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Restore Department'" +
                "   data-id='" +
                $id +
                "'><i class='la la-reply'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtDepartment.ajax.reload();
    });

    $(document).on("click", ".btnRestoreDepartment", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreDepartment");
        modalWindowRemove.find("input#departmentId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentDepartment", function () {
        var dataId = $("#modalRestoreDepartment").find("input#departmentId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_department"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestoreDepartment")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore Department", 5000);
                        dtDepartmentArchived.ajax.reload();
                        $("#modalRestoreDepartment").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore Department", 5000);
                    }
    
                    $("#modalRestoreDepartment")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}