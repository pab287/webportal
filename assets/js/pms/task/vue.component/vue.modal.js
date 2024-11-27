var taskModal = function () {
    var modalSubtask = new vueModal({
        id: "modalEditSubTask",
        setContentId: "editSubTask",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_edit_modal_subtask"),
                dataType: "json",
            }
        }, initVueInstance: function (modal) {
            var currentModal = modal;
            if (typeof currentModal.contentId !== "undefined") {
                var tempInstance = new Vue({
                    el: "#" + currentModal.contentId,
                    data: { row: {} },
                    methods: {
                        updateRow: function (e) {
                            var _this = this;
                            var currentName = e.target.name;
                            var currentValue = e.target.value;
                            var currentRow = _this.row;
                            return _this.row = Object.assign({}, currentRow, { [currentName]: currentValue });
                        },
                        checkDatePicker: function (e) {
                            console.log(e);
                        },
                        validateFields: function (targetForm) {
                            $.validate({
                                form: "#" + targetForm,
                                lang: "en",
                                scrollToTopOnError: false,
                                validateOnBlur: false,
                                onSuccess: function (form) {
                                    var currentForm = form[0];
                                    var formUrl = currentForm.action;
                                    var formType = currentForm.method;
                                    var formData = $(currentForm).serialize();
                                    $.ajax({
                                        url: formUrl,
                                        type: formType,
                                        data: formData,
                                        dataType: "json",
                                        success: function (json) {
                                            if (json.response) {
                                                var tempInstance = vmRenderTask.instance;
                                                var currentRow = tempInstance.row;
                                                currentModal.modalClose();
                                                toastr.success(json.toast_msg, "Update Subtask Successful", 5000);
                                                generateCurrentTask(_tempContentData.id, true);
                                                var xhrData = getCurrentSubtaskData(currentRow.task);
                                                xhrData.done(function (json) {
                                                    if (json.response) {
                                                        vmRenderTask.setCurrentSubTask(json.task);
                                                    }
                                                });
                                            } else {
                                                toastr.error(json.toast_msg, "Error Updating Subtask", 5000);
                                            }
                                        }
                                    });
                                    return false;
                                }
                            });
                        },
                        updateDatePickerDates: function (cdate1, cdate2) {
                            var _this = this;
                            var currentElement = $(_this.$el);
                            var currentRow = _this.row;
                            var tempDatePicker = currentElement.find(".form-datepicker");
                            tempDatePicker.datepicker("clearDates");
                            tempDatePicker.datepicker("destroy");
                            currentElement.find("#issued_date").datepicker({
                                orientation: "bottom left",
                                templates: {
                                    leftArrow: '<i class="la la-angle-left"></i>',
                                    rightArrow: '<i class="la la-angle-right"></i>'
                                },
                                startDate: cdate1,
                                endDate: cdate2,
                                format: "yyyy-mm-dd",
                                autoclose: true
                            }).datepicker("setDate", currentRow.issued_date)
                                .on("changeDate", function (e) {
                                    var currentTarget = e.target;
                                    var self = $(currentTarget);
                                    var rowData = _this.updateRow(e);
                                    currentRow = Object.assign({}, currentRow, rowData);
                                    /*** _this.checkDatePicker(e); **/
                                    var tempStartDate = moment(e.target.value).add(1, "d").format("YYYY-MM-DD");

                                    var dpDueDate = currentElement.find("#due_date");
                                    dpDueDate.datepicker("clearDates");
                                    dpDueDate.datepicker("destroy");
                                    dpDueDate.datepicker({
                                        orientation: "bottom left",
                                        templates: {
                                            leftArrow: '<i class="la la-angle-left"></i>',
                                            rightArrow: '<i class="la la-angle-right"></i>'
                                        },
                                        startDate: tempStartDate,
                                        endDate: cdate2,
                                        format: "yyyy-mm-dd",
                                        autoclose: true
                                    }).datepicker("setDate", currentRow.due_date)
                                        .on("changeDate", function (e) {
                                            var rowData = _this.updateRow(e);
                                            currentRow = Object.assign({}, currentRow, rowData);

                                            var currentTarget = e.target;
                                            var self = $(currentTarget);
                                            self.validate();
                                        });
                                    self.validate();
                                });

                            var xtempStartDate = moment(currentRow.issued_date).add(1, "d").format("YYYY-MM-DD");
                            currentElement.find("#due_date").datepicker({
                                orientation: "bottom left",
                                templates: {
                                    leftArrow: '<i class="la la-angle-left"></i>',
                                    rightArrow: '<i class="la la-angle-right"></i>'
                                },
                                startDate: xtempStartDate,
                                endDate: cdate2,
                                format: "yyyy-mm-dd",
                                autoclose: true
                            }).datepicker("setDate", currentRow.due_date)
                                .on("changeDate", function (e) {
                                    var rowData = _this.updateRow(e);
                                    currentRow = Object.assign({}, currentRow, rowData);

                                    var currentTarget = e.target;
                                    var self = $(currentTarget);
                                    self.validate();
                                });

                        }
                    },

                    mounted: function () {
                        var _this = this;
                        var currentElement = $(_this.$el);
                        var currentRow = _this.row;
                        if (typeof currentElement !== "undefined") {
                            if (typeof currentRow !== "undefined" && Object.keys(currentRow).length > 0) {
                                var tempOption = new Option(currentRow.contractor, currentRow.contractor_id, true, true);
                                var select2Contractor = currentElement.find("select#contractor_id").empty().html(tempOption);
                                select2Contractor.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    dropdownParent: $("#" + currentModal.id),
                                    ajax: {
                                        url: baseUrl("pms/task/get_select2_task_contractor/" + currentRow.task_id + "/" + currentRow.unit_id),
                                        dataType: "json",
                                        delay: 250,
                                        processResults: function (data) {
                                            return data;
                                        }
                                    }
                                }).on("select2:select", function (e) {
                                    _this.updateRow(e);
                                });

                                this.updateDatePickerDates(currentRow.cdate1, currentRow.cdate2);
                                var currentFormId = currentElement.find("form").attr("id");
                                if (typeof currentFormId !== "undefined") {
                                    _this.validateFields(currentFormId);
                                }
                            }
                        }
                    }
                });

                return tempInstance;
            }
        }
    });

    var modalSubtaskActivity = new vueModal({
        id: "modalSubTaskActivity",
        setSize: "modal-lg",
        setContentId: "subTaskActivity",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_subtask_activity"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmTaskActivityLog = new Vue({
                el: "#" + contentId,
                data: { row: {}, activity: {}, activity_count: 0 },
                methods: {
                    getSubtaskData: function (id, e) {
                        $.ajax({
                            url: baseUrl("pms/task/render_subtask_form/" + id),
                            dataType: "json",
                            success: function (json) {
                                if (json.response) {
                                    currentModal.modalClose();
                                    var tempInstance = modalSubtask.getVueInstance();
                                    tempInstance.row = Object.assign({}, json.row);
                                    tempInstance.$mount();
                                    setTimeout(modalSubtask.modalShow, 500);
                                }
                            }
                        });
                    }, validateFields: function () {
                        var _this = this;
                        $.validate({
                            form: "#frmAddActivity",
                            lang: "en",
                            scrollToTopOnError: false,
                            validateOnBlur: false,
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
                                                "Task activity has been added.",
                                                5000
                                            );
                                            currentForm.reset();
                                            _this.activity = Object.assign({}, json.rows);
                                            _this.activity_count = json.row_count;
                                            var xhrData = getCurrentSubtaskData(json.task_id);
                                            xhrData.done(function (json) {
                                                if (json.response) {
                                                    vmRenderTask.setCurrentSubTask(json.task);
                                                }
                                            });
                                            getCurrentAcitivities(json.unit_id);
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error adding task acitivity!",
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
            });
        }
    });

    var modalContractStatus = new vueModal({
        id: "modalContractStatus",
        setSize: "modal-md",
        setContentId: "contractStatus",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_status"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {

        }
    });

    var modalTodoTask = new vueModal({
        id: "modalTodoTask",
        setSize: "modal-xl",
        setContentId: "todoTask",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_todo_task"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmTodoTask = new Vue({
                el: "#" + contentId,
                data: { rows: {} },
                methods: {
                    redirectToTask: function (itemId) {
                        var unitId = _tempContentData.id;
                        if (typeof unitId !== "undefined" && unitId !== "0") {
                            getSelectedTaskRender(unitId, itemId, function () {
                                currentModal.modalClose();
                            });
                        }
                    }
                }
            });
        }
    });

    var modalPunchlistTask = new vueModal({
        id: "modalPunchlist",
        setSize: "modal-lg",
        setContentId: "punchlistTask",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_punchlist_task"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmPunchlistTask = new Vue({
                el: "#" + contentId,
                data: { unit_id: 0, selected_items: [] },
                methods: {
                    getJsTreeData: function (data) {
                        var tempInstance = this;
                        var treeTaskList = $("#" + contentId).find("#tree-punchlist_items");
                        var inputSelectedItems = $("#" + contentId).find("#selected_items");
                        if (typeof treeTaskList !== "undefined") {
                            treeTaskList.jstree("destroy");
                            treeTaskList.jstree({
                                core: {
                                    data: data,
                                    check_callback: true,
                                    dblclick_toggle: false,
                                },
                                conditionalselect: function (node, event) {
                                    return (node.parent == "#") ? false : true;
                                },
                                checkbox: {
                                    keep_selected_style: false,
                                },
                                plugins: ["checkbox", "conditionalselect"]
                            }).on("deselect_node.jstree", function (e, data) {
                                var currentJsTree = $(this).jstree(true);
                                var selected = currentJsTree.get_selected();
                                tempInstance.selected_items = selected;
                                setTimeout(function () {
                                    if (selected.length == 0) {
                                        if (typeof inputSelectedItems !== "undefined") { inputSelectedItems.validate(); }
                                    }
                                }, 100);
                            }).on("select_node.jstree", function (e, data) {
                                var currentJsTree = $(this).jstree(true);
                                var selected = currentJsTree.get_selected();
                                tempInstance.selected_items = selected;
                                setTimeout(function () {
                                    if (typeof inputSelectedItems !== "undefined") { inputSelectedItems.validate(); }
                                }, 100);
                            }).on("ready.jstree", function () {
                                $(this).jstree("open_all");
                                var jstreeCheckbox = $(this).find("a.parent_node > .jstree-checkbox");
                                if (typeof jstreeCheckbox !== "undefined" && jstreeCheckbox.length > 0) {
                                    jstreeCheckbox.remove();
                                }
                            });
                        }
                    }, validateFields: function () {
                        var currentVm = this;
                        $.validate({
                            form: "#frmPunchlistTask",
                            lang: "en",
                            scrollToTopOnError: false,
                            validateHiddenInputs: true,
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
                                            currentForm.reset();
                                            currentModal.modalClose(function () {
                                                toastr.success(json.toastr_msg, "Punchlist Items", { timeOut: 5000 });
                                                generateCurrentTask(currentVm.unit_id, true);
                                            });
                                        }
                                    }
                                });

                                return false;
                            }
                        });
                    }
                }
            });
        }
    });

    var modalPunchlistTaskLog = new vueModal({
        id: "modalPunchlistLog",
        setSize: "modal-lg",
        setContentId: "punchlistTaskLog",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_punchlist_task_log"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmPunchlistTaskLog = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    getJsTreeData: function (data) {
                        var treeTaskList = $("#" + contentId).find("#tree-punchlist_items");
                        if (typeof treeTaskList !== "undefined") {
                            treeTaskList.jstree("destroy");
                            treeTaskList.jstree({
                                core: {
                                    data: data,
                                    check_callback: true,
                                    dblclick_toggle: false,
                                    types: {
                                        root: { icon: "fa fa-check-square m--font-brand" },
                                        child: { icon: "fa fa-check-square m--font-brand" },
                                    },
                                    plugins: ["types", "wholerow"],
                                }
                            }).on("ready.jstree", function () {
                                $(this).jstree("open_all");
                            });
                        }
                    }
                }
            });
        }
    });

    return {
        edit_subtask: function () {
            return modalSubtask;
        },
        edit_subtask_activity: function () {
            return modalSubtaskActivity;
        },
        edit_contract_status: function () {
            return modalContractStatus;
        }
        , preview_todo_task: function () {
            return modalTodoTask;
        }
        , preview_punchlist_task: function () {
            return modalPunchlistTask;
        }
        , preview_punchlist_task_log: function () {
            return modalPunchlistTaskLog;
        }
    };
}

