toastr.options = { newestOnTop: true, positionClass: "toast-bottom-right" };

const tableTaxable = $("#table-fixed_taxable_deduction");
const modalSelect2 = $("#modal-add-taxable-deduction select#employee_id");
const modalEditTaxable = $("#modal-edit-taxable-deduction");
let searchRequest = '';

const vmEmployee = new Vue({
    el: "#tempEmployeeData",
    data: { basic_rate: 0, payroll_type: null },
    methods: {
        resetContent: function () {
            this.basic_rate = 0.00;
            this.payroll_type = null;
            return this;
        }
    }
});

const vmEditEmployeeTax = new Vue({
    el: "#editEmployeeData",
    data: { row: {} },
    methods: {
        resetContent: function () {
            let { row } = this;
            row.id = 0;
            row.employee_name = null;
            row.basic_rate = 0.00;
            row.payroll_type = null;
            return this;
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
}).on("change", function (e) {
    $(e.target).validate();
});

$('#generalSearch').donetyping(function (_callback) {
    searchRequest = $(this).val();
    dtTableTaxable.ajax.reload();
});

$(document).on("click", ".btnEditEmployeeFixedTaxable", function () {
    const rowData = $(this).data("row");
    vmEditEmployeeTax.row = { ...rowData };
    modalEditTaxable.modal("show");
});

$(document).on("click", ".btnUpdateEmployeeFixedTaxable", function () {
    const rowData = $(this).data("row");
    const { id, is_active, employee_name, taxable_amount, employee_id } = rowData;
    const state = parseInt(is_active) === 1 ? 'Deactivate' : 'Activate';
    const tempState = parseInt(is_active) === 1 ? 'danger' : 'success';
    Swal.fire({
        title: state + ' Fixed Amount Request?',
        html: "Are you sure you want to <strong class='text-"+ tempState +"'>`"+ state.toUpperCase() +"`</strong> this fixed amount taxable deduction of <strong class='text-primary'>`"+ employee_name.toUpperCase() +"`</strong> with taxable amount of <strong>`"+ taxable_amount +"`</strong>?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes '+ state + ' it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("payroll/employee/update_status_taxable_deduction"),
                type: "post",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                    is_active: is_active,
                    taxable_amount: taxable_amount,
                    employee_id: employee_id
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(json.toastr_msg, "Fixed Taxable Deduction", 5000);
                        dtTableTaxable.ajax.reload(null, false);
                    }
                }
            });
        }
    });
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
        { data: "employee_name", width: "*" },
        { data: "taxable_amount", width: "12%", 
            render: function (data) {
            return numberFormat(data);
        }},
        { data: "basic_rate", width: "12%", 
            render: function (data) {
            return numberFormat(data);
        }},
        { data: "payroll_type", width: "12%" },
        { data: "updated_by", width: "15%", 
            render: function (data, _type, row) {
                const recordDate = moment(row.updated_at).format("LLL");
                const _html = `<p class="m--font-bolder mb-0">${data}</p><p class="mb-0"><small>${recordDate}</small></p>`;
                return _html;
            }
        },
        { data: "is_active", width: "5%", className: "text-center", 
            render: function (data) { return data == 1 ? "<i class='fa fa-check-circle text-success m--icon-font-size-lg3'></i>" : "<i class='fa fa-times-circle text-danger m--icon-font-size-lg3'></i>" } 
        },
        { data: null, width: "8%", className: "text-center", 
            render: function (_data, _type, row) {
                let actionCtr = 0;
                let _actionButton = "";
                const tempIcon = parseInt(row.is_active) === 1 ? "fa-toggle-on" : "fa-toggle-off";
                const tempTooltip = parseInt(row.is_active) === 1 ? "Deactivate Taxable Deduction" : "Activate Taxable Deduction";
                const tempState = parseInt(row.is_active) === 1 ? "danger" : "success";

                const rawData = JSON.stringify(row);
                if (typeof _currentActions != "undefined" && _currentActions.length > 0 && jQuery.inArray("edit", _currentActions) !== -1) {
                    _actionButton += `<button type='button' 
                    class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditEmployeeFixedTaxable' 
                    data-placement='bottom' data-toggle='m-tooltip' title='' 
                    data-original-title='Edit Taxable Deduction' data-row='${rawData}'>
                        <i class='la la-edit'></i>
                    </button>`;
                    _actionButton += `<button type='button' 
                    class='btn btn-default m-btn m-btn--hover-${tempState} m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnUpdateEmployeeFixedTaxable' 
                    data-placement='bottom' data-toggle='m-tooltip' title='' 
                    data-original-title='${tempTooltip}' data-row='${rawData}'>
                        <i class='fa ${tempIcon}'></i>
                    </button>`;
                    actionCtr++;
                }
                if(actionCtr == 0) { _actionButton = '---'; }
                return _actionButton;
            }
        },
    ],
    columnDefs: [{ targets: "_all", defaultContent: "" }]
});

$.validate({
    form: "#frm-add--taxable-deduction",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formData = $(currentForm).serialize();

        Swal.fire({
            title: 'Fixed Amount Request?',
            html: "Are you sure you want to add this new fixed amount taxable deduction?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
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
                            $(currentForm).find("#employee_id").empty();
                            vmEmployee.resetContent();
                            $("#modal-add-taxable-deduction").modal("hide");
                            dtTableTaxable.ajax.reload();
                        }else{
                            toastr.error(json.toastr_msg, "Taxable Deduction", 5000);
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            }
        });

        return false;
    }
});

$.validate({
    form: "#frm-edit--taxable-deduction",
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const currentForm = form[0];
        const formData = $(currentForm).serialize();

        Swal.fire({
            title: 'Fixed Amount Request?',
            html: "Are you sure you want to update this fixed amount taxable deduction?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: siteUrl("payroll/employee/update_taxable_deduction"),
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
                            vmEditEmployeeTax.resetContent();
                            modalEditTaxable.modal("hide");
                            dtTableTaxable.ajax.reload();
                        }else{
                            toastr.error(json.toastr_msg, "Taxable Deduction", 5000);
                        }
                        $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
                    }
                });
            }
        });

        return false;
    }
});