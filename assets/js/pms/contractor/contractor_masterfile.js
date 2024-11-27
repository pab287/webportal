var modalWindow = $("#modalTempContent");
var tableContractorList = $("#table-contractor");

var search_val = "";
var dtContractor = tableContractorList.DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ordering: false,
    ajax: {
        url: baseUrl("pms/contractor/get_contractor_datatable_request"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
        }
    },
    columns: [
        {data: "contractor", width: "20%"},
        {data: "project", width: "20%"},
        {data: "company", width: "15%"},
        {data: "representative", width: "17%"},
        {data: "phone", width: "10%"},
        {
            data: "status",
            width: "10%",
            render: function (data, type, row) {
                switch (data) {
                    case "inactive":
                        return "<span class='m-badge m-badge--danger px-2'>Inactive</span>";
                        break;
                    case "blacklisted":
                        return "<span class='m-badge m-badge--metal px-2'>Blacklisted</span>";
                        break;
                    default:
                        return "<span class='m-badge m-badge--success px-2'>Active</span>";
                }
            }
        },
        {data: null, width: "8%", className: "text-center"}
    ],
    columnDefs: [{
        data: null,
        defaultContent: "",
        targets: -1,
        orderable: false,
        render: function (data, type, row, meta) {
            return tempDataTableActions(row.id, row.status);
        }
    }, {
        targets: "_all",
        defaultContent: ""
    }]
});

function tempDataTableActions($id, status) {
    if ($id) {
        var _actionButton = "";
        _actionButton +=
            " <button type='button' " +
            "   data-toggle='m-tooltip'" +
            "   data-placement='bottom'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-original-title='Edit Contractor'" +
            "   class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
            "   m-btn--icon-only m-btn--pill btnEditContractor' data-id='" + $id + "'>" +
            "   <i class='la la-edit'></i>" +
            " </button>";
        _actionButton +=
            " <button type='button' " +
            "   data-toggle='m-tooltip'" +
            "   data-placement='bottom'" +
            "   data-skin='dark'" +
            "   data-delay='{\"show\": 300}'" +
            "   data-original-title='Remove Contractor'" +
            "   class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only " +
            "   m-btn--pill btnRemoveContractor' data-id='" + $id + "'" +
            "   data-status='" + status + "'>" +
            "   <i class='la la-file-archive-o'></i>" +
            " </button>";
        return _actionButton;
    } else {
        return false;
    }
}

$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    dtContractor.ajax.reload();
});

$(document).on("click", ".btnRemoveContractor", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    const status = _self.data("status");
    var modalWindowRemove = $("#modalRemoveContractor");

    const el = $("#modal-alert").find("#archive-message");
    checkIfContractorIsAssigned(dataId)
        .then((data) => {
            const contractorCtr = parseInt(data.count.toString());
            if (contractorCtr > 0) {
                el.html('' +
                    'Contractor: ' +
                    '<span id="contractor" class="m--font-boldest text-primary">' + data.contractor + '</span> ' +
                    'has an existing contract.');
                $("#modal-alert").modal("show");
            } else if (status === 'active') {
                el.html('' +
                    'Contractor: ' +
                    '<span id="contractor" class="m--font-boldest text-primary">' + data.contractor + '</span> ' +
                    'is still <span class="m--font-bolder text-success">active</span>.');
                $("#modal-alert").modal("show");
            } else {
                modalWindowRemove.find("input#contractorId").val(dataId);
                modalWindowRemove.modal("show");
            }
        });
});

function checkIfContractorIsAssigned(id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: baseUrl("pms/contractor/check_if_contractor_is_assigned/" + id),
            type: "GET",
            dataType: "JSON",
            success: resolve,
            error: reject
        })
    });
}

$(document).on("click", ".btnRemoveCurrentContractor", function () {
    var dataId = $("#modalRemoveContractor").find("input#contractorId").val();
    if (typeof dataId !== "undefined" && dataId !== 0) {
        $.ajax({
            url: baseUrl("pms/contractor/remove_current_contractor"),
            type: "post",
            dataType: "json",
            data: {csrf_token: _csrf_hash, id: dataId},
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
        url: baseUrl("pms/contractor/get_contractor_modal_content/edit"),
        type: "post",
        dataType: "json",
        data: {csrf_token: _csrf_hash, id: dataId},
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#company").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });

                    if (typeof json.data !== "undefined") {
                        var tempData = json.data;
                        select2Data.val(tempData.company).trigger("change");
                    }
                    select2Data.on("change", function (e) {
                        var self = $(e.target);
                        self.validate();
                    });

                    $("#status")
                        .select2({
                            placeholder: "Select an Option",
                            width: "100%",
                            dropdownParent: modalWindow
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
        url: baseUrl("pms/contractor/get_contractor_modal_content/add"),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                if (typeof modalWindow !== "undefined" && modalWindow.length == 1) {
                    var tempModalContent = modalWindow.find("#modalTempContainer");
                    tempModalContent.empty().html(json.html);
                    var select2Data = modalWindow.find("select#company").select2({
                        width: "100%",
                        placeholder: "Select an option",
                        dropdownParent: modalWindow
                    });
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