var renderVueContract = function () {
    var tempInstance = new Vue({
        el: "#contract-content_preview",
        data: {
            is_editable: false,
            co_type: 0,
            row: {},
            wo_data: {},
            wo_code: "",
            issued_date: "",
            due_date: "",
            checklist_id: 0,
        },
        methods: {
            modalGenerateCode: function () {
                modalContractCode.modalShow();
                vmContractCode.validateFields();
                var tempIssuedDate, tempDueDate;
                var currentModal = $("#" + modalContractCode.id);
                var dpIssuedDate = currentModal.find("#issued_date");

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
                    var dpDueDate = currentModal.find("#due_date");
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

                var dpDueDate = currentModal.find("#due_date");
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
            }, submitContractData: function (formName, toRedirect = false) {
                doRedirect = toRedirect;
                $("#" + formName).submit();
            }, renderSelect2Data: function () {
                var _this = this;
                if (_this.is_editable == true) {
                    var currentRow = _this.row;
                    var tempFormContent = $(_this.$el);
                    var tempContractor = tempFormContent.find("#contractor_id");
                    if (typeof tempContractor !== "undefined") {
                        var optContractor = new Option(currentRow.contractor, currentRow.contractor_id, false, true);
                        tempContractor.append(optContractor).trigger("change");
                    }
                    var tempIncharge = tempFormContent.find("#task_incharge");
                    if (typeof tempIncharge !== "undefined") {
                        var optIncharge = new Option(currentRow.incharge, currentRow.task_incharge, false, true);
                        tempIncharge.append(optIncharge).trigger("change");
                    }
                    var tempForeman = tempFormContent.find("#foreman_id");
                    if (typeof tempForeman !== "undefined") {
                        var optForeman = new Option(currentRow.foreman, currentRow.foreman_id, false, true);
                        tempForeman.append(optForeman).trigger("change");
                    }
                }
            },
        },
    });
    var responseData = {
        instance: tempInstance,
    }

    return responseData;
}