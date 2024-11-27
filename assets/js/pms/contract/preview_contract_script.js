var tempModal = new contractModal();
var modalContractStatus, modalContractRemarks, modalContractIncharge, modalContractExtension,
    modalContractChangeOrder, modalContractForeman, modalContractLeadman, modalEditRedirect,
    modalAdjustmentRedirect;
var vmContractStatus, vmContractRemarks, vmContractIncharge, vmContractExtension, vmContractChangeOrder,
    vmContractForeman, vmContractLeadman, vmEditRedirect, vmAdjustmentRedirect;
var tempContractItemTable;

var tableContractItem = $("#table-task_contract");
var tableChangeOrder = $("#table-change_order_contract");
var tableContractExtension = $("#table-task_extension_contract");
if (typeof tableContractExtension !== "undefined") {
    var dtContractExtension = tableContractExtension.DataTable({
        dom: 'ti',
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_contract_extension_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.contract_id = _tempContentData.id;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtContractExtension.ajax.reload(null, false);
                }
            }
        }, columns: [
            { data: "extension_date", "title": "Extension Date", width: "10%" },
            { data: "due_date", "title": "Due Date", width: "12%" },
            { data: "created_name", "title": "Extended By", width: "30.5%" },
            { data: "extended_name", "title": "Approved / Disapproved By", width: "32.5%" },
            { data: "extension_status", "title": "Status", width: "15%", className: "text-center", defaultContent: "0" }
        ], columnDefs: [{
            targets: "_all",
            defaultContent: ""
        }, {
            data: "created_name",
            defaultContent: "---",
            targets: 2,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if (row.extended_by !== "0") {
                    row.remarks = (typeof row.remarks !== "undefined" && row.remarks) ? row.remarks : "---";
                    tempHtml += "<div class='custom-details'>";
                    tempHtml += "<p class='m--marginless m--font-boldest'>" + data + "</p>";
                    tempHtml += "<p class='m--marginless'><small class='m--font-boldest'> REMARKS: <span class='m--font-brand'>" + row.remarks + "</span></small></p>";
                    tempHtml += "<p class='m--marginless'><small>" + row.created_at + "</small></p>";
                    tempHtml += "</div>";
                } else {
                    tempHtml = "---";
                }
                return tempHtml;
            }
        }, {
            data: "extended_name",
            defaultContent: "---",
            targets: 3,
            orderable: false,
            render: function (data, type, row, meta) {
                var tempHtml = "";
                if (row.extended_by !== "0") {
                    row.extended_remarks = (typeof row.extended_remarks !== "undefined" && row.extended_remarks) ? row.extended_remarks : "---";
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
            targets: -1,
            render: function (data, type, row, meta) {
                _status = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light m--font-boldest'>Pending</span>";
                switch (data) {
                    case "1": _status = "<span class='m-badge m-badge--custom-wide m-badge--success m-badge--wide m--font-light m--font-boldest'>Approved</span>"; break;
                    case "2": _status = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide m--font-light m--font-boldest'>Cancelled</span>"; break;
                    default: _status = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-dark m--font-boldest'>Pending</span>"; break;
                }

                return _status;
            }
        }],
    });
}

if (typeof tableContractItem !== "undefined") {
    var dtContractItem = tableContractItem.DataTable({
        dom: 'ti',
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_contract_item_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.contract_id = _tempContentData.id;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtContractItem.ajax.reload(null, false);
                }
            }
        },
        columns: [
            { data: "item", width: "*" },
            { data: "qty", width: "10%", className: "text-right", defaultContent: "0" },
            { data: "lots", width: "10%", className: "text-center", defaultContent: "0" },
            { data: "unit", width: "10%", className: "text-center", defaultContent: "---" },
            { data: "tariff", width: "15%", className: "text-right", defaultContent: "0.00" },
            { data: "total", width: "15%", className: "text-right", defaultContent: "0.00" }
        ],
        columnDefs: [{
            targets: "_all",
            defaultContent: ""
        }],
        drawCallback: function (settings, json) {
            var json = settings.json;
            var grandTotal = json.grand_total;
            var api = this.api(), data;
            $(api.column(5).footer()).html(grandTotal);
        }
    });

    var tempDataTableStatus = function (status) {
        tempState = "AWAITING";
        if (status) {
            switch (status) {
                case "1": tempState = "TO DO"; break;
                case "2": tempState = "DEFERRED"; break;
                case "3": tempState = "IN PROGRESS"; break;
                case "4": tempState = "COMPLETED"; break;
                case "5": tempState = "TERMINATED"; break;
                default: tempState = "AWAITING"; break;
            }
        }
        return tempState;
    }
}

