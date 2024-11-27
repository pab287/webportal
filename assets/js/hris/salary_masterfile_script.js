var modalWindow = $("#modalTempContent");
var tableSalaryList = $("#table-salary");
var tableSalaryArchivedList = $("#table-archived-salary");
var search_val = "";

if (typeof tableSalaryList !== "undefined") {
    var search_val = "";
    var dtSalary = tableSalaryList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_salary_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {data: "description", width: "67%"},
            {data: "created_by", width: "25%"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row.id);
            }
        },
            {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });

    function tempDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditSalary'" +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Edit Salary'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveSalary'" +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Archive Salary'" +
                "   data-delay='{\"show\": 300}'" +
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
        dtSalary.ajax.reload();
    });
}

$(document).on("click", ".btnRemoveSalary", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveSalary");
    modalWindowRemove.find("input#salaryId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentSalary", function () {
    var dataId = $("#modalRemoveSalary").find("input#salaryId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_salary"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemoveSalary")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove Salary", 5000);
                    dtSalary.ajax.reload();
                    $("#modalRemoveSalary").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove Salary", 5000);
                }

                $("#modalRemoveSalary")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditSalary", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_salary_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-edit_salary",
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
                                            "Employee salary details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtSalary.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating salary details!",
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

$(document).on("click", ".btnNewSalary", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_salary_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-add_salary",
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
                                            "Employee salary details has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtSalary.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding salary details!",
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

if (typeof tableSalaryArchivedList !== "undefined") {
    var search_val = "";
    var dtSalaryArchived = tableSalaryArchivedList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_salary_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.is_archived = 1
            }
        },
        columns: [
            {data: "description", width: "67%"},
            {data: "created_by", width: "25%"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempArchiveDataTableActions(row.id);
            }
        },
            {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });

    function tempArchiveDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestoreSalary'" +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Restore Salary'" +
                "   data-delay='{\"show\": 300}'" +
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
        dtSalaryArchived.ajax.reload();
    });

    $(document).on("click", ".btnRestoreSalary", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreSalary");
        modalWindowRemove.find("input#salaryId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentSalary", function () {
        var dataId = $("#modalRestoreSalary").find("input#salaryId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_salary"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestoreSalary")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore Salary", 5000);
                        dtSalaryArchived.ajax.reload();
                        $("#modalRestoreSalary").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore Salary", 5000);
                    }
    
                    $("#modalRestoreSalary")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}