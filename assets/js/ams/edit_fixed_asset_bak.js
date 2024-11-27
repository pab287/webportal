let search_document = "";

var id = $("input[name=id]").val();

$('#add-document-asset-id').attr("value", id);

//get asset data
$.ajax({
    url: baseUrl("ams/assets/get_asset_data"),
    type: "POST",
    data: {id: id, csrf_token: _csrf_hash},
    success: function (data) {
        vmEditFrm.vm_edit_frm = Object.assign({}, data);
    }
});

var vmEditFrm = new Vue({
    el: "#frm_edit_fixed_asset",
    data: {vm_edit_frm: {}},
    mounted: function () {
        setTimeout(function () {
                var vmData = this.vmEditFrm.vm_edit_frm;

                //company select2
                var companyOption = new Option(vmData.company_description, vmData.company_code, true, true);
                $('#select2_company').append(companyOption).trigger('change');
                //department select2
                var departmentOption = new Option(vmData.department_description, vmData.department_code, true, true);
                $('#select2_department').append(departmentOption).trigger('change');
                //category select2
                var categoryOption = new Option(vmData.asset_description, vmData.asset_category, true, true);
                $('#select2_category').append(categoryOption).trigger('change');
                //type select2
                var typeOption = new Option(vmData.sub_cat_description, vmData.sub_cat_code, true, true);
                $('#select2_type').append(typeOption).trigger('change');
                //station select2
                var stationOption = new Option(vmData.station_description, vmData.area_id, true, true);
                $('#select2_station').append(stationOption).trigger('change');
                //location select2
                var locationOption = new Option(vmData.location_description, vmData.location, true, true);
                $('#select2_area').append(locationOption).trigger('change');
                //status select2
                var locationOption = new Option(vmData.status_description, vmData.status, true, true);
                $('#select2_status').append(locationOption).trigger('change');
            }
            , 400);
    }
});

// init select2 company
$("#select2_company").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_company_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

// init select2 department
$("#select2_department").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_department_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

// init select2 department
$("#select2_category").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_category_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

// init select2 location
$("#select2_station").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_location_collection"),
        processResults: function (data) {
            return data;
        }
    }
});

// init select2 location
$("#select2_area").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_area_collection"),
        processResults: function (data) {
            return data;
        }
    }
});

// init select2 location
$("#select2_status").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_status_collection"),
        processResults: function (data) {
            return data;
        }
    }
});


//init select2 type
$("#select2_type").select2({
    placeholder: 'Select option',
    width: '100%',
});

// init select2 department
$("#select2_category").select2({
    placeholder: 'Select option',
    width: '100%',
    ajax: {
        url: baseUrl("ams/assets/get_category_collection"),
        processResults: function (data) {
            return data;
        }

    }
});

$('#select2_category').on('select2:select', function (e) {
    var data = e.params.data;
    var id = data.id;
    $.ajax({
        url: baseUrl("ams/assets/type_lookup"),
        type: "POST",
        dataType: "JSON",
        data: {cat_id: data.cat_id, csrf_token: _csrf_hash},
        success: function (data) {
            $("#select2_type").select2("destroy");

            $("#select2_type option").each(function () {
                $(this).remove();
            });

            $("#select2_type").select2({placeholder: 'Select option', width: '100%',});
            $.each(data.results, function (key, value) {
                var newOption = new Option(value.text, value.id, false, true);
                $('#select2_type').append(newOption).trigger('change');
            });
        }
    });
});

//init component datatable
var tblAssetComponent = $("#table-asset-component")
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_components"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.id = id;
            }
        },
        searching: false,
        paging: false,
        lengthChange: false,
        info: false,
        columns: [
            {data: "asset_code"},
            {data: "description"},
            {data: "remarks"},
            {data: null, width: "8%", className: "text-center"},
        ],
        columnDefs: [
            {
                data: null,
                defaultContent: "",
                targets: -1,
                orderable: false,
                render: function (data, type, row, meta) {
                    /* updated this line of code from getComponentTblActions($id)
                    * to add asset-id as an attribute into button for delete reference */

                    let _actionButton = "";
                    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger " +
                        "m-btn--icon m-btn--icon-only m-btn--pill btnCancel' data-id='" + row.id + "' " +
                        "title='Exclude' data-asset-code='" + row.asset_code + "'>" +
                        "<i class='la la-reply'></i></button>";

                    return _actionButton;
                },
            },
            {
                targets: "_all",
                defaultContent: "",
            }
        ],
    });

