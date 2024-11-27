let tblArchivedEmployees = $('#table-archived-employees')
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        order: [[1, 'asc']],
        ajax: {
            url: baseUrl('hris/archive/get_archived_employees'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $('#search-archived-employees').val();
            }
        },
        columns: [
            {
                data: "image",
                orderable: false,
                className: "text-center",
                width: '6%',
                render: function (data) {
                    return '' +
                        '<div clas="m-card-profile__pic-wrapper">' +
                        '   <img class="m--img-rounded m--marginless m--img-centered user__pic" src="' + data + '">' +
                        '</div>';
                }
            },
            {
                data: 'employee_name',
                width: '35%',
                render: function (data, type, row) {
                    return '' +
                        '<p class="m-0 m--font-bolder mb-2">' + data + '</p>' +
                        '<p class="m-0 text-muted m--regular-font-size-sm1">' + row._position + '</p>' +
                        '<p class="m-0 text-muted m--regular-font-size-sm1">' + row._department + '</p>' +
                        '<p class="m-0 text-muted m--regular-font-size-sm1 m--font-boldest">' + row._company + '</p>';
                }
            },
            {
                data: 'archive_remarks',
                orderable: false,
                render: function (data) {
                    return data ? data : 'N/A';
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let btnActions = '' +
                        '<button type="button" ' +
                        '   class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary btnRestoreOrDeleted"' +
                        '   data-id="' + row.id + '"' +
                        '   onclick="restoreEmployee(' + row.id + ')"' +
                        '   title="" data-placement="left" data-toggle="m-tooltip" data-original-title="Restore">' +
                        '   <i class="la la-reply"></i>' +
                        '</button>';

                    /*btnActions += ' ' +
                        '<button type="button" ' +
                        '   class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-danger btnRestoreOrDeleted"' +
                        '   data-id="' + row.id + '"' +
                        '   onclick="deleteEmployee(' + row.id + ')"' +
                        '   title="" data-placement="left" data-toggle="m-tooltip" data-original-title="Delete Permanently">' +
                        '   <i class="la la-trash-o"></i>' +
                        '</button>';*/

                    return btnActions;
                },
                orderable: false,
                className: 'text-center',
                width: '5%'
            },
        ]
    });

$('#search-archived-employees')
    .donetyping(function () {
        tblArchivedEmployees.ajax.reload();
    });

function restoreEmployee(emp_id) {
    $.ajax({
        url: baseUrl('hris/masterfile/open_confirm_modal'),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: 'Confirm Restore',
                message: 'Are you sure to restore this employee?',
                action: 'hris/archive/restore_employee/' + emp_id,
                color: 'btn-danger'
            },
            path: 'ams/confirmation_dialog',
            function_name: 'passDataToDialog'
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

function deleteEmployee(emp_id) {
    $.ajax({
        url: baseUrl('hris/masterfile/open_confirm_modal'),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: 'Confirm Delete',
                message: 'Are you sure to delete this employee permanently?',
                action: 'hris/masterfile/delete_employee/' + emp_id,
                color: 'btn-danger'
            },
            path: 'ams/confirmation_dialog',
            function_name: 'passDataToDialog'
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}

$('.m-content')
    .on('submit', '#confirmation-dialog', function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');

        const id = url.split('/').pop();
        const tr = $('.btnRestoreOrDeleted[data-id="' + id + '"]').closest('tr');

        $.ajax({
            url: baseUrl(url),
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, response.title, 10000);
                    tr.remove();
                } else {
                    toastr.error(response.message, response.title, 10000);
                }

                $('.document-modal-container').modal('hide');
            }
        });
    });