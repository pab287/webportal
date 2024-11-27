var renderVueTask = function () {
    var tempInstance = new Vue({
        el: "#task-preview",
        data: {
            row: {},
            contractor: {},
            task: {},
            task_count: 0,
            contractor_count: 0,
            item_id: 0,
            unit_id: 0,
            current_remarks: "",
            done_typing: false,
            task_duration: false,
            is_pastdue: false,
            done_timer: 0,
        },
        watch: {
            current_remarks: function (e) {
                var vm = this;
                vm.done_typing = true;
                clearTimeout(vm.done_timer);
                vm.done_timer = setTimeout(() => {
                    vm.done_typing = false;
                }, 500);
            },
            done_typing: function (value) {
                var vm = this;
                clearTimeout(vm.done_timer);
                if (!value) {
                    vm.updateTaskRemarks(this.current_remarks);
                }
            },
        },
        methods: {
            isPastDueItem: function (item) {
                var _tempDue = item.due_date;
                var _currentDate = moment().format("YYYY-MM-DD");
                if (typeof item.extension_id !== "undefined" && item.extension_id !== "0") {
                    _tempDue = item.extension_date;
                }
                return (_currentDate > _tempDue) ? true : false;
            },
            updateTaskDuration: function () {
                var _this = this;
                var currentRow = _this.row;
                if (typeof currentRow.cdate1 !== "undefined" && typeof currentRow.cdate2 !== "undefined") {
                    if ((currentRow.cdate1 !== "0000-00-00" && currentRow.cdate1 !== "") && (currentRow.cdate2 !== "0000-00-00" && currentRow.cdate2 !== "")) {
                        _this.task_duration = true;
                        var _currentDate = moment().format("YYYY-MM-DD");
                        if (_currentDate > currentRow.cdate2) {
                            _this.is_pastdue = true;
                        }
                    }

                }
            },
            setNewContract: async function (item_id, unit_id) {
                setNewVueContract(item_id, unit_id);
            },
            setTaskDuration: async function (item_id, unit_id) {
                setNewVueTaskDuration(item_id, unit_id);
            },
            setNewTask: async function (item_id, unit_id) {
                setNewVueTask(item_id, unit_id);
            }, getModalSubTaskContent: function (id) {
                $.ajax({
                    url: baseUrl("pms/task/render_subtask_form/" + id),
                    global: false,
                    dataType: "json",
                    success: function (json) {
                        if (json.response) {
                            if (typeof modalSubTaskActivity !== "undefined" && typeof modalSubTaskActivity == "object") {
                                vmTaskActivityLog.row = Object.assign({}, json.row);
                                vmTaskActivityLog.activity = Object.assign({}, json.activity);
                                vmTaskActivityLog.activity_count = parseInt(json.activity_count);
                                vmTaskActivityLog.$mount();
                                modalSubTaskActivity.modalShow();
                                vmTaskActivityLog.validateFields();
                            }
                        }
                    }
                });
            }, updateTaskStatus: function (e) {
                var vm = this;
                var currentRow = vm.row;
                var currentValue = e.target.value;
                $.ajax({
                    url: baseUrl("pms/task/update_task_status"),
                    global: false,
                    type: "post",
                    dataType: "json",
                    data: { csrf_token: _csrf_hash, id: currentRow.task, status: currentValue },
                    success: function (json) {
                        if (json.response) {
                            generateCurrentTask(vm.unit_id, true);
                            vm.row = Object.assign({}, currentRow, { status: currentValue });
                            toastr.info(json.toastr_msg, "Task Status Update", 5000);
                        }
                    }
                });
            }, updateTaskRemarks: function (value) {
                var vm = this;
                var currentRow = this.row;
                var _this = this;
                $.ajax({
                    url: baseUrl("pms/task/update_task_remarks"),
                    global: false,
                    type: "post",
                    dataType: "json",
                    data: { csrf_token: _csrf_hash, id: currentRow.task, remarks: value },
                    success: function (json) {
                        if (json.response) {
                            vm.row = Object.assign({}, currentRow, { remarks: value });
                            toastr.info(json.toastr_msg, "Task Remarks Update", 5000);
                        }
                    }
                });
            }, contractorClass: function (status) {
                var tempClass = "m-widget2__item--metal";
                switch (status) {
                    case "1": tempClass = "m-widget2__item--success"; break;
                    case "2": tempClass = "m-widget2__item--primary"; break;
                    case "3": tempClass = "m-widget2__item--danger"; break;
                    default: tempClass = "m-widget2__item--metal"; break;
                }

                return tempClass;
            },
            previewContractor: function (id) {
                window.open(baseUrl("pms/contract/view_work_order/" + id), "_blank");
            },
            getCurrentTaskData: function (parent_id) {
                if (typeof parent_id !== "undefined" && parent_id) {
                    var _self = this;
                    var xhrData = getCurrentSubtaskData(parent_id);
                    xhrData.done(function (json) {
                        if (json.response) {
                            _self.task = Object.assign({}, json.task);
                            _self.task_count = json.task_count;
                        }
                    });
                }
            },
        }, mounted() {
            var _self = this;
            setTimeout(function () {
                var row = _self.row;
                _self.getCurrentTaskData(row.task);
                _self.updateTaskDuration();
                mApp.initScroller($(".m-scrollable"), {});
            }, 500);
        }
    });

    var updateInstanceTask = function (object = {}) {
        var count = Object.keys(object).length;
        if (count > 0) {
            tempInstance.task = Object.assign({}, object);
            tempInstance.task_count = count;
        }
        return tempInstance;
    }

    var updateInstanceTodo = function (object = {}) {
        var count = Object.keys(object).length;
        if (count > 0) {
            tempInstance.todo = Object.assign({}, object);
            tempInstance.todo_count = count;
        }
        return tempInstance;
    }

    var responseData = {
        instance: tempInstance,
        setCurrentRemarks: function (remarks) {
            return tempInstance.current_remarks = remarks;
        },
        setCurrentSubTask: function (object) {
            return updateInstanceTask(object);
        },
        setCurrentTodoTask: function (object) {
            return updateInstanceTodo(object);
        },
        mount: function () {
            return tempInstance.$mount();
        }
    };

    return responseData;
}

