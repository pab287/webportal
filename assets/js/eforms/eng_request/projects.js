let projectTable = null;
let is_archive = 0;
let selectedId = null;

projectTable = $('#project_table').DataTable({
    serverSide: true,
    processing: true,
    searching: false,
    rowId: 'id',
    order: [[ 0, "desc" ]],
    ajax: {
        url: baseUrl("eforms/engineering_request_forms/get_projects"),
        type: "POST",
        dataType: "json",
        global: false,
        data: function ( d ) {
            d.csrf_token = _csrf_hash;
            d.is_archive = is_archive;
            d.search['value'] = $("#generalSearch").val();
        }
    },
    columns: [
        { data: "id", visible: false, searchable: false, name: "id" },
        { data: "project_name" },
        { data: "project_location" },
        { data: "created_at",
            render: function ( data, type, row ) {
                return moment(data, 'YYYY-MM-DD HH:mm:ss').format('MMM DD, YYYY hh:mm A');
            }
        },
        { data: "created_by_name" },
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
        // { data: "date_created" }
    ],



});

$.validate({
    form : '#project_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/save_project"),
            type: "POST",
            dataType: "json",
            data: $('#project_form').serialize() + '&csrf_token=' + _csrf_hash,
            success: function (response) {
                if(response.success){
                    toastr.success(response.message, 'Success');
                }else{
                    toastr.error(response.message, 'Error');
                }
                $('#newProjectModal').modal('hide');
                projectTable.ajax.reload();
            }
        });
        return false;
    }
});


$.validate({
    form : '#edit_project_form',
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess : function(form) {
        $.ajax({
            url: siteUrl("eforms/engineering_request_forms/update_project"),
            type: "POST",
            dataType: "json",
            data: $('#edit_project_form').serialize() + '&csrf_token=' + _csrf_hash + '&id=' + selectedId,
            success: function (response) {
                if(response.success){
                    toastr.success(response.message, 'Success');
                }else{
                    toastr.error(response.message, 'Error');
                }
                projectTable.ajax.reload(null, false);
                $('#editProjectModal').modal('hide');
                $('#edit_project_form')[0].reset();
            }
        });
        return false;
    }
});




function editRow(id){
    let rowData = projectTable.row('#'+id).data();
    $('#edit_project_location').val(rowData.project_location);
    $('#edit_project_name').val(rowData.project_name);
    $('#editProjectModal').modal('show');
    selectedId = id;
}

$('#generalSearch').donetyping(function () {
    projectTable.ajax.reload();
});
