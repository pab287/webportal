var modalContainer = $("#modal-container");
var tableChecklistItems = $("#table-checklist_item_qty");
var tableChecklistRequestQty = $("#table-checklist_requested_qty");
var _tempItemId = 0;
var renderTaskModal = new checklistModal();
var modalChecklistQtyHistory, dtChecklistHistory;

if (typeof tableChecklistRequestQty !== "undefined") {
    var search_request_val = "";
    var dtChecklistRequest = tableChecklistRequestQty.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/task/do_post_event/get_checklist_requested_qty_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_request_val;
                d.checklist_id = _tempContentData.id;
                return d;
            }
        },
        columns: [
            { data: "task", title: "Task Name", width: "*" },
            { data: "parent_task", title: "Parent Task", width: "15%" },
            { data: "qty", title: "Quantity", width: "10%", className: "text-center", defaultContent: "0" },
            { data: "encoded_by", title: "Encoded By", width: "20%" },
            { data: "approval_by", title: "Approved/Declined By", width: "20%" },
            { data: "approval_status", title: "Status", width: "8%", className: "text-center", defaultContent: "---" },
            { data: null, title: "Actions", width: "10%", className: "text-center" }
        ],
        columnDefs: [{
            data: "encoded_by",
            defaultContent: "--",
            targets: 3,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if (typeof data !== "undefined" && data) {
                    tempHtml += "<div class='custom-details'>";
                    tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                    if (typeof row.remarks !== "undefined" && row.remarks) {
                        tempHtml += "<p class='m--marginless'><small class='m--font-boldest'> REMARKS: <span class='m--font-brand'>" + row.remarks + "</span></small></p>";
                    }
                    tempHtml += "<p class='m--marginless'><small class='m--font-boldest'>" + row.created_at + "</small></p>";
                    tempHtml += "</div>";
                } else {
                    tempHtml = "---";
                }
                return tempHtml;
            }
        }, {
            data: "approval_by",
            defaultContent: "--",
            targets: 4,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if (typeof data !== "undefined" && data) {
                    tempHtml += "<div class='custom-details'>";
                    tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                    if (typeof row.remarks !== "undefined" && row.approval_remarks) {
                        tempHtml += "<p class='m--marginless'><small class='m--font-boldest'> REMARKS: <span class='m--font-brand'>" + row.approval_remarks + "</span></small></p>";
                    }
                    tempHtml += "<p class='m--marginless'><small class='m--font-boldest'>" + row.approval_date + "</small></p>";
                    tempHtml += "</div>";
                } else {
                    tempHtml = "---";
                }
                return tempHtml;
            }
        }, {
            data: "approval_status",
            defaultContent: "--",
            targets: -2,
            orderable: false,
            render: function (data, type, row, meta) {
                return (typeof data !== "undefined" && data) ? tempRequestStatus(data) : "---";
            }
        }, {
            data: null,
            defaultContent: "---",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempRequestQtyDataTableActions(row);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }
        ], drawCallback: function (settings) {
            var currentJson = settings.json;
            vmTempActions.count = currentJson.count;
        }
    });

    function tempRequestStatus($status) {
        var _html = "<span class='m-badge m-badge--warning m-badge--wide m--font-light'> PENDING </span>";
        switch ($status) {
            case "1": _html = "<span class='m-badge m-badge--custom-wide m-badge--success m-badge--wide m--font-light'> APPROVED </span>"; break;
            case "2": _html = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide m--font-light'> DECLINED </span>"; break;
            default: _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light'> PENDING </span>"; break;
        }
        return _html;
    }

    $(document).on("click", ".btnNewRequest", function () {
        $.ajax({
            url: baseUrl("pms/task/clear_temp_requested_qty/" + _tempContentData.id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    dtChecklistRequestQty.ajax.reload();
                    modalChecklistQtyRequest.modalShow();
                }
            }
        });
    });
}

