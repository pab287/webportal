const tableTaxable = $("#table-fixed_taxable_deduction");
const modalSelect2 = $("#modal-add-taxable-deduction select#employee_id");
let searchRequest = '';

const vmEmployee = new Vue({
    el: "#tempEmployeeData",
    data: { basic_rate: 0, payroll_type: null },
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
}).on("change", function (e) {
    $(e.target).validate();
});

$('#generalSearch').donetyping(function (_callback) {
    searchRequest = $(this).val();
    dtTableTaxable.ajax.reload();
});

const dtTableTaxable = tableTaxable.DataTable({
    dom: '<"toolbar">rtlip',
    processing: true,
    serverSide: true,
    ordering: false,
    ajax: {
        url: siteUrl("payroll/employee/get_fixed_taxable_deduction"),
        type: "POST",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = searchRequest;
            return d;
        }
    },
    columns: [
        { data: "employee_name" },
        { data: "taxable_amount" },
        { data: "basic_rate" },
        { data: "payroll_type" },
        { data: "updated_by", width: "15%" },
        { data: "action", width: "15%", className: "text-center", 
            render: function (_data, _type, row) {
                return `---`;
            }
        },
    ],
});

$.validate({
    form: "#frm-add--taxable-deduction",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formData = $(currentForm).serialize();

        $.ajax({
            url: siteUrl("payroll/employee/add_taxable_deduction"),
            type: "post",
            dataType: "json",
            data: formData,
            beforeSend: function () {
                $(currentForm)
                    .find(".btn-submit")
                    .addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (json) {
                if(json.response) {
                    toastr.success(json.toastr_msg, "Taxable Deduction", 5000);
                    currentForm.reset();
                    $("#modal-add-taxable-deduction").modal("hide");
                    dtTableTaxable.ajax.reload();
                }
            }
        });
        return false;
    }
});