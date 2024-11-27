var tableSequenceList = $("#table-sequence_items");
var modalAddSequenceItem = $("#modal-add_sequence_items");
var modalSequenceItemList = $("#modal-sequence_item-list");
const modalContainer = $("#modal-container");
const modalArchiveConfirmation = $("#confirm-archive-modal");

if (typeof tableSequenceList !== "undefined") {
    var search_val = "";
    var dtSequence = tableSequenceList.DataTable({
        dom: '<"toolbar">frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/task/get_sequence_item_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
                return d;
            }
        },
        columns: [
            { data: "name", width: "20%" },
            { data: "label", width: "22%" },
            { data: "parent_name", width: "22%" },
            { data: "wo_code", width: "8%", className: "text-center" },
            { data: "wo_type", width: "15%", className: "text-center" },
            { data: "is_active", width: "5%", className: "text-center" },
            { data: null, width: "8%", className: "text-center" }
        ],
        columnDefs: [{
            data: "is_active",
            defaultContent: "",
            targets: -2,
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
        }]
    });

    function tempDataTableActions(row) {
        var _actionButton = "";
        _actionButton +=
            " <button type='button' class='btn btn-default " +
            "                              m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill " +
            "                              btnEdit btnEditSequenceItem' " +
            "         data-placement='bottom' data-toggle='m-tooltip' title='' " +
            "         data-original-title='Edit Sequence Item' onclick='openEditModal(" + JSON.stringify(row) + ")'>" +
            "   <i class='la la-edit'></i>" +
            "</button>";
        _actionButton +=
            " <button type='button' class='btn btn-default m-btn m-btn--hover-warning m-btn--icon " +
            "                              m-btn--icon-only m-btn--pill btnRemoveSequenceItem btnDelete' " +
            "         data-placement='bottom' data-toggle='m-tooltip' " +
            "         title='' data-original-title='Archive Sequence Item' " +
            "         onclick='openConfirmArchiveModal(" + JSON.stringify(row) + ")'>" +
            "   <i class='la la-file-archive-o'></i>" +
            "</button>";
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
        dtSequence.ajax.reload();
    });
}

var validateSequenceItemData = function () {
    $.validate({
        form: "#frmAddSequenceItem",
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
                            "Sequence item data has been added.",
                            5000
                        );
                        if (typeof modalAddSequenceItem !== "undefined") {
                            modalAddSequenceItem.modal("hide");
                        }
                        dtSequence.ajax.reload();
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding sequence item data!",
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

    var select2WoType = modalAddSequenceItem.find("select#wo_type_id").select2({
        width: "100%",
        allowClear: true,
        placeholder: "Select an option",
        dropdownParent: modalAddSequenceItem,
        ajax: {
            url: baseUrl("pms/work_order/get_wo_type_select2_data"),
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return data;
            }
        }
    });
}

jQuery(document).on("click", "#sequence_item-list", function () {
    var _self = $(this);
    $.ajax({
        url: baseUrl("pms/task/list_sequence_item"),
        beforeSend: function () {
            if (typeof _self !== "undefined") {
                _self.addClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
            }
        },
        success: function (json) {
            if (json.response) {
                modalSequenceItemList
                    .find(".modal-content")
                    .empty()
                    .append(json.html);

                var treeAcl = modalSequenceItemList.find("#tree_sequence_item-list");

                $(treeAcl)
                    .jstree({
                        core: {
                            data: json.data,
                            check_callback: true
                        },
                        types: {
                            root: { icon: "fa fa-folder" },
                            child: { icon: "fa fa-file" }
                        },
                        plugins: ["dnd", "types"]
                    })
                    .on("ready.jstree", function () {
                        $(this).jstree("open_all");
                    });

                jQuery(modalSequenceItemList).modal("show");
            }
            if (typeof _self !== "undefined") {
                _self.removeClass(
                    "m-btn--custom m-loader m-loader--light m-loader--right"
                );
            }
        }
    });
});

let _typingTimer;

