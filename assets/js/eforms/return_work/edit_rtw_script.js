var tempId = _tempContentData.id;
var dtPicker1, dtPicker2;
var tempForm = $("#frm-edit_rtw");
var vReturnToWork = new Vue({
    el: "#return_to_work-content",
    data: { row: {} },
    methods: {
        setCompanyDetails: function () {
            var _self = this;
            var tempRow = _self.row;
            var tempText = "";
            if (typeof tempRow.company !== "undefined") {
                tempText += tempRow.company;
            }
            if (typeof tempRow.department !== "undefined") {
                tempText += "\n" + tempRow.department;
            }
            if (typeof tempRow.position !== "undefined") {
                tempText += "\n" + tempRow.position;
            }
            return tempText;
        },
        setEmployeeTypeData: function () {
            var currentDiv = $(this.$el);
            var currentRow = this.$data.row;
            currentDiv.find('#date_from_to').val("");
            currentDiv.find(".check_type:checked").click();
            if (currentRow.status !== 0) {
                setTimeout(function () {
                    currentDiv.find("#date_from").datepicker("setDate", currentRow.dt_from);
                }, 500);
            }
        },
        setEmployeeDropdownData: function () {
            var currentDiv = $(this.$el);
            var currentRow = this.$data.row;
            var empSelect = currentDiv.find("select#employee");
            if (typeof empSelect !== "undefined") {
                var employeeOption = new Option(currentRow.requested_name, currentRow.employee_id, true, true);
                empSelect.empty().html(employeeOption).trigger("change");
            }
        }
    }
});

$.validate({
    form: "#frm-edit_rtw",
    lang: "en",
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
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if (json.response) {
                    toastr.success("Edit return to work", json.toastr_msg, { timeOut: 5000 });
                    setTimeout(function () {
                        window.location.replace(siteUrl("eforms/return_to_work/view_return_to_work/" + tempId));
                    }, 1500);
                } else {
                    toastr.error("Edit return to work", json.toastr_msg, { timeOut: 5000 });
                }
                $(currentForm)
                    .find(".btn-submit")
                    .removeClass(
                        "m-btn--custom m-loader m-loader--light m-loader--right"
                    );
            }
        });

        return false;
    }
});

if (typeof tempForm !== "undefined") {
    $.ajax({
        url: siteUrl("eforms/return_to_work/get_rtw_data/" + tempId),
        dataType: "json",
        success: function (json) {
            if (json.response) {
                vReturnToWork.row = Object.assign({}, json.row);
                vReturnToWork.setEmployeeDropdownData();
                setTimeout(function () {
                    vReturnToWork.setEmployeeTypeData();
                }, 200);
            }
        }
    });

    var employeeSelect = tempForm.find("#employee");
    if (typeof employeeSelect !== "undefined") {
        employeeSelect.select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                url: baseUrl("eforms/return_to_work/get_employee_select2_data"),
                dataType: "json",
                delay: 500,
                processResults: function (data) {
                    return data;
                }
            }
        }).on("select2:select", function (e) {
            var currentValue = e.target.value;
            $.ajax({
                url: siteUrl("eforms/return_to_work/get_employee_select2_company/" + currentValue),
                dataType: "json",
                success: function (json) {
                    if (json.response) {
                        vReturnToWork.row = Object.assign({}, vReturnToWork.row, json.row);
                    }
                }
            });
        });
    }

    var checkType = tempForm.find(".check_type");
    if (typeof checkType !== "undefined") {
        checkType.on("click", function () {
            var _self = $(this);
            $.ajax({
                url: baseUrl("eforms/return_to_work/get_temp_fields/" + _self.val()),
                dataType: "json",
                success: function (json) {
                    if (json.html) {
                        tempForm.find(".temp-fields").empty().html(json.html);
                        dateFrom();
                        dateFromTo();
                    }
                }
            });
        });
    }

    var dateFrom = function () {
        var vmRow = vReturnToWork.row;
        dtPicker1 = tempForm.find("#date_from").datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: 'yyyy/mm/dd',
            autoclose: true
        });
        if (typeof vmRow.dt_from !== "0000-00-00") {
            tempForm.find("#date_from").datepicker("setDate", vmRow.dt_from);
        }
    }

    var dateFromTo = function () {
        var vmRow = vReturnToWork.row;
        if (vmRow.address) { tempForm.find("#address").val(vmRow.address); }
        dtPicker2 = tempForm.find("#date_from_to").daterangepicker({
            locale: { format: 'YYYY/MM/DD' },
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            startDate: vmRow.dt_from,
            endDate: vmRow.dt_to,
        });
    }
    dateFromTo();
    var redirectMasterfile = function () {
        window.location.replace(siteUrl("eforms/return_to_work/view_return_to_work/" + _tempContentData.id));
    }
}