if (typeof tableChangeOrder !== "undefined") {
    var dtChangeOrder = tableChangeOrder.DataTable({
        dom: 'ti',
        serverSide: true,
        processing: true,
        ordering: false,
        ajax: {
            url: baseUrl("pms/contract/do_post_event/get_change_order_datatable_request"),
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.contract_id = _tempContentData.id;
                return d;
            }, error: function (xhr, error, code) {
                if (error == "parsererror") {
                    toastr.warning(code, "Data Table Reloading", 5000);
                    dtContractItem.ajax.reload(null, false);
                }
            }
        },
        columns: [
            { data: "current_code", width: "15%" },
            { data: "previous_code", width: "15%" },
            { data: "remarks", width: "*", defaultContent: "" },
            /*** { data: null, width: "5%", className: "text-center" } ***/
        ],
        columnDefs: [{
            targets: "_all",
            defaultContent: ""
        }, {
            data: "remarks",
            targets: 2,
            render: function (data, type, row, meta) {
                var _html = "<div class='custom-details'>";
                _html += "<p>" + data + "</p>";
                _html += "<p class='m--marginless'><small class='m--font-bolder'>" + row.created_by + "</small> <small>" + row.created_at + "</small></p>";
                _html += "</div>";
                return _html;
            }
        } /*** {
            targets: -1,
            render: function (data, type, row, meta) {
                return tempDataTableActions(row.id);
            }
        } ***/
        ],
        drawCallback: function (settings, json) {
            var json = settings.json;
            var recordsTotal = json.recordsTotal;
        }
    });

    var tempDataTableActions = function (id) {
        tempActions = "";
        if (id) {
            tempActions = "<button class='m-btn btnEdit m-btn--pill m-btn--hover-brand btn btn-sm btn-brand'>PREVIEW</button>";
        }
        return tempActions;
    }
}

var vmContractExtensionData = new Vue({
    el: "#contract_extension-content",
    data: { row: {}, show_extension: true }
});

var vmContractProject = new Vue({
    el: "#contract_project-content",
    data: { rows: {}, records: false },
    methods: {
        redirectToTask: function (id) {
            window.open(baseUrl("pms/project/task/" + id), "_blank");
        }
    }
});

var altMethods = {
    checkDateExtension: function () {
        var _this = this;
        var currentRow = _this.row;
        var _tempDueDate = currentRow.due_date;
        if (typeof currentRow.extension_id !== "undefined" && currentRow.extension_id !== "0") {
            _tempDueDate = currentRow.extension_date;
        }

        var dueDate = moment(_tempDueDate).format("YYYY-MM-DD");
        var currentDate = moment().format("YYYY-MM-DD");
        return (currentDate >= dueDate && currentRow.status == 1) ? true : false;
    }, updateDueDate: function (id) {
        var tempExtension = this.checkDateExtension();
        if (tempExtension) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vmContractExtension.row = Object.assign({}, json.row);
                        vmContractExtension.validateFields();
                        modalContractExtension.modalShow();
                    }
                }
            });
        } else {
            toastr.info("Task due date is currently not past due!", "Task extension", { timeOut: 5000 });
        }
    }, editContract: function (id) {
        if (id) {
            $.ajax({
                url: baseUrl("pms/contract/check_task_history/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        if (json.is_redirect) {
                            window.location.replace(json.redirect_url);
                        } else {
                            vmEditRedirect.count = json.count;
                            vmEditRedirect.rows = Object.assign({}, json.data);
                            modalEditRedirect.modalShow();
                        }
                    }
                }
            });
        } else {
            return false;
        }
    }, adjustmentContract: function (id) {
        if (id) {
            $.ajax({
                url: baseUrl("pms/contract/check_task_history/" + id + "/adjustment"),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        if (json.is_redirect) {
                            window.location.replace(json.redirect_url);
                        } else {
                            vmAdjustmentRedirect.count = json.count;
                            vmAdjustmentRedirect.rows = Object.assign({}, json.data);
                            modalAdjustmentRedirect.modalShow();
                        }
                    }
                }
            });
        } else {
            return false;
        }
    }, additionalContract: function (id) {
        if (id) {
            $.ajax({
                url: baseUrl("pms/contract/check_task_history/" + id + "/additional"),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        window.location.replace(json.redirect_url);
                    }
                }
            });
        } else {
            return false;
        }
    }
};

var vmContractTaskActions = new Vue({
    el: "#task-actions",
    data: { row: {}, show_extension: true },
    methods: altMethods,
});

