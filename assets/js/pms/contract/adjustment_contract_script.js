var tempModal = new newContractModal();
var vmRenderContract = renderVueContract();
var vmTempInstance, vmGenerateLot, vmRenderedLots, vmEditBlockLot, vmRemoveCurrentTask, tempChecklist = 0;
var tempBlock = 0, tempUnit = 0, tempProjectId = 0, tempChecklistSelected = [], tempMultipleBlock = [];
var tempAddedBlock = 0, tempAddedUnit = 0;
var modalGenerateLot, modalEditBlockLot, modalRemoveCurrentTask, modalEditQtyUnitCost;
var _dtTaskEditQtyUnitCost, dtTaskEditQtyUnitCost;
var tempIsEditable = 1;
var tempIsAdjustment = true;

var contractContent = $("#contract-content");
var formAssignContract = $("#frmAssignContract");
if (typeof formAssignContract !== "undefined") {
    formAssignContract.prop("action", baseUrl("pms/contract/do_post_event/set_adjustment_current_contract"));
}
var defaultRenderedLots = function () {
    return { rows: {}, count: 0, blocks: [], block_count: {} };
}

var vmWoInformationActions = new Vue({
    el: "#woInformationAction",
    data: { show_action: false }
});

var getCurrentContract = function (id, wo_task_id) {
    $.ajax({
        url: baseUrl("pms/contract/get_current_contract/" + id),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var tempRowData = Object.assign({}, { wo_task_id: wo_task_id }, json.row);
                tempChecklist = tempRowData.checklist_id;
                tempProjectId = tempRowData.project_id;

                vmTempWorkOrder.row = tempRowData;
                var tempJstree = $("#checklist_preview");
                if (typeof tempJstree !== "undefined") {
                    tempJstree.jstree("destroy");
                    tempJstree.jstree({
                        core: {
                            data: json.checklist_data,
                            check_callback: true,
                            dblclick_toggle: false,
                        }, checkbox: {
                            keep_selected_style: false,
                        }, types: {
                            root: { icon: "fa fa-folder" },
                            child: { icon: "fa fa-file" },
                        },
                        plugins: ["checkbox", "types"]
                    }).on("deselect_node.jstree", function (e, data) {
                        var currentJsTree = $(this).jstree(true);
                        var selected = currentJsTree.get_selected();
                        tempChecklistSelected = [];
                        tempChecklistSelected = selected;
                        var tempSelectedItems = contractContent.find("#selected_items");
                        if (typeof tempSelectedItems !== "undefined" && tempSelectedItems.length == 1) {
                            tempSelectedItems.val(selected).validate();
                        }
                        if (typeof select2BlockMultiple !== "undefined") { select2BlockMultiple.val("").trigger("change"); }
                        if (selected.length == 0) {
                            currentJsTree.close_all();
                            setTimeout(function () {
                                currentJsTree.refresh(true);
                            }, 200);
                        }
                    }).on("select_node.jstree", function (e, data) {
                        var currentJsTree = $(this).jstree();
                        var currentNode = data.node;
                        var selectedId = data.selected;
                        selectedId = selectedId.map(Number);
                        var parentIds = [], nodeIds = [];

                        if (typeof currentNode.parent !== "undefined" && currentNode.parent !== "#") {
                            var parentNode = parseInt(currentNode.parent);
                            selectedId.push(parentNode);
                        }

                        $.each(json.data, function (kk, vv) {
                            var currentId = parseInt(vv.id);
                            var isInArray = jQuery.inArray(currentId, selectedId);
                            if (isInArray == -1 && vv.parent == "#") {
                                parentIds.push(currentId);
                                nodeIds.push(currentId);
                            }
                            if (vv.parent !== "#") {
                                var parentId = parseInt(vv.parent);
                                var isParentInArray = jQuery.inArray(parentId, parentIds);
                                if (isParentInArray !== -1) {
                                    nodeIds.push(currentId);
                                }
                            }
                        });

                        var currentJsTreex = $(this).jstree(true);
                        var selected = currentJsTreex.get_selected();
                        tempChecklistSelected = [];
                        tempChecklistSelected = selected;
                        var tempSelectedItems = contractContent.find("#selected_items");
                        if (typeof tempSelectedItems !== "undefined" && tempSelectedItems.length == 1) {
                            tempSelectedItems.val(selected).validate();
                        }
                        if (typeof select2BlockMultiple !== "undefined") { select2BlockMultiple.val("").trigger("change"); }
                        currentJsTree.open_node(data.node);
                    }).on("ready.jstree", function () {
                        var tempSelectedItems = contractContent.find("#selected_items");
                        if (typeof tempSelectedItems == "object" && tempSelectedItems.length == 0) {
                            $('<input type="hidden" id="selected_items" name="selected_items" value="" data-validation="required" data-validation-error-msg-required="Select on the above task" />').appendTo("#selectedItems");
                        }
                    });
                }

                var select2BlockMultiple = contractContent.find("select#block_multiple");
                if (typeof select2BlockMultiple !== "undefined") {
                    var select2BlockMultipleContent = select2BlockMultiple.select2({
                        width: "100%",
                        placeholder: "Select an option",
                        ajax: {
                            url: baseUrl("pms/contract/get_select2_blocks"),
                            global: false,
                            dataType: "json",
                            delay: 250,
                            data: function (params) {
                                params.checklist_id = tempChecklist;
                                params.project_id = tempProjectId;
                                params.selected_items = tempChecklistSelected;
                                return params;
                            }, processResults: function (data) {
                                return data;
                            }
                        }
                    }).on("select2:select", function (e) {
                        tempMultipleBlock = $(e.target).val();
                        var target = $(e.target);
                        //target.validate();
                    }).on('select2:unselect', function (e) {
                        tempMultipleBlock = $(e.target).val();
                        var target = $(e.target);
                        //target.validate();
                    });
                }

                vmTempInstance.row = tempRowData;
                setTimeout(function () {
                    vmTempInstance.renderSelect2Data();
                }, 500);
                // generateItemList(tempRowData.id, wo_task_id);
                validateContractPreview();
            }
        }
    });
}

