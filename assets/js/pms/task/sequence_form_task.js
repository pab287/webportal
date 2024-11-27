var tempModal = new taskModal();
var vmRenderTask = renderVueTask();

var taskContentPreview = $("#task-preview");
var modalTempContainer = $("#modalTempContainer");
var modalTempContainerLg = $("#modalTempContainerLg");
var modalEditTask, modalSubTaskActivity, modalTodoTask, modalPunchlistTask, modalPunchlistTaskLog;
var vmTaskActivityLog, vmUpdateStatus, vmTodoTask, vmPunchlistTask, vmPunchlistTaskLog;

var generateCurrentTask = function (id, refresh = false) {
    $.ajax({
        url: baseUrl("pms/task/generate_current_task"),
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash, id: id },
        success: function (json) {
            if (json.response) {
                getCurrentAcitivities(id);
                getCurrentContractors(id);
                getCurrentPastDue(id);
                getCurrentTodoTask(id);
                getCurrentPunchlistedTask(id);
                getTaskPunchlistLogs(id);
                generateTreeData(json.data, json.completed, refresh);
                vmTaskData.row = Object.assign({}, json.row);
            }
        }
    });
}

var getSelectedTaskRender = function (unitId, itemId, callback) {
    var xhr = $.ajax({
        global: false,
        url: baseUrl("pms/task/get_selected_task_data"),
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash, unit_id: unitId, item_id: itemId },
        success: function (json) {
            if (json.response) {
                $("#task-preview").empty().append(json.html);
                var tempRow = json.row;
                vmRenderTask = renderVueTask();
                var tempInstance = vmRenderTask.instance;
                tempInstance.row = Object.assign({}, tempRow);
                tempInstance.contractor = Object.assign({}, json.contractor);
                tempInstance.contractor_count = json.contractor_count;
                tempInstance.item_id = json.item_id;
                tempInstance.unit_id = json.unit_id;
                tempInstance.current_remarks = tempRow.remarks;

                setTimeout(function () {
                    var portletPreview = $('#m_portlet_preview').mPortlet();
                }, 500);
            }
        }
    });
    if (typeof callback == "function") {
        xhr.done(function () {
            return callback();
        });
    } else {
        return this;
    }
}

$(document).on("click", "#task-preview .btnClose", function () {
    $("#task-preview").empty();
});

var renderTaskInstance = function (json = {}, update = false) {
    var currentRemarks = "";
    if (typeof json.row !== "undefined" && json.row == "object") {
        var rowData = json.row;
        currentRemarks = rowData.remarks;
    }
}

var generateTreeData = function (data, completedIds, refresh = false) {
    var treeAcl = $("#tree_task_item-list");
    if (refresh == true) { $(treeAcl).jstree("destroy"); }
    $(treeAcl)
        .jstree({
            core: {
                data: data,
                check_callback: true,
                dblclick_toggle: false,
            },
            types: {
                root: { icon: "fa fa-square" },
                child: { icon: "fa fa-square", a_attr: false },
                completed: { icon: "fa fa-check-square", a_attr: false },
                terminated: { icon: "fa fa-window-close m--font-danger", a_attr: false },
                punchlist: { icon: "fa fa-check-square m--font-brand" },
                punchlisted: { icon: "fa fa-check-square m--font-success" }
            },
            conditionalselect: function (node) {
                return (node.parent == "#") ? true : false;
            },
            plugins: ["noclose", "types", "conditionalselect", "wholerow"]
        })
        .on("ready.jstree", function () {
            var _this = this;
            $(_this).jstree("open_all");
            $(_this).find("li.jstree-parent_node").each(function () {
                var currentId = parseInt(this.id);
                var currentValue = jQuery.inArray(currentId, completedIds);
                if (currentValue == -1) {
                    $(this).children(".jstree-icon.jstree-ocl")
                        .addClass("jstree-hidden").on("click", function (e) { return false; });
                }
            });
        })
        .on("select_node.jstree", function (e, data) {
            var currentJsTree = $(this).jstree();
            var selectedId = parseInt(data.selected.toString());
            getSelectedTaskRender(_tempContentData.id, selectedId);
            currentJsTree.deselect_node(data.node);
        });
}

