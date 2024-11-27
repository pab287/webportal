var tableChecklistItems = $("#table-sequence_checklist_items");
var modalAddChecklistItem = $("#modal-add_checklist_item");
var modalChecklistItemList = $("#modal-checklist_item-list");
const modalContainer = $("#modal-container");
const modalContainerOverlay = $("#modal-container-overlay");
const modalArchiveConfirmation = $("#confirm-archive-modal");
const confirmModal = $("#confirm-update-rate-modal");

let dtTblRateCard;

if (typeof tableChecklistItems !== "undefined") {
    var search_val = "";
    var dtChecklist = tableChecklistItems.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/task/get_checklist_item_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        columns: [
            {data: "name", width: "20%"},
            {data: "label", width: "32%"},
            {data: "development_site", width: "25%"},
            {data: "is_active", width: "8%", className: "text-center"},
            {data: null, width: "15%", className: "text-center"}
        ],
        columnDefs: [{
            data: "is_active",
            defaultContent: "",
            targets: 3,
            orderable: false,
            className: "dt-column-center",
            render: function (data, type, row, meta) {
                return tempDatatableStatus(row.is_active);
            }
        }, {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row);
            }
        }, {
            targets: "_all",
            defaultContent: ""
        }
        ]
    });

    function tempDataTableActions(row) {
        var _actionButton = "";
        _actionButton +=
            " <button type='button' class='btn btn-default m-btn m-btn--hover-success " +
            "               m-btn--icon m-btn--icon-only m-btn--pill btnAssignChecklistItem btnAssign' " +
            "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
            "         data-original-title='Assign Checklist Item' data-id='" + row.id + "'>" +
            "   <i class='la la-list-alt'></i>" +
            " </button>";
        if (_currentActions.includes("edit")) {
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-success " +
                "               m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
                "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
                "         data-original-title='Rate Card' " +
                "         onclick='openRateCardModal(" + row.id + ", \"" + row.label + "\")'>" +
                "   <i class='fa fa-credit-card'></i>" +
                " </button>";
            _actionButton +=
             " <button type='button' class='btn btn-default m-btn m-btn--hover-success " +
             "               m-btn--icon m-btn--icon-only m-btn--pill btnEdit' " +
             "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
             "         data-original-title='Checklist Item Quantities' " +
             "         onclick='openItemQuantityRedirect(" + row.id + ")'>" +
             "   <i class='fa fa-sliders'></i>" +
             " </button>";
            _actionButton +=
                " <button type='button' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon " +
                "               m-btn--icon-only m-btn--pill btnEdit btnEditChecklistItem' " +
                "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
                "         data-original-title='Edit Checklist Item'" +
                "         onclick='openEditModal(" + JSON.stringify(row) + ")'>" +
                "   <i class='la la-edit'></i>" +
                " </button>";
        }
        _actionButton +=
            " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only " +
            "               m-btn--pill btnRemoveChecklistItem btnDelete' data-placement='bottom' " +
            "         data-toggle='m-tooltip' title='' data-original-title='Archive Checklist Item'" +
            "         onclick='openConfirmArchiveModal(" + JSON.stringify(row) + ")'>" +
            "   <i class='la la-file-archive-o'>" +
            " </i></button>";
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

    $(document).on("click", ".btnEditChecklistItem", function () {
    });
}

