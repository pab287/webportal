var modalWindow = $("#modalTempContent");
var tableCompanyList = $("#table-company");
var tableArchivedCompanyList = $("#table-archive-company");
var search_val = "";

if (typeof tableCompanyList !== "undefined") {
    var search_val = "";
    var dtCompany = tableCompanyList.DataTable({
        
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_company_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                    d.search['value'] = search_val
            }
        },
        columns: [
            {
                data: "logo",
                width: "5%",
                className: "text-center"
            },
            {data: "code", width: "15%"},
            {data: "description"},
            {
                width: "13%",
                data: "work_days_in_year",
                className: "text-center"
            },
            {
                data: "sss_class",
                render: function (data, type, row) {
                    if (parseInt(data) === 1) {
                        return `EMPLOYED, SELF-EMPLOYED & ETC.`;
                    } else {
                        return `HOUSEHOLD EMPLOYERS & KASAMBAHAY`;
                    }
                }
            },
            {
                data: "email_to"
            },
            {   
                data: "cc_to"
            },
            {   
                data: "bcc_to"
            },
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [
            {
                data: "logo",
                targets: 0,
                render: function (data, type, row, meta) {
                    var _html =
                        "<div clas='m-card-profile__pic-wrapper'><img class='m--img-rounded m--marginless m--img-centered user__pic' src='" +
                        data +
                        "' /></div>";
                    return _html;
                }
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return employeeDataTableActions(row.id);
                }
            },
            {
                data: "email_to",
                targets: 5,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
                }
            },
            {
                data: "cc_to",
                targets: 6,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
                }
            },
            {
                data: "bcc_to",
                targets: 7,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
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
                "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditCompany' onclick=edit_template('"+$id+"') " +
                "   data-toggle='modal' data-target='#edit_modal' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Edit Company'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                $id +
                "'><i class='la la-edit'></i></button>";
            _actionButton +=
                " <button type='button' " +
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRemoveCompany' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Archive Company'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                $id +
                "'><i class='la la-file-archive-o'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtCompany.ajax.reload();
    });
}


$(document).on("click", ".btnRemoveCompany", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    var modalWindowRemove = $("#modalRemoveCompany");
    modalWindowRemove.find("input#companyId").val(dataId);
    modalWindowRemove.modal("show");
});

$(document).on("click", ".btnRemoveCurrentCompany", function () {
    var dataId = $("#modalRemoveCompany").find("input#companyId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("hris/masterfile/remove_current_company"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
            beforeSend: function () {
                $("#modalRemoveCompany")
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }, success: function (json) {
                if (json.response) {
                    toastr.info(json.toastr_msg, "Remove Company", 5000);
                    dtCompany.ajax.reload();
                    $("#modalRemoveCompany").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Remove Company", 5000);
                }

                $("#modalRemoveCompany")
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        })
    }
});