//component tbl actions
function getComponentTblAction($id) {
    if ($id) {
        var _actionButton = "";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-accent " +
            "m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-edit'></i></button>";
        _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger " +
            "m-btn--icon m-btn--icon-only m-btn--pill btnCancel' data-id='" + $id + "'><i class='la la-trash'></i></button>";

        return _actionButton;
    } else {
        return false;
    }
}

//init accountability datatable
var tblAssetAccountability = $("#table-asset-accountability").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/assets/get_asset_accountability"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.id = id
        }
    },
    searching: false,
    paging: false,
    lengthChange: false,
    info: false,
    columns: [
        {data: "date_issued"},
        {data: "reference_no"},
        {data: "issued_to"},
        {data: "status"},
    ]
});

//init borrowing datatable
var tblAssetBorrowing = $("#table-asset-borrowing").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/assets/get_asset_borrowing"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.id = id
        }
    },
    searching: false,
    paging: false,
    lengthChange: false,
    info: false,
    columns: [
        {data: "transaction_date"},
        {data: "borrower"},
        {data: "status"},
        {data: "date"},
    ]
});


//init add component datatable mother
var tblAssetMother = $("#table-mother-asset-list").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/assets/get_mother_asset_collection"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.id = id
        }
    },
    autoWidth: false,
    columns: [
        {data: "assetacode", width: "15%"},
        {data: "description", width: "50%"},
        {data: "location", width: "15%"},
        {data: null, width: "10%"},
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return getIncludeMotherBtn(row.id);
        },
    },
        {
            targets: "_all",
            defaultContent: "",
        }],
});

function getIncludeMotherBtn($id) {
    return "<button type='button' data-id='" + $id + "' id='include-mother' " +
        "class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
        "m-btn--icon-only m-btn--pill btnEditItem'><i class='la la-check'></i></button>"
}

//init add component datatable component
var tblAssetComponentList = $("#table-asset-component-list").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("ams/assets/get_asset_component_collection"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.id = id
        }
    },
    autoWidth: false,
    columns: [
        {data: "assetacode", width: "15%"},
        {data: "description", width: "50%"},
        {data: "location", width: "15%"},
        {data: null, width: "10%"},
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return getIncludeComponentBtn(row.id);
        },
    },
        {
            targets: "_all",
            defaultContent: "",
        }],
});

function getIncludeComponentBtn(id) {
    return "<a href='" + id + "' " +
        "class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEditItem'>" +
        "<i class='la la-check'></i></a>";
}

$("#table-mother-asset-list").on("click", "#include-mother", function () {
    var id = $(this).attr("data-id");
    var asset_id = $("input[name=id]").val();

    $.ajax({
        url: baseUrl("ams/assets/save_include"),
        type: "POST",
        data: {id: id, asset_id: asset_id, csrf_token: _csrf_hash},
        success: function (data) {
            console.log(data);
            if (data.response) {
                toastr.success("Asset successfully added.", "Notification", 5000);
                tblAssetMother.ajax.reload();
                tblAssetComponentList.ajax.reload();
                tblAssetComponent.ajax.reload();
            } else {
                toastr.danger("Unable to processing request.", "Notification", 5000);
            }
        }
    })
});

$("#table-asset-component-list").on("click", "#include-component", function () {
    var id = $(this).attr("data-id");
    var asset_id = $("input[name=id]").val();

    $.ajax({
        url: baseUrl("ams/assets/save_include"),
        type: "POST",
        data: {id: id, asset_id: asset_id, csrf_token: _csrf_hash},
        success: function (data) {
            console.log(data);
            if (data.response) {
                toastr.success("Asset successfully added.", "Notification", 5000);
                tblAssetMother.ajax.reload();
                tblAssetComponentList.ajax.reload();
                tblAssetComponent.ajax.reload();
            } else {
                toastr.danger("Unable to processing request.", "Notification", 5000);
            }
        }
    })
});

//document file upload
/*$("#frm_add_document").submit(function(e) {
      $.ajax({
        url: baseUrl("ams/assets/upload_asset_document"),
        processData: false,
        type: "POST",
        beforeSend: function(){
          $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
        },
        success: function(data){
          if(data.status){
            toastr.success(data.toastr_msg, "Notification", 5000);
          }else{
            toastr.error(data.toastr_msg, "Notification", 5000);
          }

          $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

          e.preventDefault();
        }
      });

      return false;
});*/