var vmContractTask = new Vue({
    el: "#task-details",
    data: {
        row: {}, show_extension: true, items: {}, adjustments: {}, item_count: 0,
        item_counter: [], sub_total: [], grand_total: 0.00
    },
    mounted: function () {
        setTimeout(function () { mApp.initTooltips(); }, 1000);
    }
});

var vmContract = new Vue({
    el: "#contract-content",
    data: { row: {}, show_extension: true },
    methods: {
        changeOrder: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_contract_units/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vmContractChangeOrder.id = id;
                        vmContractChangeOrder.rows = Object.assign({}, json.rows);
                        vmContractChangeOrder.units = json.units;
                        vmContractChangeOrder.unit_cost = json.unit_cost;
                        vmContractChangeOrder.cost_count = json.cost_count;
                        vmContractChangeOrder.count = json.count;
                    }
                }
            })
            modalContractChangeOrder.modalShow(function () {
                if (typeof tempContractItemTable !== "undefined") { tempContractItemTable.ajax.reload(); }
                else { vmContractChangeOrder.renderItemTable(_tempContentData.id); }
                vmContractChangeOrder.validateFields();
            });
        },
        updateStatus: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalContractStatus.modalShow();
                        vmContractStatus.row = Object.assign({}, json.row);
                        vmContractStatus.validateFields();
                    }
                }
            });
        },
        updateRemarks: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalContractRemarks.modalShow();
                        vmContractRemarks.row = Object.assign({}, json.row);
                        vmContractRemarks.validateFields();
                    }
                }
            });
        },
        updateIncharge: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalContractIncharge.modalShow();
                        vmContractIncharge.row = Object.assign({}, json.row);
                        vmContractIncharge.validateFields();
                    }
                }
            });
        },
        updateLeadman: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalContractLeadman.modalShow();
                        vmContractLeadman.row = Object.assign({}, json.row);
                        vmContractLeadman.validateFields();
                    }
                }
            });
        },
        updateForeman: function (id) {
            $.ajax({
                url: baseUrl("pms/contract/get_current_contract/" + id),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        modalContractForeman.modalShow();
                        vmContractForeman.row = Object.assign({}, json.row);
                        vmContractForeman.validateFields();
                    }
                }
            });
        },
    }, mounted: function () {
        setTimeout(function () { mApp.initTooltips(); }, 1000);
    }
});

var getContractInformation = function (reloadTable = false) {
    var currentId = (typeof _tempContentData.id !== "undefined" && _tempContentData.id) ? _tempContentData.id : 0;
    $.ajax({
        url: baseUrl("pms/contract/get_current_contract/" + currentId),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var tempData = Object.assign({}, json.row);
                var tempItemData = Object.assign({}, json.items);
                var showExtension = (typeof tempData.extension_id !== "undefined" && tempData.extension_id !== "0") ? true : false;
                vmContract.row = tempData;
                vmContractTask.items = tempItemData;
                vmContractTask.adjustments = Object.assign({}, json.adjustment_items);
                vmContractTask.item_count = json.item_count;
                vmContractTask.item_counter = json.item_counter;
                vmContractTask.sub_total = json.sub_total;
                vmContractTask.grand_total = json.grand_total;
                //vmContract.show_extension = showExtension;

                vmContractExtensionData.row = tempData;
                //vmContractExtensionData.show_extension = showExtension;

                vmContractTask.row = tempData;
                vmContractTaskActions.row = tempData;
                //vmContractTask.show_extension = showExtension;
            }
        }
    });

    $.ajax({
        url: baseUrl("pms/contract/get_contract_project/" + currentId),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmContractProject.rows = Object.assign({}, json.rows);
                vmContractProject.records = true;
            }
        }
    });

    if (reloadTable) {
        dtContractItem.ajax.reload(null, false);
        dtContractExtension.ajax.reload(null, false);
    }
}

jQuery(document).ready(function () {
    getContractInformation();
    modalContractStatus = tempModal.edit_contract_status();
    modalContractRemarks = tempModal.edit_contract_remarks();
    modalContractIncharge = tempModal.edit_contract_incharge();
    modalContractLeadman = tempModal.edit_contract_leadman();
    modalContractForeman = tempModal.edit_contract_foreman();
    modalContractExtension = tempModal.edit_contract_extension();
    modalContractChangeOrder = tempModal.contract_change_order();
    modalEditRedirect = tempModal.edit_redirect_task();
    modalAdjustmentRedirect = tempModal.adjustment_redirect_task();
});