var search_val = "";
var temp_images = [];
var clone_element = null;
var tblResume = $("#table-resume")
    .DataTable({
        dom: 'rtlip',
        serverSide: true,
        processing: true,
        order: [[ 0, "desc" ]],
        ajax: {
            url: baseUrl("crs/get_archive_collection/"),
            dom: "ft",
            type: "post",
            global: false,
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = search_val;
            }
        },
        searching: true,
        columns: [
            {
                data: "id",
                width: "1%",
                "visible": false,
            },
            {
                data: "name",
            },
            {
                data: "school",
                render: function (data) {
                    
                    return formatTag(data);
                }
            },
            {
                data: "course",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "position",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "status",
            },
            {
                data: "remarks",
            },
            {
                data: "tag1",
                render: function (data) {
                    return formatTag(data);
                }
            },
            {
                data: "recruitment",
            },
            {
                data: "applied_dt",
            },
            {
                data: null,
                width: "4%",
                className: "text-center",
                orderable: false,
                render: function (data, type, row, meta) {
                    return itemDatatableActions(row.id, row.status);
                },
            },
        ],buttons: [
          { 
              extend: 'csv',
              title: 'CRS - ARCHIVED RESUME REPORT',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
            },    
            action: function (e, dt, button, config) {
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                logexport(config.title);

            }
          }, { 
              extend: 'excelHtml5',
              title: 'CRS - ARCHIVED RESUME REPORT',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
            },
            action: function (e, dt, button, config) {
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                logexport(config.title);
            }
          }, { 
              extend: 'pdfHtml5',
              title: 'CRS - ARCHIVED RESUME REPORT',
              orientation: 'landscape',
              pageSize: 'LEGAL',
              titleAttr: 'PDF',
              exportOptions: {
                  columns: [1,2,3,4,5,6,7,8,9]
              },
              action: function (e, dt, button, config) {
                $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                logexport(config.title);
            }
          }
      ],
    });

    $("#ExportExcel").on("click", function(e) {
      e.preventDefault();
      tblResume.button( '.buttons-excel' ).trigger();
  });
  
  $("#ExportCSV").on("click", function(e) {
      e.preventDefault();
      tblResume.button( '.buttons-csv' ).trigger();
  });
  
  $("#ExportPDF").on("click", function(e) {
      e.preventDefault();
      tblResume.button( '.buttons-pdf' ).trigger();
  });

function formatTag(data) {
    if (data.charAt(0) === ",") {
        return data.substr(1);
    } else {
        return data;
    }
}

function itemDatatableActions($id, $status) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<div class='dropdown'>";
        _actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
        _actionButton += "<i class='fa fa-ellipsis-v'></i>";
        _actionButton += "</a>";
        _actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
        if ($status == "doneinterview") {
            _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='hire_resume(" + $id + ")'><i class='la la-thumbs-o-up'></i>Hire</a>";
            _actionButton += "<div class='dropdown-divider'></div>";
        }
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='view_resume(" + $id + ")'><i class='la la-eye'></i>View</a>";
        _actionButton += " </div>";
        _actionButton += "</div>";
        
        return _actionButton;
    } else {
        return "";
    }
}

//custom global search init
$('#generalSearch').donetyping(function (callback) {
    search_val = $(this).val();
    if(search_val.length >= 3){
        $.ajax({
            url: baseUrl("crs/search_confirm_val"),
            type: "post",
            data: {
                csrf_token: _csrf_hash,
                search_val: search_val
            },
            success: function(resp){
                if(resp == true){
                    toastr.error("This user status is currently Blacklisted.", "Invalid Data!", 10000);
                }
                
            }
        });
    }
    
    tblResume.ajax.reload();
});

//refresh datatable 
$("#reload_dtTbl").on("click", function () {
    tblResume.ajax.reload();
});

function view_resume($id) {
    window.open(baseUrl('crs/view_resume/?id=' + $id));
}

function logexport(type){
    $.ajax({
        url: baseUrl("crs/log_export/"),
        type: "post",
        global: false,
        dataType: "json",
        data: {
            csrf_token : _csrf_hash,
            type : type,
        },
    });
  }