//add document modal validate start
/*$.validate({
    form: '#frm_add_document',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: form[0].action,
            type: "POST",
            dataType: "json",
            data: $("#frm_add_document").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.status) {
                    toastr.success(data.toastr_msg, "Notification", 5000);
                    //window.location.replace(baseUrl("ams/assets/fixed_masterfile"));
                } else {
                    toastr.error(data.toastr_msg, "Notification", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});*/
//add document modal validate end


//form submit start
$.validate({
    form: '#frm_edit_fixed_asset',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: form[0].action,
            type: "POST",
            dataType: "json",
            data: $("#frm_edit_fixed_asset").find("input,select,textarea,checkbox").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data.status) {
                    toastr.success(data.toastr_msg, "Notification", 5000);
                    //window.location.replace(baseUrl("ams/assets/fixed_masterfile"));
                } else {
                    toastr.error(data.toastr_msg, "Notification", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});
//form submit end

loadImagePrimary(id);

//load primary image start
function loadImagePrimary(id) {
    $.ajax({
        url: baseUrl("ams/assets/ajax_uploaded_images"),
        type: "POST",
        data: {id: id, csrf_token: _csrf_hash},
        success: function (json) {
            if (json.html) {
                $(".custom-image_container").empty().append(json.html);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (errorThrown) {
                toastr.error("Mother Asset - Update", errorThrown);
            }
        }
    });
}

$("#btnChangeOpenModal").on("click", function () {
    $.ajax({
        url: baseUrl("ams/assets/load_modal_upload"),
        dataType: "json",
        type: "post",
        data: {itemId: id, is_asset: true, csrf_token: _csrf_hash},
        success: function (json) {
            $("#fileupload-modal_content").empty().append(json.html);
            $("#modalUploadImage").modal("show");
        }
    });
});

$('#add-document-file-attachment')
    .on('change', function (e) {
        const target = e.target.files[0];
        const filename = target ? target.name : null;
        $('#filename').attr("value", filename);
        $('#frm_add_document .custom-file-control')[0].innerHTML = filename;
    });

/* edwin */
/* start submit new asset document */
$('#frm_add_document').on('submit', function (e) {
    e.preventDefault();
    const form = $(this);
    const formData = new FormData(this);

    if ($(form).isValid()) {
        $.ajax({
            url: baseUrl("ams/assets/upload_asset_document"),
            type: "post",
            data: formData,
            dataType: "JSON",
            success: function (result) {
                if (result.success) {
                    toastr.success(result.message, "Notification", 5000);
                    $('#mdl_add_document').modal('hide');
                    $('#frm_add_document .custom-file-control')[0].innerHTML = "";
                    tableAssetDocuments.ajax.reload();
                    form.resetForm();
                } else {
                    toastr.error(result.message, "Notification", 5000);
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    }
});
/* end submit new asset document */

/* start get asset documents */
var tableAssetDocuments = $('#table-asset-documents')
    .DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("ams/assets/get_asset_documents"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.asset_id = id;
                d.search = {
                    value: search_document,
                    regex: false
                };
            }
        },
        searching: false,
        autoWidth: false,
        columns: [
            {data: "description"},
            {data: "filename"},
            {
                data: null, width: "8%",
                className: "text-center",
                render: function (data, type, row) {
                    let _actionButton = "";
                    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-primary " +
                        "m-btn--icon m-btn--icon-only m-btn--pill btnEdit' title='Edit' data-id='" + row.id + "'>" +
                        "<i class='la la-pencil-square-o'></i></button>";
                    _actionButton += " <button type='button' class='btn btn-default m-btn m-btn--hover-danger " +
                        "m-btn--icon m-btn--icon-only m-btn--pill btnCancel' title='Delete' data-id='" + row.id + "'>" +
                        "<i class='la la-trash-o'></i></button>";

                    return _actionButton;
                }
            },
        ],
    });
/* end get asset documents */

/* search an asset document */
$('#search-asset-document')
    .donetyping(function (val) {
        search_document = $(this).val();
        tableAssetDocuments.ajax.reload();
    });
/* end search an asset document */

/* remove an asset component from attachment of mother asset */
$("#table-asset-component")
    .on("click", ".btnCancel", function (e) {
        const element = $(this);
        const id = $(element).attr("data-id");
        const asset_id = $(element).attr("data-asset-id");
        const asset_code = $(element).attr("data-asset-code");
        const confirmDialog = $("#mdl-delete-asset-component-from-mother_asset");

        $('#frm-delete-asset-component-from-mother_asset .id').attr("value", id);
        $('#frm-delete-asset-component-from-mother_asset .asset-id').attr("value", asset_id);
        $('#frm-delete-asset-component-from-mother_asset .asset-code').attr("value", asset_code);
        const confirmationMessageContainer = $('#frm-delete-asset-component-from-mother_asset .confirmation-message-container');

        confirmationMessageContainer.html("<p style='line-height: 1.4em; " +
            "text-align: justify; text-transform: none; font-size: 18px;'" +
            ">Are you sure to exclude this asset as component with asset code " +
            "<strong>" + asset_code + "</strong>?</p>");

        confirmDialog.modal("show");
    });

$('#frm-delete-asset-component-from-mother_asset')
    .on("submit", function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const confirmDialog = $("#mdl-delete-asset-component-from-mother_asset");

        $.ajax({
            url: baseUrl("ams/assets/remove_asset_component"),
            type: "post",
            data: formData,
            dataType: "JSON",
            success: function (response) {
                if (response.success) {
                    tblAssetComponent.ajax.reload();
                    toastr.success(response.message, "Notification", 5000);
                } else {
                    toastr.error(response.message, "Error", 5000);
                }

                confirmDialog.modal("hide");
            }
        });
    });
/* end remove an asset component from attachment of mother asset */

/* edit an asset document */
$('#table-asset-documents')
    .on("click", ".btnEdit", function () {
        const asset_document_id = $(this).attr('data-id');
        openModal(baseUrl("ams/assets/open_modal"), "assets/modals/edit_document", {asset_document_id: asset_document_id});
    });
/* end edit an asset document */

/* delete an asset document */
$('#table-asset-documents')
    .on("click", ".btnCancel", function () {
        const asset_document_id = $(this).attr('data-id');
        $("#mdl-delete-document-confirmation").modal("show");
        $('#frm-delete-document .confirmation-message-container').html("<p style='line-height: 1.4em; " +
            "text-align: justify; text-transform: none; font-size: 18px;'" +
            ">Are you sure to delete this document?</p>");
        $("#frm-delete-document input[name='id']").attr("value", asset_document_id);
    });
/* delete an asset document */

/* delete asset document */
$("#frm-delete-document").on("submit", function (e) {
    e.preventDefault();
    const data = $(this).serializeArray();
    const id = data[1].value;

    $.ajax({
        url: baseUrl("ams/assets/delete_asset_document/?id=" + id),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, "Notification", 5000);
                $("#mdl-delete-document-confirmation").modal("hide");
                tableAssetDocuments.ajax.reload();
            } else {
                toastr.error(response.message, "Error", 5000);
            }
        }
    })
});
/* delete asset document */

