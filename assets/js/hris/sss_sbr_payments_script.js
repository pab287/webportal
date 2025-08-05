let _companies = [];
let _years = [];
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
}

$("#modal-sbr_payment #company").select2({ 
    data: _companies, 
    width: '100%',
    allowClear: true,
    placeholder: 'Select Company',
    dropdownParent: $("#modal-sbr_payment")
});
$("#modal-sbr_payment #payment_date").datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    endDate: moment().format('YYYY-MM-DD'),
});
$("#modal-sbr_payment #month")
    .select2({
        width: '100%',
        data: months,
        placeholder: "SELECT MONTH",
        allowClear: true,
        dropdownParent: $("#modal-sbr_payment")
    });

$("#modal-sbr_payment #year")
    .select2({
        width: '100%',
        data: _years,
        placeholder: "SELECT YEAR",
        allowClear: true,
        dropdownParent: $("#modal-sbr_payment")
    });

const dtSbrPayments = $("#table-sbr_payments").DataTable({
    "dom": 'frtlip',
    "processing": false,
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
            const html = `<button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnRegenerateSbr"
            data-id="${row.id}"><i class="la la-refresh"></i></button>
            <button class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnPreviewSbr" data-id="${row.id}">
            <i class="la la-list"></i></button>`;
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
            if(json.response){
                vmContribution.rows = json.data;
                vmContribution.count = json.count;
                $("#modal-sbr_employee_details").modal("show");
            }else{
                toastr.error(json.toastr_msg, "Preview SBR Payment", 5000);
            }
        }
    })
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
    data: { rows: [], count: 0 }
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
                }else{
                    toastr.error(json.toastr_msg, "Save SBR Payment", 5000);
                }
            }
        });
        return false;
    }
});