if (typeof tableChecklistItems !== "undefined") {
    var search_val = "";
    var dtChecklist = tableChecklistItems.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/task/do_post_event/get_checklist_item_qty_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                d.checklist_id = _tempContentData.id;
                return d;
            }
        },
        columns: [
            { data: "task", width: "*" },
            { data: "parent_task", width: "20%" },
            { data: "qty", width: "10%", className: "text-center", defaultContent: "0" },
            { data: "applied_by", width: "20%", defaultContent: "---" },
            { data: "applied_date", width: "12%", defaultContent: "---" },
        ],
        columnDefs: [{
            targets: "_all",
            defaultContent: ""
        }]
    });

    function tempRequestQtyDataTableActions(row) {
        let _actionButton = "";
        const id = row.id;
        const qty_id = row.qty_id;
        const checklist_id = row.checklist_id;

        if (qty_id) {
            if (_currentActions.includes("edit")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemQty' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Edit Item Qty' " +
                    "         data-id='" + id + "' onclick='openModal(\"edit\",\"" + row.task + "\", " + qty_id + ")'>" +
                    "         <i class='la la-edit'></i>" +
                    " </button>";
            }
        } /** else {
            if (_currentActions.includes("new")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemQty' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Add Item Qty' " +
                    "         data-id='" + id + "' onclick='openModal(\"add\", \"" + row.task + "\", " + id + ", " + checklist_id + ")'>" +
                    "         <i class='la la-plus'></i>" +
                    " </button> ";
            }
        } **/

        if (_currentActions.includes("view")) {
            const historyHoverStyle = qty_id ? "m-btn--hover-accent" : "";
            _actionButton +=
                " <button type='button' " + (qty_id ? "" : "disabled") +
                "         class='btn btn-default " + historyHoverStyle +
                "                m-btn--icon m-btn--icon-only m-btn--pill " +
                "                btnEdit btnEditItemQty' " +
                "         data-placement='bottom' " +
                "         data-toggle='m-tooltip' title='' " +
                "         data-original-title='View History' " +
                "         data-id='" + id + "' onclick='openModal(\"history\", \"" + row.task + "\", " + qty_id + ", " + id + ")'>" +
                "         <i class='la la-history'></i>" +
                " </button>";
        }

        if (_currentActions.includes("approve_action") || _currentActions.includes("disapprove")) {
            const statusActionIsEnabled = (id && parseInt(row.approval_status) === 0);
            const statusButtonHover = statusActionIsEnabled ? "m-btn--hover-accent" : "";
            _actionButton += ' ' +
                '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"' +
                '         data-dropdown-toggle="click" aria-expanded="true">' +
                '        <button class="m-portlet__nav-link m-dropdown__toggle ' +
                '                  btn btn-default m-btn m-btn--icon m-btn--icon-only ' +
                '                  m-btn--pill ' + statusButtonHover + '" ' +
                '                  ' + (statusActionIsEnabled ? "" : "disabled") + '' +
                '                 data-toggle="m-tooltip" data-original-title="Approve or Decline"' +
                '                 data-placement="top" data-delay=\'{\"show\": 300}\' data-skin="dark">' +
                '            <i class="la la-ellipsis-v"></i>' +
                '        </button>' +
                '        <div class="m-dropdown__wrapper">' +
                '            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"' +
                '                  style="left: auto; right: 27.6015px;"></span>' +
                '            <div class="m-dropdown__inner">' +
                '                <div class="m-dropdown__body">' +
                '                    <div class="m-dropdown__content">' +
                '                        <ul class="m-nav">' +
                '                            <li class="m-nav__item">' +
                '                                <a href="javascript:void(0);" class="m-nav__link" ' +
                '                                   onclick="openModal(\'set_status\', \'approve\',' + qty_id + ')">' +
                '                                    <i class="m-nav__link-icon la la-thumbs-o-up"></i>' +
                '                                    <span class="m-nav__link-text">' +
                '                                        Approve' +
                '                                    </span>' +
                '                                </a>' +
                '                            </li>' +
                '                            <li class="m-nav__item">' +
                '                                <a href="javascript:void(0);" class="m-nav__link" ' +
                '                                   onclick="openModal(\'set_status\', \'decline\',' + qty_id + ')">' +
                '                                    <i class="m-nav__link-icon la la-thumbs-o-down"></i>' +
                '                                    <span class="m-nav__link-text">' +
                '                                        Decline' +
                '                                    </span>' +
                '                                </a>' +
                '                            </li>' +
                '                        </ul>' +
                '                    </div>' +
                '                </div>' +
                '            </div>' +
                '        </div>' +
                '    </div>';
        }

        return _actionButton;
    }

    function tempDataTableActions(row) {
        let _actionButton = "";
        const id = row.id;
        const qty_id = row.qty_id;
        const checklist_id = row.checklist_id;

        if (qty_id) {
            if (_currentActions.includes("edit")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemQty' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Edit Item Qty' " +
                    "         data-id='" + id + "' onclick='openModal(\"edit\",\"" + row.task + "\", " + qty_id + ")'>" +
                    "         <i class='la la-edit'></i>" +
                    " </button>";
            }
        } /** else {
            if (_currentActions.includes("new")) {
                _actionButton +=
                    " <button type='button' " +
                    "         class='btn btn-default m-btn m-btn--hover-accent " +
                    "                m-btn--icon m-btn--icon-only m-btn--pill " +
                    "                btnEdit btnEditItemQty' " +
                    "         data-placement='bottom' " +
                    "         data-toggle='m-tooltip' title='' " +
                    "         data-original-title='Add Item Qty' " +
                    "         data-id='" + id + "' onclick='openModal(\"add\", \"" + row.task + "\", " + id + ", " + checklist_id + ")'>" +
                    "         <i class='la la-plus'></i>" +
                    " </button> ";
            }
        } **/

        if (_currentActions.includes("view")) {
            const historyHoverStyle = qty_id ? "m-btn--hover-accent" : "";
            _actionButton +=
                " <button type='button' " + (qty_id ? "" : "disabled") +
                "         class='btn btn-default " + historyHoverStyle +
                "                m-btn--icon m-btn--icon-only m-btn--pill " +
                "                btnEdit btnEditItemQty' " +
                "         data-placement='bottom' " +
                "         data-toggle='m-tooltip' title='' " +
                "         data-original-title='View History' " +
                "         data-id='" + id + "' onclick='openModal(\"history\", \"" + row.task + "\", " + qty_id + ", " + id + ")'>" +
                "         <i class='la la-history'></i>" +
                " </button>";
        }

        return _actionButton;
    }

    function tempDatatableStatus($isActive) {
        var _html = "";
        if ($isActive == 1) {
            _html =
                "<span class='btn btn-info m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-check'></i></span>";
        } else {
            _html =
                "<span class='btn btn-danger m-btn m-btn--icon m-btn--icon-only btn-sm'><i class='la la-remove'></i></span>";
        }
        return _html;
    }

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        dtChecklist.ajax.reload();
    });

    $('#generalRequestSearch').donetyping(function (callback) {
        search_request_val = $(this).val();
        dtChecklistRequest.ajax.reload();
    });

    $(document).on("click", ".btnEditChecklistItem", function () { });

    function openModal(mode, label, id = null, temp_id = null) {
        let path, function_name, formData;

        switch (mode) {
            case "add":
                path = "pms/task/modal_content/add_checklist_qty_modal";
                function_name = "getModalChecklistQty";
                formData = { label, id, temp_id };
                break;
            case "edit":
                path = "pms/task/modal_content/edit_checklist_qty_modal";
                function_name = "getModalExistingChecklistQty";
                formData = { label, id };
                break;
            case "set_status":
                path = "pms/task/modal_content/edit_approval_checklist_qty_modal";
                function_name = "getModalApprovalChecklistQty";
                formData = { label, id };
                break;
            default:
                path = "pms/task/modal_content/add_checklist_qty_modal";
                function_name = "getModalChecklistQty";
                formData = { label, id, temp_id };
                break;
        }

        if (typeof mode !== "undefined") {
            if (mode == "history") {
                _tempItemId = id;
                dtChecklistHistory.ajax.reload();
                modalChecklistQtyHistory.modalShow();
            } else {
                $.ajax({
                    url: baseUrl("pms/task/open_modal"),
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        csrf_token: _csrf_hash,
                        path,
                        function_name,
                        model: "Task_m",
                        formData,
                    },
                    success: function (response) {
                        const html = response.html;

                        if (mode == 'add' || mode == 'edit') {
                            if (typeof modalContainer !== "undefined") {
                                modalContainer.empty().append(html);
                                modalContainer.find("#tempQty").maskMoney({
                                    allowZero: true,
                                    affixesStay: true,
                                    allowNegative: false,
                                }).maskMoney("mask");

                                setTimeout(function () {
                                    $.formUtils.addValidator({
                                        name: 'valid_quantity',
                                        validatorFunction: function (value, $el, config, language, $form) {
                                            return parseFloat(value) > 0;
                                        },
                                        errorMessage: 'Quantity must be greater than 0.00',
                                        errorMessageKey: 'badQuantity'
                                    });

                                    $.validate({
                                        form: "#frmSetTaskQty",
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
                                                            "Checklist Item Quantity",
                                                            { timeOut: 5000 }
                                                        );
                                                        dtChecklist.ajax.reload();
                                                        dtChecklistRequest.ajax.reload();
                                                        modalContainer.modal("hide");
                                                    } else {
                                                        toastr.success(
                                                            json.toastr_msg,
                                                            "Checklist Item Quantity",
                                                            { timeOut: 5000 }
                                                        );
                                                    }
                                                    $(currentForm)
                                                        .find(".btn-submit")
                                                        .removeClass(
                                                            "m-btn--custom m-loader m-loader--light m-loader--right"
                                                        ).prop("disabled", false);
                                                }
                                            });

                                            return false;
                                        }
                                    });
                                }, 500);
                                modalContainer.modal("show");
                            }
                        }

                        if (mode == "set_status") {
                            if (typeof modalContainer !== "undefined") {
                                modalContainer.empty().append(html);
                                modalContainer.modal("show");
                                var formSubmit = modalContainer.find("#frmApprovalTaskQty");
                                if (typeof formSubmit !== "undefined") {
                                    formSubmit.on("submit", function (e) {
                                        e.preventDefault();
                                        var currentForm = e.target;
                                        var tempUrl = currentForm.action;
                                        var tempType = currentForm.method;
                                        var formData = $(currentForm).serialize();

                                        $.ajax({
                                            url: tempUrl,
                                            type: tempType,
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
                                                    toastr[json.state](
                                                        json.toastr_msg,
                                                        "Checklist Item Quantity",
                                                        { timeOut: 5000 }
                                                    );
                                                    dtChecklist.ajax.reload();
                                                    dtChecklistRequest.ajax.reload();
                                                    modalContainer.modal("hide");
                                                } else {
                                                    toastr.error(
                                                        json.toastr_msg,
                                                        "Checklist Item Quantity",
                                                        { timeOut: 5000 }
                                                    );
                                                }
                                                $(currentForm)
                                                    .find(".btn-submit")
                                                    .removeClass(
                                                        "m-btn--custom m-loader m-loader--light m-loader--right"
                                                    ).prop("disabled", false);
                                            }
                                        })
                                    });
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}

