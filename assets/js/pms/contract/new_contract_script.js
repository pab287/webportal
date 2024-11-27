var tempModal = new newContractModal();
var vmRenderContract = renderVueContract();
var vmTaskContent;
var doRedirect = false;
var _contractContent = $("#contract-content");
var tempBlock = 0, tempChecklist = 0, tempProjectId = 0, tempUnit = 0, tempChecklistSelected = [], tempMultipleBlock = [];
var modalContractCode, modalGenerateLot, modalEditBlockLot, modalRemoveCurrentTask, modalEditQtyUnitCost;
var vmContractCode, vmTempInstance, vmGenerateLot, vmRenderedLots, vmEditBlockLot, vmRemoveCurrentTask;
var tempAddedBlock = 0, tempAddedUnit = 0;

var _dtTaskEditQtyUnitCost, dtTaskEditQtyUnitCost;
var defaultRenderedLots = function () {
    return { rows: {}, count: 0, blocks: [], block_count: {} };
}

var vmTempWorkOrder = new Vue({
    el: "#frmGenerateTask",
    data: { is_editable: false, row: {} }
});

var vmWoInformationActions = new Vue({
    el: "#woInformationAction",
    data: { show_action: false }
});

jQuery(document).ready(function () {
    validateGenerateTask();
    validateContractPreview();
    modalContractCode = tempModal.generate_contract_code();
    modalGenerateLot = tempModal.generate_lot();
    modalEditBlockLot = tempModal.edit_block_lot();
    modalRemoveCurrentTask = tempModal.remove_current_task();
    modalEditQtyUnitCost = tempModal.edit_qty_unitcost();
    vmTempInstance = vmRenderContract.instance;
});

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
            data: function (d) {
                d.csrf_token = _csrf_hash;
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

var renderEditQtyUnitCost = function () {
    modalEditQtyUnitCost.modalShow(function () {
        dtTaskEditQtyUnitCost.ajax.reload();
    });
}

var getCurrentTaskItems = function (isTemp = false) {
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
                            var tempRow = Object.assign({}, json.row);
                            vmTempInstance.wo_data = Object.assign({}, json.wo_data);
                            vmTempInstance.row = tempRow;
                            vmContractCode.row = tempRow;
                            dtTaskItems.ajax.reload();

                            var multipleBlocks = $(currentForm).find("#block_multiple");
                            if (typeof multipleBlocks !== "undefined") {
                                multipleBlocks.val("").trigger("change");
                                vmRenderedLots.resetData();

                                var frmAssignContract = $("#frmAssignContract");
                                if (typeof frmAssignContract !== "undefined") {
                                    frmAssignContract[0].reset();
                                    frmAssignContract.find("select").val("").trigger("change");
                                }
                            }

                            var singleBlock = $(currentForm).find("#block");
                            if (typeof singleBlock !== "undefined") {
                                singleBlock.val("").trigger("change");
                            }

                            var singleLot = $(currentForm).find("#unit_id");
                            if (typeof singleLot !== "undefined") {
                                singleLot.val("").trigger("change");
                            }
                            tempChecklistSelected = [];
                        }
                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                            .prop("disabled", false);
                    }
                });
            } else {
                toastr.warning("No task has been selected!", "Please select a task", 5000);
            }

            return false;
        }
    });

    var select2WoVersion = _contractContent.find("select#wo_version").select2({
        width: "100%",
        placeholder: "Select an option",
        ajax: {
            url: baseUrl("pms/contract/get_select2_wo_version"),
            global: false,
            dataType: "json",
            delay: 250,
            processResults: function (data) {
                return data;
            }
        }
    }).on("select2:select", function (e) {
        var target = $(e.target);
        var currentValue = e.target.value;
        _contractContent.find("#template_preview, #checklist_preview, #selectedItems").empty();
        if (currentValue) {
            $.ajax({
                url: baseUrl("pms/contract/get_wo_version_template_content/" + currentValue),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        var preview = _contractContent.find("#template_preview");
                        if (typeof preview !== "undefined") {
                            preview.empty().html(json.html);
                            if (json.is_empty) { dtTaskItems.ajax.reload(); }
                            var select2Project = preview.find("select#project_id");
                            var select2Checklist = preview.find("select#checklist_id");
                            var select2Units = preview.find("select#unit_id");
                            var select2Blocks = preview.find("select#block");
                            var select2BlockMultiple = preview.find("select#block_multiple");
                            var select2Lots = preview.find("select#lots");
                            var otherBlockLot = preview.find("input#other-block_lot");
                            var BlockLot = preview.find("select#block_lot");

                            if (typeof select2Project !== "undefined") {
                                var select2ProjectContent = select2Project.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
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
                                    tempProjectId = e.target.value;
                                    var target = $(e.target);
                                    select2Checklist.val("").trigger("change");
                                    var treeChecklist = _contractContent.find("#checklist_preview");
                                    if (typeof treeChecklist !== "undefined") {
                                        treeChecklist.jstree("destroy");
                                        treeChecklist.empty().text("NO CHECKLIST PREVIEW!");
                                    }
                                    target.validate();
                                });
                            }

                            if (typeof select2Checklist !== "undefined") {
                                var select2ChecklistContent = select2Checklist.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    ajax: {
                                        url: baseUrl("pms/contract/get_select2_checklist"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.project_id = tempProjectId;
                                            return params;
                                        }, processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on("select2:select", function (e) {
                                    tempChecklist = e.target.value;
                                    if (typeof select2Units !== "undefined") { select2Units.val("").trigger("change"); }
                                    if (typeof select2Blocks !== "undefined") { select2Blocks.val("").trigger("change"); }
                                    if (typeof select2BlockMultiple !== "undefined") { select2BlockMultiple.val("").trigger("change"); }
                                    if (typeof select2Lots !== "undefined") { select2Lots.val("").trigger("change"); }
                                    $.ajax({
                                        url: baseUrl("pms/contract/get_checklist_content/" + tempChecklist),
                                        dataType: "json",
                                        success: function (json) {
                                            if (json.response) {
                                                var treeChecklist = _contractContent.find("#checklist_preview");
                                                if (typeof treeChecklist !== "undefined") {
                                                    if (json.is_empty) { dtTaskItems.ajax.reload(); }
                                                    treeChecklist.jstree("destroy");
                                                    treeChecklist.jstree({
                                                        core: {
                                                            data: json.data,
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
                                                        var tempSelectedItems = _contractContent.find("#selected_items");
                                                        if (typeof tempSelectedItems !== "undefined" && tempSelectedItems.length == 1) {
                                                            tempSelectedItems.val(selected).validate();
                                                        }

                                                        if (typeof select2Blocks !== "undefined") { select2Blocks.val("").trigger("change"); }
                                                        if (typeof select2BlockMultiple !== "undefined") { select2BlockMultiple.val("").trigger("change"); }
                                                        if (typeof select2Lots !== "undefined") { select2Lots.val("").trigger("change"); }
                                                        if (typeof select2Units !== "undefined") { select2Units.val("").trigger("change"); }
                                                        var otherSwitch = _contractContent.find("#other-block_lot");
                                                        if (typeof otherSwitch !== "undefined") {
                                                            otherSwitch.prop("checked", false);
                                                        }
                                                        var otherContent = _contractContent.find("#others-content");
                                                        if (typeof otherContent !== "undefined") {
                                                            otherContent.empty();
                                                        }

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
                                                        var tempSelectedItems = _contractContent.find("#selected_items");
                                                        if (typeof tempSelectedItems !== "undefined" && tempSelectedItems.length == 1) {
                                                            tempSelectedItems.val(selected).validate();
                                                        }

                                                        if (typeof select2Blocks !== "undefined") { select2Blocks.val("").trigger("change"); }
                                                        if (typeof select2BlockMultiple !== "undefined") { select2BlockMultiple.val("").trigger("change"); }
                                                        if (typeof select2Lots !== "undefined") { select2Lots.val("").trigger("change"); }
                                                        if (typeof select2Units !== "undefined") { select2Units.val("").trigger("change"); }
                                                        var otherSwitch = _contractContent.find("#other-block_lot");
                                                        if (typeof otherSwitch !== "undefined") {
                                                            otherSwitch.prop("checked", false);
                                                        }
                                                        var otherContent = _contractContent.find("#others-content");
                                                        if (typeof otherContent !== "undefined") {
                                                            otherContent.empty();
                                                        }

                                                        //currentJsTree.close_node(parentIds);
                                                        //currentJsTree.disable_node(nodeIds);
                                                        currentJsTree.open_node(data.node);
                                                    }).on("ready.jstree", function () {
                                                        var tempSelectedItems = _contractContent.find("#selected_items");
                                                        if (typeof tempSelectedItems == "object" && tempSelectedItems.length == 0) {
                                                            $('<input type="hidden" id="selected_items" name="selected_items" value="" data-validation="required" data-validation-error-msg-required="Select on the above task" />').appendTo("#selectedItems");
                                                        }
                                                    });

                                                }
                                            }
                                        }
                                    });
                                    var target = $(e.target);
                                    target.validate();
                                });
                            }

                            if (typeof select2Blocks !== "undefined") {
                                var select2BlockContent = select2Blocks.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    ajax: {
                                        url: baseUrl("pms/contract/get_select2_blocks"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.block = tempAddedBlock;
                                            params.checklist_id = tempChecklist;
                                            params.project_id = tempProjectId;
                                            params.selected_items = tempChecklistSelected;
                                            return params;
                                        }, processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on("select2:select", function (e) {
                                    tempBlock = e.target.value;
                                    var target = $(e.target);
                                    target.validate();
                                });
                            }

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
                                    target.validate();
                                }).on('select2:unselect', function (e) {
                                    tempMultipleBlock = $(e.target).val();
                                    var target = $(e.target);
                                    target.validate();
                                });

                                vmRenderedLots = new Vue({
                                    el: "#block_lot-content",
                                    data: defaultRenderedLots(),
                                    methods: {
                                        resetData: function () {
                                            Object.assign(this.$data, defaultRenderedLots());
                                            tempMultipleBlock = [];
                                            _contractContent.find("#checklist_preview").jstree().deselect_all(true);
                                            _contractContent.find("#checklist_preview").jstree('close_all');
                                            _contractContent.find("#selected_items").val("");

                                        }
                                    }
                                });
                            }

                            if (typeof select2Lots !== "undefined") {
                                var select2LotsContent = select2Lots.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    scrollAfterSelect: false,
                                    closeOnSelect: false,
                                    ajax: {
                                        url: baseUrl("pms/contract/get_select2_lots"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.block = tempBlock;
                                            params.checklist_id = tempChecklist;
                                            params.selected_items = tempChecklistSelected;
                                            return params;
                                        }, processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on('select2:selecting', function (e) {
                                    $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop());
                                }).on('select2:select', function (e) {
                                    $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop'));
                                    var target = $(e.target);
                                    target.validate();
                                });

                                select2Lots.css("width", "auto");
                            }

                            if (typeof BlockLot !== "undefined") {
                                var select2LotsContent = BlockLot.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    scrollAfterSelect: false,
                                    closeOnSelect: false,
                                    ajax: {
                                        url: baseUrl("pms/contract/get_select2_block_lot"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.checklist_id = tempChecklist;
                                            params.selected_items = tempChecklistSelected;
                                            return params;
                                        }, processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on('select2:selecting', function (e) {
                                    $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop());
                                }).on('select2:select', function (e) {
                                    $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop'));
                                    var target = $(e.target);
                                    target.validate();
                                });

                                select2Lots.css("width", "auto");
                            }

                            if (typeof select2Units !== "undefined") {
                                var select2Unit = select2Units.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    ajax: {
                                        url: baseUrl("pms/contract/get_select2_units"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.unit_id = tempAddedUnit;
                                            params.checklist_id = tempChecklist;
                                            params.project_id = tempProjectId;
                                            params.selected_items = tempChecklistSelected;
                                            return params;
                                        }, processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on("select2:select", function (e) {
                                    var target = $(e.target);
                                    tempUnit = e.target.value;
                                    target.validate();
                                });
                            }

                            if (typeof otherBlockLot !== "undefined") {
                                var otherContent = $("#others-content");
                                otherBlockLot.on("change", function () {
                                    var self = $(this);
                                    isChecked = self.is(":checked");
                                    if (typeof otherContent !== "undefined") {
                                        if (isChecked == true) {
                                            $.ajax({
                                                url: baseUrl("pms/contract/render_form_content/render_other_block_lot"),
                                                dataType: "json",
                                                success: function (json) {
                                                    if (json.response) {
                                                        otherContent.empty().html(json.html);
                                                        var select2MultipleUnits = $("select#other_units");

                                                        if (typeof select2MultipleUnits !== "undefined") {
                                                            var select2MultipleUnits = select2MultipleUnits.select2({
                                                                width: "100%",
                                                                placeholder: "Select an option",
                                                                ajax: {
                                                                    url: baseUrl("pms/contract/get_select2_multiple_units"),
                                                                    global: false,
                                                                    dataType: "json",
                                                                    delay: 250,
                                                                    data: function (params) {
                                                                        params.checklist_id = tempChecklist;
                                                                        params.project_id = tempProjectId;
                                                                        params.unit_id = tempUnit;
                                                                        params.selected_items = tempChecklistSelected;
                                                                        return params;
                                                                    }, processResults: function (data) {
                                                                        return data;
                                                                    }
                                                                }
                                                            }).on("select2:select", function (e) {
                                                                var target = $(e.target);
                                                                target.validate();
                                                            });
                                                        }
                                                    } else {
                                                        otherContent.empty();
                                                        toastr.error("Nothing to render for other block and lots", "Error Rendering", 5000);
                                                    }
                                                }
                                            });
                                        } else {
                                            otherContent.empty();
                                        }
                                    }
                                });
                            }
                        }
                    }
                }
            });
        }
        target.validate();
    });
}

