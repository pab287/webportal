var get = window.location.search;
var data = get.replace("?id=", "");
let search_val;
if(data != null){
  search_val = data;
}else{
  search_val = "";
}
var tblContent = $("#view_blacklisted_file_table").DataTable({
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
            width: "15%"
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
      _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='view_blacklist_resume(" + $id + ")'><i class='la la-eye'></i>View</a>";
      _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' href='"+baseUrl("uploads/files/hrd/resume_"+search_val+"/"+$filename)+"' download><i class='la la-cloud-download'></i>Download</a>";
      _actionButton += " </div>";
      _actionButton += "</div>";

      return _actionButton;
  } else {
      return "";
  }
}

function view_blacklist_resume($id){
  $("#view_blacklisted_modal").modal("show");
  $.ajax({
    url: baseUrl("crs/blacklist_preview_file"),
    type: "post",
    data: {
      csrf_token: _csrf_hash,
      id: $id,
      body_id: search_val
    },
    dataType: "json",
    success: function(resp){
      console.log(resp);
      $("#blacklisted_file_filename").html(resp.filename);
      $("#blacklist_preview_file_resume").attr("src", baseUrl("uploads/files/hrd/resume_"+search_val+"/"+resp.filename));
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
        case "for interview":
          vmHiredResume.class_name = "m-badge m-badge--info";
          break;
        case "done interview":
          vmHiredResume.class_name = "m-badge m-badge--success";
          break;
        case "blacklisted":
          vmHiredResume.class_name = "m-badge m-badge--secondary text-dark";
          break;
        default:
          // code block
      }
      vmHiredResume.row = Object.assign({}, reps);
    }
  });
});

var vmHiredResume = new Vue({
  el: "#viewBlacklisted",
    data: { 
    row: {},
    class_name: "m-badge m-badge--metal"
  },
  methods: {
    listItems: function(data){
      var name_data = (data || "").split(',');
      return name_data;
    }
  }
});
