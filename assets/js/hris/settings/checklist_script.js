var checklist = $("#table-checklist");
var archivedChecklist = $("#table-archived-checklist");
var search_val = '';
var modalWindow = $("#modalTempContent");
var tbl, archived;

if(checklist !== 'undefined'){
    tbl = checklist.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl("hris/settings/get_checklist_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "name", },
            { data: "description", },
            { data: "added_dt" },
            { data: null, width: "10%", className: "text-center" }
        ],
        columnDefs: [
            {
                targets: 2,
                render: function(data, type, row, meta){
                    var html = '';

                    html += '<p class="m-0">'+row.emp_add+'</p>';
                    html += '<p class="m-0"><small>'+moment(data).format('LLL')+'</small></p>';

                    return html;
                }
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return checklistDataTableActions(row.id);
                }
            }
        ]
    });

    function checklistDataTableActions(id){
        if (id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEditChecklist' " +
                "   data-toggle='modal' data-target='#edit_modal' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Edit Checklist'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnRemoveChecklist' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Archive Checklist'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-file-archive-o'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", "#btnNewChecklist", function () {
        $.ajax({
            url: baseUrl("hris/settings/get_checklist_modal_content/add"), 
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                        var tempModalContent = modalWindow.find("#modalTempContainer");
                        tempModalContent.empty().html(json.html);
                        modalWindow.modal("show");

                        $.validate({
                            form: "#form-add_checklist",
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
                                            toastr.success(json.toastr_msg, "Checklis has been saved.", 5000);
                                            currentForm.reset();
                                            modalWindow.modal("hide");
                                            tbl.ajax.reload();
                                        } else {
                                            toastr.error(json.toastr_msg, "Error adding Checklis!", 5000);
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

    $(document).on("click", ".btnRemoveChecklist", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRemoveChecklist");
        modalWindowRemove.find("input#checklistId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRemoveCurrentChecklist", function () {
        var dataId = $("#modalRemoveChecklist").find("input#checklistId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/settings/remove_current_checklist"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRemoveChecklist")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Archive Checklist", 5000);
                        tbl.ajax.reload();
                        $("#modalRemoveChecklist").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Archive Checklist", 5000);
                    }
    
                    $("#modalRemoveChecklist")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
    
    $(document).on("click", ".btnEditChecklist", function () {
        var _self = $(this);
        var dataId = _self.data("id");
    
        $.ajax({
            url: baseUrl("hris/settings/get_checklist_modal_content/edit"),
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
                            form: "#form-edit_checklist",
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
                                            toastr.success(json.toastr_msg, "Update Checklist", 5000);
    
                                            currentForm.reset();
                                            modalWindow.modal("hide");
                                            tbl.ajax.reload();
                                        } else {
                                            toastr.error(json.toastr_msg, "Update Checklist!", 5000);
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

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        tbl.ajax.reload();
    });
}

if(archivedChecklist !== 'undefined'){
    archived = archivedChecklist.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ajax: {
            url: baseUrl("hris/settings/get_archived_checklist_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            { data: "name", },
            { data: "description", },
            { data: "added_dt" },
            { data: null, width: "10%", className: "text-center" }
        ],
        columnDefs: [
            {
                targets: 2,
                render: function(data, type, row, meta){
                    var html = '';

                    html += '<p class="m-0">'+row.emp_add+'</p>';
                    html += '<p class="m-0"><small>'+moment(data).format('LLL')+'</small></p>';

                    return html;
                }
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return archivedChecklistDataTableActions(row.id);
                }
            }
        ]
    });

    function archivedChecklistDataTableActions(id){
        if (id) {
            var _actionButton = "";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnRestoreChecklist' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Restore Checklist'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                id +
                "'><i class='la la-reply'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $(document).on("click", ".btnRestoreChecklist", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreChecklist");
        modalWindowRemove.find("input#checklistId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentChecklist", function () {
        var dataId = $("#modalRestoreChecklist").find("input#checklistId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/settings/restore_current_checklist"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestoreChecklist")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore Checklist", 5000);
                        archived.ajax.reload();
                        $("#modalRestoreChecklist").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore Checklist", 5000);
                    }
    
                    $("#modalRestoreChecklist")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        archived.ajax.reload();
    });
}