var generateBlockLots = function () {
    tempMultipleBlock = [];
    tempMultipleBlock.push(tempBlock);
    getMultipleLotsByBlocks(tempMultipleBlock, tempChecklist, tempProjectId, tempChecklistSelected);
}
var generateLots = function () {
    getMultipleLotsByBlocks(tempMultipleBlock, tempChecklist, tempProjectId, tempChecklistSelected);
}

var getMultipleLotsByBlocks = function (
    arrBlocks = [],
    tempChecklist = null,
    tempProjectId = null,
    tempChecklistSelected = []) {

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
        },
        success: function (json) {
            if (json.response) {
                vmGenerateLot.count = json.count;
                vmGenerateLot.blocks = json.blocks;
                vmGenerateLot.units = Object.assign({}, json.units);
                modalGenerateLot.modalShow(function () {
                    vmGenerateLot.resetChecked();
                });
            }
        }
    });
}

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
                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                        toastr.success(json.toastr_msg, "Saving of data successful", 5000);
                        setTimeout(function () {
                            if (doRedirect) { window.location.replace(json.redirect_url); }
                            else { window.location.reload(); }
                        }, 1500);
                    } else {
                        $(currentForm)
                            .find(".btn-submit")
                            .removeClass("m-btn--custom m-loader m-loader--light m-loader--right")
                            .prop("disabled", false);
                        toastr.error(json.toastr_msg, "Error saving data", 5000);
                    }
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