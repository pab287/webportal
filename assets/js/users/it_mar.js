let _employee = [];
let selected = {}; 
let ITMar = null;
let selectedEmpId = null;
let is_archive = 0;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}
const allEmployees = _employee;

$(document).on('click', '.btnArchive', function (e) {
    e.preventDefault();
    is_archive = is_archive == 0 ? 1 : 0;
    const text = is_archive == 1 ? 'Masterfile' : 'Archive';
    $(this).find('.m-nav__link-text').text(text);
    ITMar.ajax.reload();
});

$(document).on('show.bs.modal', '#newITMARModal', function () {
    const selectedApp = $('input[name="app_name_select"]:checked').val();
    if (selectedApp) {
        $('input[name="app_name"][value="' + selectedApp + '"]')
            .prop('checked', true)
            .trigger('change');
    }
});

$(document).ready(function () {
    const selectedApp = $("input[name='app_name']:checked").val();
    loadEmployeesByApp(selectedApp);
    ITMar = $('#itmarTable').DataTable({
        serverSide: true,
        processing: true,
        searching: false,
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
            { data: "app_name" },
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
                render: function (data, type, row, meta) {
                    const isRestore = row.is_archive == 1;
                    return `
                        <button type="button"
                            class="btn btn-default m-btn m-btn--hover-${isRestore ? 'success' : 'danger'} m-btn--icon m-btn--icon-only m-btn--pill btnDelete ml-1"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="${isRestore ? 'Restore' : 'Archive'}"
                            onclick="archiveRow(${row.id})"
                            data-delay='{"show":300}'>
                            <i class="la ${isRestore ? 'la-reply' : 'la-archive'}"></i>
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
}).on("change", function () {
    if (!this.value) {
        selectedEmpId = null;
        $("#position, #department, #ass_loc").val("");
    }
});

$.validate({
    form : '#new_itmar',
    lang: 'en',
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
                    $('#newITMARModal').modal('hide');
                    ITMar.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
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
    const willArchive = is_archive == 0; 
    Swal.fire({
        title: willArchive ? 'Archive record?' : 'Restore record?',
        text: willArchive
            ? 'This record will be moved to archive.'
            : 'This record will be restored to masterfile.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: willArchive ? 'Yes, archive it' : 'Yes, restore it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: willArchive ? '#d33' : '#28a745',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("users/archive_itmar"),
                type: 'POST',
                data: {
                    id: id,
                    is_archive: willArchive ? 1 : 0,
                    csrf_token: _csrf_hash
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(
                            willArchive
                                ? 'Record archived successfully.'
                                : 'Record restored successfully.'
                        );
                        ITMar.ajax.reload();
                    } else {
                        toastr.error(response.message || 'Action failed.');
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