modalSequenceItemList
    .on("keyup", "#searchBox", function () {
        const _self = $(this);
        const search = _self.val();

        clearTimeout(_typingTimer);
        _typingTimer = setTimeout(function () {
            searchAclList(search, _self);
        }, 1000);
    }).on("keydown", "#searchBox", function () {
        clearTimeout(_typingTimer);
    });

function searchAclList(search = "", _self) {
    $.ajax({
        url: baseUrl("pms/task/list_sequence_item/?search=" + search),
        success: function (json) {
            if (json.response) {
                var treeSequence = modalSequenceItemList.find("#tree_sequence_item-list");
                $(treeSequence).jstree("destroy");
                $(treeSequence)
                    .jstree({
                        core: {
                            data: json.data,
                            check_callback: true
                        },
                        types: {
                            root: { icon: "fa fa-folder" },
                            child: { icon: "fa fa-file" }
                        },
                        plugins: ["dnd", "types"]
                    })
                    .on("ready.jstree", function () {
                        $(this).jstree("open_all");
                    });
            }
        }
    });
}

jQuery(document).on(
    "click",
    "#modal-sequence_item-list .btn-submit-list",
    function () {
        var _self = $(this);
        var treeSequence = modalSequenceItemList.find("#tree_sequence_item-list");
        if (typeof treeSequence !== "undefined") {
            var jsonData = jQuery(treeSequence)
                .jstree(true)
                .get_json("#", { flat: true });
            var _string = JSON.stringify(jsonData);

            $.ajax({
                url: baseUrl("pms/task/get_json_sequence_items"),
                type: "POST",
                data: { nodes: _string, csrf_token: _csrf_hash },
                dataType: "json",
                beforeSend: function () {
                    if (typeof _self !== "undefined") {
                        _self.addClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        );
                    }
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Sequence Item List", 5000);
                        $(modalSequenceItemList).modal("hide");
                        dtSequence.ajax.reload();
                    } else {
                        toastr.error(json.toastr_msg, "Error Sequence Item List", 5000);
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

function openEditModal(data) {
    $.ajax({
        url: baseUrl("pms/task/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/task/modal_content/edit_task_modal",
            function_name: "getTaskDetails",
            model: "Task_m",
            formData: data,
            // init_modal_data_function: "getCategory"
        },
        success: function (response) {
            const html = response.html;
            const info = response.info;

            modalContainer.empty().append(html);

            $("#work-order-type")
                .select2({
                    placeholder: "Select an Option",
                    allowClear: true,
                    width: "100%",
                    dropdownParent: modalContainer,
                    ajax: {
                        url: baseUrl("pms/work_order/get_wo_type_select2_data"),
                        dataType: "json",
                        delay: 500,
                        processResults: function (data) {
                            return data;
                        }
                    }
                });

            const option = new Option(data.wo_type, data.wo_type_id, false, true);
            $("#work-order-type").append(option).trigger('change');

            modalContainer.modal("show");
        }
    });
}

var validateEditForm = function () {
    $.validate({
        form: "#frm-edit-task-modal",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (frm) {
            const form = $(frm);
            const url = form.attr("action");
            const data = form.serialize();

            $.ajax({
                url,
                type: "POST",
                datType: "JSON",
                data,
                success: function (response) {
                    if (response) {
                        dtSequence.ajax.reload();
                        toastr.success("Item was successfully updated.", "Item Updated.", 10000);
                    } else {
                        toastr.success("An error occurred while updating.", "Update Error.", 10000);
                    }

                    modalContainer.modal("hide");
                }
            });

            return false;
        }
    });
}

modalContainer.on("show.bs.modal", function () {
    validateEditForm();
});

function openConfirmArchiveModal(data) {
    const form = $("#frm-archive-confirmation");
    form.attr("action", "pms/task/archive_item/" + data.id);
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
                    dtSequence.ajax.reload();
                    toastr.success("Item was successfully archived.", "Item Archived.", 10000);
                } else {
                    toastr.success("An error occurred while archiving.", "Archive Error.", 10000);
                }

                modalArchiveConfirmation.modal("hide");
            }
        });
    });