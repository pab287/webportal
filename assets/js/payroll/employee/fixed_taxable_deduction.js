const modalSelect2 = $("#modal-add-taxable-deduction select#employee_id");

modalSelect2.select2({
    placeholder: 'Select an option',
    width: '100%',
    ajax: {
        url: baseUrl('payroll/employee/get_employee_select2_data'),
        dataType: 'JSON',
        delay: 750,
    },
    dropdownParent: $("#modal-add-taxable-deduction")
});