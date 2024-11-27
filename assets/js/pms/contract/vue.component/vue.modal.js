var accomplishmentListModal = function () {
    var modalNewAccomplishment = new vueModal({
        id: "modalNewAccomplishment",
        setSize: "modal-xl",
        setContentId: "newAccomplishment",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_new_accomplishment"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmNewAccomplishment = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        var _this = this;
                        $.validate({
                            form: "#frmNewAccomplishment",
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
                                                "Contract Accomplishment",
                                                { timeOut: 5000 }
                                            );
                                            currentForm.reset();
                                            currentModal.modalClose(function () {
                                                dtAccomplishment.ajax.reload();
                                            });
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Contract Accomplishment",
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
                    }
                }
            });
        }
    });

    return {
        new_accomplishment: function () {
            return modalNewAccomplishment;
        }
    }
}

var contractListModal = function () {
    var modalExtensionApproval = new vueModal({
        id: "modalExtensionApproval",
        setSize: "modal-lg",
        setContentId: "contractExtensionApproval",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_extension_aprroval"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmExtensionApproval = new Vue({
                el: "#" + contentId,
                data: { row: {}, type: "Approve" },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmExntensionApproval",
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
                                                "Contract Extension",
                                                { timeOut: 5000 }
                                            );
                                            currentForm.reset();
                                            currentModal.modalClose(function () {
                                                dtContractExtension.ajax.reload();
                                            });
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Contract Extension",
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
                    }
                }
            });
        }
    });

    return {
        contract_extension_approval: function () {
            return modalExtensionApproval;
        }
    }
}