var vmTempWorkOrder = new Vue({
    el: "#frmGenerateTask",
    data: {
        is_editable: true,
        row: {},
        rows: {},
        count: 0,
        blocks: [],
        block_count: {}
    }, methods: {
        resetData: function () {
            Object.assign(this.$data, defaultRenderedLots());
            tempMultipleBlock = [];
            contractContent.find("#checklist_preview").jstree().deselect_all(true);
            contractContent.find("#checklist_preview").jstree('close_all');
            contractContent.find("#selected_items").val("");
        }
    }
});

var validateContractPreview = function () {
    var _contractContent = $("#contract-content_preview");
    $.formUtils.addValidator({
        name: 'valid_quantity',
        validatorFunction: function (value, $el, config, language, $form) {
            return parseFloat(value) > 0;
        },
        errorMessage: 'Quantity must be greater than 0.00',
        errorMessageKey: 'badQuantity'
    });

    $.validate({
        form: "#frmAssignContract",
        lang: "en",
        scrollToTopOnError: false,
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formType = currentForm.method;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: formUrl,
                type: formType,
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
                        toastr.success(json.toastr_msg, "Saving of data successful", 5000);
                        setTimeout(function () {
                            window.location.replace(json.redirect);
                        }, 1500);
                    } else {
                        toastr.error(json.toastr_msg, "Error saving data", 5000);
                        $(currentForm)
                            .find(".btn-submit")
                            .prop("disabled", true);
                    }
                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                }
            });

            return false;
        }
    });

    if (typeof _contractContent !== "undefined" && _contractContent.length == 1) {
        var select2TaskContractor = _contractContent.find("select#contractor_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("pms/contractor/get_contractor_select2_data"),
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
        var select2TaskIncharge = _contractContent.find("select#task_incharge").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("pms/contractor/get_task_incharge_select2_data"),
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
        var select2Leadman = _contractContent.find("select#foreman_id").select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("pms/contractor/get_foreman_select2_data"),
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

var generateLots = function () {
    getMultipleLotsByBlocks(tempMultipleBlock, tempChecklist, tempProjectId, tempChecklistSelected, _tempContentData.id, _tempContentData.wo_task_id);
}

var generateItemList = function (id, wo_task_id) {
    $.ajax({
        url: baseUrl("pms/contract/render_contract_items/" + id + "/" + wo_task_id),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                setTimeout(function () {
                    dtTaskItems.ajax.reload();
                }, 500);
            }
        }
    });
}

