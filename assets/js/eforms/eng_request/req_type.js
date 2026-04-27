let _employee = null;
let reqTable = null;
let is_archive = 0;
let selectedId = null;

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.employee !== "undefined" && _tempContentData.employee.length > 0){
        _employee = _tempContentData.employee;
    }
}

reqTable = $('#rfi_type').DataTable({
    serverSide: true,
    processing: true,
    searching: false,
    rowId: 'id',
    ajax: {
        url: baseUrl("eforms/engineering_request_forms/get_req_types"),
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

        { data: 'type_name', },
        { data: 'type_code', },
        { data: 'person_in_charge_name', },
        { data: "created_at", orderable: false, searchable: false, },
        { data: "created_by_name", orderable: false, searchable: false, },
        {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                    <button type="button"
                        class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit"
                        data-toggle="m-tooltip"
                        data-original-title="Edit Request"
                        data-placement="bottom"
                        data-delay='{"show":250}'
                        onclick="editType(${row.id})">
                        <i class="la la-pencil-square"></i>
                    </button>
        
                    <button type="button"
                        class="btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill ml-1"
                        data-toggle="m-tooltip"
                        data-original-title="Archive Request"
                        data-placement="bottom"
                        data-delay='{"show":250}'
                        onclick="archiveType(${row.id})">
                        <i class="la la-archive"></i>
                    </button>
                `;
            }
        }
        
    ],
}); 


$('#person_in_charge').select2({
    width: '100%',
    dropdownParent: $('#type_modal'),
    placeholder: 'Select person in charge',
    allowClear: true,
    data: _employee
});

$('#edit_person_in_charge').select2({
    width: '100%',
    dropdownParent: $('#edit_type_modal'),
    placeholder: 'Select person in charge',
    allowClear: true,
    data: _employee
});

$.validate({
    form: "#new_type",
    lang: "en",
    onSuccess: function (form) {
        let formData = $(form).serializeArray();
        formData.push({ name: 'csrf_token', value: _csrf_hash });
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/save_req_type"),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (response) {
                if(response.success){
                    toastr.success(response.message,'Success',5000);
                }else{
                    toastr.error(response.message,'Error',5000);
                }
                $('#new_type')[0].reset();
                $('#person_in_charge').val(null).trigger('change');
                $('#type_modal').modal('hide');
                reqTable.ajax.reload();
            }
        });
        return false;
    }
});

$.validate({
    form: "#edit_type",
    lang: "en",
    onSuccess: function (form) {
        let formData = $(form).serializeArray();
        formData.push({ name: 'id', value: selectedId});
        formData.push({ name: 'csrf_token', value: _csrf_hash });
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/update_req_type"),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (response) {
                if(response.success){
                    toastr.success(response.message,'Success',5000);
                }else{
                    toastr.error(response.message,'Error',5000);
                }
                $('#edit_type')[0].reset();
                $('#edit_person_in_charge').val(null).trigger('change');
                $('#edit_type_modal').modal('hide');
                reqTable.ajax.reload();
            }
        });
        return false;
    }
});

function editType(id){
    selectedId = id;
    let data = reqTable.row('#'+id).data();
    $('#edit_type_id').val(data.id);
    $('#edit_request_type').val(data.type_name);
    $('#edit_type_code').val(data.type_code);
    $('#edit_person_in_charge').val(data.person_in_charge).trigger('change');
    $('#edit_type_modal').modal('show')
}

function archiveType(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This record will be archived.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: siteUrl("eforms/engineering_request_forms/archive_req_type"),
                type: 'POST',
                global: false,
                data: { id: id, csrf_token: _csrf_hash },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message,'Success', 5000);
                    }else{
                        toastr.error(response.message,'Error', 5000);
                    }
                    reqTable.ajax.reload();
                },
            });
        }
    });
}