var contractModal = function () {
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
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractStatus = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractStatus",
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
                                                "Contract status has been updated.",
                                                5000
                                            );
                                            vmContract.row = Object.assign({}, json.row);
                                            currentForm.reset();
                                            modalContractStatus.modalClose();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating contract status!",
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

    var modalContractRemarks = new vueModal({
        id: "modalContractRemarks",
        setSize: "modal-lg",
        setContentId: "contractRemarks",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_remarks"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractRemarks = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractRemarks",
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
                                                "Contract remarks has been updated.",
                                                5000
                                            );
                                            vmContract.row = Object.assign({}, json.row);
                                            currentForm.reset();
                                            modalContractRemarks.modalClose();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating contract remarks!",
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

    var modalContractIncharge = new vueModal({
        id: "modalContractIncharge",
        setSize: "modal-md",
        setContentId: "contractIncharge",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_incharge"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractIncharge = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractIncharge",
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
                                                "Contract task in-charge has been updated.",
                                                5000
                                            );
                                            vmContract.row = Object.assign({}, json.row);
                                            currentForm.reset();
                                            modalContractIncharge.modalClose();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating contract task in-charge!",
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

                        var tempModal = $("#" + currentModal.id);
                        var select2TaskIncharge = tempModal.find("select#task_incharge").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: tempModal,
                            ajax: {
                                url: baseUrl("pms/contractor/get_task_incharge_select2_data"),
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
            });
        }
    });

    var modalContractForeman = new vueModal({
        id: "modalContractForeman",
        setSize: "modal-md",
        setContentId: "contractForeman",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_foreman"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractForeman = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractForeman",
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
                                                "Contract task foreman has been updated.",
                                                5000
                                            );
                                            vmContract.row = Object.assign({}, json.row);
                                            currentForm.reset();
                                            modalContractForeman.modalClose();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating contract task foreman!",
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

                        var tempModal = $("#" + currentModal.id);
                        var select2TaskForman = tempModal.find("select#foreman_id").select2({
                            width: "100%",
                            placeholder: "Select an option",
                            dropdownParent: tempModal,
                            ajax: {
                                url: baseUrl("pms/contractor/get_foreman_select2_data"),
                                global: false,
                                dataType: "json", modalContractForeman,
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
            });
        }
    });

    var modalContractLeadman = new vueModal({
        id: "modalContractLeadman",
        setSize: "modal-md",
        setContentId: "contractLeadman",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_leadman"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractLeadman = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractLeadman",
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
                                                "Contract task leadman has been updated.",
                                                5000
                                            );
                                            vmContract.row = Object.assign({}, json.row);
                                            currentForm.reset();
                                            modalContractLeadman.modalClose();
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error updating contract task leadman!",
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

    var modalContractExtension = new vueModal({
        id: "modalContractExtension",
        setSize: "modal-md",
        setContentId: "contractExtension",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_extension"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractExtension = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        var _this = this;
                        var currentRow = _this.row;
                        var _tempDueDate = currentRow.due_date;
                        if (typeof currentRow.extension_id !== "undefined" && currentRow.extension_id !== "0") {
                            _tempDueDate = currentRow.extension_date;
                        }

                        var tempStartDate = moment(_tempDueDate).add(1, "d").format("YYYY-MM-DD");

                        $.validate({
                            form: "#frmContractExtension",
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
                                            var tempData = Object.assign({}, json.row);
                                            var showExtension = (typeof tempData.extension_id !== "undefined" && tempData.extension_id !== "0") ? true : false;
                                            vmContract.row = tempData;
                                            vmContract.show_extension = showExtension;
                                            vmContractExtensionData.row = tempData;
                                            vmContractExtensionData.show_extension = showExtension;

                                            currentForm.reset();
                                            modalContractExtension.modalClose(function () {
                                                getContractInformation(true);
                                            });

                                            toastr.success(
                                                json.toastr_msg,
                                                "Contract task extension has been made.",
                                                5000
                                            );
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error adding contract task extension!",
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

                        var tempModal = $("#" + currentModal.id);
                        var extensionDate = tempModal.find("#extension_date");
                        extensionDate.datepicker("destroy");
                        extensionDate.datepicker({
                            orientation: "bottom left",
                            templates: {
                                leftArrow: '<i class="la la-angle-left"></i>',
                                rightArrow: '<i class="la la-angle-right"></i>'
                            },
                            format: "yyyy-mm-dd",
                            startDate: tempStartDate,
                            autoclose: true
                        }).on("changeDate", function (e) {
                            var currentTarget = $(e.target);
                            currentTarget.validate();
                        });
                    }
                }
            });
        }
    });

    var modalContractChangeOrder = new vueModal({
        id: "modalContractChangeOrder",
        setSize: "modal-xl",
        setContentId: "contractChangeOrder",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_change_order"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;

            vmContractChangeOrder = new Vue({
                el: "#" + contentId,
                data: {
                    id: 0, rows: {}, units: [], unit_cost: [], update_cost: {},
                    cost_count: 0, count: 0, grand_total: 0, temp_total: 0, triggerUpdate: false,
                },
                methods: {
                    renderItemTable: function (id) {
                        var vmInstance = this;
                        var tempModal = $("#" + currentModal.id);
                        if (typeof tempModal !== "undefined") {
                            var contractItemsTable = tempModal.find("#table-task_contract_items");
                            tempContractItemTable = contractItemsTable.DataTable({
                                dom: 't',
                                serverSide: true,
                                processing: true,
                                ordering: false,
                                ajax: {
                                    url: baseUrl("pms/contract/do_post_event/get_contract_item_datatable_request"),
                                    type: "post",
                                    dataType: "json",
                                    data: function (d) {
                                        d.csrf_token = _csrf_hash;
                                        d.contract_id = id;
                                        d.units = vmInstance.units;
                                        d.costs = vmInstance.update_cost;
                                        return d;
                                    }, error: function (xhr, error, code) {
                                        if (error == "parsererror") {
                                            toastr.warning(code, "Data Table Reloading", 5000);
                                            tempContractItemTable.ajax.reload(null, false);
                                        }
                                    }
                                },
                                columns: [
                                    { data: "item", width: "*" },
                                    { className: "text-center" },
                                    { data: "lots", width: "10%", className: "text-center", defaultContent: "0" },
                                    { data: "unit", width: "10%", className: "text-center", defaultContent: "---" },
                                    { data: "tariff", width: "15%", className: "text-right", defaultContent: "0.00" },
                                    { data: "total", width: "15%", className: "text-right total-tariff", defaultContent: "0.00" }
                                ],
                                columnDefs: [{
                                    targets: 1,
                                    render: function (data, type, row, meta) {
                                        var _html = "<div class='form-group m-form__group m--marginless'>";
                                        _html += "<input id='tempQty-" + row.id + "' type='text' class='form-control m--input text-right form-control-table_field maskQty' name='qty[" + row.id + "]' data-lots='" + row.lots + "' data-tariff='" + row.temp_price + "' value='" + row.temp_qty + "' data-validation='valid_quantity' />";
                                        _html += "</div>";
                                        return _html;
                                    }
                                }, {
                                    targets: "_all",
                                    defaultContent: ""
                                }],
                                drawCallback: function (settings) {
                                    var _selfTable = this;
                                    var json = settings.json;
                                    var grandTotal = json.grand_total;
                                    var api = this.api(), data;
                                    vmInstance.grand_total = grandTotal;
                                    $(api.column(5).footer()).html(grandTotal);
                                    var timeout;
                                    var maskQty = $('.form-control-table_field.maskQty').keyup(function (e) {
                                        var _self = this;
                                        var parentRow = $(_self).parent("div.form-group.m-form__group").parent("td").parent("tr");
                                        clearTimeout(timeout);
                                        timeout = setTimeout(function () {
                                            if (typeof parentRow !== "undefined" && parentRow.length == 1) {
                                                var totalContainer = parentRow.find(".total-tariff");
                                                if (typeof totalContainer !== "undefined") {
                                                    var tempGrandTotal = 0;
                                                    var _value = $(_self).val();
                                                    var _lots = $(_self).data("lots");
                                                    var _price = $(_self).data("tariff");
                                                    _value = _value.replace(/,/g, "");
                                                    var _total = (parseFloat(_value) * parseInt(_lots)) * parseFloat(_price);
                                                    _total = numberFormat(_total);
                                                    totalContainer.text(_total);
                                                    var totalCell = _selfTable.find("td.total-tariff");
                                                    if (typeof totalCell !== "undefined" && totalCell.length > 0) {

                                                        $.each(totalCell, function (i, v) {
                                                            var tempObj = $(v);
                                                            var tempValue = tempObj.text();
                                                            tempValue = tempValue.replace(/,/g, "");
                                                            tempGrandTotal += parseFloat(tempValue);
                                                        });
                                                        var xTempGrandTotal = numberFormat(tempGrandTotal);
                                                        vmInstance.grand_total = xTempGrandTotal;
                                                        $(api.column(5).footer()).html(xTempGrandTotal);

                                                        if (parseFloat(toNumber(vmInstance.grand_total)) == parseFloat(toNumber(vmInstance.temp_total))) { vmInstance.triggerUpdate = false }
                                                        else { vmInstance.triggerUpdate = true }
                                                    }

                                                }
                                            }
                                            //$(_self).validate();
                                        }, 200);
                                    }).maskMoney({
                                        allowZero: true,
                                        affixesStay: true,
                                        allowNegative: false,
                                    }).maskMoney('mask');
                                },
                                initComplete: function (settings, json) {
                                    vmInstance.temp_total = json.grand_total;
                                }
                            });
                        }
                    },
                    updateBlockLotChecked: function () {
                        var vmInstance = this;
                        var tempModal = $("#" + currentModal.id);
                        var tempUnits = [];
                        if (typeof tempModal !== "undefined") {
                            var tempCheckAll = tempModal.find("#checkAll");
                            if (typeof tempCheckAll !== "undefined" && tempCheckAll.is(":checked") == true) {
                                tempCheckAll.prop("checked", false);
                            }
                            var checkboxItems = tempModal.find("input.tempCheckbox");
                            var selectedItems = tempModal.find("input.tempCheckbox:checked");
                            $.each(selectedItems, function (ii, vv) {
                                var tempVal = parseInt($(vv).val());
                                tempUnits.push(tempVal);
                            });
                            vmInstance.units = tempUnits;
                            if (typeof checkboxItems !== "undefined" && typeof selectedItems !== "undefined" &&
                                checkboxItems.length == selectedItems.length) { tempCheckAll.prop("checked", true); }
                            tempContractItemTable.ajax.reload(function () {
                                setTimeout(function () {
                                    if (parseFloat(toNumber(vmInstance.grand_total)) == parseFloat(toNumber(vmInstance.temp_total))) { vmInstance.triggerUpdate = false }
                                    else { vmInstance.triggerUpdate = true }
                                }, 500);
                            }, false);
                        }
                    }, updatedCostChecked: function () {
                        var vmInstance = this;
                        var tempModal = $("#" + currentModal.id);
                        var tempCost = [];
                        if (typeof tempModal !== "undefined") {
                            var tempCheckAll = tempModal.find("#checkAllUnitCost");
                            if (typeof tempCheckAll !== "undefined" && tempCheckAll.is(":checked") == true) {
                                tempCheckAll.prop("checked", false);
                            }
                            var selectedItems = tempModal.find("input.tempCostCheckbox:checked");
                            $.each(selectedItems, function (ii, vv) {
                                var tempVal = parseInt($(vv).val());
                                var tempId = $(vv).data("id");
                                tempCost[tempId] = tempVal;
                            });
                            vmInstance.update_cost = Object.assign({}, tempCost);
                            tempContractItemTable.ajax.reload(function () {
                                setTimeout(function () {
                                    if (parseFloat(toNumber(vmInstance.grand_total)) == parseFloat(toNumber(vmInstance.temp_total))) { vmInstance.triggerUpdate = false }
                                    else { vmInstance.triggerUpdate = true }
                                }, 500);
                            }, false);
                        }
                    }, checkAllEvent: function (e) {
                        var vmInstance = this;
                        var isChecked = e.target.checked;
                        var tempUnits = [];
                        if (isChecked == true) {
                            var tempModal = $("#" + currentModal.id);
                            if (typeof tempModal !== "undefined") {
                                var selectedItems = tempModal.find("input.tempCheckbox");
                                $.each(selectedItems, function (ii, vv) {
                                    var currentCheckbox = $(vv);
                                    currentCheckbox.prop("checked", true);
                                    var tempVal = parseInt(currentCheckbox.val())
                                    tempUnits.push(tempVal);
                                });
                                vmInstance.units = tempUnits;
                            }
                        } else {
                            var tempModal = $("#" + currentModal.id);
                            if (typeof tempModal !== "undefined") {
                                var selectedItems = tempModal.find("input.tempCheckbox");
                                $.each(selectedItems, function (ii, vv) {
                                    var currentCheckbox = $(vv);
                                    currentCheckbox.prop("checked", false);
                                });
                                tempUnits = [];
                                vmInstance.units = [];
                            }
                        }
                        tempContractItemTable.ajax.reload(function () {
                            setTimeout(function () {
                                if (parseFloat(toNumber(vmInstance.grand_total)) == parseFloat(toNumber(vmInstance.temp_total))) { vmInstance.triggerUpdate = false }
                                else { vmInstance.triggerUpdate = true }
                            }, 500);
                        }, false);
                    }, checkAllCostEvent: function (e) {
                        var vmInstance = this;
                        var isChecked = e.target.checked;
                        var tempCost = [];
                        if (isChecked == true) {
                            var tempModal = $("#" + currentModal.id);
                            if (typeof tempModal !== "undefined") {
                                var selectedItems = tempModal.find("input.tempCostCheckbox");
                                $.each(selectedItems, function (ii, vv) {
                                    var currentCheckbox = $(vv);
                                    currentCheckbox.prop("checked", true);
                                    var tempVal = parseInt(currentCheckbox.val());
                                    var tempId = currentCheckbox.data("id");
                                    tempCost[tempId] = tempVal;
                                });
                                vmInstance.update_cost = Object.assign({}, tempCost);
                            }
                        } else {
                            var tempModal = $("#" + currentModal.id);
                            if (typeof tempModal !== "undefined") {
                                var selectedItems = tempModal.find("input.tempCostCheckbox");
                                $.each(selectedItems, function (ii, vv) {
                                    var currentCheckbox = $(vv);
                                    currentCheckbox.prop("checked", false);
                                });
                                tempCost = [];
                                vmInstance.update_cost = {};
                            }
                        }
                        tempContractItemTable.ajax.reload(function () {
                            setTimeout(function () {
                                if (parseFloat(toNumber(vmInstance.grand_total)) == parseFloat(toNumber(vmInstance.temp_total))) { vmInstance.triggerUpdate = false }
                                else { vmInstance.triggerUpdate = true }
                            }, 500);
                        }, false);
                    }, validateFields: function () {
                        $.formUtils.addValidator({
                            name: 'valid_quantity',
                            validatorFunction: function (value, $el, config, language, $form) {
                                return parseFloat(value) > 0;
                            },
                            errorMessage: 'Quantity must be greater than 0.00',
                            errorMessageKey: 'badQuantity'
                        });

                        $.validate({
                            form: "#frmChangeOrder",
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
                                                "Change order has been made",
                                                5000
                                            );
                                            currentForm.reset();
                                            currentModal.modalClose(function () {
                                                getContractInformation(true);
                                            });
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error creating change order!",
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
                    },
                    select2BlockLots: function () {
                        var _this = this;
                        var tempId = _this.id;
                        var tempUnits = _this.units;

                        var tempModal = $("#" + currentModal.id);
                        if (typeof tempModal !== "undefined") {
                            var tempOtherBlockLot = tempModal.find("select#other_block_lot");
                            if (typeof tempOtherBlockLot !== "undefined") {
                                var select2MultipleUnits = tempOtherBlockLot.select2({
                                    width: "100%",
                                    placeholder: "Select an option",
                                    ajax: {
                                        url: baseUrl("pms/contract/change_order_select2_multiple_units"),
                                        global: false,
                                        dataType: "json",
                                        delay: 250,
                                        data: function (params) {
                                            params.contract_id = tempId;
                                            params.units = tempUnits;
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

                        }
                    }
                }
            });
        }
    });

    var modalEditRedirect = new vueModal({
        id: "modalEditRedirect",
        setSize: "modal-md",
        setContentId: "editRedirect",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_edit_redirect"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmEditRedirect = new Vue({
                el: "#" + contentId,
                data: { rows: {}, count: 0 },
                methods: {
                    redirectEditContract: function (id, wo_task_id) {
                        window.location.replace(baseUrl("pms/contract/edit_work_order" + "/" + id + "/" + wo_task_id));
                    },
                    woIndexCount: function (i) {
                        return parseInt(i) + 1;
                    }
                }
            });
        }
    });

    var modalAdjustmentRedirect = new vueModal({
        id: "modalAdjustmentRedirect",
        setSize: "modal-md",
        setContentId: "adjustmentRedirect",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_adjustment_redirect"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmAdjustmentRedirect = new Vue({
                el: "#" + contentId,
                data: { rows: {}, count: 0 },
                methods: {
                    redirectAdjustmentContract: function (id, wo_task_id) {
                        window.location.replace(baseUrl("pms/contract/adjustment_work_order" + "/" + id + "/" + wo_task_id));
                    },
                    woIndexCount: function (i) {
                        return parseInt(i) + 1;
                    }
                }
            });
        }
    });

    return {
        edit_contract_status: function () {
            return modalContractStatus;
        },
        edit_contract_remarks: function () {
            return modalContractRemarks;
        },
        edit_contract_incharge: function () {
            return modalContractIncharge;
        },
        edit_contract_leadman: function () {
            return modalContractLeadman;
        },
        edit_contract_foreman: function () {
            return modalContractForeman;
        },
        edit_contract_extension: function () {
            return modalContractExtension;
        },
        contract_change_order: function () {
            return modalContractChangeOrder;
        },
        contract_extension_approval: function () {
            return modalExtensionApproval;
        },
        edit_redirect_task: function () {
            return modalEditRedirect;
        },
        adjustment_redirect_task: function () {
            return modalAdjustmentRedirect;
        }
    };
}

