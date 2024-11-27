const addExcludedEmployeeModal = $("#add-excluded-employee-from-timesheet-modal");
const editExcludedEmployeeModal = $("#edit-excluded-employee-from-timesheet-modal");
const tblExcludedEmployees = $("#tbl-excluded-employees");
const confirmationModal = $("#timesheet-confirmation-modal");
const cbSelectAll = $("#cb-select-all");
const alertModal = $('#alert-modal');

let dtExcludedEmployees = tblExcludedEmployees
    .DataTable({
        dom: '<"row"<"col-12" rt>><"row"<"col-6" l><"col-6" p>>',
        serverSide: true,
        destroy: true,
        ajax: {
            url: baseUrl('gcctime/timesheet/get_excluded_employee_list'),
            type: 'POST',
            dataType: 'JSON',
            data: function (_data) {
                _data.csrf_token = _csrf_hash;
                _data.search.value = $("#search").val();
            },
            global: false
        },
        columns: [
            {
                data: null,
                width: "40px",
                orderable: false,
                render: function (data, type, row) {
                    return `<label class="m-checkbox m-checkbox--bold m-checkbox--state-brand table-cb">
                                           <input type="checkbox" name="selected[]" value="${row.id}" 
                                                  class="cb-row-${row.id}"><span></span>
                                      </label>`;
                },
                className: "text-center",
            },
            {
                width: "70px",
                data: "pic_url",
                render: function (data) {
                    return `<div class="table-avatar"><img src="${data}" alt="profile-image"></div>`;
                },
                orderable: false,
                className: "text-center"
            },
            {
                width: "25%",
                data: "employee_name",
                render: function (data, type, row, meta) {
                    return `<div class="m--font-bolder" id="row-${row.id}" data-row="${meta.row}">
                                <div class="mb-1">${data}</div>
                                <div class="m--regular-font-size-sm1 text-muted">${row.position}</div>
                                <div class="m--regular-font-size-sm1 text-muted">${row.company}</div>
                            </div>`;
                }
            },
            {data: "reason"},
            {
                width: "15%",
                data: "_created_by",
                render: function (data, type, row, meta) {
                    return `<div class="mb-1">${data}</div>
                            <div class="m--regular-font-size-sm1 text-muted">
                                ${moment(row.created_at).format("lll")}                           
                            </div>`;
                }
            },
            {
                width: "8%",
                data: null,
                render: function (data, type, row, meta) {
                    let buttons = ``;
                    if (_currentActions.includes('delete')) {
                        buttons += `<button type="button" onclick="openModal('confirmation', '${row.employee_name}', ${row.id})" 
                                            class="btn btn-default btn-sm m-btn m-btn--pill 
                                                   m-btn--icon m-btn--icon-only m-btn--hover-danger
                                                   mr-2
                                                   btnDelete"
                                            data-toggle="m-tooltip"
                                            data-original-title="Delete"
                                            data-skin="dark"
                                            data-delay='{"show": 300}'>
                                        <i class="fa fa-trash"></i>
                                    </button>`;
                    }

                    if (_currentActions.includes('edit')) {
                        buttons += `<button type="button" onclick="openModal('edit', '${row.employee_name}', ${row.id})"
                                            class="btn btn-default btn-sm m-btn m-btn--pill 
                                                   m-btn--icon m-btn--icon-only m-btn--hover-info
                                                   btnEdit"
                                            data-toggle="m-tooltip"
                                            data-original-title="Edit Reason"
                                            data-skin="dark"
                                            data-delay='{"show": 300}'>
                                        <i class="fa fa-pencil"></i>
                                    </button>`;
                    }

                    return buttons;
                },
                orderable: false,
                className: "text-center"
            },
        ],
        autoWidth: false,
        order: [[2, 'ASC']],
        columnDefs: []
    });


cbSelectAll
    .on('change', function (e) {
        const checkedValue = e.target.checked;
        $('tbody input[type=\'checkbox\']', tblExcludedEmployees).prop('checked', checkedValue);

        if (checkedValue) {
            $('#btn-mass-delete').removeAttr('disabled');
        } else {
            $('#btn-mass-delete').attr('disabled', 'true');
        }
    });

