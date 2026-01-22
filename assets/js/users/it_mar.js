let _employee = [];
let selected = {}; 
let ITMar = null;
let selectedEmpId = null;
let is_archive = 0;
let selectedId = null;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}
let allEmployees = _employee;

$(document).on('click', '.btnArchive', function (e) {
    e.preventDefault();
    is_archive = is_archive == 0 ? 1 : 0;
    const text = is_archive == 1 ? 'Masterfile' : 'Archive';
    $(this).find('.m-nav__link-text').text(text);
    const headerText = is_archive == 1 ? 'IT Mobile Application Archive' : 'IT Mobile Application Masterfile';
    $('#header').text(headerText);
    if (is_archive == 1) {
        $('#addNew').hide();
    } else {
        $('#addNew').show();
    }
    ITMar.ajax.reload();
});

$(document).on('show.bs.modal', '#newITMARModal', function () {
    const selectedApp = $('input[name="app_name_select"]:checked').val();
    if (selectedApp) {
        $('input[name="app_name"][value="' + selectedApp + '"]').prop('checked', true).trigger('change');
    }
});

$(document).ready(function () {
    toggleAppFields();
    $('input[name="app_name"]').on('change', function () {
        toggleAppFields();
    });

    const selectedApp = $("input[name='app_name']:checked").val();
    loadEmployeesByApp(selectedApp);
    ITMar = $('#itmarTable').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
        rowId: 'id',
        ajax: {
            url: baseUrl("users/get_itmar_list"),
            type: "post",
            dataType: "json",
            global: false,
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.app_name = $("input[name='app_name_select']:checked").val();
                d.search['value'] = $("#generalSearch").val();
                d.date_range = selected;
                d.is_archive = is_archive;
                return d;
            },
        },
        columns: [
            { data: "id", visible: false, searchable: false },
            { data: "emp_name",
                render: function (data, type, row, meta) {
                    return `
                        <p class='mb-0'>${data}</p>
                        <p><small class='m--font-bolder'><strong>${row.position_name || 'No Position'}</strong> </br>${row.department_name || 'No Department'}</small></p>
                    `;
                }
             },
            { data: "purpose", sortable: false },
            { data: "app_name", sortable: false },
            {
                data: "created_at",
                render: function (data) {
                    if (!data) return '';
                    return moment(data, 'YYYY-MM-DD HH:mm:ss')
                        .format('MMMM DD, YYYY h:mm A');
                }
            },
            { data: "created_name" },
            {
                data: null,
                sortable: false,
                className: "text-center",
                render: function (data, type, row, meta) {
                    if (is_archive) {
                        return '---'; 
                    }
            
                    return `
                        <button type="button"
                            class="btn btn-default m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill btnEdit"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="Edit"
                            data-delay='{"show":300}'
                            onclick="editRow(${row.id})">
                            <i class="la la-edit"></i>
                        </button>
            
                        <button type="button"
                            class="btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnDelete ml-1"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="Archive"
                            data-delay='{"show":300}'
                            onclick="archiveRow(${row.id})">
                            <i class="la la-archive"></i>
                        </button>
                    `;
                }
            },
        ],
    });
});



$("#employee").on("select2:select", function (e) {
    const data = e.params.data;
    selectedEmpId = data.id || null;
    $("#position").val(data.position || "");
    $("#department").val(data.department || "");
    $("#ass_loc").val(data.site_locations ? data.site_locations.split("|").join(", ") : "");
    $("#telegram_id").val(data.telegram_id ? data.telegram_id.split("|").join(", ") : "");
}).on("change", function () {
    if (!this.value) {
        selectedEmpId = null;
        $("#position, #department, #ass_loc").val("");
    }
});