var getMultipleLotsByBlocks = function (
    arrBlocks = [],
    tempChecklist = null,
    tempProjectId = null,
    tempChecklistSelected = [],
    contract_id = 0,
    wo_task_id = 0) {

    $.ajax({
        url: baseUrl("pms/contract/do_post_event/get_multiple_lots"),
        type: "post",
        dataType: "json",
        data: {
            csrf_token: _csrf_hash,
            blocks: arrBlocks,
            checklist_id: tempChecklist,
            project_id: tempProjectId,
            selected_items: tempChecklistSelected,
            contract_id: contract_id,
            wo_task_id: wo_task_id,
        },
        success: function (json) {
            if (json.response) {
                vmGenerateLot.count = json.count;
                vmGenerateLot.blocks = json.blocks;
                vmGenerateLot.units = Object.assign({}, json.units);
                vmGenerateLot.is_editable = true;
                modalGenerateLot.modalShow(function () {
                    vmGenerateLot.resetChecked();
                });
            }
        }
    });
}

var _dtTaskItems = $("#table-task_items");

if (typeof _dtTaskItems !== "undefined") {
    var dtTaskItems = _dtTaskItems.DataTable({
        dom: "tr",
        serverSide: true,
        processing: true,
        paging: false,
        searching: false,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_temp_task_item_datatable_request"),
            type: "post",
            dataType: "json",
            global: false,
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.is_editable = 1;
                return d;
            },
            error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtTaskItems.ajax.reload(null, false);
                }
            }
        }, columns: [
            { data: null, className: 'details-control text-center', orderable: false, defaultContent: '' },
            { data: "label" },
            { data: "qty", className: "text-right" },
            { data: "lot_count", className: "text-right" },
            { data: "unit", className: "text-center" },
            { data: "unit_cost", className: "text-right" },
            { data: "total", className: "text-right" },
        ], columnDefs: [{
            targets: "_all",
            defaultContent: "",
        }],
        createdRow: function (row, data, dataIndex) {
            if (data.is_parent == 1) {
                $('td:eq(1)', row).attr('colspan', 7).addClass("parent-node");
                $('td:not(:eq(1))', row).remove();
            } else {
                $('td:eq(1)', row).addClass("child-node");
            }
        }, drawCallback: function () {
            var api = this.api();
            var jsonData = api.ajax.json();

            var tfooter = api.table().footer();
            var tempDetails = $(tfooter).find(".details-control.text-center");
            if (typeof tempDetails !== "undefined") {
                tempDetails.removeClass("details-control text-center");
            }
            var tempGrandTotal = $(tfooter).find("#grandTotal");
            if (typeof tempDetails !== "undefined" && typeof jsonData.grand_total !== "undefined") {
                tempGrandTotal.empty().text(jsonData.grand_total);
                if (jsonData.grand_total !== "0.00" && (parseInt(tempBlock) > 0 || parseInt(tempUnit) > 0)) {
                    tempAddedBlock = tempBlock;
                    tempAddedUnit = tempUnit;
                } else {
                    tempAddedBlock = 0;
                    tempAddedUnit = 0;
                }
            }

            if (jsonData.recordsTotal > 0) {
                vmWoInformationActions.show_action = true;
            } else {
                vmWoInformationActions.show_action = false;
            }
        }, initComplete: function () {
            var api = this.api();
            setTimeout(function () {
                //api.ajax.reload();
            }, 500);
        }
    });

    function format(d) {
        var tempLots = d.lots;
        var tempHtml = "";
        $.each(tempLots, function (kk, vv) {
            tempHtml += "<div class='row'>";
            tempHtml += "<div class='col-1 m--font-boldest'>BLOCK " + kk + "</div>";
            $.each(vv, function (xx, zz) {
                tempHtml += "<div class='col-1'>LOT " + zz.lot + "</div>";
            });
            tempHtml += "</div>";
        });

        return '<table class="table table-bordered table-striped" width="100%">' +
            '<thead>' +
            '<tr>' +
            '<th>Units <a class="pull-right" href="javascript:void(0);" onclick="getCurrentBlocks(' + d.id + ')"><i class="fa fa-pencil" /></a></th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' +
            '<tr>' +
            '<td>' + tempHtml + '</td>' +
            '</tr>' +
            '<tbody>' +
            '</table>';
    }

    $("tbody", _dtTaskItems).on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dtTaskItems.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
}

