var get = window.location.search;
var data = get.replace("?id=", "");
let search_val;
if(data != null){
  search_val = data;
}else{
  search_val = "";
}
var tblContent = $("#view_file_table").DataTable({
    dom: 'rtlip',
    serverSide: true,
    processing: true,
    ajax: {
        url: baseUrl("crs/get_all_attach_file/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
          d.csrf_token = _csrf_hash;
          d.search['id'] = search_val;
        }
    },
    searching: false,
    columns: [
        {
            data: "filename",
        },
        {
            data: "file_size",
            width: "15%"
        },
        {
            data: "created_by",
            width: "15%",
            render: function (data, type, row, meta) {
              if(data != null){
                return data['employee_name'];
                /*** if(data['suffix'] != "" || typeof data["suffix"] != "undefined" || data["suffix"] != NULL ){
                  return data['firstname'] + " " + data['lastname'] + " " + data['suffix'];
                }else{
                  return data['firstname'] + " " + data['lastname'];
                } ***/
              }else{
                return "ONLINE UPLOAD";
              }
              
            },
        },
        {
            data: "created_at",
            width: "10%"
        },
        {
            data: null,
            width: "5%",
            className: "text-center",
            orderable: false,
            render: function (data, type, row, meta) {
                return itemDatatableActions(row.id, row.status, row.filename);
            },
        },
    ]
});

function itemDatatableActions($id, $status, $filename) {
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
      _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' href='"+baseUrl("uploads/files/hrd/resume_"+search_val+"/"+$filename)+"' download><i class='la la-cloud-download'></i>Download</a>";
      _actionButton += " </div>";
      _actionButton += "</div>";

      return _actionButton;
  } else {
      return "";
  }
}

function view_resume($id){
  $("#view_file_modal").modal("show");
  $.ajax({
    url: baseUrl("crs/preview_file"),
    type: "post",
    data: {
      csrf_token: _csrf_hash,
      id: $id,
    },
    dataType: "json",
    success: function(reps){
      const data = reps['data'];
        const status = reps['status'];
        $("#file_filename").html(reps['filename']);
        if(status != 0){
           $("#preview_file_resume").attr("src", baseUrl("uploads/files/hrd/resume_"+search_val+"/"+data['filename']));
        }else{
           $("#preview_file_resume").attr("src", baseUrl("uploads/files/hrd/"+data['filename']));
        }
    }
  });
}

$(document).ready(function(){
  $.ajax({
    url: baseUrl("crs/get_all_resume_data"),
    type: "post",
    data: {
      csrf_token: _csrf_hash,
      id: search_val
    },
    dataType: "json",
    success: function(reps){
      switch(reps.status) {
        case "pending":
          vmHiredResume.class_name = "m-badge m-badge--danger";
          break;
        case "forinterview":
          vmHiredResume.class_name = "m-badge m-badge--info";
          break;
        case "doneinterview":
          vmHiredResume.class_name = "m-badge m-badge--success";
          break;
        case "blacklisted":
          vmHiredResume.class_name = "m-badge m-badge--secondary text-dark";
          break;
        case "hired":
          vmHiredResume.class_name = "m-badge m-badge--success";
          break;
        case "reserve":
          vmHiredResume.class_name = "m-badge m-badge--info";
          break;
        case "disqualified":
          vmHiredResume.class_name = "m-badge m-badge--secondary text-dark";
          break;
        case "overqualified":
          vmHiredResume.class_name = "m-badge m-badge--secondary text-dark";
          break;   
        case "disregard":
          vmHiredResume.class_name = "m-badge m-badge--secondary text-dark";
          break;      
        case "eligible":
          vmHiredResume.class_name = "m-badge m-badge--info";
          break;
        case "shortlisted":
          vmHiredResume.class_name = "m-badge m-badge--success";
          break;    
        case "pooling":
          vmHiredResume.class_name = "m-badge m-badge--primary";
          break;    
        default:
          // code block
      }
      vmHiredResume.row = Object.assign({}, reps);
    }
  });

});

var vmHiredResume = new Vue({
  el: "#viewResume",
  data: { 
    row: {}, 
    class_name: "m-badge m-badge--metal",
  },
  methods: {
    listItems: function(data){
      var name_data = (data || "").split(',');
      return name_data;
    },
    isEmpty: function(data){
      return jQuery.isEmptyObject(data);
    }, view_resume: function(id){
      $("#view_file_modal").modal("show");
      $.ajax({
        url: baseUrl("crs/preview_file"),
        type: "post",
        data: {
          csrf_token: _csrf_hash,
          id: id,
        },
        dataType: "json",
        success: function(reps){
          const data = reps['data'];
          const status = reps['status'];
          $("#file_filename").html(reps['filename']);
          if(status != 0){
            $("#preview_file_resume").attr("src", baseUrl("uploads/files/hrd/resume_"+search_val+"/"+data['filename']));
          }else{
            $("#preview_file_resume").attr("src", baseUrl("uploads/files/hrd/resume_"+search_val+"/"+data['filename']));
          }
      }
      });
    }, downloadFile: function(file){
      var data = baseUrl("uploads/files/hrd/resume_"+search_val+"/"+file);
      var a = document.createElement('a');
      
      a.href = data;
      a.download = file;
      document.body.append(a);
      a.click();
      a.remove();
    }
  }
});


$("#position_id, #edit_resume_position_id").select2({
  placeholder: 'SELECT AN OPTION',
  width: '100%',
  ajax: {
      url: baseUrl("crs/get_position"),
      processResults: function (data) {
          return data;
      },
      delay: 500
  }
});
