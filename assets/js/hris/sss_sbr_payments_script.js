let searchValue = "";
let _companies = [];
let _years = [];
let entryDate = null;
const months = [
    { id: 1, text: "January" },
    { id: 2, text: "February" },
    { id: 3, text: "March" },
    { id: 4, text: "April" },
    { id: 5, text: "May" },
    { id: 6, text: "June" },
    { id: 7, text: "July" },
    { id: 8, text: "August" },
    { id: 9, text: "September" },
    { id: 10, text: "October" },
    { id: 11, text: "November" },
    { id: 12, text: "December" }
];

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.years !== "undefined" && _tempContentData.years.length > 0){
        _years = _tempContentData.years;
    }
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }

    if(typeof _tempContentData.entry_date !== "undefined" && _tempContentData.entry_date){
        entryDate = _tempContentData.entry_date;
    }
}

$("#generalSearch").donetyping(function () {
    searchValue = $(this).val();
    dtSbrPayments.ajax.reload();
});

$("#modal-sbr_payment #company").select2({ 
    data: _companies, 
    width: '100%',
    allowClear: true,
    placeholder: 'Select Company',
    dropdownParent: $("#modal-sbr_payment")
}).on("select2:select", function(e){
    $(e.target).validate();
});

$("#modal-sbr_payment #payment_date").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    startDate: moment(entryDate).format('YYYY-MM-DD'),
    endDate: moment().format('YYYY-MM-DD'),
}).on("changeDate", function (e) {
    $(e.target).validate();
}).on("show", function (e) {
    let m = $("#modal-sbr_payment #month").val();
    let y = $("#modal-sbr_payment #year").val();
    if(m && y){
        m = parseInt(m) + 1;
        if(m > 12){
            m = 1;
            y = parseInt(y) + 1;
        }
        $("#modal-sbr_payment #payment_date").datepicker("setDate", moment(`${y}-${m}-01`).format('YYYY-MM-DD'));
    }
});

$("#modal-sbr_payment #year").select2({
    width: '100%',
    data: _years,
    placeholder: "SELECT YEAR",
    allowClear: true,
    dropdownParent: $("#modal-sbr_payment")
}).on("select2:select", function(e){
    $(e.target).validate();
    const cValue = $(e.target).val();
    const entryYear = moment(entryDate).year();
    if(cValue == moment().year()){
        let tempMonth = moment().format('M');
        tempMonth = parseInt(tempMonth) + 1;
        const monthSelect = $("#modal-sbr_payment #month");
        if(typeof monthSelect !== "undefined" && monthSelect.length > 0){
            monthSelect.empty();
            $.each(months, function(k, v){
                const nOption = new Option(v.text, v.id, false, false);
                if(k >= tempMonth){ nOption.disabled = true; }
                monthSelect.append(nOption);
            });
            monthSelect.val("").trigger("change.select2");
        }
    } else if(cValue >= entryYear){
        let tempMonth = moment(entryDate).format('M');
        tempMonth = parseInt(tempMonth);
        const monthSelect = $("#modal-sbr_payment #month");
        if(typeof monthSelect !== "undefined" && monthSelect.length > 0){
            monthSelect.empty();
            $.each(months, function(k, v){
                const nOption = new Option(v.text, v.id, false, false);
                if(k < tempMonth){ nOption.disabled = true; }
                monthSelect.append(nOption);
            });
            monthSelect.val("").trigger("change.select2");
        }
    } else{
        const monthSelect = $("#modal-sbr_payment #month");
        if(typeof monthSelect !== "undefined" && monthSelect.length > 0){
            monthSelect.empty();
            $.each(months, function(k, v){
                const nOption = new Option(v.text, v.id, false, false);
                monthSelect.append(nOption);
            });
            monthSelect.val("").trigger("change.select2");
        }
    }
});

$("#modal-sbr_payment #month").select2({
    width: '100%',
    data: months,
    placeholder: "SELECT MONTH",
    allowClear: true,
    dropdownParent: $("#modal-sbr_payment")
}).on("select2:select", function(e){
    $(e.target).validate();
});