var validateSequenceItemData = function () {
    $.validate({
        form: "#frmAddChecklistItem",
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
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", true);
                },
                success: function (json) {
                    if (json) {
                        toastr.success(
                            "Checklist item data has been added.",
                            "Checklist successfully added.",
                            5000
                        );
                        if (typeof modalAddChecklistItem !== "undefined") {
                            modalAddChecklistItem.modal("hide");
                        }
                        dtChecklist.ajax.reload();
                    } else {
                        toastr.error(
                            "Error adding checklist item data!",
                            "Error",
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

    if (typeof modalAddChecklistItem !== "undefined") {
        var select2DevelopmentSite = modalAddChecklistItem.find("select#project_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalAddChecklistItem,
            ajax: {
                url: baseUrl("pms/contract/get_select2_project"),
                global: false,
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            var target = $(e.target);
            target.validate();
        });
    }
}

jQuery(document).on("click", "#table-sequence_checklist_items .btnAssignChecklistItem", function () {
    var _self = $(this);
    var dataId = _self.data("id");
    $.ajax({
        url: baseUrl("pms/task/get_assigned_sequence_items"),
        type: "POST",
        dataType: "json",
        data: {id: dataId, csrf_token: _csrf_hash},
        beforeSend: function () {
            if (typeof _self !== "undefined") {
                if (
                    !_self.hasClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    )
                ) {
                    _self.addClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        },
        success: function (json) {
            if (json.response) {
                var _modalContent = modalChecklistItemList.find(".modal-content");
                if (typeof _modalContent !== "undefined") {
                    _modalContent.empty().append(json.html);
                    var treeAclAction = modalChecklistItemList.find("#tree_sequence_items-action");
                    $(treeAclAction)
                        .jstree({
                            core: {
                                data: json.data,
                                dblclick_toggle: false,
                                check_callback: true,
                                themes: {icons: false}
                            },
                            checkbox: {
                                cascade: "down",
                                three_state: true
                            },
                            plugins: ["checkbox", "wholerow"]
                        })
                        .on("ready.jstree", function () {
                            $(this).jstree("open_all");

                            // set selected items using array of ids directly
                            // $(this).jstree('select_node', json.checklist_id);

                            var _current = $(this);
                            var _treeItem = _current.find("li[role=treeitem]");
                            if (typeof _treeItem !== "undefined") {
                                _treeItem.each(function (i, v) {
                                    var _id = $(v).attr("id");
                                    _id = parseInt(_id);
                                    if (jQuery.inArray(_id, json.checklist_id) !== -1) {
                                        _current.jstree("select_node", this);
                                    }
                                });
                            }

                            var jsTreeCheckbox = $(this).find(
                                "i.jstree-icon.jstree-checkbox"
                            );

                            var jsTreeOcl = $(this).find("i.jstree-icon.jstree-ocl");
                            if (typeof jsTreeOcl !== "undefined" && jsTreeOcl.length > 0) {
                                jsTreeOcl.remove();
                            }

                            if (typeof jsTreeCheckbox !== "undefined" && jsTreeCheckbox.length > 0) {
                                jsTreeCheckbox.css("margin-right", "15px");
                            }
                        });

                    $(modalChecklistItemList).modal("show");
                }
            }
            if (typeof _self !== "undefined") {
                if (
                    _self.hasClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    )
                ) {
                    _self.removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
                }
            }
        }
    });
});

jQuery(document).on(
    "click",
    "#modal-checklist_item-list .btn-submit-list",
    function () {
        var _self = $(this);

        var dataId = $(this).data("id");
        var _jsTree = modalChecklistItemList.find("#tree_sequence_items-action");
        if (typeof _jsTree !== "undefined") {
            var _jsonData = $(_jsTree)
                .jstree(true)
                .get_json("#", {flat: true});
            var _string = JSON.stringify(_jsonData);

            $.ajax({
                url: baseUrl("pms/task/set_checklist_json_actions"),
                type: "POST",
                data: {nodes: _string, id: dataId, csrf_token: _csrf_hash},
                dataType: "json",
                beforeSend: function () {
                    if (typeof _self !== "undefined") {
                        if (
                            !_self.hasClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            )
                        ) {
                            _self.addClass(
                                "m-btn--custom m-loader m-loader--light m-loader--right"
                            );
                        }
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Checklist Template", 5000);
                        $(modalChecklistItemList).modal("hide");
                    } else {
                        toastr.error(json.toastr_msg, "Error Checklist Template", 5000);
                    }
                    if (typeof _self !== "undefined") {
                        _self.removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                }
            });
        }
    }
);

jQuery(document).ready(function () {
    validateSequenceItemData();
});

function openItemQuantityRedirect(id) {
    mapBlockUI(function () {
        window.location.href = siteUrl("pms/task/checklist_item_qty/" + id);
    });
}

function openEditModal(data) {
    $.ajax({
        url: baseUrl("pms/task/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/task/modal_content/edit_checklist_modal",
            function_name: "getTaskDetails",
            model: "Task_m",
            formData: data,
            init_modal_data_function: "getCheckListProjectId"
        },
        success: function (response) {
            const html = response.html;
            const info = response.info;

            modalContainer.empty().append(html);

            modalContainer.find("#project_id")
                .select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: modalContainer,
                    ajax: {
                        url: baseUrl("pms/contract/get_select2_project"),
                        global: false,
                        dataType: "json",
                        delay: 500,
                        processResults: function (data) {
                            return data;
                        }
                    }
                })
                .on("select2:select", function (e) {
                    var target = $(e.target);
                    target.validate();
                });

            const option = new Option(data.development_site, info.project_id, true, false);
            modalContainer.find("#project_id").append(option).trigger('change');

            modalContainer.modal("show");
        }
    });
}

function openRateCardModal(id, label) {
    $.ajax({
        url: baseUrl("pms/task/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/task/modal_content/rate_card_modal",
            function_name: "getChecklistPrivilegeId",
            model: "Rate_card_m",
            formData: {id, label},
            init_modal_data_function: "getInitModalData"
        },
        success: function (response) {
            const html = response.html;
            const info = response.info;
            const tree = info.tree;

            modalContainer.empty().append(html);

            const treeEl = modalContainer.find("#tree-view");
            $(treeEl)
                .jstree({
                    core: {
                        data: tree,
                        dblclick_toggle: false,
                        check_callback: true,
                        themes: {
                            icons: false,
                        }
                    },
                    checkbox: {
                        cascade: "down",
                        three_state: true
                    },
                })
                .on("ready.jstree", function () {
                    // $(this).jstree("open_all");
                    $(treeEl).off("click.jstree", ".jstree-anchor");
                });

            const table = modalContainer.find("#tbl-rate-card-updates");

            getRateCardUpdates(id, table);

            modalContainer.modal("show");
        }
    });
}

function getRateCardUpdates(checklist_id, table) {
    dtTblRateCard = table.DataTable({
        dom: "tr",
        serverSide: true,
        processing: true,
        destroy: true,
        ajax: {
            url: baseUrl("pms/task/get_rate_card_updates/" + checklist_id),
            type: "GET",
            dataType: "JSON",
        },
        columns: [
            {
                data: "label",
                render: function (data, type, row) {
                    const unit = row.unit ? " / " + row.unit : "";
                    const rate = "&#8369; " + parseFloat(row.tariff).toLocaleString(undefined, {maximumFractionDigits: 2}) + unit;

                    return "" +
                        "<p class='m--font-bolder mb-0'>" + data + "</p>" +
                        "<p class='m--font-boldest mb-0 m--regular-font-size-sm2'>" +
                        "   <span class='text-muted'>Parent: </span><span>" + (row.parent ? row.parent : "--") + "</span>" +
                        "</p>" +
                        "<p class='m--font-bolder mb-0 m--regular-font-size-sm2'>" +
                        "   <span class='text-muted'>Category: </span><span>" + (row.category ? row.category : "--") + "</span>" +
                        "</p>" +
                        "<p class='m--font-bolder mb-0 mt-2'>" + rate + "</p>" +
                        "";
                },
                width: "45%"
            },
            {
                data: null,
                render: function (data, type, row) {
                    const highlight_category = ((row.rates_category && row.category) ? row.rates_category.toLowerCase() !== row.category.toLowerCase() : "") ? "text-danger font-weight-bold" : "";
                    const highlight_price = (parseFloat(row.rates_tariff) !== parseFloat(row.tariff)) ? "text-danger font-weight-bold" : "";
                    const highlight_unit = ((row.rates_unit && row.unit) ? row.rates_unit.toLowerCase() !== row.unit.toLowerCase() : "") ? "text-danger font-weight-bold" : "";

                    const unit = row.rates_unit ? ("<span class='" + highlight_unit + "'> / " + row.rates_unit + "</span>") : "";
                    const rate = "<span class='" + highlight_price + "'>&#8369; " + parseFloat(row.rates_tariff).toLocaleString(undefined, {maximumFractionDigits: 2}) + "</span>" + unit;

                    return "" +
                        "<p class='m--font-bolder mb-0'>" + row.label + "</p>" +
                        "<p class='m--font-boldest mb-0 m--regular-font-size-sm2'>" +
                        "   <span class='text-muted'>Parent: </span><span>" + (row.parent ? row.parent : "--") + "</span>" +
                        "</p>" +
                        "<p class='m--font-bolder mb-0 m--regular-font-size-sm2'>" +
                        "   <span class='text-muted'>Category: </span>" +
                        "   <span class='" + highlight_category + "'>" + (row.rates_category ? row.rates_category : "--") + "</span>" +
                        "</p>" +
                        "<p class='m--font-bolder mb-0 mt-2'>" + rate + "</p>" +
                        "";
                },
                width: "45%"

            },
            {
                data: null,
                render: function (data, type, row) {
                    return "" +
                        "<button type='button' class='btn btn-success m-btn m-btn--pill m-btn--sm'" +
                        "        style='font-size: 0.7rem; padding: 4px 8px;' " +
                        "        onclick='confirmUpdateRate(\"" + row.rates_tariff + "\", \"" + row.rates_unit + "\"," + row.id + ", " + checklist_id + ", " + row.rates_category_id + ")'>" +
                        "   Apply Update" +
                        "</button>";
                },
                orderable: false
            }
        ],
        oLanguage: {
            sZeroRecords: "No updates to show."
        },
        autoWidth: false,
        ordering: false,
        initComplete: setUpdateHeaderStyle
    });
}

function setUpdateHeaderStyle() {
    if (parseInt($("#tbl-rate-card-updates").find("tbody > tr[role='row']").length) > 0) {
        $("#update-header").html('<span class="m-badge m-badge--danger px-3 m--font-bolder">UPDATES</span>');
    } else {
        $("#update-header").html('UPDATES');
    }
}

function confirmUpdateRate(new_rate, new_unit, privilege_rate_id, checklist_id, category_id) {
    const message = confirmModal.find(".modal-body p");
    message.html("Are you sure to apply the updates?");

    confirmModal.find("[name='id']").val(privilege_rate_id);
    confirmModal.find("[name='tariff']").val(new_rate);
    confirmModal.find("[name='unit']").val(new_unit);
    confirmModal.find("[name='checklist_id']").val(checklist_id);
    confirmModal.find("[name='category_id']").val(category_id);

    confirmModal.modal("show");
}

$("#frm-update-rate-modal").on("submit", function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    $.ajax({
        url: baseUrl("pms/task/update_checklist_rate"),
        type: "POST",
        dataType: "JSON",
        data: formData,
        success: function (response) {
            const treeEl = modalContainer.find("#tree-view");

            if (response.success) {
                const tree = response.data.tree;
                if (tree.length > 0) {
                    $(treeEl).jstree("destroy");
                    $(treeEl)
                        .jstree({
                            core: {
                                data: tree,
                                dblclick_toggle: false,
                                check_callback: true,
                                themes: {
                                    icons: false
                                }
                            },
                            checkbox: {
                                cascade: "down",
                                three_state: true
                            },
                        })
                        .on("ready.jstree", function () {
                            $(treeEl).off("click.jstree", ".jstree-anchor");
                        });
                }

                dtTblRateCard.ajax.reload(setUpdateHeaderStyle, false);
                toastr.success("Rates was successfully updated.", "Rates updated.", 10000);
            }

            confirmModal.modal("hide");
        }
    });
});

var validateForm = function () {
    $.validate({
        form: "#frm-edit-checklist-item",
        lang: "en",
        onSuccess: function (frm) {
            const form = $(frm);
            const url = form.attr("action");
            const data = form.serialize();

            $.ajax({
                url,
                type: "post",
                dataType: "json",
                data,
                beforeSend: function () {
                    form.find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", true);
                },
                success: function (json) {
                    if (json) {
                        toastr.success(
                            "Checklist item data has been updated.",
                            "Update Checklist Item.",
                            5000
                        );

                        modalContainer.modal("hide");
                        dtChecklist.ajax.reload();
                    } else {
                        toastr.error(
                            "Error updating checklist item data!",
                            "Update Error",
                            5000
                        );
                    }

                    form.find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        ).prop("disabled", true);
                }
            });
            return false;
        }
    });
}

