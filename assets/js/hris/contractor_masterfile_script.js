var modalWindow = $("#modalTempContent");
var tableContractorList = $("#table-contractor");
var tableContractorArchivedList = $("#table-archived-contractor");
var search_val = "";

if (typeof tableContractorList !== "undefined") {
    var search_val = "";
    var dtContractor = tableContractorList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_contractor_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        columns: [
            { data: "contractor", width: "25%" },
            { data: "project", width: "25%" },
            { data: "company", width: "15%" },
            { data: "representative", width: "17%" },
            { data: "phone", width: "10%" },
            { data: null, width: "8%", className: "text-center" }
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row.id);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }]
    });

    function tempDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Edit Contractor'" +
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditContractor' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Archive Contractor'" +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveContractor' data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtContractor.ajax.reload();
    });
}

$(document).on("click", ".btnRemoveContractor", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveContractor");
    modalWindowRemove.find("input#contractorId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentContractor", function () {
    var dataId = $("#modalRemoveContractor").find("input#contractorId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_contractor"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, id: dataId },
            beforeSend: function () {
                $("#modalRemoveContractor")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove Contractor", 5000);
                    dtContractor.ajax.reload();
                    $("#modalRemoveContractor").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove Contractor", 5000);
                }

                $("#modalRemoveContractor")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditContractor", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_contractor_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash, id: dataId },
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#company").select2({ width: "100%", placeholder: "Select an option", dropdownParent: modalWindow });
                    if (typeof json.data !== "undefined") {
                        var tempData = json.data;
                        select2Data.val(tempData.company).trigger("change");
                    }
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });
                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-edit_contractor",
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
                                            "Contractor details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtContractor.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating contractor details!",
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

$(document).on("click", ".btnNewContractor", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_contractor_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#company").select2({ width: "100%", placeholder: "Select an option", dropdownParent: modalWindow });
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });

                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-add_contractor",
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
                                            "Contractor details has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtContractor.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding contractor details!",
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

if (typeof tableContractorArchivedList !== "undefined") {
    var search_val = "";
    var dtContractorArchived = tableContractorArchivedList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_contractor_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.is_archived = 1;
            }
        },
        columns: [
            { data: "contractor", width: "25%" },
            { data: "project", width: "25%" },
            { data: "company", width: "15%" },
            { data: "representative", width: "17%" },
            { data: "phone", width: "10%" },
            { data: null, width: "8%", className: "text-center" }
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableArchivedActions(row.id);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }]
    });

    function tempDataTableArchivedActions($id) {
        if ($id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-original-title='Restore Contractor'" +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestoreContractor' data-id='" +
                $id +
                "'><i class='la la-reply'></i></button>";
            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtContractorArchived.ajax.reload();
    });

    $(document).on("click", ".btnRestoreContractor", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreContractor");
        modalWindowRemove.find("input#contractorId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentContractor", function () {
        var dataId = $("#modalRestoreContractor").find("input#contractorId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_contractor"),
                type: "post",
                dataType: "json",
                data: { csrf_token: _csrf_hash, id: dataId },
                beforeSend: function () {
                    $("#modalRestoreContractor")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore Contractor", 5000);
                        dtContractorArchived.ajax.reload();
                        $("#modalRestoreContractor").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore Contractor", 5000);
                    }
    
                    $("#modalRestoreContractor")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}