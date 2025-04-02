const modalSelect2 = $("#modal-add-taxable-deduction select#employee_id");
const vmEmployee = new Vue({
    el: "#tempEmployeeData",
    data: { basic_rate: 0, payroll_type: null },
    methods: {
        getTemporaryTaxableDeduction: function () {
            const _this = this;
            $.ajax({
                url: baseUrl('payroll/employee/get_employee_temporary_tax/' + _this.basic_rate + '/' + _this.payroll_type),
                type: 'GET',
                dataType: 'JSON',
                success: function (json) {
                    console.log(json);
                }
            });
        }
    }
});

modalSelect2.select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl('payroll/employee/get_employee_select2_data'),
        dataType: 'JSON',
        delay: 750,
        global: false,
    },
    dropdownParent: $("#modal-add-taxable-deduction")
}).on("select2:select", function (e) {
    const data = e.params.data;
    vmEmployee.basic_rate = data.basic_rate;
    vmEmployee.payroll_type = data.payroll_type;
    setTimeout(function () {
        vmEmployee.getTemporaryTaxableDeduction();
    }, 750);
});