modalContainer.on("show.bs.modal", function () {
    validateForm();
});

function openConfirmArchiveModal(data) {
    const form = $("#frm-archive-confirmation");
    form.attr("action", "pms/task/archive_checklist_item/" + data.id);
    $("#item-name").html(data.label);

    modalArchiveConfirmation.modal("show");
}

$("#frm-archive-confirmation")
    .on("submit", function (e) {
        e.preventDefault();
        const url = $(this).attr("action");

        $.ajax({
            url: baseUrl(url),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response) {
                    dtChecklist.ajax.reload();
                    toastr.success("Item was successfully archived.", "Item Archived.", 10000);
                } else {
                    toastr.success("An error occurred while archiving.", "Archive Error.", 10000);
                }

                modalArchiveConfirmation.modal("hide");
            }
        });
    });

$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});

$('body, .modal-body')
    .tooltip({
        selector: '[data-original-title]',
        skin: "dark",
        delay: {
            show: 300
        }
    });

let dtRateCardUpdateHistory;

function openUpdateHistoryModal(checklist_priv_id) {
    $.ajax({
        url: baseUrl("pms/task/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/task/modal_content/rate_card_history",
            // function_name: "getTaskDetails",
            // model: "Task_m",
            // formData: data,
            // init_modal_data_function: "getCategory"
        },
        success: function (response) {
            const html = response.html;
            const info = response.info;
            modalContainerOverlay.empty().append(html);

            const table = $("#table-rate-card-update-history");
            populateRateCardUpdateHistoryTable(table, checklist_priv_id);

            modalContainerOverlay.modal("show");
        }
    });
}