$(tblExcludedEmployees)
    .on('change', 'tbody input[type=\'checkbox\']', function () {
        const el = this;
        const id = $(el).val();
        const checkedValue = $(el)[0].checked;

        $(`.cb-row-${id}`).prop('checked', checkedValue);
        checkCbSelectAll();
    });

function checkCbSelectAll() {
    const cbCount = $('tbody input[type=\'checkbox\']', tblExcludedEmployees).length;
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblExcludedEmployees).length;

    if (parseInt(checkedCbCount) >= 1) {
        $('#btn-mass-delete').removeAttr('disabled');
    } else {
        $('#btn-mass-delete').attr('disabled', 'true');
    }

    cbSelectAll.prop('checked', (parseInt(cbCount) === parseInt(checkedCbCount) && parseInt(checkedCbCount) >= 1));
}

$('#employee-list', addExcludedEmployeeModal).select2({
    width: '100%',
    ajax: {
        delay: 1000,
        global: false,
        url: baseUrl('gcctime/timesheet/get_employees_for_filter/0'),
        dataType: 'JSON',
        type: 'GET'
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    templateResult: function (data) {
        return data.html;
    },
    templateSelection: function (data) {
        return data.text;
    }
});

$('#employee-list', editExcludedEmployeeModal).select2({
    width: '100%',
    ajax: {
        delay: 1000,
        global: false,
        url: baseUrl('gcctime/timesheet/get_employees_for_filter/0'),
        dataType: 'JSON',
        type: 'GET'
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    templateResult: function (data) {
        return data.html;
    },
    templateSelection: function (data) {
        return data.text;
    }
});

$.validate({
    form: $("#frm-add-excluded-employee-from-timesheet"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const submit = $('button[type="submit"]', $(form));
        const formData = new FormData($(form)[0]);
        formData.append("csrf_token", _csrf_hash);

        $.ajax({
            url: baseUrl(`gcctime/timesheet/add_excluded_employee_from_timesheet`),
            data: formData,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                submit.addClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            },
            success: function (response) {
                if (response) {
                    const toast = response.success ? "success" : "error";
                    toastr[toast](response.message, response.title, {timeOut: 10000});

                    $(form).resetForm();
                    $("#employee-list", addExcludedEmployeeModal).val([]).trigger('change');
                    dtExcludedEmployees.ajax.reload();
                }

                submit.removeClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            }
        })

        return false;
    }
});

$.validate({
    form: $('#frm-timesheet-confirmation-modal'),
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const functionCall = $(form).attr('data-function');
        eval(functionCall)(form);
        confirmationModal.modal('hide');
        return false;
    }
});

function deleteExcluded(form = null) {
    const url = $(form).attr("action");
    const id = url.split("/").pop();

    $.ajax({
        url,
        dataType: "JSON",
        type: "GET",
        processData: false,
        contentType: false,
        success: function (response) {
            if (response) {
                const toast = response.success ? "success" : "error";
                toastr[toast](response.message, response.title, {timeOut: 10000});
            }

            const tr = $(`#row-${id}`).closest("tr");
            tr.remove();
            const tbody = tr.parent();

            if (parseInt($("tr", tbody).length) <= 0) {
                dtExcludedEmployees.ajax.reload();
            }

            $("form", confirmationModal).removeAttr("action");
            confirmationModal.modal("hide");
        }
    });
}

$.validate({
    form: $("#frm-edit-excluded-employee-from-timesheet"),
    lang: "en",
    scrollToTopOnError: false,
    onSuccess: function (form) {
        const url = $(form).attr("action");
        const submit = $('button[type="submit"]', $(form));
        const formData = new FormData($(form)[0]);
        formData.append("csrf_token", _csrf_hash);
        const id = url.split("/").pop();
        const row = $("#row-" + id).attr("data-row");

        $.ajax({
            url,
            data: formData,
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function () {
                submit.addClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            },
            success: function (response) {
                if (response) {
                    toastr[response.toast](response.message, response.title, {timeOut: 10000});

                    if (response.toast !== 'warning') {
                        dtExcludedEmployees.row(row).data(response.data).draw();
                        editExcludedEmployeeModal.modal("hide");
                    }
                }

                submit.removeClass(`m-btn--custom m-loader m-loader--light m-loader--left`);
            }
        });

        return false;
    }
});

