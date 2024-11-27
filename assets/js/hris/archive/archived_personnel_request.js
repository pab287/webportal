let tblArchivedPersonnelRequest = $('#table-archived-personnel-request')
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        // order: [[1, 'asc']],
        ajax: {
            url: baseUrl('hris/archive/get_archived_personnel_request'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = $('#search-archived-personnel-request').val();
            }
        },
        columns: [
            {
                data: '_company',
                render: function (data, type, row) {
                    return "" +
                        "<p class='mb-0 m--font-boldest'>" + row._position + "</p>" +
                        "<p class='mb-0 mt-2 text-muted m--regular-font-size-sm1'>" + row._department + "</p>" +
                        "<p class='mb-0 m--font-boldest m--regular-font-size-sm1'>" + row._company + "</p>";
                }
            },
            {
                data: 'type',
                width: '10%'
            },
            {
                data: 'people_no',
                width: '7%',
                className: 'text-center'
            },
            {
                data: null,
                defaultContent: "0",
                className: 'text-center',
                width: '9%',
                render: function (data, type, row) {
                    const currentDate = moment();
                    const dateNeeded = moment(row.need_dt);
                    if (dateNeeded.isBefore(currentDate)) {
                        return currentDate.diff(dateNeeded, 'days');
                    }
                }
            },
            {
                data: 'requested_dt',
                width: '10%',
                render: function (data, type, row) {
                    return moment(data).format('ll');
                }
            },
            {
                data: 'need_dt',
                width: '10%',
                render: function (data, type, row) {
                    return moment(data).format('ll');
                }
            },
            {
                data: 'status',
                width: '10%',
                render: function (data) {
                    switch (data) {
                        case 'onGoing':
                            return 'On Going';
                            break;
                        case 'onHold':
                            return 'On Hold';
                            break;
                        default:
                            return 'For Approval';
                            break;
                    }
                }
            },
            {
                data: 'archive_remarks',
                width: '15%',
                orderable: false
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    let actions = '' +
                        '<button data-id="' + row.id + '" ' +
                        '        class="btn m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary btnRestoreOrDeleted"' +
                        '        data-placement="left" data-toggle="m-tooltip" ' +
                        '        title="" data-original-title="Restore" onclick="restorePersonnelRequest(' + row.id + ')">' +
                        '   <i class="la la-reply"></i>' +
                        '</button>';

                    return actions;
                },
                width: '6%',
            }
        ]
    });

$('#search-archived-personnel-request')
    .donetyping(function () {
        tblArchivedPersonnelRequest.ajax.reload();
    });

function restorePersonnelRequest(personnel_request_id) {
    $.ajax({
        url: baseUrl('hris/masterfile/open_confirm_modal'),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: '<i class="la la-reply mr-2"></i>Confirm Restore',
                message: 'Are you sure to restore this personnel request?',
                action: 'hris/archive/restore_personnel_request/' + personnel_request_id,
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