const dtSbrPayments = $("#table-sbr_payments").DataTable({
    "dom": 'frtlip',
    "serverSide": true,
    "processing": true,   
    "searching": false,
    "ordering": false,
    "lengthMenu": [ 10, 25, 50, 100 ],
    "pageLength": 10,
    "ajax": {
        "url": siteUrl("hris/masterfile/get_sbr_payments_datatable_request"),
        "type": "POST",
        "dataType": "JSON",
        "global": false,
        "data": function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = searchValue;
            return d;
        }
    },
    "columns": [
        { "data": "company_code", width: "*" },
        { "data": "sbr_no", width: "14%" },
        { "data": "payment_date", className: "text-center", width: "10%", render: function (data) {
            return moment(data).format("MM/DD/YYYY");
        }},
        { "data": "year", className: "text-center", width: "8%" },
        { "data": "month_name", className: "text-center", width: "12%" },
        { "data": "contribution_count", width: "12%", className: "text-center" },
        { "data": null, width: "10%", className: "text-center", render: function (data, type, row) {
            const { id, contribution_count } = row;
            const rawData = JSON.stringify(row);
            let html = `<button class="btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnRegenerateSbr"
                data-id="${id}"
                data-placement="top"
                data-toggle="m-tooltip"
                title="Generate SBR Payment Employee(s)"><i class="la la-refresh"></i></button> `;
            html += `<button class="btn btn-default m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditSbrPayment"
            data-row='${rawData}'
            data-placement="top"
            data-toggle="m-tooltip"
            title="Edit SBR Payment Details"><i class="la la-edit"></i></button> `;

            if (contribution_count > 0){
                html += `<button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnPreviewSbr" data-id="${id}"
                data-placement="top"
                data-toggle="m-tooltip"
                title="Preview SBR Payment List">
                <i class="la la-list"></i></button>`;
            } else {
                html += `<button class="btn btn-default m-btn m-btn--hover-default m-btn--icon m-btn--icon-only m-btn--pill btnEdit" disabled>
                <i class="la la-list"></i></button>`;
            }
            return html;
        }}
    ]
});

$(document).on("click", ".btnPreviewSbr", function (e) {
    const id = $(this).data("id");
    $.ajax({
        url: siteUrl("hris/masterfile/get_sbr_payment_preview"),
        type: "POST",
        dataType: "JSON",
        data: { id: id, csrf_token: _csrf_hash },
        success: function (json) {
            vmContribution.rows = [];
            vmContribution.count = 0;
            vmContribution.info = {};
            if(json.response){
                vmContribution.rows = json.data;
                vmContribution.count = json.count;
                vmContribution.info = { ...json.payment_info };
                $("#modal-sbr_employee_details").modal("show");
            }else{
                toastr.error(json.toastr_msg, "Preview SBR Payment", 5000);
            }
        }
    })
});

$(document).on("click", ".btnEditSbrPayment", function (e) {
    const rawData = $(this).data("row");
    const { month_name, contribution_count } = rawData;
    const monthNum = moment().month(month_name).format("M");
    rawData.month = monthNum;
    vmSbrPayment.row = { ...rawData };
    if(parseInt(contribution_count) === 0){
        setTimeout(function () {
            vmSbrPayment.setDatePicker();
            vmSbrPayment.setSelect2Containers();
        }, 250);
    }else{
        vmSbrPayment.destroySelect2();
    }
    $("#modal-edit_sbr_payment").modal("show");
});

$(document).on("click", ".btnRegenerateSbr", function (e) {
    const id = $(this).data("id");
    Swal.fire({
        title: "SBR Payment - Employees",
        text: "Are you sure you want to generate this SBR Payment Employees?",
        type: "warning",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Generate it!",
        showLoaderOnConfirm: true,
    }).then(function (result) {
        if(result.isConfirmed) {
            $.ajax({
                url: siteUrl("hris/masterfile/generate_sbr_payment"),
                type: "POST",
                dataType: "JSON",
                data: { id: id, csrf_token: _csrf_hash },
                success: function (json) {
                    if(json.response){
                        toastr.success(json.toastr_msg, "Generate SBR Payment", 5000);
                        dtSbrPayments.ajax.reload(null, false);
                    }else{
                        toastr.error(json.toastr_msg, "Generate SBR Payment", 5000);
                    }
                }
            });
        }
    });
});

const vmContribution = new Vue({
    el: "#contribution_content",
    data: { rows: [], count: 0, info: {} },
    watch: {
        'info.created_at'(val) {
            this.info.created_at = moment(val).format('LLL');
        },
    }, methods: {
        getGrandTotal: function () {
            let total = 0;
            this.rows.forEach(function (row) {
                total += parseFloat(row.sss_total);
            });
            const tempTotal = total.toFixed(2);
            return numberFormat(tempTotal);
        },
        numberFormatter: function (num) {
            return numberFormat(num);
        }
    }
});