var vmTaskData = new Vue({
    el: "#task-content",
    data: {
        row: {},
        activity: {},
        activity_count: 0,
        contractors: {},
        contractor_count: 0,
        pastdue: {},
        pastdue_count: 0,
        todo: {},
        todo_count: 0,
        punchlisted: {},
        punchlisted_count: 0,
        punchlist_count: 0,
        punchlist_log: {},
        punchlist_log_count: 0,
        temp_status: "",
        class_status: "",
    },
    computed: {
        updateTempStatus: function () {
            var _this = this;
            var currentRow = _this.row;
            if (typeof currentRow.sf_status !== "undefined") {
                currentRow.sf_status = parseInt(currentRow.sf_status);
                switch (currentRow.sf_status) {
                    case 1: _this.temp_status = "ON GOING"; break;
                    case 2: _this.temp_status = "ON HOLD"; break;
                    case 3: _this.temp_status = "COMPLETED"; break;
                    default: _this.temp_status = "AWAITING"; break;
                }
            }
            return _this.temp_status;
        }, updateClassStatus: function () {
            var _this = this;
            var currentRow = _this.row;
            if (typeof currentRow.sf_status !== "undefined") {
                currentRow.sf_status = parseInt(currentRow.sf_status);
                switch (currentRow.sf_status) {
                    case 1: _this.class_status = "m--font-success"; break;
                    case 2: _this.class_status = "m--font-warning"; break;
                    case 3: _this.class_status = "m--font-primary"; break;
                    default: _this.class_status = ""; break;
                }
            }
            return _this.class_status;
        }
    },
    methods: {
        setRedirectAction: function (row) {
            var tempUrl = baseUrl("pms/project/index");
            var projectId = (row.project_id) ? parseInt(row.project_id) : 0;
            if (projectId) { tempUrl = baseUrl("pms/project/units/" + projectId); }
            window.location.href = tempUrl;
        },
        updateRemarks: function (unitId) {
            if (unitId) {
                $.ajax({
                    url: baseUrl("pms/project/get_project_unit_remarks/" + unitId),
                    dataType: "json",
                    success: function (json) {
                        if (json.response) {
                            if (typeof modalTempContainer !== "undefined") {
                                var modalContent = modalTempContainer.find(".modal-content");
                                if (typeof modalContent !== "undefined") {
                                    modalContent.empty().html(json.html);
                                    modalTempContainer.modal("show");
                                    validateUnitRemarks();
                                }
                            }
                        }
                    }
                });
            }
        },
        updateStatus: function (unitId) {
            if (unitId) {
                $.ajax({
                    url: baseUrl("pms/project/get_project_unit_status/" + unitId),
                    dataType: "json",
                    success: function (json) {
                        if (json.response) {
                            if (typeof modalTempContainer !== "undefined") {
                                var modalContent = modalTempContainer.find(".modal-content");
                                if (typeof modalContent !== "undefined") {
                                    modalContent.empty().html(json.html);
                                    modalTempContainer.modal("show");
                                    var vmTempStatus = new Vue({
                                        el: "#frmUpdateUnitStatus",
                                        data: { row: {} },
                                    });
                                    vmTempStatus.row = Object.assign({}, json.row);
                                    validateUnitStatus();
                                }
                            }
                        }
                    }
                });
            }
        },
        previewTodoTask: function (unitId) {
            $.ajax({
                url: baseUrl("pms/task/generate_todo_task/" + unitId),
                global: false,
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vmTodoTask.rows = Object.assign({}, json.rows);
                        modalTodoTask.modalShow();
                    }
                }
            });
        },
        previewPunchlistTask: function (unitId) {
            $.ajax({
                url: baseUrl("pms/task/generate_punchlist_task/" + unitId),
                global: false,
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vmPunchlistTask.unit_id = json.unit_id;
                        vmPunchlistTask.getJsTreeData(json.data);
                        modalPunchlistTask.modalShow(function () {
                            vmPunchlistTask.validateFields();
                        });
                    }
                }
            });
        },
        previewPunchlistLog: function (id) {
            $.ajax({
                url: baseUrl("pms/task/generate_punchlist_task_log/" + id),
                global: false,
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vmPunchlistTaskLog.row = Object.assign({}, json.row);
                        vmPunchlistTaskLog.getJsTreeData(json.data);
                        modalPunchlistTaskLog.modalShow();
                    }
                }
            });
        },
        renderPunchlistedItems: function () {
            var currentVm = this;
            var currentElement = currentVm.$el;
            var jsTreePunchlisted = $(currentElement).find("#tree_punchlisted_item-list");
            if (typeof jsTreePunchlisted !== "undefined" && jsTreePunchlisted.length > 0) {
                var tempData = JSON.parse(JSON.stringify(currentVm.punchlisted));
                var arrTempData = [];
                $.each(tempData, function (ii, vv) {
                    arrTempData.push(vv);
                });
                jsTreePunchlisted.jstree("destroy");
                jsTreePunchlisted.jstree({
                    core: { data: arrTempData },
                    types: {
                        root: { icon: "fa fa-check-square m--font-brand" },
                        child: { icon: "fa fa-check-square m--font-brand" },
                    },
                    plugins: ["types"],
                }).on("ready.jstree", function () {
                    $(this).jstree("open_all");
                });
            }
        }
    }, mounted: function () { }
});