var checklistModal = function () {
    var modalRequestChecklistQty = new vueModal({
        id: "modalChecklistQtyRequest",
        setSize: "modal-xl",
        setContentId: "checklistQtyRequest",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_checklist_qty_request"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            var tableChecklistQtyRequest = $("#" + contentId).find("#table-task_requested_qty");
            if (typeof tableChecklistQtyRequest !== "undefined") {
                dtChecklistRequestQty = tableChecklistQtyRequest.DataTable({
                    dom: '<"toolbar">rti',
                    serverSide: true,
                    processing: true,
                    ordering: false,
                    paging: false,
                    ajax: {
                        url: baseUrl("pms/task/do_post_event/get_checklist_temp_qty_datatable_request"),
                        type: "post",
                        dataType: "json",
                        data: function (d) {
                            d.csrf_token = _csrf_hash;
                            d.checklist_id = _tempContentData.id;
                            return d;
                        }
                    },
                    columns: [
                        { data: "task_name", title: "Task", width: "*" },
                        { data: "qty", title: "Quantity", width: "15%", className: "text-center" },
                        { data: null, title: "Action", width: "8%", className: "text-center" },
                    ], columnDefs: [{
                        data: "qty",
                        targets: 1,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return "<input type='text' class='form-control m-input text-right form-control-table_field maskQty' id='updateTempQty' data-id='" + row.id + "' value='" + data + "' data-validation='valid_quantity' />";
                        }
                    }, {
                        data: null,
                        targets: -1,
                        defaultContent: "--",
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return "<button type='button' class='btn btn-sm btn-danger btnEdit' onclick='removeTempQty(" + row.id + ")'><i class='la la-trash'></i></button>";
                        }
                    }], drawCallback: function () {
                        var timeout;
                        var currentField = $('.form-control-table_field.maskQty');
                        currentField.keyup(function (e) {
                            var _self = this;
                            var parentRow = $(_self).parent("div.form-group.m-form__group").parent("td").parent("tr");
                            clearTimeout(timeout);
                            timeout = setTimeout(function () {
                                var value = $(_self).val();
                                var tempId = $(_self).data("id");
                                $.ajax({
                                    url: baseUrl("pms/task/do_post_event/update_temp_requested_qty"),
                                    type: "post",
                                    dataType: "json",
                                    global: false,
                                    data: { csrf_token: _csrf_hash, id: tempId, qty: value },
                                });
                            }, 500);
                        }).maskMoney({
                            allowZero: true,
                            affixesStay: true,
                            allowNegative: false,
                        }).maskMoney("mask");

                        submitValidation();
                    }
                });
            }

            var selectTask = $("#" + contentId).find("select#task_name");
            if (typeof selectTask !== "undefined") {
                selectTask.select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: $("#" + contentId),
                    ajax: {
                        url: baseUrl("pms/task/get_select2_requested_task"),
                        global: false,
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            params.checklist_id = _tempContentData.id;
                            return params;
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                }).on("select2:select", function (e) {
                    var target = $(e.target);
                    var currentValue = e.target.value;
                    if (typeof currentValue !== "undefined" && parseInt(currentValue) > 0) {
                        $.ajax({
                            url: baseUrl("pms/task/do_post_event/insert_temp_requested_qty"),
                            type: "post",
                            dataType: "json",
                            global: false,
                            data: {
                                csrf_token: _csrf_hash,
                                item_id: currentValue,
                                checklist_id: _tempContentData.id,
                            },
                            success: function (json) {
                                if (json.response) {
                                    dtChecklistRequestQty.ajax.reload();
                                    toastr.success(json.toastr_msg, "Request Task Quantity", { timeOut: 5000 });
                                    target.empty().trigger("change");
                                } else {
                                    toastr.error(json.toastr_msg, "Request Task Quantity", { timeOut: 5000 });
                                }
                            }
                        });
                    }
                });
            }
        }
    });

    var modalChecklistQtyHistory = new vueModal({
        id: "modalChecklistQtyHistory",
        setSize: "modal-xl",
        setContentId: "checklistQtyHistory",
        setContent: {
            ajax: {
                url: baseUrl("pms/task/render_modal_content/render_modal_checklist_qty_history"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            var tableChecklistQtyHistory = $("#" + contentId).find("#table-checklist__qty_history");

            if (typeof tableChecklistQtyHistory !== "undefined") {
                dtChecklistHistory = tableChecklistQtyHistory.DataTable({
                    dom: '<"toolbar">frtlip',
                    serverSide: true,
                    processing: true,
                    ordering: false,
                    ajax: {
                        url: baseUrl("pms/task/do_post_event/get_checklist_qty_history_datatable_request"),
                        type: "post",
                        dataType: "json",
                        data: function (d) {
                            d.csrf_token = _csrf_hash;
                            d.checklist_id = _tempContentData.id;
                            d.item_id = _tempItemId;
                            return d;
                        }
                    },
                    columns: [
                        { data: "task", title: "Task", width: "*" },
                        { data: "qty", title: "Quantity", width: "10%", className: "text-center", defaultContent: "0" },
                        { data: "approval_status", title: "Status", width: "8%", className: "text-center", defaultContent: "---" },
                        { data: "approval_by", title: "Approved/Declined By", width: "20%" },
                        { data: "encoded_by", title: "Encoded By", width: "20%" },
                    ],
                    columnDefs: [{
                        data: "task",
                        defaultContent: "--",
                        targets: 0,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            var tempHtml = "";
                            if (typeof data !== "undefined" && data) {
                                tempHtml += "<div class='custom-details'>";
                                tempHtml += "<p class='m--marginless'>" + data + "</p>";
                                if (typeof row.parent_task !== "undefined" && row.parent_task) {
                                    tempHtml += "<p class='m--marginless'><small class='m--font-boldest'>" + row.parent_task + "</small></p>";
                                }
                                tempHtml += "</div>";
                            } else {
                                tempHtml = "---";
                            }
                            return tempHtml;
                        }
                    }, {
                        data: "approval_status",
                        defaultContent: "--",
                        targets: 2,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return (typeof data !== "undefined" && data) ? tempHistoryStatus(data) : "---";
                        }
                    }, {
                        data: "approval_by",
                        defaultContent: "--",
                        targets: 3,
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
                        data: "encoded_by",
                        defaultContent: "--",
                        targets: -1,
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
                        targets: "_all",
                        defaultContent: ""
                    }
                    ]
                });

                function tempHistoryStatus($status) {
                    var _html = "<span class='m-badge m-badge--warning m-badge--wide m--font-light'> PENDING </span>";
                    switch ($status) {
                        case "1": _html = "<span class='m-badge m-badge--custom-wide m-badge--success m-badge--wide m--font-light'> APPROVED </span>"; break;
                        case "2": _html = "<span class='m-badge m-badge--custom-wide m-badge--danger m-badge--wide m--font-light'> DECLINED </span>"; break;
                        default: _html = "<span class='m-badge m-badge--custom-wide m-badge--warning m-badge--wide m--font-light'> PENDING </span>"; break;
                    }
                    return _html;
                }
            }
        }
    });

    return {
        preview_checklist_qty_history: function () {
            return modalChecklistQtyHistory;
        }, request_new_checklist_qty: function () {
            return modalRequestChecklistQty;
        }
    };
}