function populateRateCardUpdateHistoryTable(table, checklist_priv_id) {
    dtRateCardUpdateHistory = table.DataTable({
        dom: "ftrlp",
        serverSide: true,
        processing: true,
        ajax: {
            url: baseUrl("pms/rate_card/get_rate_card_update_history/" + checklist_priv_id),
            type: "POST",
            dataType: "JSON",
            data: function (d) {
                d.csrf_token = _csrf_hash;
            }
        },
        autoWidth: false,
        columns: [
            {
                data: "label"
            },
            {
                data: "prev_tariff",
                render: function (data, type, row) {
                    const highlight_category = (row.prev_category.toLowerCase() !== row.new_category.toLowerCase()) ? "text-danger font-weight-bold" : "";
                    const highlight_price = (parseFloat(row.prev_tariff) !== parseFloat(row.new_tariff)) ? "text-danger font-weight-bold" : "";
                    const highlight_unit = ((row.prev_unit && row.new_unit) ? row.prev_unit.toLowerCase() !== row.new_unit.toLowerCase() : "") ? "text-danger font-weight-bold" : "";


                    const rate = "<span class='m--font-bolder'><span class='" + highlight_price + "'>" +
                        "           &#8369; " + parseFloat(data).toLocaleString(undefined, {minimumFractionDigits: 2}) + " </span> / " +
                        "           <span class='" + highlight_unit + "'>" + row.prev_unit + "</span>" +
                        "         </span>";
                    const category = "<span class='m--regular-font-size-sm2'>" +
                        "               <span class='text-muted'>CATEGORY</span>: " +
                        "               <span class='m--font-bolder " + highlight_category + "'>" + row.prev_category + "</span>" +
                        "             </span>";
                    return rate + "<br/>" + category;
                }
            },
            {
                data: "new_tariff",
                render: function (data, type, row) {
                    const highlight_category = (row.prev_category.toLowerCase() !== row.new_category.toLowerCase()) ? "text-success font-weight-bold" : "";
                    const highlight_price = (parseFloat(row.prev_tariff) !== parseFloat(row.new_tariff)) ? "text-success font-weight-bold" : "";
                    const highlight_unit = ((row.prev_unit && row.new_unit) ? row.prev_unit.toLowerCase() !== row.new_unit.toLowerCase() : "") ? "text-success font-weight-bold" : "";


                    const rate = "<span class='m--font-bolder'><span class='" + highlight_price + "'>" +
                        "           &#8369; " + parseFloat(data).toLocaleString(undefined, {minimumFractionDigits: 2}) + " </span> / " +
                        "           <span class='" + highlight_unit + "'>" + row.new_unit + "</span>" +
                        "         </span>";
                    const category = "<span class='m--regular-font-size-sm2'>" +
                        "               <span class='text-muted'>CATEGORY</span>: " +
                        "               <span class='m--font-bolder " + highlight_category + "'>" + row.new_category + "</span>" +
                        "             </span>";
                    return rate + "<br/>" + category;
                }
            },
            {
                data: "applied_by_name",
                render: function (data) {
                    return "<span class='m--regular-font-size-sm1'>" +
                        "       " + data +
                        "   </span>";
                },
                width: "18%"
            },
            {
                data: "applied_at",
                render: function (data) {
                    return "<span class='m--regular-font-size-sm1'>" +
                        "       " + moment(data).format("MMM. DD, YYYY") + "" +
                        "       <br/>" +
                        "       " + moment(data).format("hh:mm:ss A") + "" +
                        "   </span>";
                },
                width: "14%"
            },
        ],
        initComplete: function () {
            let search_thread = null;
            $(".dataTables_filter input")
                .unbind()
                .bind("input", function (e) {
                    clearTimeout(search_thread);
                    search_thread = setTimeout(function () {
                        const dtable = table.dataTable().api();
                        const elem = $(".dataTables_filter input");
                        return dtable.search($(elem).val()).draw();
                    }, 1000);
                });
        },
        order: [[4, "desc"]],
        // pageLength: 1,
    });
}

