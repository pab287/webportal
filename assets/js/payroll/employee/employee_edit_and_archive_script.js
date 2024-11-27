$('table')
    .on('click', '.btnEditCashAdvance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("payroll/employee/open_edit_modal"),
                type: "POST",
                dataType: "JSON",
                data: {
                    csrf_token: _csrf_hash,
                    path: "payroll/employee_profile/modals/edit_cash_advance",
                    function_name: "getCashAdvances",
                    model: "Employee_model",
                    formData: {id}
                },
                success: function (response) {
                    initRegularEditDialog(response);
                }
            });
        })
    .on('click', '.btnRemoveCashAdvance',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("payroll/employee/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "payroll/employee/archive_employee_cash_advance/" + id + "/btnRemoveCashAdvance",
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $(".document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");
                }
            });
        })
    .on('click', '.btnRemoveSalaryHistory',
        function () {
            const id = $(this).attr('data-id');
            $.ajax({
                url: baseUrl("payroll/employee/open_confirm_modal"),
                type: "POST",
                data: {
                    csrf_token: _csrf_hash,
                    formData: {
                        title: "Confirm Archive",
                        message: "Are you sure to archive this record?",
                        action: "payroll/employee/archive_salary_history/" + id + "/btnRemoveSalaryHistory",
                        color: "btn-danger"
                    },
                    path: "ams/confirmation_dialog",
                    function_name: "passDataToDialog"
                },
                success: function (modal) {
                    const _modal = $(".document-modal-container");
                    _modal.html(modal);
                    _modal.modal("show");
                }
            });
        })


$(".m-content")
    .on('submit', '#employee-data-update-cash-advance',
        function (e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr("action");
            const formData = new FormData(this);

            const id = form.find("input[name='id']").val();
            const tr = $('.btnEditCashAdvance[data-id="' + id + '"]').closest("tr");

            if (form.isValid()) {
                $.ajax({
                    url,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                tr.find('td:eq(1)').html(data.amt_applied);
                                tr.find('td:eq(2)').html(data.purpose);
                                tr.find('td:eq(3)').html(data.amt_approved);
                            }
                        }

                        _toaster(response, 'Cash Advance Updated.', 10000);
                        closeDialog();
                    }
                });
            }
        })

function initEditDocumentDialog(response) {
    const html = response.html;
    const data = response.info.data;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    const select2_doc_type = _modal.find("#doc_type");
    const _docType = data.doc_type;
    select2_doc_type.val(_docType).trigger('change');
    select2_doc_type
        .select2({
            placeholder: "Select Type",
            width: "100%",
            dropdownParent: $(".document-modal-container")
        });
    _modal.modal('show');
}

function initRegularEditDialog(response) {
    const html = response.html;
    const _modal = $('.document-modal-container');
    _modal.empty();
    _modal.append(html);

    _modal.find("input.date")
        .datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy-mm-dd",
            autoclose: true
        });

    _modal.find(".date-range")
        .daterangepicker({
            buttonClasses: 'm-btn btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            showDropdowns: true
        }, function (start, end, label) {
            $('.date-range .form-control')
                .val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        });

    _modal.find(".date-year")
        .datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            },
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
    _modal.modal('show');
}

function setFilename(el, elContainer) {
    const files = $(el)[0].files;
    if (files && files.length > 0) {
        const file = files[0];
        $('.modal-content ' + elContainer).html(file.name);
    } else {
        $('.modal-content ' + elContainer).html($("input[name='current_filename']").val());
    }
}

function closeDialog() {
    $('.document-modal-container').modal('hide');
}

function _toaster(response, title, duration) {
    const type = response.success ? 'success' : 'error';
    const _title = response.success ? title : 'Error';
    toastr[type](response.message, _title, duration);
}