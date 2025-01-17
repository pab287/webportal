var modalWindow = $("#modalTempContent");
var tablePositionList = $("#table-position");
var tablePositionArchivedList = $("#table-archived-position");
var search_val = "";

if (typeof tablePositionList !== "undefined") {
    var search_val = "";
    var dtPosition = tablePositionList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_position_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {data: "name", width: "35%"},
            {data: "type", width: "17%"},
            {data: "created_by", width: "20%"},
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
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditPosition' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-original-title='Edit Position'" +
                "   data-skin='dark'" +
                "   data-delay='{\"show\": 300}'" +
                "   data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemovePosition' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-original-title='Archive Position'" +
                "   data-skin='dark'" +
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
        dtPosition.ajax.reload();
    });
}



$(document).on("click", ".btnRemovePosition", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemovePosition");
    modalWindowRemove.find("input#positionId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentPosition", function () {
    var dataId = $("#modalRemovePosition").find("input#positionId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_position"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemovePosition")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove Position", 5000);
                    dtPosition.ajax.reload();
                    $("#modalRemovePosition").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove Position", 5000);
                }

                $("#modalRemovePosition")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditPosition", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_position_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#type").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    if (typeof json.data !== "undefined") {
                        var tempData = json.data;
                        select2Data.val(tempData.type).trigger("change");
                    }
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });
                    modalWindow.modal("show");


                    ClassicEditor
                    .create( document.querySelector( '#qualification' ) )
                    .then( editor => {
                    } )
                    .catch( error => {
                    } );

                    ClassicEditor
                    .create( document.querySelector( '#job_desc' ) )
                    .then( editor => {
                    } )
                    .catch( error => {
                    } );
                   
                    $.validate({
                        form: "#form-edit_position",
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
                                        dtPosition.ajax.reload();
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

$(document).on("click", ".btnNewPosition", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_position_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");
                    
                    ClassicEditor
                    .create( document.querySelector( '#qualification' ) )
                    .then( editor => {
                        editor.ui.view.editable.element.style.height = '100px';
                        editor.data.set("**nothing follows**");
                    } )
                    .catch( error => {
                    } );
                    
                    

                    ClassicEditor
                    .create( document.querySelector( '#job_desc' ) )
                    .then( editor => {
                        editor.ui.view.editable.element.style.height = '100px';
                        editor.data.set("**nothing follows**");
                    } )
                    .catch( error => {
                    } );
                    
                    var select2Data = modalWindow.find("select#type").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });

                    $.validate({
                        form: "#form-add_position",
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
                                            "Employee position details has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtPosition.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding position details!",
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
// alert('asdadasd');

if (typeof tablePositionArchivedList !== "undefined") {
    var dtPositionArchived = tablePositionArchivedList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_position_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.is_archived = 1
            }
        },
        columns: [
            {data: "name", width: "35%"},
            {data: "type", width: "17%"},
            {data: "created_by", width: "20%"},
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
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestorePosition' " +
                "   data-toggle='m-tooltip'" +
                "   data-placement='bottom'" +
                "   data-original-title='Restore Position'" +
                "   data-skin='dark'" +
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
        dtPositionArchived.ajax.reload();
    });

    $(document).on("click", ".btnRestorePosition", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestorePosition");
        modalWindowRemove.find("input#positionId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentPosition", function () {
        var dataId = $("#modalRestorePosition").find("input#positionId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_position"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestorePosition")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Remove Position", 5000);
                        dtPositionArchived.ajax.reload();
                        $("#modalRestorePosition").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Remove Position", 5000);
                    }
    
                    $("#modalRestorePosition")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}