function confirmRevert(data) {
    const modalConfirmRevert = $("#confirm-revert-modal");
    const body = modalConfirmRevert.find(".modal-body");

    $.ajax({
        url: baseUrl("pms/rate_card/get_current_checklist_rate/" + data.priv_rates_id),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            body.empty();
            body.append("" +
                "<p>Are you sure to revert <span class='text-primary m--font-boldest'>" + response.label + "</span> into " +
                "   <span class='text-primary m--font-boldest'>" +
                "       &#8369;" + parseFloat(data.prev_tariff).toLocaleString(undefined, {minimumFractionDigits: 2}) +
                "       " + "/ " + data.prev_unit + "</span> with " +
                "       <span class='m--font-bolder'>category:</span> " +
                "   <span class='text-primary m--font-boldest'>" + data.prev_category + "</span>?" +
                "</p>"
            );

            body.append("" +
                "<p class='mt-5'>" +
                "   <span class='m--font-bold'>CURRENT:</span> <br/>" +
                "   <span class='text-success m--font-boldest'>" +
                "   &#8369;" + parseFloat(response.tariff).toLocaleString(undefined, {minimumFractionDigits: 2})
                + " / " + response.unit + "</span>, <span class='m--font-bolder'>CATEGORY: </span>" +
                "   <span class='text-success m--font-boldest'>" + response.category + "</span>" +
                "</p>");
            modalConfirmRevert.attr("data-id", data.id);
            modalConfirmRevert.modal("show");
        }
    });
}