/*  edit document */
$(document).on("change", "#edit-document-file-attachment",
    function (e) {
        const target = e.target.files[0];
        let filename = target ? target.name : "";
        let isFileUploaded = 0;
        if (filename === null || filename === '') {
            filename = $("input[name='current_file_name']").val();
            isFileUploaded = 0;
        } else {
            isFileUploaded = 1;
        }

        $('#frm-edit-document-is-file-updated').attr("value", isFileUploaded);
        $('#frm-edit-document .custom-file-control').html(filename);
    });

$(document).on("submit", "#frm-edit-document",
    function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = $(this).attr('action');

        $.ajax({
            url: baseUrl(url),
            type: "POST",
            dataType: "JSON",
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, "Notification", 5000);
                    tableAssetDocuments.ajax.reload();
                    closeModal();
                } else {
                    toastr.error(response.message, "Error", 5000);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        })
    });

/* end edit document */

function openModal(ctrlUrl, view_path, formData) {
    $.ajax({
        url: ctrlUrl,
        type: "POST",
        data: {
            path: view_path,
            csrf_token: _csrf_hash,
            function_name: "get_asset_document_details",
            formData
        },
        success: function (response) {
            const modal = $(".document-modal-container");
            modal.html(response);
            modal.modal("show");
        }
    })
};

function closeModal() {
    const modal = $(".document-modal-container");
    modal.modal("hide");
}

/* end edwin */





