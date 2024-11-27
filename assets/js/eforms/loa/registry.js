$("#selectall").click(function () {
    $('#table-registry tbody input[type="checkbox"]').prop('checked', this.checked);
});

$("#table-registry")
    .on("click", "tbody input[type='checkbox']", function () {
        const allCheckboxes = $("#table-registry tbody input[type='checkbox']").length;
        const checkedCheckboxes = $("#table-registry tbody input[type='checkbox']:checked").length;
        const checked = allCheckboxes <= checkedCheckboxes;
        console.log(checked);
        $('#selectall').prop('checked', checked);
    });

var search_val = "";
var query_builder = "";
var tblLoa = $("#table-registry").DataTable({
    dom: '<"toolbar">rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("eforms/Loa/get_datatable_request_registry/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash,
                d.search['value'] = search_val,
                d.query_builder = query_builder
        }
    },
    aaSorting: [],
    searching: true,
    columns: [ 
            {
                data: "id", orderable: false, render: function (data, type, row, meta) {
                    return renderStatusHtml(row)
                }
            },
           { "data": "reason" },
           { "data": "created_by" },
           { "data": "updated_by" },
           { "data": "created_at" },
           { "data": "updated_at" },
           { "data": "" },
    ],
    columnDefs: [
        {
            targets: [0], orderable: false,
            width: "2%",
            checkboxes: {
                selectRow: true
            }
        },
        { targets: [5], width: "15%" },
        { targets: [4], width: "15%" },
        { targets: [1], className: "statusAlign" },
        { targets: [3], width: "10%" },
        { targets: [2], width: "10%" },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,

            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id);
            },
        },


    ], buttons: [
        {
            extend: 'csv',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'excel',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }, {
            extend: 'pdf',
            exportOptions: {
                columns: "thead th:not(.notExport)"
            }
        }
    ]
});

$("#ExportExcel").on("click", function () {
    tblLoa.button('.buttons-excel').trigger();
});

$("#ExportCSV").on("click", function () {
    tblLoa.button('.buttons-csv').trigger();
});

$("#ExportPDF").on("click", function () {
    tblLoa.button('.buttons-pdf').trigger();
});
 

function renderStatusHtml(row) {
    switch (row.status) {
        case "1":
            return '<div class="m-badge text-white m-badge--success m-badge--wide" role="alert"><strong>Active</strong></div>';
            break;
        default:
            return '<div class="m-badge m-badge--default m-badge--wide" role="alert"><strong>Inactive</strong></div>';
            break;
    }
}

  
 

function itemDatatableActions($id) {
    if ($id) {
        var _actionButton = "";
        if ($.inArray("edit", _currentActions) !== -1) {
            _actionButton += "<a   href='javascript:'  value='"+$id+"' id='edit' class='btn btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditItem'  ><i class='la la-pencil-square'></i></a>";
        }
        return _actionButton;
    } else {
        return false;
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    tblLoa.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblLoa.ajax.reload();
});


$(document).ready(function(){
    $("#frm-invalid-reason-add").submit(function (event) {
            event.preventDefault();
            var hash = {csrf_token:_csrf_hash};
            var formData = $(this).serialize() + '&' + $.param(hash);    
            $.ajax({
                type: "POST",
                url: baseUrl("eforms/Loa/insert_registry/"),
                global: false,
                data: formData,
                dataType: "json",
                success:function(data){
                    if (data.reps !== "error") {
                        toastr.success("", "Successfully saved!", 3000);
                        $("#modal_invalid_reason").modal("hide");
                        $('#frm-invalid-reason-add')[0].reset();
                        tblLoa.ajax.reload();
                        console.log(data);
                    }else{
                        toastr.error("", "Unable to save!", 3000);
                    }
                }
            })
    });
});
$(document).on("click","#edit",function(e){
            e.preventDefault();
            $("#reason").val("");
            $("#status").val("");
            $("#modal_invalid_reason_edit").modal("show");
            var edit_id = $(this).attr("value");
            $("#Uid").val(edit_id);

            $.ajax({
                type: "POST",
                url: baseUrl("eforms/Loa/edit_registry/"),
                global: false,
                data: {id:edit_id, csrf_token:_csrf_hash},
                dataType: "json",
                success:function(data){
                    if(data.reps!="error"){
                        $("#reason").val(data.post.reason);
                        $("#status").val(data.post.status);
                    }else{
                        toastr.error("", "Unable to fetch data!", 3000);
                    }
                }
            })
})
$("#frm-invalid-reason-update").submit(function (event) {
            event.preventDefault();
            var hash = {csrf_token:_csrf_hash};
            var formData = $(this).serialize() + '&' + $.param(hash);               
            $.ajax({
                type: "POST",
                url: baseUrl("eforms/Loa/update_registry/"),
                global: false,
                data: formData,
                dataType: "json",
                success:function(data){
                    if(data.reps!="error"){
                        $("#modal_invalid_reason_edit").modal("hide");
                        $('#frm-invalid-reason-update')[0].reset();
                        toastr.success("", "Successfully updated!", 3000);
                        tblLoa.ajax.reload();
                        console.log(data);
                    }else{
                        toastr.error("", "Unable to update data!", 3000);
                    }
                    
                }
            })
});