function openModal(modal, employee_name, id) {
    if (modal === 'confirmation') {
        $(".modal-title", confirmationModal).html("Delete Confirmation");
        $(".modal-body", confirmationModal).html(`<p class="m--regular-font-size-lg2 m--font-bold">
                                          Are you sure to delete <u><span class="m--font-boldest">${employee_name}</span></u> from exclusion list?
                                       </p>`);
        $("form", confirmationModal).attr("action", baseUrl(`gcctime/timesheet/delete_employee_from_exclusion/${id}`));
        $('form', confirmationModal).attr('data-function', 'deleteExcluded');
        $(".btnSave", confirmationModal).html("Yes");
        $(".btnClose", confirmationModal).html("No");
        confirmationModal.modal("show");
    } else {
        $("form", editExcludedEmployeeModal).attr("action", baseUrl(`gcctime/timesheet/update_excluded_employee/${id}`));

        $.ajax({
            url: baseUrl(`gcctime/timesheet/get_excluded_employee_detail/${id}`),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                const option = new Option(data.employee_name, data.emp_id, false, true);
                $('#employee-list', editExcludedEmployeeModal).append(option);
                $("#exclusion-reason", editExcludedEmployeeModal).val(data.reason);
                editExcludedEmployeeModal.modal("show");
            }
        });
    }
}


$("#search")
    .donetyping(function () {
        dtExcludedEmployees.ajax.reload();
    });

function openConfirmationModal(functionCall) {
    $('.modal-title', confirmationModal).html(
        `<span class="m--font-bolder m--regular-font-size-lg3">Action Confirmation</span>`);
    $('.modal-body', confirmationModal).html(`
                        <span class="m--font-bold m--regular-font-size-lg2">Are you sure to delete selected time record(s)?</span>`);
    $('.btnSave', confirmationModal).html(`Yes`);
    $('.btnClose', confirmationModal).html(`No`);
    $('form', confirmationModal).attr('data-function', functionCall);
    confirmationModal.modal('show');
}

function massDelete(form = null) {
    const checkedCbCount = $('tbody input[type=\'checkbox\']:checked', tblExcludedEmployees).length;
    if (parseInt(checkedCbCount) <= 0) {
        $('.modal-title', alertModal).html(`<span class="m--font-danger m--font-bolder m--regular-font-size-lg3">Oops! Unable to Delete.</span>`);
        $('.modal-body', alertModal).html(`<p class="m-0 m--regular-font-size-lg1 m--font-bolder">Please select/check at least one(1) record.</p>`);
        alertModal.modal('show');
    } else {
        const selectedCheckboxes = dtExcludedEmployees.rows().nodes().to$().find('input[type="checkbox"]:checked');
        let cbIdArrays = [];
        $.each(selectedCheckboxes, function (i, cb) {
            cbIdArrays.push($(cb).val());
        });

        $.ajax({
            url: baseUrl('gcctime/timesheet/delete_employee_from_exclusion/0/1'),
            type: 'post',
            dataType: 'JSON',
            data: {
                id: cbIdArrays,
                csrf_token: _csrf_hash,
            },
            success: function (response) {
                $.each(selectedCheckboxes, function (i, cb) {
                    const tr = $(cb).closest('tr');
                    tr.remove();
                });

                if ($("tbody tr", tblExcludedEmployees).length <= 0) {
                    dtExcludedEmployees.ajax.reload();
                }

                const toast = response.success ? 'success' : 'error';
                toastr[toast](response.message, response.title);

                cbSelectAll.prop('checked', false);
            }
        });
    }
}