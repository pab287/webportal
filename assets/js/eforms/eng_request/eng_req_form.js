let _employee = null;
let _projects = null;
let rfiTable = null;
let rfaTable = null;
let is_archive = 0;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
    if(typeof _tempContentData.projects !== "undefined" && _tempContentData.projects.length > 0){
        _projects = _tempContentData.projects;
    }
}


$(document).ready(function () {
    rfiTable = $('#rfi_table').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        rowId: 'id',
        order: [[ 0, "desc" ]],
        ajax: {
            url: baseUrl("eforms/engineering_request_forms/get_rfis"),
            type: "POST",
            dataType: "json",
            global: false,
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.is_archive = is_archive;
            }
        },
        columns: [
            { data: "id",visible : false, searchable: false },
            { data: "rfi_no",
                render: function (data, type, row) {
            
                    let badge = "";
            
                    switch (row.status) {
                        case "pending":
                            badge = `<span class="badge badge-success">PENDING</span>`;
                            break;

                        case "approved":
                            badge = `<span class="badge badge-success">APPROVED</span>`;
                            break;
            
                        // case "disapproved":
                        //     badge = `<span class="badge badge-danger">DISAPPROVED</span>`;
                        //     break;
            
                        case "for_approve":
                            badge = `<span class="badge badge-warning">FOR APPROVAL</span>`;
                            break;
            
                        case "noted":
                            badge = `<span class="badge badge-primary">NOTED</span>`;
                            break;
            
                        default:
                            badge = `<span class="badge badge-secondary">${row.status ?? 'UNKNOWN'}</span>`;
                    }
            
                    return `
                        <div>
                            <strong>Reference No:</strong> ${row.rfi_no}<br>
                            ${badge}
                        </div>
                    `;
                }
            },
            { data: "project_name",
                render: function (data, type, row) {
                    return `${row.project_name} - ${row.project_location}`;
                }
            },
            { data: "reply_needed",
                render: function (data, type, row) {
                    if (type !== "display") return data;
                    if (!data) return "";
                    const formattedDate = moment(data).format("MMMM DD, YYYY");
                    let badge = "";
            
                    if (row.status !== "noted") {
                        const today = moment().startOf("day");
                        const dueDate = moment(data).startOf("day");
            
                        if (dueDate.isBefore(today)) {
                            badge = ` <span class="badge badge-danger ml-1">OVERDUE</span>`;
                        }
                    }
                    return `${formattedDate}${badge}`;
                }
            },
            {
                data: "created_at",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return moment(data, "YYYY-MM-DD HH:mm:ss").format("MMMM DD, YYYY hh:mm A");
                }
            },
            { data: "created_by_name", orderable: false, searchable: false, },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let actionButton = '';
                    if (is_archive == 1) {
                        actionButton = `
                            <button class="btn btn-default m-btn m-btn--hover-success 
                                m-btn--icon m-btn--icon-only m-btn--pill"
                                onclick="restoreRow(${row.id})"
                                data-toggle="m-tooltip"
                                data-placement="bottom"
                                data-skin="dark"
                                data-original-title="Restore"
                                data-delay='{"show":300}'>
                                <i class="la la-undo"></i>
                            </button>
                        `;
                    } else {
                        actionButton = `
                        <a href="${baseUrl('eforms/engineering_request_forms/view_rfi_request/')}${row.id}" 
                            target="_blank"
                            class="btn btn-default m-btn m-btn--hover-brand 
                            m-btn--icon m-btn--icon-only m-btn--pill"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="View Request"
                            data-delay='{"show":300}'>
                            <i class="la la-eye"></i>
                        </a>
                        <button class="btn btn-default m-btn m-btn--hover-brand 
                            m-btn--icon m-btn--icon-only m-btn--pill"
                            onclick="archiveRow(${row.id})"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="Archive"
                            data-delay='{"show":300}'>
                            <i class="la la-archive"></i>
                        </button>
                        `;
                    }
            
                    return actionButton;
                }
            },
        ],
    }); 

});

$('#project_name').select2({
    dropdownParent: $('#newRFIModal'),
    width: '100%',
    placeholder: 'Select project',
    allowClear: true,
    data: _projects
});

$("#project_name").on("select2:select", function (e) {
    const data = e.params.data;
    $('#project_location').val(data.project_location);
}).on("select2:unselect", function () {
    $('#project_location').val('');
});

// $('#cc_to').select2({
//     placeholder: 'Select. .',
//     dropdownParent: $('#newRFIModal'),
//     tags: true,
//     multiple: true,
//     allowClear: false,
//     tokenSeparators: [',', ' '],
//     width: '100%',
//     createTag: function (params) {
//         const term = $.trim(params.term);
//         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

//         if (term === '' || !emailRegex.test(term)) {
//             return null;
//         }

//         return {
//             id: term,
//             text: term,
//             newTag: true
//         };
//     }
// });

$.validate({
    form: "#project_form",
    lang: "en",
    onSuccess: function (form) {
        let formData = $(form).serializeArray();
        let ccValue = $('#cc_to').val();
        formData = formData.filter(item => item.name !== 'cc_to');
        if (ccValue) {
            ccValue.split(',').map(e => e.trim()).filter(e => e.length)
                .forEach(email => {
                    formData.push({ name: 'cc_to[]', value: email });
                });
        }

        formData.push({ name: 'csrf_token', value: _csrf_hash });
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/save_rfi"),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (response) {
                
            }
        });
        return false;
    }
});

$('#prepared_dt').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#prepared_dt').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#prepared_dt').on('cancel.daterangepicker', function () {
    $(this).val('');
});

$('#reply_needed').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment('2023-01-01'),
    maxDate: moment().add(365, 'days'),
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#reply_needed').on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('MMM DD, YYYY'));
});

$('#reply_needed').on('cancel.daterangepicker', function () {
    $(this).val('');
});

function archiveRecordShow() {
    is_archive = is_archive === 1 ? 0 : 1;
    $('#header').text(is_archive === 1 ? 'Request Forms Archive' : 'Request Form Masterfile');
    $('#archiveLabel').text(is_archive === 1 ? 'Back to Masterfile' : 'Archive');
    rfiTable.ajax.reload();
}

function archiveRow(id) {
    Swal.fire({
        title: 'Archive this request?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("eforms/engineering_request_forms/archive_request"),
                type: "POST",
                dataType: "json",
                data: {
                    id: id,
                    archive: 1,
                    csrf_token: _csrf_hash
                },
                success: function (response) {

                    if (response.success) {
                        toastr.success(response.message, 'Success');
                    } else {
                        toastr.error(response.message, 'Error');
                    }

                    rfiTable.ajax.reload(null, false);
                },
                error: function () {
                    toastr.error('Something went wrong.', 'Error');
                }
            });

        }
    });
}

function restoreRow(id) {
    Swal.fire({
        title: 'Restore this request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, restore it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("eforms/engineering_request_forms/restore_request"),
                type: "POST",
                dataType: "json",
                data: {
                    id: id,
                    archive: 0,
                    csrf_token: _csrf_hash
                },
                success: function (response) {

                    if (response.success) {
                        toastr.success(response.message, 'Success');
                    } else {
                        toastr.error(response.message, 'Error');
                    }

                    rfiTable.ajax.reload(null, false);
                }
            });

        }
    });
}