var newContractModal = function () {
    var modalContractCode = new vueModal({
        id: "modalContractCode",
        setSize: "modal-md",
        setContentId: "contractCode",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_contract_code"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmContractCode = new Vue({
                el: "#" + contentId,
                data: { row: {} },
                methods: {
                    validateFields: function () {
                        $.validate({
                            form: "#frmContractCode",
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
                                                "Contract code has been generated.",
                                                5000
                                            );
                                            currentForm.reset();
                                            modalContractCode.modalClose(function () {
                                                var currentData = json.data;
                                                vmTempInstance.wo_code = currentData.wo_code;
                                                vmTempInstance.issued_date = currentData.issued_date;
                                                vmTempInstance.due_date = currentData.due_date;
                                            });
                                            setTimeout(function () {
                                                $("#contract-content_preview").find("input").validate();
                                            }, 200);
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Error generating contract code!",
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

    var modalGenerateLot = new vueModal({
        id: "modalGenerateLot",
        setSize: "modal-lg",
        setContentId: "generateLot",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_generate_lot"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmGenerateLot = new Vue({
                el: "#" + contentId,
                data: { id: 0, blocks: [], units: {}, count: 0, is_editable: false },
                methods: {
                    generateLots: function (e) {
                        var _this = this;
                        e.preventDefault();
                        var form = $(e.target);
                        if (typeof form !== "undefined") {
                            checkedLots = form.find("input[type=checkbox]:checked");
                            if (typeof checkedLots !== "undefined") {
                                var arrUnits = [];
                                var ctr = 0;
                                var blockCounter = {};
                                $.each(checkedLots, function (i, v) {
                                    var checkedValue = $(v).val();
                                    var lot = $(v).attr("data-lot");
                                    var block = $(v).attr("data-block");
                                    var tempLot = Object.assign({}, { id: checkedValue, lot: lot, block: parseInt(block) });
                                    blockCounter[parseInt(block)] = (blockCounter[parseInt(block)] || 0) + 1;
                                    arrUnits.push(tempLot);
                                    ctr++;
                                });
                                if (_this.is_editable) {
                                    vmTempWorkOrder.rows = Object.assign({}, arrUnits);
                                    vmTempWorkOrder.count = ctr;
                                    vmTempWorkOrder.blocks = _this.blocks;
                                    vmTempWorkOrder.block_count = blockCounter;
                                } else {
                                    vmRenderedLots.rows = Object.assign({}, arrUnits);
                                    vmRenderedLots.count = ctr;
                                    vmRenderedLots.blocks = _this.blocks;
                                    vmRenderedLots.block_count = blockCounter;
                                }
                                currentModal.modalClose();
                            }
                        }
                    }, resetChecked: function () {
                        var currentForm = $("#" + contentId).find("form");
                        if (typeof currentForm !== "undefined") { currentForm[0].reset(); }
                    }
                }
            });
        }
    });

    var modalEditBlockLot = new vueModal({
        id: "modalEditBlockLot",
        setSize: "modal-lg",
        setContentId: "editBlockLot",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_edit_block_lot"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmEditBlockLot = new Vue({
                el: "#" + contentId,
                data: { id: 0, blocks: [], count: 0, units: {}, url: baseUrl("pms/contract/do_post_event/update_contract_temp_blocks") },
                methods: {
                    validateFields: function () {
                        $("#frmEditBlockLot")[0].reset();
                        $.validate({
                            form: "#frmEditBlockLot",
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
                                            currentModal.modalClose(function () {
                                                dtTaskItems.ajax.reload(null, false);
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

    var modalRemoveTask = new vueModal({
        id: "modalRemoveTask",
        setSize: "modal-md",
        setContentId: "removeTask",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_remove_task"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;
            vmRemoveCurrentTask = new Vue({
                el: "#" + contentId,
                data: { id: 0, count: 0, rows: [], url: "" },
                methods: {
                    generateChecklist: function () {
                        var _this = this;
                        if (_this.count > 0) {
                            var currentChecklist = $("#" + contentId).find("#checklist_preview");
                            if (typeof currentChecklist !== "undefined") {
                                currentChecklist.jstree("destroy");
                                currentChecklist.jstree({
                                    core: {
                                        data: _this.rows,
                                        check_callback: true,
                                        dblclick_toggle: false,
                                    }, checkbox: {
                                        keep_selected_style: false,
                                    }, types: {
                                        root: { icon: "fa fa-folder" },
                                        child: { icon: "fa fa-file" },
                                    },
                                    plugins: ["checkbox", "types"]
                                }).on('ready.jstree', function () {
                                    currentChecklist.jstree("open_all");
                                });
                            }
                        }
                    }, confirmTaskRemoval: function () {
                        var tempHtml = `<div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-danger alert-dismissible animated fadeIn" role="alert">
                            <div class="m-alert__icon">
                                <i class="flaticon-exclamation-1"></i>
                                <span></span>
                            </div>
                            <div class="m-alert__text">
                                <div class="row">
                                    <div class="col-12">
                                        <strong>To be removed!</strong> Are you sure you want to remove these task item(s)?
                                    </div>
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-brand btn-sm m-btn m-btn--pill m-btn--wide">
                                            Yes
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm m-btn m-btn--pill m-btn--wide" data-dismiss="alert" aria-label="Close">
                                            No
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        var _this = this;
                        if (_this.count > 0) {
                            var currentChecklist = $("#" + contentId).find("#checklist_preview");
                            if (typeof currentChecklist !== "undefined") {
                                var selected = currentChecklist.jstree(true).get_selected();
                                var customAlert = $("#" + contentId).find("#custom-alert");
                                if (selected.length > 0) {
                                    if (typeof customAlert !== "undefined") { customAlert.empty().append(tempHtml); }
                                } else {
                                    var tempHtml = `<div class="alert alert-warning alert-dismissible animated fadeIn" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                                    <strong>
                                        No selected Item!
                                    </strong>
                                    Nothing to remove.
                                </div>`;
                                    if (typeof customAlert !== "undefined") { customAlert.empty().append(tempHtml); }
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    var modalEditQtyUnitCost = new vueModal({
        id: "modalEditQtyUnitCost",
        setSize: "modal-xl",
        setContentId: "editQtyUnitCost",
        setContent: {
            ajax: {
                url: baseUrl("pms/contract/render_modal_content/render_modal_edit_qty_unitcost"),
                dataType: "json",
            }
        }, drawCallback: function (modal) {
            var currentModal = modal;
            var contentId = currentModal.contentId;

            $.formUtils.addValidator({
                name: 'valid_quantity',
                validatorFunction: function (value, $el, config, language, $form) {
                    return parseFloat(value) > 0;
                },
                errorMessage: 'Quantity must be greater than 0.00',
                errorMessageKey: 'badQuantity'
            });
            $.formUtils.addValidator({
                name: 'valid_unitcost',
                validatorFunction: function (value, $el, config, language, $form) {
                    return parseFloat(value) > 0;
                },
                errorMessage: 'Unit cost must be greater than 0.00',
                errorMessageKey: 'badUnitCost'
            });

            _dtTaskEditQtyUnitCost = $(currentModal.modal).find("#table-edit_qty_unitcost");
            if (typeof _dtTaskEditQtyUnitCost !== "undefined") {
                dtTaskEditQtyUnitCost = _dtTaskEditQtyUnitCost.DataTable({
                    dom: "tr",
                    serverSide: true,
                    processing: true,
                    paging: false,
                    searching: false,
                    ordering: false,
                    autoWidth: false,
                    ajax: {
                        url: baseUrl("pms/contract/do_post_event/get_temp_task_item_datatable_request"),
                        type: "post",
                        dataType: "json",
                        data: function (d) {
                            d.csrf_token = _csrf_hash;
                            if (typeof tempIsEditable !== "undefined") {
                                d.is_editable = tempIsEditable;
                            }
                            return d;
                        },
                        error: function (xhr, error, code) {
                            if (error == "parsererror") {
                                toastr.warning(code, "Data Table Reloading", 5000);
                                dtTaskEditQtyUnitCost.ajax.reload(null, false);
                            }
                        }
                    }, columns: [
                        { data: "label" },
                        { data: "qty", className: "text-right", width: "15%" },
                        { data: "unit_cost", className: "text-right", width: "15%" },
                    ], columnDefs: [{
                        targets: "_all",
                        defaultContent: "",
                    }],
                    createdRow: function (row, data, dataIndex) {
                        if (data.is_parent == 1) {
                            $('td:eq(0)', row).attr('colspan', 3).addClass("parent-node");
                            $('td:not(:eq(0))', row).remove();
                        } else {
                            $('td:eq(1)', row).empty().html("<div class='form-group m-form__group m--marginless'><input type='text' class='form-control m--input text-right form-control-table_field maskQty' name='qty[" + data.id + "]' value='" + data.qty + "' data-validation='valid_quantity' /></div>");
                            $('td:eq(2)', row).empty().html("<div class='form-group m-form__group m--marginless'><input type='text' class='form-control m--input text-right form-control-table_field maskQty' name='unit_cost[" + data.id + "]' value='" + data.unit_cost + "' data-validation='valid_unitcost' /></div>");
                        }
                    }, drawCallback: function (settings) {
                        $('.form-control-table_field.maskQty')
                            .maskMoney({
                                allowZero: true,
                                affixesStay: true,
                                allowNegative: false,
                            }).maskMoney('mask');

                        $.validate({
                            form: "#frmEditQtyUnitCost",
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
                                                "Edit scope of work",
                                                { timeOut: 5000 }
                                            );
                                            currentModal.modalClose(function () {
                                                dtTaskItems.ajax.reload();
                                            });
                                        } else {
                                            toastr.error(
                                                json.toastr_msg,
                                                "Edit scope of work",
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
                    }
                });
            }
        }
    });

    return {
        generate_contract_code: function () {
            return modalContractCode;
        }, generate_lot: function () {
            return modalGenerateLot;
        }, edit_block_lot: function () {
            return modalEditBlockLot;
        }, remove_current_task: function () {
            return modalRemoveTask;
        }, edit_qty_unitcost: function () {
            return modalEditQtyUnitCost;
        }
    };
}