var validateUnitRemarks = function () {
    if (typeof modalTempContainer !== "undefined") {
        $.validate({
            form: "#frmUpdateUnitRemarks",
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
                            vmTaskData.row = Object.assign({}, json.row);
                            toastr.success(
                                json.toastr_msg,
                                "Unit remarks has been updated.",
                                5000
                            );
                            if (typeof modalTempContainer !== "undefined") {
                                modalTempContainer.modal("hide");
                            }
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error updating unit remarks!",
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
}

var validateUnitStatus = function () {
    if (typeof modalTempContainer !== "undefined") {
        $.validate({
            form: "#frmUpdateUnitStatus",
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
                            vmTaskData.row = Object.assign({}, json.row);
                            toastr.success(
                                json.toastr_msg,
                                "Status has been updated.",
                                5000
                            );
                            if (typeof modalTempContainer !== "undefined") {
                                modalTempContainer.modal("hide");
                            }
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error updating status!",
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
}

var validateTaskTimeline = function () {
    if (typeof modalTempContainer !== "undefined") {
        $.validate({
            form: "#frmNewTask",
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
                                "Task item has been added.",
                                5000
                            );
                            var tempInstance = vmRenderTask.instance;
                            var currentRow = tempInstance.row;
                            generateCurrentTask(_tempContentData.id, true);
                            var xhrData = getCurrentSubtaskData(currentRow.task);
                            xhrData.done(function (json) {
                                if (json.response) {
                                    vmRenderTask.setCurrentSubTask(json.task);
                                }
                            });
                            if (typeof modalTempContainer !== "undefined") { modalTempContainer.modal("hide"); }
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error adding task item!",
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
}

var validateWoCodeGenerator = function () {
    if (typeof modalTempContainerLg !== "undefined") {
        $.validate({
            form: "#frmGenerateCode",
            lang: "en",
            scrollToTopOnError: false,
            onSuccess: function (form) {
                var currentForm = form[0];
                var formUrl = currentForm.action;
                var formMethod = currentForm.method;
                var formData = $(currentForm).serialize();

                $.ajax({
                    url: formUrl,
                    type: formMethod,
                    dataType: "json",
                    data: formData,
                    success: function (json) {
                        if (json.response) {
                            var data = json.data;
                            modalTempContainerLg.find("#wo_code")
                                .val(data.wo_code)
                                .validate();
                            modalTempContainerLg.find("#wo_issued_date").val(data.issued_date);
                            modalTempContainerLg.find("#wo_due_date").val(data.due_date);
                            modalTempContainerLg.find("#task_items").val(data.sub_task);
                        }
                    }
                });

                return false;
            }
        });
        var inputs = modalTempContainerLg.find("#frmGenerateCode select, #frmGenerateCode input");
        if (typeof inputs !== "undefined" && inputs.length > 0) {
            inputs.on("change", function (e) {
                modalTempContainerLg.find("#wo_code").val("");
            });
        }
    }
}
var validateNewContract = function () {
    var vueInstanceData = {};
    if (typeof modalTempContainerLg !== "undefined") {
        $.validate({
            form: "#frmAddNewContract",
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
                            if (typeof modalTempContainerLg !== "undefined") {
                                modalTempContainerLg.modal("hide");
                                getSelectedTaskRender(json.unit_id, json.item_id);
                            }
                        } else {
                            toastr.error(
                                json.toastr_msg,
                                "Error adding contract information data!",
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

        var select2TaskContractor = modalTempContainerLg.find("select#contractor_id").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalTempContainerLg,
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

        var select2TaskIncharge = modalTempContainerLg.find("select#task_incharge").select2({
            width: "100%",
            placeholder: "Select an option",
            dropdownParent: modalTempContainerLg,
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
    }
    return vueInstanceData;
}

jQuery(document).ready(function () {
    generateCurrentTask(_tempContentData.id);
    modalEditTask = tempModal.edit_subtask();
    modalSubTaskActivity = tempModal.edit_subtask_activity();
    modalTodoTask = tempModal.preview_todo_task();
    modalPunchlistTask = tempModal.preview_punchlist_task();
    modalPunchlistTaskLog = tempModal.preview_punchlist_task_log();
});