var validateGenerateTask = function () {
    $.validate({
        form: "#frmGenerateTask",
        lang: "en",
        scrollToTopOnError: false,
        validateHiddenInputs: true,
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            var selectedItems = [];
            var jstreeContent = $(currentForm).find("#checklist_preview");
            if (typeof jstreeContent !== "undefined") {
                selectedItems = jstreeContent.jstree(true).get_selected();
            }
            if (selectedItems.length > 0) {
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
                            dtTaskItems.ajax.reload();
                        }
                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                            .prop("disabled", false);
                        var multipleBlocks = $(currentForm).find("#block_multiple");
                        if (typeof multipleBlocks !== "undefined") {
                            multipleBlocks.val("").trigger("change");
                            vmTempWorkOrder.resetData();
                        }
                        tempChecklistSelected = [];
                    }
                });
            }

            return false;
        }
    });
}

var getCurrentTaskItems = function (isTemp = false) {
    console.log(isTemp);
    $.ajax({
        url: baseUrl("pms/contract/get_temp_task_items/" + isTemp),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                modalRemoveCurrentTask.modalShow(function () {
                    vmRemoveCurrentTask.id = (typeof json.id !== "undefined") ? json.id : 0;
                    vmRemoveCurrentTask.count = json.count;
                    vmRemoveCurrentTask.rows = json.data;
                    vmRemoveCurrentTask.url = json.url;
                    setTimeout(function () {
                        vmRemoveCurrentTask.generateChecklist();
                    }, 200);
                });
            }
        }
    });
}

var getCurrentBlocks = function (id) {
    if (id) {
        $.ajax({
            url: baseUrl("pms/contract/get_temp_item_blocks/" + id),
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    modalEditBlockLot.modalShow(function () {
                        vmEditBlockLot.id = json.id;
                        vmEditBlockLot.count = json.count;
                        vmEditBlockLot.blocks = Object.assign({}, json.blocks);
                        vmEditBlockLot.units = Object.assign({}, json.units);
                        vmEditBlockLot.url = json.url;
                        vmEditBlockLot.validateFields();
                    });
                }
            }
        });
    }
}

$(document).on("submit", "#frmRemoveTask", function (e) {
    e.preventDefault();
    var currentChecklist = $(this).find("#checklist_preview");
    if (typeof currentChecklist !== "undefined") {
        var vv = currentChecklist.jstree(true).get_json('#', { flat: true });
        var jsonData = JSON.stringify(vv);
        $.ajax({
            url: e.target.action,
            type: e.target.method,
            dataType: "json",
            data: { nodes: jsonData, csrf_token: _csrf_hash },
            success: function (json) {
                if (json.response) {
                    modalRemoveCurrentTask.modalClose(function () {
                        var customAlert = $(e.target).find("#custom-alert");
                        if (typeof customAlert !== "undefined") { customAlert.empty(); }
                        dtTaskItems.ajax.reload();
                    });
                }
            }
        });
    }

});

var renderEditQtyUnitCost = function () {
    modalEditQtyUnitCost.modalShow(function () {
        dtTaskEditQtyUnitCost.ajax.reload();
    });
}

jQuery(document).ready(function () {
    vmTempInstance = vmRenderContract.instance;
    vmTempInstance.is_editable = true;
    vmTempInstance.co_type = 3;
    getCurrentContract(_tempContentData.id, _tempContentData.wo_task_id);
    validateGenerateTask();
    modalGenerateLot = tempModal.generate_lot();
    modalEditBlockLot = tempModal.edit_block_lot();
    modalRemoveCurrentTask = tempModal.remove_current_task();
    modalEditQtyUnitCost = tempModal.edit_qty_unitcost();
});