function revertRate() {
    const modalConfirmRevert = $("#confirm-revert-modal");
    const id = modalConfirmRevert.attr("data-id");

    $.ajax({
        url: baseUrl("pms/rate_card/revert_rate/" + id),
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.success) {
                toastr.success("Item Rate Reverted.", "Rate was successfully reverted.", {timeOut: 10000});
                const treeEl = modalContainer.find("#tree-view");

                const tree = response.data.tree;
                if (tree.length > 0) {
                    $(treeEl).jstree("destroy");
                    $(treeEl)
                        .jstree({
                            core: {
                                data: tree,
                                dblclick_toggle: false,
                                check_callback: true,
                                themes: {
                                    icons: false
                                }
                            },
                            checkbox: {
                                cascade: "down",
                                three_state: true
                            },
                        })
                        .on("ready.jstree", function () {
                            $(this).jstree("open_all");
                            $(treeEl).off("click.jstree", ".jstree-anchor");
                        });
                }

                dtTblRateCard.ajax.reload(setUpdateHeaderStyle, false);
            } else {
                toastr.error("Error", "A problem occurred while reverting.", {timeOut: 10000});
            }
        }
    });

    dtRateCardUpdateHistory.ajax.reload();
    modalConfirmRevert.modal("hide");
    modalContainerOverlay.modal("hide");
}