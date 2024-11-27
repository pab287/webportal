var tempModal = new contractListModal();
var modalExtensionApproval;
var vmExtensionApproval;

var tableContract = $("#table-contract");
var tableContractExtension = $("#table-contract_extension");
var modalAddContract = $("#modal-add_contract");

if (typeof tableContractExtension !== "undefined") {
    var search_val_extension = "";
    var dtContractExtension = tableContractExtension.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_contract_extension_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val_extension;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtContractExtension.ajax.reload(null, false);
                }
            }
        },
        columns: [
            { data: "wo_code", width: "15%" },
            { data: "due_date", width: "10%", className: "text-center" },
            { data: "extension_date", width: "10%", className: "text-center" },
            { data: "created_name" },
            { data: "extended_name", width: "20%" },
            { data: "extension_status", width: "8%", className: "text-center" },
            { data: null, width: "8%", className: "text-center" }
        ],
        columnDefs: [{
            data: "wo_code",
            defaultContent: "",
            targets: 0,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                tempHtml += "<div class='custom-details'>";
                tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                tempHtml += "<p class='m--marginless'><small class='m--font-boldest'>" + row.contractor + "</small></p>";
                tempHtml += "</div>";
                return tempHtml;
            }
        }, {
            data: "due_date",
            defaultContent: "",
            targets: 1,
            orderable: false,
            render: function (data, type, row, meta) {
                var currentDate = moment().format("YYYY-MM-DD");
                var dueDate = moment(data).format("YYYY-MM-DD");
                if (currentDate >= dueDate) {
                    var tempHtml = "<span class='m--font-danger m--font-boldest'>" + dueDate + "</span>";
                    return tempHtml;
                } else {
                    return dueDate;
                }
            }
        }, {
            data: "created_name",
            defaultContent: "---",
            targets: 3,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                tempHtml += "<div class='custom-details'>";
                tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                tempHtml += "<p class='m--marginless'><small class='m--font-boldest'> REMARKS: <span class='m--font-brand'>" + row.remarks + "</span></small></p>";
                tempHtml += "<p class='m--marginless'><small class='m--font-boldest'>" + row.created_at + "</small></p>";
                tempHtml += "</div>";
                return tempHtml;
            }
        }, {
            data: "extended_name",
            defaultContent: "---",
            targets: 4,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if (row.extended_by !== "0") {
                    tempHtml += "<div class='custom-details'>";
                    tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                    tempHtml += "<p class='m--marginless'><small class='m--font-boldest'> REMARKS: <span class='m--font-brand'>" + row.extended_remarks + "</span></small></p>";
                    tempHtml += "<p class='m--marginless'><small>" + row.extended_at + "</small></p>";
                    tempHtml += "</div>";
                } else {
                    tempHtml = "---";
                }
                return tempHtml;
            }
        }, {
            data: "extension_status",
            defaultContent: "",
            targets: -2,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempExtensionStatus(data);
            }
        }, {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                if (row.extension_status == 0) { return tempExtensionActions(row.id); }
                else if (row.extension_status == 1) { return "<a href='javascript:void(0);' class='btn btn-sm btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill m-btn--air'><i class='la la-thumbs-up m--font-success'></i></a>"; }
                else if (row.extension_status == 2) { return "<a href='javascript:void(0);' class='btn btn-sm btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill m-btn--air'><i class='la la-thumbs-down'></i></a>"; }
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }]
    });

    function tempExtensionStatus($status) {
        var _html = "<span class='m-badge m-badge--warning m-badge--wide m--font-light'> PENDING </span>";
        switch ($status) {
            case "1": _html = "<span class='m-badge m-badge--custom-wide m-badge--success m-badge--wide m--font-light'> APPROVED </span>"; break;
            case "2": _html = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide m--font-light'> DISAPPROVED </span>"; break;
            default: _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light'> PENDING </span>"; break;
        }
        return _html;
    }

    function tempExtensionActions($id) {
        if ($id) {
            var _actionButton = "";
            if (jQuery.inArray("approve_action", _currentActions) !== -1) {
                _actionButton +=
                    "<button type='button' class='btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnApproveExtension btnApprove' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Approve Contract Extension' data-type='Approve' data-id='" + $id + "'><i class='la la-thumbs-up'></i></button>";
            }
            if (jQuery.inArray("disapprove_action", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDisapproveExtension btnDiapprove' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Disapprove Contract Extension' data-type='Disapprove' data-id='" +
                    $id +
                    "'><i class='la la-thumbs-down'></i></button>";
            }
            return (_actionButton) ? _actionButton : "---";
        } else {
            return false;
        }
    }

    $('#generalSearchExtension').donetyping(function (callback) {
        search_val_extension = $(this).val();
        dtContractExtension.ajax.reload();
    });

    $(document).on("click", ".btnApproveExtension, .btnDisapproveExtension", function () {
        var dataId = $(this).data("id");
        var dataType = $(this).data("type");
        mapBlockUI(function () {
            $.ajax({
                url: baseUrl("pms/contract/get_contract_extension_data/" + dataId),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalExtensionApproval.modalShow(function () {
                            vmExtensionApproval.row = Object.assign({}, json.row);
                            vmExtensionApproval.type = dataType;
                            setTimeout(function () {
                                vmExtensionApproval.validateFields();
                            }, 500);
                        });
                    }
                }
            });
        });
    });
}