var getAllAvailableTask = function () {
    $.ajax({
        url: baseUrl("pms/task/set_all_temp_requested_qty/" + _tempContentData.id),
        dataType: "json",
        success: function (json) {
            if (json.response) { dtChecklistRequestQty.ajax.reload(); }
        }
    });
}
var clearAllTask = function () {
    $.ajax({
        url: baseUrl("pms/task/clear_all_temp_requested_qty/" + _tempContentData.id),
        dataType: "json",
        success: function (json) {
            if (json.response) { dtChecklistRequestQty.ajax.reload(); }
        }
    });
}

var submitRequestQty = function () {
    $("#frmSetTaskRequestQty").submit();
}

var submitValidation = function () {
    $.formUtils.addValidator({
        name: 'valid_quantity',
        validatorFunction: function (value, $el, config, language, $form) {
            return parseFloat(value) > 0;
        },
        errorMessage: 'Quantity must be greater than 0.00',
        errorMessageKey: 'badQuantity'
    });

    $.validate({
        form: "#frmSetTaskRequestQty",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("pms/task/submit_temp_requested_qty/" + _tempContentData.id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalChecklistQtyRequest.modalClose(function () {
                            dtChecklist.ajax.reload();
                            dtChecklistRequest.ajax.reload(null, false);
                        });
                    }
                }
            });
            return false;
        }
    });
}

var removeTempQty = function (tempId) {
    if (tempId) {
        $.ajax({
            url: baseUrl("pms/task/do_post_event/remove_temp_requested_qty"),
            type: "post",
            dataType: "json",
            global: false,
            data: { csrf_token: _csrf_hash, id: tempId },
            success: function (json) {
                if (json.response) {
                    dtChecklistRequestQty.ajax.reload();
                }
            }
        });
    }
}

var vmTempActions = new Vue({
    el: "#temp-actions",
    data: { count: 0 }
});

var vmChecklistData = new Vue({
    el: "#checklist_qty-content",
    data: { row: {} }
});

var getChecklistData = function () {
    $.ajax({
        url: baseUrl("pms/task/get_checklist_data/" + _tempContentData.id),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmChecklistData.row = Object.assign({}, json.row);
            }
        }
    });
}

jQuery(document).ready(function () {
    getChecklistData();
    modalChecklistQtyHistory = renderTaskModal.preview_checklist_qty_history();
    modalChecklistQtyRequest = renderTaskModal.request_new_checklist_qty();
});