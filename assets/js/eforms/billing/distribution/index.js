Inputmask.extendAliases({
    decimal: {
              prefix: "",
              groupSeparator: ".",
              alias: "numeric",
              placeholder: "0",
              autoGroup: !0,
              digits: 2,
              digitsOptional: !1,
              clearMaskOnLostFocus: !1
    }
});

$("#m_water #distribute").inputmask({ alias : "decimal", removeMaskOnSubmit: true });
$("#m_edit_water #distribute").inputmask({ alias : "decimal", removeMaskOnSubmit: true });

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tbl_destribution.ajax.reload();
});

var search_val = "";
var query_builder = "";
var tbl_destribution = $("#table-water_supply").DataTable({
   dom: '<"toolbar">rtlip',
   serverSide: true,
   processing: true,
   aaSorting: [],
   ajax: {
        url: baseUrl("eforms/billing/get_distribution/"),
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
        { data: "subdivision_name", width: "5%", render: function (data) {
                return "<strong style='color: #525252;'>"+data+"</strong>";
            }
        },
        { data: "meterno", width: "5%"},
        { data: "reading_date", width: "5%"},
        { data: "name", width: "5%"},
        { data: "distribute", width: "5%", className: "text-right", render: function (data) {
                return "<strong style='color: #525252;'>" + numberWithCommas(data) + "</strong>";
            }
        },
        { data: "created_date", width: "5%", className: "text-center"},
        { data: null, width: "2%", className: "text-center"},
   ],
   order: [[2, 'desc']],
   columnDefs: [
        {
            targets: 1,
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function ( 
                data, type, row, meta ) { return itemDatatableActions(row); 
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

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

hideAddButton();
function hideAddButton(){
    $.ajax({
        url: baseUrl("eforms/billing/get_subdivision_select_distribution"),
        type: 'post',
        data: {csrf_token: _csrf_hash},
        success: function(response){ 
            if(response.results.length > 0){
                document.getElementById("btnNew").style.display='';
            } else {
                document.getElementById("btnNew").style.display='none';
            }
        },
        error: function(data){
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

function itemDatatableActions(row){
	if(row){
        var tempHtml = "";
        var tempActions = [];
        var currentActions = ["edit", "delete"];
        $.each(currentActions, function(index, value){
            tempActions.push(value);
        });

        if(!row.isArchiveHide){
            tempHtml = `<div class="dropdown">
            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
                <i class="la la-ellipsis-h"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">`;
            
            $.each(tempActions, function(ii, vv){
                switch(vv){
                    case "edit":
                        tempHtml += `<a class="dropdown-item " data-toggle='modal' data-target='#m_edit_water' href="javascript:void(0);" id='edit_water' data-id='`+row.id+`'><i class="la la-edit"></i> Edit</a>`;
                    break;
                    case "delete":
                        tempHtml += `<a class="dropdown-item" style="color: #FF8383;" data-toggle='modal' data-target='#delete_modal' href="javascript:void(0);" onclick='modalArchive(`+ row.id +`,`+`\"` + row.subdivision_name + `\",`+`\"` + row.reading_date + `\")'><i class="la la-trash" style="color: #FF8383;"></i> Archive</a>`;
                    break;
                }
            });
            tempHtml += `</div></div>`;
        }
        return tempHtml;
	}else{ return false; }
}

function modalArchive(id,subdivision_name,reading_date){
    const temp = `<p>Are you sure you wan't to archive distribution of <strong class='m--font-boldest'>${subdivision_name}</strong> date of <strong class='m--font-boldest'>${reading_date}</strong>?</p>`;
    $('#archive_text').empty().html(temp);
    $('#m_archived').modal('show');
    $("#m_archived #distribution_id").val(id);
    $("#m_archived #archive_subd_name").val(subdivision_name);
    $("#m_archived #archive_distribute_date").val(reading_date);
}

function archiveSubdivision(){
    var id = document.getElementById('distribution_id').value;
    var archive_subd_name = document.getElementById('archive_subd_name').value;
    var archive_distribute_date = document.getElementById('archive_distribute_date').value;
    $.ajax({
        url: baseUrl("eforms/billing/archive_destribution"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: id, archive_subd_name: archive_subd_name, archive_distribute_date: archive_distribute_date },
        success: function (data) {
            if(data.status){
                toastr.success(data.msg, "Notification");
                $('#m_archived').modal('hide');
                tbl_destribution.ajax.reload();
                hideAddButton();
            } else {
                toastr.warning(data.msg, "Notification");
            }
        },
        error: function(data){
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$(".btnNew").on("click",function(){
    $('#m_water').modal('show');
    $('#m_datepicker_reading_date').datepicker("setDate", getCurrentDate());
});

$("#select_subd").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("eforms/billing/get_subdivision_select_distribution"),
        global: false,
        processResults: function (data) {
            return data;
        }
    }
});

$('#select_subd').on('select2:select', function (e) {
    $("#m_water input[name=subd_name]").val(e.params.data.text);
    $("#m_water input[name=meterno_raw]").val(e.params.data.meterno_raw);
});

$.validate({
    form : '#formWater',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#formWater').serialize(),
            dataType: "JSON",
            success: function(data){
                if(data.status == true){
                    toastr.success(data.msg, "Notification");
                    tbl_destribution.ajax.reload();
                    $('#formWater').trigger("reset");
                    $('#m_water #select_subd').val('').trigger("change");
                    $("#m_water").modal("hide");
                    hideAddButton();
                }else{
                    toastr.warning(data.msg, "Notification");
                }
            },
            error: function(data){
                toastr.error("Please check your internet connection.", "Connection error");
            }
      });
      return false;
    },
});

$("#table-water_supply").on("click","#edit_water",function(e){
    var id = $(this).attr("data-id");
    $.ajax({
        url: baseUrl("eforms/billing/get_distribution_details"),
        type: 'post',
        data: {csrf_token: _csrf_hash, id: id},
        success: function(response){
            $('#m_edit_water #edit_select_subd').val(response.subdivision);
            $('#m_edit_water #distribute').val(response.distribute);
            $('#m_datepicker_reading_date_edit').datepicker("setDate", response.reading_date);
            $("#m_edit_water input[name=id]").val(id);
            $("#m_edit_water input[name=old_distribute]").val(response.distribute);
            $("#m_edit_water input[name=old_reading_date]").val(response.reading_date);
            $("#m_edit_water input[name=subdivision_id]").val(response.subdivision_id);
            $("#m_edit_water input[name=meterno]").val(response.meterno);
        },
        error: function(data){
          toastr.error("Please check your internet connection.", "Connection error");
        }
    });
});

$.validate({
    form : '#formUpdateDistribution',
    lang: 'en',
    onSuccess : function(form) {
        $.ajax({
            url : $(form).attr("action"),
            type: "POST",
            data: $('#formUpdateDistribution').serialize(),
            dataType: "JSON",
            success: function(data){
              if(data.status == true){
                toastr.success(data.msg, "Notification");
                tbl_destribution.ajax.reload();
                $('#m_edit_water').modal('hide');
              }else{
                toastr.warning(data.msg, "Notification");
              }
          },
          error: function(data){
            toastr.error("Please check your internet connection.", "Connection error");
          }
      });
      return false;
    },
});

$('#m_datepicker_reading_date').datepicker({
    format: 'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate(),
});

$('#m_datepicker_reading_date_edit').datepicker({
    format: 'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom left",
    templates: {
        leftArrow: '<i class="la la-angle-left"></i>',
        rightArrow: '<i class="la la-angle-right"></i>'
    },
    endDate: getCurrentDate(),
});

function getCurrentDate(){
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    return yyyy+'-'+mm+'-'+dd;
}