var renderVueActivity = function () {
    var tempInstance = new Vue({
        el: "#activityWidget",
        data: { activity: {}, activity_count: 0 }
    });

    var updateInstanceActivity = function (object = {}) {
        var count = Object.keys(object).length;
        if (count > 0) {
            tempInstance.activity = Object.assign({}, object);
            tempInstance.activity_count = count;
        }
        return tempInstance;
    }

    var responseData = {
        instance: tempInstance,
        setCurrentActivity: function (object) {
            return updateInstanceActivity(object);
        }
    };

    return responseData;
}

var renderVueTodoTask = function () {
    var tempInstance = new Vue({
        el: "#todo-content",
        data: { todo_rows: {}, todo_count: 0 }
    });

    var updateInstanceData = function (object = {}) {
        var count = Object.keys(object).length;
        if (count > 0) {
            tempInstance.todo_rows = Object.assign({}, object);
            tempInstance.todo_count = count;
        }
        return tempInstance;
    }

    var responseData = {
        instance: tempInstance,
        setCurrentTodoTask: function (object) {
            return updateInstanceData(object);
        }
    };

    return responseData;
}

var setNewVueTaskDuration = function (item_id, unit_id) {
    $.ajax({
        url: baseUrl("pms/task/render_task_duration_form/" + item_id + "/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var modalContent = modalTempContainer.find(".modal-content");
                modalContent.empty().html(json.html);
                modalTempContainer.modal("show");

                var tempTaskData = new Vue({
                    el: "#frmSetTaskDuration",
                    data: { row: {} },
                    methods: {
                        validateFields: function () {
                            var _this = this;
                            var currentRow = _this.row;

                            $.validate({
                                form: "#frmSetTaskDuration",
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
                                        success: function (json) {
                                            if (json.response) {
                                                toastr.success(
                                                    json.toastr_msg,
                                                    "Task duration has been set.",
                                                    5000
                                                );
                                                if (typeof modalTempContainer !== "undefined") {
                                                    modalTempContainer.modal("hide");
                                                    getSelectedTaskRender(json.unit_id, json.item_id);
                                                }
                                            }
                                        }
                                    });
                                    return false;
                                }
                            });

                            var tempIssuedDate, tempDueDate;
                            if (typeof currentRow.issued_date !== "undefined" && currentRow.issued_date !== "0000-00-00") {
                                tempIssuedDate = moment(currentRow.issued_date).format("YYYY-MM-DD");
                            }
                            if (typeof currentRow.due_date !== "undefined" && currentRow.due_date !== "0000-00-00") {
                                tempDueDate = moment(currentRow.due_date).format("YYYY-MM-DD");
                            }

                            var dpIssuedDate = modalTempContainer.find("#issued_date");
                            dpIssuedDate.datepicker({
                                orientation: "bottom left",
                                templates: {
                                    leftArrow: '<i class="la la-angle-left"></i>',
                                    rightArrow: '<i class="la la-angle-right"></i>'
                                },
                                format: "yyyy-mm-dd",
                                autoclose: true
                            }).on("changeDate", function (e) {
                                var currentTarget = $(e.target);
                                currentTarget.validate();
                                var tempStartDate = moment(e.target.value).add(1, "d").format("YYYY-MM-DD");
                                var tempDate = moment(e.target.value).add(1, "M").format("YYYY-MM-DD");
                                var dpDueDate = modalTempContainer.find("#due_date");
                                dpDueDate.datepicker("clearDates");
                                dpDueDate.datepicker("destroy");
                                dpDueDate.datepicker({
                                    orientation: "bottom left",
                                    templates: {
                                        leftArrow: '<i class="la la-angle-left"></i>',
                                        rightArrow: '<i class="la la-angle-right"></i>'
                                    },
                                    format: "yyyy-mm-dd",
                                    defaultViewDate: tempDate,
                                    startDate: tempStartDate,
                                    autoclose: true
                                }).on("changeDate", function (e) {
                                    var currentTarget = $(e.target);
                                    currentTarget.validate();
                                });
                            });

                            var dpDueDate = modalTempContainer.find("#due_date");
                            dpDueDate.datepicker({
                                orientation: "bottom left",
                                templates: {
                                    leftArrow: '<i class="la la-angle-left"></i>',
                                    rightArrow: '<i class="la la-angle-right"></i>'
                                },
                                format: "yyyy-mm-dd",
                                startDate: tempIssuedDate,
                                autoclose: true
                            }).on("changeDate", function (e) {
                                var currentTarget = $(e.target);
                                currentTarget.validate();
                            });
                        }
                    },
                    mounted: function () {
                        var _this = this;
                        setTimeout(_this.validateFields, 200);
                    }
                });

                tempTaskData.row = Object.assign({}, json.row);
            }
        }
    });
}
var setNewVueContract = function (item_id, unit_id) {
    $.ajax({
        url: baseUrl("pms/task/render_contract_form/" + item_id + "/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var tempData = json.data;
                var tempIssue = moment(tempData.issued_date).format("YYYY-MM-DD");
                var tempDue = moment(tempData.due_date).format("YYYY-MM-DD");
                var modalContent = modalTempContainerLg.find(".modal-content");
                modalContent.empty().html(json.html);
                modalTempContainerLg.modal("show");

                var woVersionSelect2 = modalTempContainerLg.find("#wo_version").select2({
                    width: "100%",
                    placeholder: "Select an option",
                }).on("select2:select", function (e) {
                    var target = $(e.target);
                    var dataCount = target.data("count");
                    var currentValue = e.target.value;
                    $.ajax({
                        url: baseUrl("pms/contract/get_version_template"),
                        type: "post",
                        dataType: "json",
                        data: {
                            csrf_token: _csrf_hash,
                            data: json.data,
                            version_template: currentValue,
                            contractor_count: dataCount
                        }, success: function (json) {
                            if (json.response) {
                                var versionTemplate = modalContent.find("#version_template");
                                if (typeof versionTemplate !== "undefined") {
                                    versionTemplate.empty().html(json.html);
                                    var select2WoType = versionTemplate.find("#version-wo_type").select2({
                                        width: "100%",
                                        placeholder: "Select an option",
                                        dropdownParent: modalTempContainerLg,
                                        ajax: {
                                            url: baseUrl("pms/work_order/get_wo_type_select2_data/series"),
                                            dataType: "json",
                                            delay: 250,
                                            processResults: function (data) {
                                                return data;
                                            }
                                        }
                                    });

                                    var inputs = modalTempContainerLg.find("#frmGenerateCode select, #frmGenerateCode input");
                                    if (typeof inputs !== "undefined" && inputs.length > 0) {
                                        inputs.on("change", function (e) {
                                            modalTempContainerLg.find("#wo_code").val("");
                                        });
                                    }
                                    /*** var select2GenTask = versionTemplate.find("#gen_task").select2({
                                        width: "100%",
                                        placeholder: "Select an option",
                                        dropdownParent: modalTempContainerLg,
                                        ajax: {
                                            url: baseUrl("pms/task/get_task_codes/"+item_id),
                                            dataType: "json",
                                            delay: 250,
                                            processResults: function (data) {
                                                return data;
                                            }
                                        }
                                    }); ***/
                                }
                            }
                        }
                    });
                    target.validate();
                });

                validateWoCodeGenerator();
                validateNewContract();

                setTimeout(function () {
                    modalTempContainerLg.find("#issued_date").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        startDate: tempIssue,
                        endDate: tempDue,
                        autoclose: true,
                    }).on("changeDate", function (e) {
                        var currentTarget = $(e.target);
                        currentTarget.validate();
                        var tempStartDate = e.target.value;

                        var dpDueDate = modalTempContainerLg.find("#due_date");
                        dpDueDate.datepicker("clearDates");
                        dpDueDate.datepicker("destroy");
                        dpDueDate.datepicker({
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            startDate: tempStartDate,
                            endDate: tempDue,
                            format: "yyyy-mm-dd",
                            autoclose: true,
                        }).on("changeDate", function (e) {
                            var currentTarget = $(e.target);
                            currentTarget.validate();
                        });
                    });

                    var dpDueDate = modalTempContainerLg.find("#due_date");
                    dpDueDate.datepicker("clearDates");
                    dpDueDate.datepicker("destroy");
                    dpDueDate.datepicker({
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        startDate: tempIssue,
                        endDate: tempDue,
                        format: "yyyy-mm-dd",
                        autoclose: true,
                    }).on("changeDate", function (e) {
                        var currentTarget = $(e.target);
                        currentTarget.validate();
                    });


                }, 200);

                var generateCode = modalTempContainerLg.find("#generateCode");
                $(generateCode).on("click", function () {
                    modalTempContainerLg.find("#frmGenerateCode").submit();
                });
            }
        }
    });
}

