var tempForm = $("#frm-new_rtw");
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
        }
    }
});

$.validate({
    form: "#frm-new_rtw",
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
                    toastr.success("Return to work", json.toastr_msg, { timeOut: 5000 });
                    $(form).find("#employee").empty().trigger("change");
                    $(form).find("#return_type_0").click();
                    currentForm.reset();
                } else {
                    toastr.error("Return to work", json.toastr_msg, { timeOut: 5000 });
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
    var employeeSelect = tempForm.find("#employee");
    if (typeof employeeSelect !== "undefined") {
        employeeSelect.select2({
            width: "100%",
            placeholder: "Select an option",
            ajax: {
                global: false,
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
                        vReturnToWork.row = Object.assign({}, json.row);
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
        tempForm.find("#date_from").datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: 'yyyy/mm/dd',
            autoclose: true
        });
    }

    var dateFromTo = function () {
        tempForm.find("#date_from_to").daterangepicker({
            locale: { format: "YYYY/MM/DD" },
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
        }).on('apply.daterangepicker', function (ev, picker) {
            $(ev.target).validate();
        });
        tempForm.find("#date_from_to").val("");
    }
    dateFromTo();

    var redirectMasterfile = function () {
        window.location.replace(siteUrl("eforms/return_to_work/masterfile"));
    }
}