const vmSbrPayment = new Vue({
    el: "#sbr_payment-content",
    data: { row: {} },
    watch: {
        'row.month_name'(val) {
            this.row.month_name = val.toUpperCase();
            return this;
        },
    },
    methods: {
        initMonth: function (destroy = false, nMonths = []) {
            const currentElement = this.$el;
            if (destroy && $("#edit_month", currentElement).hasClass("select2-hidden-accessible")) {
                $("#edit_month", currentElement).select2("destroy");
            }
            setTimeout(function () {
                
            }, destroy ? 0 : 150);
        },
        setSelect2Containers: function () {
            const _this = this;
            const { company_id, month, year }= this.row;
            const currentElement = this.$el;
            $("#edit_company", currentElement).select2({
                data: _companies,
                width: '100%',
                allowClear: true,
                placeholder: 'Select Company',
                dropdownParent: $(currentElement)
            });

            $("#edit_year", currentElement).select2({
                data: _years,
                width: '100%',
                allowClear: true,
                placeholder: 'Select Year',
                dropdownParent: $(currentElement)
            }).on("select2:select", function(e){
                const year = $(this).val();
                if(year == moment().year()){
                    let tempMonth = moment().format('M');
                    tempMonth = parseInt(tempMonth) + 1;

                    const monthSelect = $("#edit_month", currentElement);
                    if(typeof monthSelect !== "undefined" && monthSelect.length > 0){
                        monthSelect.empty();
                        $.each(months, function(k, v){
                            const nOption = new Option(v.text, v.id, false, false);
                            if(k >= tempMonth){ nOption.disabled = true; }
                            monthSelect.append(nOption);
                        });
                        monthSelect.val("").trigger("change.select2");
                    }
                }else{
                    const monthSelect = $("#edit_month", currentElement);
                    if(typeof monthSelect !== "undefined" && monthSelect.length > 0){
                        monthSelect.empty();
                        $.each(months, function(k, v){
                            const nOption = new Option(v.text, v.id, false, false);
                            monthSelect.append(nOption);
                        });
                        monthSelect.val("").trigger("change.select2");
                    }
                }
            });

            $("#edit_month", currentElement).select2({
                    data: months,
                    width: '100%',
                    allowClear: true,
                    placeholder: 'Select Month',
                    dropdownParent: $(currentElement)
                }).on("select2:select", function (e) {
                    $(e.target).validate();
                });

            $("#edit_company", currentElement).val(company_id).trigger("change");
            $("#edit_month", currentElement).val(month).trigger("change");
            $("#edit_year", currentElement).val(year).trigger("change");

            return this;
        }, setDatePicker: function () {
            const { payment_date }= this.row;
            const currentElement = this.$el;
            $("#edit_payment_date", currentElement).datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                startDate: moment(entryDate).format('YYYY-MM-DD'),
                endDate: moment().format('YYYY-MM-DD'),
            });
            $("#edit_payment_date", currentElement).datepicker("setDate", payment_date);
            return this;
        }, destroySelect2: function () {
            const currentElement = this.$el;
            $("#edit_company", currentElement).select2("destroy");
            $("#edit_year", currentElement).select2("destroy");
            $("#edit_month", currentElement).select2("destroy");
            return this;
        }
    }
});

$.validate({
    form: "#form-sbr_payment",
    lang: "en",
    onSuccess: function (form) {
        const currentForm = $(form);
        $.ajax({
            url: siteUrl("hris/masterfile/save_sbr_payment"),
            type: "POST",
            dataType: "JSON",
            data: currentForm.serialize(),
            success: function (json) {
                if(json.response){
                    toastr.success(json.toastr_msg, "Save SBR Payment", 5000);
                    $("#modal-sbr_payment").modal("hide");
                    dtSbrPayments.ajax.reload(null, false);
                    setTimeout(function () { resetForm(currentForm); }, 250);
                }else{
                    toastr.error(json.toastr_msg, "Save SBR Payment", 5000);
                }
            }
        });
        return false;
    }
});

$.validate({
    form: "#form-edit_sbr_payment",
    lang: "en",
    onSuccess: function (form) {
        const currentForm = $(form);
        $.ajax({
            url: siteUrl("hris/masterfile/update_sbr_payment"),
            type: "POST",
            dataType: "JSON",
            data: currentForm.serialize(),
            success: function (json) {
                if(json.response){
                    toastr.success(json.toastr_msg, "Update SBR Payment", 5000);
                    $("#modal-edit_sbr_payment").modal("hide");
                    dtSbrPayments.ajax.reload(null, false);
                    setTimeout(function () { resetForm(currentForm); }, 250);
                }else{
                    toastr.error(json.toastr_msg, "Update SBR Payment", 5000);
                }
            }
        });
        return false;
    }
});

const resetForm = (currentForm) => {
    currentForm.find("select").val("").trigger("change");
    currentForm[0].reset();
};