var setNewVueTask = function (item_id, unit_id) {
    $.ajax({
        url: baseUrl("pms/task/render_task_form/" + item_id + "/" + unit_id),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                var modalContent = modalTempContainer.find(".modal-content");
                modalContent.empty().html(json.html);
                modalTempContainer.modal("show");

                var vmNewTask = new Vue({
                    el: "#tempNewTask",
                    methods: {
                        checkDatePicker: function (e) {
                            var currentProp = true;
                            var _this = this;
                            var currentElement = $(_this.$el);

                            var currentId = e.target.id;
                            var currentValue = e.target.value;

                            if (currentId == "issued_date" && currentValue) {
                                currentProp = false;
                            }
                            setTimeout(function () {
                                var tempObject = currentElement.find("#due_date");
                                tempObject.prop("disabled", currentProp);
                                if (currentProp == true) {
                                    tempObject.val("");
                                    tempObject.datepicker("clearDates");
                                    tempObject.datepicker("destroy");
                                }
                            }, 200);
                        },
                    }
                });

                validateTaskTimeline();

                var select2CurrentTask = modalContent.find("#task_id").select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: modalTempContainer,
                    ajax: {
                        url: baseUrl("pms/task/get_child_task_codes/" + item_id),
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            params = Object.assign({}, params, { p1: item_id, p2: unit_id });
                            return params;
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                }).on("select2:select", function (e) {
                    var currentTarget = e.target;
                    $(currentTarget).validate();
                });

                var select2CurrentContractor = modalContent.find("#contractor_id").select2({
                    width: "100%",
                    placeholder: "Select an option",
                    dropdownParent: modalTempContainer,
                    ajax: {
                        url: baseUrl("pms/task/get_select2_task_contractor/" + item_id + "/" + unit_id),
                        dataType: "json",
                        delay: 250,
                        processResults: function (data) {
                            return data;
                        }
                    }
                }).on("select2:select", function (e) {
                    var tempData = e.params.data;
                    var currentTarget = e.target;
                    $(currentTarget).validate();

                    var formDatePicker = modalTempContainer.find(".form-datepicker");

                    formDatePicker.prop("disabled", false);
                    formDatePicker.datepicker("clearDates");
                    formDatePicker.datepicker("destroy");
                    modalTempContainer.find("#issued_date").datepicker({
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        startDate: tempData.issued_date,
                        endDate: tempData.due_date,
                        autoclose: true
                    }).on("changeDate", function (e) {
                        vmNewTask.checkDatePicker(e);
                        var currentTarget = $(e.target);
                        currentTarget.validate();
                        var tempStartDate = moment(e.target.value).add(1, "d").format("YYYY-MM-DD");
                        var dpDueDate = modalTempContainer.find("#due_date");
                        dpDueDate.datepicker("clearDates");
                        dpDueDate.datepicker("destroy");
                        dpDueDate.datepicker({
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            startDate: tempStartDate,
                            endDate: tempData.due_date,
                            autoclose: true
                        }).on("changeDate", function (e) {
                            var currentTarget = $(e.target);
                            currentTarget.validate();
                        });
                    });
                });

            }
        }
    });
}