if (typeof tableContract !== "undefined") {
    var search_val = "";
    var dtContract = tableContract.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_contract_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtContract.ajax.reload(null, false);
                }
            }
        },
        columns: [
            { data: "wo_code", width: "15%" },
            { data: "reference_code", width: "15%" },
            { data: "issued_date", width: "10%", className: "text-center" },
            { data: "due_date", width: "10%", className: "text-center" },
            { data: "contractor" },
            { data: "task_incharge", width: "20%" },
            { data: "status", width: "8%", className: "text-center" },
            { data: null, width: "8%", className: "text-center" }
        ],
        columnDefs: [{
            data: "due_date",
            defaultContent: "",
            targets: 3,
            orderable: false,
            render: function (data, type, row, meta) {
                var currentDate = moment().format("YYYY-MM-DD");
                var dueDate = moment(data).format("YYYY-MM-DD");
                if (currentDate >= dueDate) {
                    var tempHtml = "<span class='m--font-danger m--font-boldest'>" + dueDate + "</span>";
                    return tempHtml;
                } else {
                    return dueDate;
                }
            }
        }, {
            data: "status",
            defaultContent: "",
            targets: -2,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDatatableStatus(data);
            }
        }, {
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
            if (jQuery.inArray("edit", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditContract' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Edit Contract' data-id='" + $id + "'><i class='la la-edit'></i></button>";
            }
            if (jQuery.inArray("assign", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnAssignContractProgress' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Assign Contract Progress' data-id='" + $id + "'><i class='la la-sliders'></i></button>";
            }
            if (jQuery.inArray("archive", _currentActions) !== -1) {
                _actionButton +=
                    " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnArchiveContract btnArchive' data-placement='bottom' data-toggle='m-tooltip' title='' data-original-title='Archive Contract' data-id='" +
                    $id +
                    "'><i class='la la-file-archive-o'></i></button>";
            }
            return _actionButton;
        } else {
            return false;
        }
    }

    function tempDatatableStatus($status) {
        var _html = "<span class='m-badge m-badge--warning m-badge--wide'> ACTIVE </span>";
        switch ($status) {
            case "1": _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light'> ACTIVE </span>"; break;
            case "2": _html = "<span class='m-badge m-badge--custom-wide m-badge--primary m-badge--wide'> COMPLETED </span>"; break;
            case "3": _html = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide'> TERMINATED </span>"; break;
            default: _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide'> ACTIVE </span>"; break;
        }

        return _html;
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtContract.ajax.reload();
    });

    $(document).on("click", ".btnNewContract", function () {
        mapBlockUI(function () {
            window.location.href = baseUrl("pms/contract/new_work_order");
        });
    });

    $(document).on("click", ".btnContractExtension", function () {
        mapBlockUI(function () {
            window.location.href = baseUrl("pms/contract/new_work_order");
        });
    });

    $(document).on("click", ".btnEditContract", function () {
        var dataId = $(this).data("id");
        mapBlockUI(function () {
            window.location.href = baseUrl("pms/contract/view_work_order/" + dataId);
        });
    });
    $(document).on("click", ".btnAssignContractProgress", function () {
        var dataId = $(this).data("id");
        mapBlockUI(function () {
            window.location.href = baseUrl("pms/contract/work_order_accomplishment/" + dataId);
        });
    });
}

var validateNewContract = function () {
    $.validate({
        form: "#frmAddContract",
        lang: "en",
        scrollToTopOnError: false,
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
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", true);
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(
                            json.toastr_msg,
                            "Contract data has been added.",
                            5000
                        );
                        if (typeof modalAddContract !== "undefined") {
                            modalAddContract.modal("hide");
                        }
                        dtContract.ajax.reload();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding contract data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        ).prop("disabled", true);
                }
            });
            return false;
        }
    });
}

jQuery(document).ready(function () {
    validateNewContract();
    modalExtensionApproval = tempModal.contract_extension_approval();
});