$.validate({
    form : '#new_itmar',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        $.ajax({
            url: siteUrl("users/save_itmar"),
            type: "POST",
            dataType: "json",
            data: {
                csrf_token: _csrf_hash, 
                emp_id: selectedEmpId,
                purpose: $('#purpose').val(),
                app_name: $('input[name="app_name"]:checked').val(),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    allEmployees = response.employees;
                    $('#new_itmar')[0].reset();
                    $('#employee').val(null).trigger('change');
                    $('#position').val('');
                    $('#department').val('');
                    $('#ass_loc').val('');
                    $('#newITMARModal').modal('hide');
                    ITMar.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
});

$.validate({
    form : '#edit_itmar',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        $.ajax({
            url: siteUrl("users/update_itmar"),
            type: "POST",
            dataType: "json",
            data: {
                csrf_token: _csrf_hash, 
                id: selectedId,
                purpose: $('#edit_purpose').val(),
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                }
                else{
                    toastr.error(response.message);
                }
                $('#editITMARModal').modal('hide');
                ITMar.ajax.reload();
            }
        });
    }
});

$("input[name='app_name']").on("change", function () {
    loadEmployeesByApp(this.value);
});

function hasApp(emp, app) {
    if (!emp.app_name) return false;
    return emp.app_name.split("|").map(a => a.trim()).includes(app);
}

function loadEmployeesByApp(selectedApp) {
    const $employee = $("#employee");
    const selectedId = $employee.val();
    const selectedEmployee = allEmployees.find(emp => emp.id == selectedId);
    const filteredEmployees = allEmployees.filter(emp => {
        return !hasApp(emp, selectedApp);
    });

    $employee.empty().select2({
        width: "100%",
        dropdownParent: $("#newITMARModal"),
        placeholder: "Select an option",
        data: filteredEmployees
    });

    if (selectedEmployee && !hasApp(selectedEmployee, selectedApp)) {
        $employee.val(selectedEmployee.id).trigger("change");
    } else {
        $employee.val(null).trigger("change");
    }
}

$("input[name='app_name_select']").on("change", function () {
    ITMar.ajax.reload();
});

function archiveRow(id) {
    let rowData = ITMar.row('#'+id).data();
    Swal.fire({
        title:'Archive record?',
        text: 'This record will be moved to archive.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, archive it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("users/archive_itmar"),
                type: 'POST',
                data: {
                    id: id,
                    csrf_token: _csrf_hash,
                    itmar_id:rowData.id,
                    emp_id:rowData.emp_id,
                    purpose:rowData.purpose,
                    app_name:rowData.app_name,
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        allEmployees = response.employees;
                        loadEmployeesByApp(rowData.app_name);
                        ITMar.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
            });
        }
    });
}

$('#generalSearch').donetyping(function () {
    ITMar.ajax.reload();
});

$('#date_range').daterangepicker({
    showDropdowns: true,
    autoUpdateInput: false,
    minDate: moment().subtract(3, 'years').startOf('day'),
    maxDate: moment(),  
    locale: {
        format: 'MMM DD, YYYY',
        cancelLabel: 'Clear'
    }
});

$('#date_range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(
        picker.startDate.format('MMM DD, YYYY') + ' - ' + picker.endDate.format('MMM DD, YYYY')
    );
    selected = {
        start: picker.startDate.format('YYYY-MM-DD'),
        end: picker.endDate.format('YYYY-MM-DD')
    };
    ITMar.ajax.reload();
});

$('#date_range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');  
    selected = {}; 
    ITMar.ajax.reload();
});

$("#edit_employee").select2({
    width: "100%",
    dropdownParent: $("#editITMARModal"),
    placeholder: "Select an option",
    data: allEmployees
});

$("#edit_employee").on("change", function () {
    const data = $("#edit_employee").select2("data")[0];

    if (!data) {
        selectedEmpId = null;
        $("#edit_position, #edit_department, #edit_ass_loc").val("");
        return;
    }

    selectedEmpId = data.id || null;
    $("#edit_position").val(data.position || "");
    $("#edit_department").val(data.department || "");
    $("#edit_ass_loc").val(
        data.site_locations ? data.site_locations.split("|").join(", ") : ""
    );
});

function editRow(id){
    let rowData = ITMar.row('#'+id).data();
    $('#editITMARModal').modal('show');
    $('#edit_purpose').val(rowData.purpose);
    $("#edit_employee").val(rowData.emp_id).trigger("change");
    $('input[name="edit_app_name"][value="' + rowData.app_name + '"]').prop('checked', true);
    $("#edit_ass_loc").val(rowData.location_name ? rowData.location_name.split("|").join(", ") : "");
    $("#edit_telegram_id").val(rowData.telegram_id ? rowData.telegram_id.split("|").join(", ") : "");
    toggleEditAppFields();
    $("#edit_employee").prop("disabled", true);
    $('input[name="edit_app_name"]').prop('disabled', true);
    selectedId = id;
}

$('input[name="edit_app_name"]').on('change', function () {
    toggleEditAppFields();
});

function toggleAppFields() {
    const selected = $('input[name="app_name"]:checked').val();
    if (selected === 'GCCTIME') {
        $('#ass_loc_group').show();
        $('#telegram_id_group').hide();
    } else if (selected === 'TGCloudBAS') {
        $('#ass_loc_group').hide();
        $('#telegram_id_group').show();
    }
}

function toggleEditAppFields() {
    const selected = $('input[name="edit_app_name"]:checked').val();

    if (selected === 'GCCTIME') {
        $('#edit_ass_loc_group').show();
        $('#edit_telegram_id_group').hide();
    } else if (selected === 'TGCloudBAS') {
        $('#edit_ass_loc_group').hide();
        $('#edit_telegram_id_group').show();
    }
}