$(document).on("click", ".btnEditCompany", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("hris/masterfile/get_company_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");
                    edit_template(dataId);
                    tempModalContent.find("#edit_email_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    }); 
                    tempModalContent.find("#edit_cc_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    });
                    tempModalContent.find("#edit_bcc_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    });
                    
                    var url = baseUrl("hris/masterfile/temp_upload_company_file/true");
                    $("#edit-fileupload_logo")
                        .fileupload({
                            url: url,
                            dataType: "json",
                            formData: {csrf_token: _csrf_hash},
                            done: function (e, data) {
                                var result = data.result;
                                if (result.response) {
                                    tempModalContent.find("#logo_attachment").val(result.filename);
                                    tempModalContent.find("#temp_fileupload").empty().text(result.filename);
                                    toastr.success(result.toastr_msg, "Upload Company Logo File", 5000);
                                } else {
                                    toastr.error(result.toastr_msg, "Upload Company Logo File", 5000);
                                }
                            }
                        })
                        .prop("disabled", !$.support.fileInput)
                        .parent()
                        .addClass($.support.fileInput ? undefined : "disabled");

                    $.validate({
                        form: "#form-edit_company",
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
                                            "Employee company details has been updated.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtCompany.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error updating company details!",
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

$(document).on("click", ".btnNewCompany", function () {
    $.ajax({
        url: baseUrl("hris/masterfile/get_company_modal_content/add"),
        dataType: "json",
        success: function (json) {
            console.log();
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    modalWindow.modal("show");
                    tempModalContent.find("#email_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    }); 
                    tempModalContent.find("#cc_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    });
                    tempModalContent.find("#bcc_to").select2({
                        placeholder: 'Select. .',
                        width: '100%',
                        multiple: true,
                        dataType: "json",
                        delay: 250,
                        ajax: {
                          global: false,
                          url: baseUrl("hris/masterfile/email_lookup_company"),
                          processResults: function (data) {
                            return data;
                          }
                        }
                    });
                    var url = baseUrl("hris/masterfile/temp_upload_company_file");
                    $("#fileupload_logo")
                        .fileupload({
                            url: url,
                            dataType: "json",
                            formData: {csrf_token: _csrf_hash},
                            done: function (e, data) {
                                var result = data.result;
                                if (result.response) {
                                    tempModalContent.find("#logo_attachment").val(result.filename);
                                    tempModalContent.find("#temp_fileupload").empty().text(result.filename);
                                    
                                    toastr.success(result.toastr_msg, "Upload Company Logo File", 5000);
                                } else {
                                    toastr.error(result.toastr_msg, "Upload Company Logo File", 5000);
                                }
                            }
                        })
                        .prop("disabled", !$.support.fileInput)
                        .parent()
                        .addClass($.support.fileInput ? undefined : "disabled");

                    $.validate({
                        form: "#form-add_company",
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
                                            "Employee company details has been saved.",
                                            5000
                                        );
                                        currentForm.reset();
                                        modalWindow.modal("hide");
                                        dtCompany.ajax.reload();
                                    } else {
                                        toastr.error(
                                            json.toastr_msg,
                                            "Error adding company details!",
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


function edit_template($id){
    $.ajax({
        url: baseUrl("hris/masterfile/edit_details_company/") +$id,
        type: "GET",
        success: function(data){
            $("#edit_email_to").empty();
            for (i = 0; i < data.email_to.length; i++) {
                var edit_email_to = new Option(data.email_to[i], data.email_to[i], true, true);
                $('#edit_email_to').append(edit_email_to);  
            }
            $("#edit_cc_to").empty();
            if(typeof data.cc_to != 'undefined' && data.cc_to != null){
                for (i = 0; i < data.cc_to.length; i++) {
                    var edit_cc_to = new Option(data.cc_to[i], data.cc_to[i], true, true);
                    $('#edit_cc_to').append(edit_cc_to);
                }    
            }
            
            $("#edit_bcc_to").empty();
            if(typeof data.bcc_to != 'undefined' && data.bcc_to != null){
                for (i = 0; i < data.bcc_to.length; i++) {
                    var edit_bcc_to = new Option(data.bcc_to[i], data.bcc_to[i], true, true);
                    $('#edit_bcc_to').append(edit_bcc_to);
                }
            }
           
        }
    });
}

// archived
if(typeof tableArchivedCompanyList !== 'undefined'){
    var dtCompanyArchived = tableArchivedCompanyList.DataTable({
        
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("hris/masterfile/get_company_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.is_archived = 1
            }
        },
        columns: [
            {
                data: "logo",
                width: "5%",
                className: "text-center"
            },
            {data: "code", width: "15%"},
            {data: "description"},
            {
                width: "13%",
                data: "work_days_in_year",
                className: "text-center"
            },
            {
                data: "sss_class",
                render: function (data, type, row) {
                    if (parseInt(data) === 1) {
                        return `EMPLOYED, SELF-EMPLOYED & ETC.`;
                    } else {
                        return `HOUSEHOLD EMPLOYERS & KASAMBAHAY`;
                    }
                }
            },
            {
                data: "email_to"
            },
            {   
                data: "cc_to"
            },
            {   
                data: "bcc_to"
            },
            {data: null, width: "8%", className: "text-center"}
        ],
        columnDefs: [
            {
                data: "logo",
                targets: 0,
                render: function (data, type, row, meta) {
                    var _html =
                        "<div clas='m-card-profile__pic-wrapper'><img class='m--img-rounded m--marginless m--img-centered user__pic' src='" +
                        data +
                        "' /></div>";
                    return _html;
                }
            },
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return employeeArchivedDataTableActions(row.id);
                }
            },
            {
                data: "email_to",
                targets: 5,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
                }
            },
            {
                data: "cc_to",
                targets: 6,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
                }
            },
            {
                data: "bcc_to",
                targets: 7,
                orderable: false,
                render: function (data, type, row, meta) {
                    return "<div style='white-space: normal;width: 200px; word-wrap: break-word'>" + data + "</div>";
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
                "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnRestoreCompany' " +
                "   data-toggle='m-tooltip' " +
                "   data-placement='bottom'" +
                "   data-skin='dark'" +
                "   data-original-title='Restore Company'" +
                "   data-delay='{\"show\": 300}' data-id='" +
                $id +
                "'><i class='la la-reply'></i></button>";

            return _actionButton;
        } else {
            return false;
        }
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtCompanyArchived.ajax.reload();
    });

    $(document).on("click", ".btnRestoreCompany", function () {
        var _self = $(this);
        var dataId = _self.data("id");
        var modalWindowRemove = $("#modalRestoreCompany");
        modalWindowRemove.find("input#companyId").val(dataId);
        modalWindowRemove.modal("show");
    });
    
    $(document).on("click", ".btnRestoreCurrentCompany", function () {
        var dataId = $("#modalRestoreCompany").find("input#companyId").val();
        if (typeof dataId !== "undefined" && dataId !== 0) {
            $.ajax({
                url: baseUrl("hris/masterfile/restore_current_company"),
                type: "post",
                dataType: "json",
                data: {csrf_token: _csrf_hash, id: dataId},
                beforeSend: function () {
                    $("#modalRestoreCompany")
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }, success: function (json) {
                    if (json.response) {
                        toastr.info(json.toastr_msg, "Restore Company", 5000);
                        dtCompanyArchived.ajax.reload();
                        $("#modalRestoreCompany").modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Restore Company", 5000);
                    }
    
                    $("#modalRestoreCompany")
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                }
            })
        }
    });
}
// archived