console.log(_tempContentData);
let _employee = [];
let ITMar = null;
let selectedEmpId = null;
if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}
const allEmployees = _employee;
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
            { data: "purpose" },
            { data: "app_name" },
            { data: "created_at" },
            { data: "created_name" },
            {
                data: null,
                sortable: false,
                render: function (data, type, row, meta) {
                        // <button type="button"
                        //     class="btn btn-default m-btn m-btn--hover-warning m-btn--icon m-btn--icon-only m-btn--pill btnEdit"
                        //     data-id="${row.id}"
                        //     data-toggle="m-tooltip"
                        //     data-placement="bottom"
                        //     data-skin="dark"
                        //     data-original-title="Edit"
                        //     data-delay='{"show":300}'>
                        //     <i class="la la-pencil"></i>
                        // </button>
            
                    return `
                        <button type="button"
                            class="btn btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnArchive ml-1"
                            data-id="${row.id}"
                            data-toggle="m-tooltip"
                            data-placement="bottom"
                            data-skin="dark"
                            data-original-title="Archive"
                            data-delay='{"show":300}'>
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
                if(response.success){
                    $('#newITMARModal').modal('hide');
                    ITMar.ajax.reload();
                }else{
                    alert(response.message);
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