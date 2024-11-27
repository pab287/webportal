var search_val = "";
var query_builder = "";
var tblSubdivision = $("#table-subdivision").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_subdivision/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d){
           d.csrf_token = _csrf_hash,
           d.search['value'] = search_val,
           d.query_builder = query_builder
       }
   },
   searching: true,
   columns: [
       { data: "name", width: "8%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
       { data: "meterno", width: "5%"},
       { data: "address", width: "10%"},
       { data: "description", width: "10%"},
       { data: "created_by", className: "text-center", width: "5%"},
       { data: "date_added", className: "text-right", width: "5%"},
       { data: null, width: "4%", className: "text-center"},
   ],
   columnDefs: [
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( data, type, row, meta ) { return itemDatatableActions(row); 
        },
    }
   ],
   select: {
    style:    'os',
    selector: 'td:first-child'
   },
   buttons: [
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

function itemDatatableActions(row){
	if(row){
        var tempHtml = "";
        if(row.status == 1){
            var tempHtml = "---";
            var tempActions = [];
            var currentActions = ["edit", "delete", "replace_meter"];
            $.each(currentActions, function(index, value){
                tempActions.push(value);
            });
    
            tempHtml = `<div class="dropdown">
                    <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
                        <i class="la la-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">`;
                $.each(tempActions, function(ii, vv){
                    switch(vv){
                        case "edit":
                        tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#m_edit_subdivision' href="javascript:void(0);" id='edit_subdivision' data-id='`+row.id+`'><i class="la la-edit"></i> Edit</a>`;
                        break;
                        case "delete":
                        tempHtml += `<a class="dropdown-item " style="color: #FF8383;" data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.name + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                        break;
                        case "replace_meter":
                        tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='showMeterReplaceModal(`+ row.id +`,\"`+ row.meterno +`\",`+`\"` + row.name + `\")'><i class="la la-tachometer"></i> Meter Replacement</a>`;
                        break;
                    }
                });
                tempHtml += `</div></div>`;
        }
        return tempHtml;

	}else{ return false; }
}

$('#table-subdivision').on("click","#edit_subdivision",function(){
    var id = $(this).attr("data-id");
    $.ajax({
        url: baseUrl("eforms/billing/get_subdivision_details"),
        type: 'post',
        data: {csrf_token: _csrf_hash, id: id},
        success: function(response){ 
            
            $("#m_edit_subdivision input[name=id]").val(response.data.id);
            $("#m_edit_subdivision .name").val(response.data.name);
            $("#m_edit_subdivision .meterno").val(response.data.meterno);
            $("#m_edit_subdivision .address").val(response.data.address);
            $("#m_edit_subdivision .description").val(response.data.description);
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
});

function modalArchive(id,name){
    const temp = `<p>Are you sure you wan't to archive <strong class='m--font-boldest'>${name}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=subd_name]").val(name);
}

function archiveSubdivision(){
    var id = document.getElementById('subd_id').value;
    var name = document.getElementById('subd_name').value;
    $.ajax({
        url: baseUrl("eforms/billing/archive_subdivision"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, name:name },
        success: function (data) {
            if(data.status){
                $('#m_archived').modal('hide');
                tblSubdivision.ajax.reload();
            }
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tblSubdivision.ajax.reload();
});

$("#ExportExcel").on("click", function() {
    tblSubdivision.button( '.buttons-excel' ).trigger();
    saveExportLogs('Payments - Export Excel');
});

$("#ExportCSV").on("click", function() {
    tblSubdivision.button( '.buttons-csv' ).trigger();
    saveExportLogs('Payments - Export CSV');
});

$("#ExportPDF").on("click", function() {
    tblSubdivision.button( '.buttons-pdf' ).trigger();
    saveExportLogs('Payments - Export PDF');
});

function saveExportLogs(export_){
    $.ajax({
        url: baseUrl("eforms/billing/save_export_logs"),
        type: 'post',
        data: { csrf_token: _csrf_hash, export_: export_ },
        success: function (data) {
            
        }
    });
}

$(".btnNew").on("click",function(){
    $('#m_subdivision').modal('show');
});

$.validate({
    form : '#formSaveSubdivision',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#formSaveSubdivision').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                toastr.success(data.msg, "Notification");
                tblSubdivision.ajax.reload();
                $('#m_subdivision').trigger("reset");
                $('#m_subdivision').modal('hide');
              }else{
                toastr.warning(data.msg, "Notification");
              }
            },
            error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection error");
            }
      });
      return false;
    },
});

$.validate({
    form : '#formUpdateSubdivision',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#formUpdateSubdivision').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                toastr.success(data.msg, "Notification");
                tblSubdivision.ajax.reload();
                $('#m_edit_subdivision').modal('hide');
              }else{
                toastr.warning(data.msg, "Notification");
              }
            },
            error: function (request, status, error) {
                toastr.error("Please check your internet connection.", "Connection error");
            }
      });
      return false;
    },
});

function showMeterReplaceModal(id,meter,name){
    $("#frm_meter_r #subdivision_name").val(name);
    $("#frm_meter_r input[name=old_meterno]").val(meter);
    $("#frm_meter_r input[name=account_id]").val(id);
    $('#frm_meter_r input[name=new_meterno]').attr('placeholder', meter);
    $("#m_meter_r").modal("show");
}
  
$.validate({
form : '#frm_meter_r',
lang: 'en',
onSuccess : function(form) {
    $.ajax({
        url : $(form).attr("action"),
        type: "POST",
        data: $('#frm_meter_r').serialize(),
        dataType: "JSON",
        success: function(data){
            if(data.status == true){
            $("#formEditAccount input[name=meterno]").val(data.new_meterno);
            $("#frm_meter_r input[name=old_meterno]").val(data.new_meterno);
            $('#frm_meter_r').trigger("reset");
            $("#m_meter_r").modal("hide");
            tblSubdivision.ajax.reload();
            toastr.success(data.msg, "Notification");
            }else{
            toastr.warning(data.msg, "Notification");
            }
        },
        error: function (request, status, error) {
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
    return false;
},
});

//document.addEventListener('contextmenu', event => event.preventDefault());