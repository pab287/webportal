var modalWindow = $("#modalTempContent");
var tableLicenseList = $("#table-license");
var tableLicenseArchivedList = $("#table-archived-license");
var search_val = "";

var type = [
    // { id: "-1", text: "Select an Option" },
    { id: "COMPANY SPONSORED - INTERNAL", text: "COMPANY SPONSORED - INTERNAL" },
    { id: "COMPANY SPONSORED - EXTERNAL", text: "COMPANY SPONSORED - EXTERNAL" },
    { id: "PERSONAL", text: "PERSONAL" },
];

if (typeof tableLicenseList !== "undefined") {
    var search_val = "";
    var dtLicense = tableLicenseList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_license_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {data: "code", width: "10%"},
            {data: "type", width: "20%"},
            {data: "description", width: "25%"},
            {data: "created_by", width: "17%"},
            {data: "add_date", width: "17%"},
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
            
            if(_currentActions.includes("edit")){
                _actionButton +=
                    " <button type='button' " +
                    "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditLicense' " +
                    "   data-toggle='m-tooltip'" +
                    "   data-placement='bottom'" +
                    "   data-skin='dark'" +
                    "   data-delay='{\"show\": 300}'" +
                    "   data-original-title='Edit license'" +
                    "   data-id='" +
                    $id +
                    "'><i class='la la-edit'></i></button>";
            }

            if(_currentActions.includes("delete") || _currentActions.includes("archive")){
                _actionButton +=
                    " <button type='button' " +
                    "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveLicense' " +
                    "   data-toggle='m-tooltip'" +
                    "   data-placement='bottom'" +
                    "   data-skin='dark'" +
                    "   data-delay='{\"show\": 300}'" +
                    "   data-original-title='Remove license'" +
                    "   data-id='" +
                    $id +
                    "'><i class='la la-file-archive-o'></i></button>";
            }

            if(_actionButton == ''){
                _actionButton = ' --- ';
            }

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtLicense.ajax.reload();
    });
}

$(document).on("click", ".btnNewLicense", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_license_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");

                    tempModalContent.find("#select2_type").select2({
                        placeholder: { id: -1, text: "Select an Option" },
                        width: "100%",
                        data: type
                    });

                    $.validate({
                        form: "#form-add_license",
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
                                            "License Type has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtLicense.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding license type!",
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

$(document).on("click", ".btnRemoveLicense", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveLicense");
    modalWindowRemove.find("input#licenseId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentLicense", function () {
    var dataId = $("#modalRemoveLicense").find("input#licenseId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_license"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemoveLicense")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove License", 5000);
                    dtLicense.ajax.reload();
                    $("#modalRemoveLicense").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove License", 5000);
                }

                $("#modalRemoveLicense")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditLicense", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_license_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    if (typeof json.data !== "undefined") {
                        var tempData = json.data;
                        
                        if(tempData.type && typeof tempData.type !== 'undefined'){
                            var option = new Option(tempData.type, tempData.type, true, false);
                            tempModalContent.find("#select2_type").append(option).trigger('change');
                        }
                        
                        tempModalContent.find("#select2_type").select2({
                            placeholder: { id: -1, text: "Select an Option" },
                            width: "100%",
                            data: type
                        });
                    }


                    modalWindow.modal("show");

                    $.validate({
                        form: "#form-edit_license",
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
                                            "License details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtLicense.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating License!",
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

if (typeof tableLicenseArchivedList !== "undefined") {
    var dtLicenseArchive = tableLicenseArchivedList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_license_datatable_request"),
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
            {data: "created_by", width: "17%"},
            {data: "add_date", width: "17%"},
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [{
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return employeeArchiveDataTableActions(row.id);
            }
        },
            {
                targets: "_all",
                defaultContent: ""
            }
        ]
    });

    function employeeArchiveDataTableActions($id) {
        if ($id) {
            var _actionButton = "";
            if(_currentActions.includes("delete") || _currentActions.includes("archive")){
                _actionButton +=
                    " <button type='button' " +
                    "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestoreLicense' " +
                    "   data-toggle='m-tooltip'" +
                    "   data-placement='bottom'" +
                    "   data-skin='dark'" +
                    "   data-delay='{\"show\": 300}'" +
                    "   data-original-title='Archive license'" +
                    "   data-id='" +
                    $id +
                    "'><i class='la la-reply'></i></button>";
            }

            if(_actionButton == ''){
                _actionButton = ' --- ';
            }

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtLicenseArchive.ajax.reload();
    });

    $(document).on("click", ".btnRestoreLicense", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreLicense");
        modalWindowRemove.find("input#licenseId").val(dataId);
        modalWindowRemove.modal("show");
    });

    $(document).on("click", ".btnRestoreCurrentLicense", function () {
        var dataId = $("#modalRestoreLicense").find("input#licenseId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_license"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestoreLicense")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore License", 5000);
                        dtLicenseArchive.ajax.reload();
                        $("#modalRestoreLicense").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore License", 5000);
                    }
    
                    $("#modalRestoreLicense")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}