var getCurrentSubtaskData = function (parent_id) {
    return $.ajax({
        url: baseUrl("pms/task/get_current_task_data/" + parent_id),
        dataType: "json",
    });
}

var getCurrentAcitivities = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/do_post_event/get_current_activities"),
        global: false,
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash, unit_id: unit_id },
        success: function (json) {
            if (json.response) {
                vmTaskData.activity = Object.assign({}, json.rows);
                vmTaskData.activity_count = json.row_count;
            }
        }
    });
}

var getCurrentContractors = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/do_post_event/get_current_contractor"),
        global: false,
        type: "post",
        dataType: "json",
        data: { csrf_token: _csrf_hash, unit_id: unit_id },
        success: function (json) {
            if (json.response) {
                vmTaskData.contractors = Object.assign({}, json.rows);
                vmTaskData.contractor_count = json.row_count;
            }
        }
    });
}

var getCurrentPastDue = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/generate_past_due_task/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmTaskData.pastdue = Object.assign({}, json.rows);
                vmTaskData.pastdue_count = json.row_count;
                setTimeout(function () {
                    mApp.initScroller($(".m-scrollable"), {});
                }, 50);
            }
        }
    });
}

var getCurrentTodoTask = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/generate_todo_task/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmTaskData.todo = Object.assign({}, json.rows);
                vmTaskData.todo_count = json.row_count;
            }
        }
    });
}

var getCurrentPunchlistedTask = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/render_punchlisted_items/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmTaskData.punchlisted = Object.assign({}, json.data);
                vmTaskData.punchlisted_count = json.count;
                vmTaskData.punchlist_count = json.punchlist_count;
                setTimeout(function () {
                    vmTaskData.renderPunchlistedItems();
                }, 100);
            }
        }
    });
}

var getTaskPunchlistLogs = function (unit_id) {
    $.ajax({
        url: baseUrl("pms/task/render_task_punchlist_logs/" + unit_id),
        global: false,
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vmTaskData.punchlist_log = Object.assign({}, json.data);
                vmTaskData.punchlist_log_count = json.count;
            }
        }
    });
}