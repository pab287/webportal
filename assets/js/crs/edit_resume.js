var get = window.location.search;
var data = get.replace("?id=", "");
let id;
if(data != null){
  id = data;
}else{
  id = "";
}
var tblContent = $("#edit_file_table").DataTable({
    dom: 'Bfrtlip',
    serverSide: true,
    processing: true,
    buttons: [
      {
        text: '<i class="la la-plus"></i>&nbsp Add',
        className: 'btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white',
        action: function () {
          $("#EditFileModal").modal("show");
        }
      }
    ],
    ajax: {
        url: baseUrl("crs/get_all_attach_file/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function (d) {
          d.csrf_token = _csrf_hash;
          d.search['id'] = id;
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
$(document).ready(function(){
    $.ajax({
        url: baseUrl("crs/edit_resume/") + id,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            $('#frm-edit-resume-dialog [name="id"]').val(data.id);
            $('#frm-edit-resume-dialog [name="current_filename"]').val(data.filename);
            $('#frm-edit-resume-dialog [name="filename"]').val(data.filename);
            $('#frm-edit-resume-dialog [name="firstname"]').val(data.firstname);
            $('#frm-edit-resume-dialog [name="lastname"]').val(data.lastname);
            $('#frm-edit-resume-dialog [name="middlename"]').val(data.middlename);
            $('#frm-edit-resume-dialog [name="suffix"]').val(data.suffix);
            $('#frm-edit-resume-dialog [name="contact_no"]').val(data.contact_no);
            $('#frm-edit-resume-dialog [name="recruitment"]').val(data.recruitment).trigger('change');
            $('#frm-edit-resume-dialog [name="status"]').val(data.status).trigger('change');
            if(data.interview_dt == '0000-00-00 00:00:00'){
                var interview_dt = "";
            }else{
                var interview_dt = data.interview_dt;
            }
            if(data.hired_dt == '0000-00-00 00:00:00'){
              var hired_dt = "";
          }else{
              var hired_dt = data.hired_dt;
          }
          $('#frm-edit-resume-dialog [name="interview_dt"]').val(interview_dt);
            $('#frm-edit-resume-dialog [name="hired_dt"]').val(hired_dt);
            var reasonBlacklist = $('#frm-edit-resume-dialog').find('#reason_blacklisted');
            if (typeof reasonBlacklist !== "undefined") {
                if (data.status == "blacklisted") {
                    if (reasonBlacklist.hasClass("m--hide")) {
                        reasonBlacklist.removeClass("m--hide");
                        reasonBlacklist.find("#blacklist_remarks").val(data.blacklist_remarks);
                    }
                } else {
                    if (!reasonBlacklist.hasClass("m--hide")) {
                        reasonBlacklist.addClass("m--hide");
                        reasonBlacklist.find("#blacklist_remarks").val("");
                    }
                }
            }
            const tag1Array = data.tag1 ? data.tag1.split(",") : "";
            const tag1 = tag1Array.length ? tag1Array.splice(1) : "";

            $("#edit_resume_tag_id").empty();
            if (tag1Array.length) {
                tag1.forEach((item) => {
                    var newOption = new Option(item, item, false, true);
                    $('#edit_resume_tag_id').append(newOption).trigger('change');
                });
            }

            const schoolArray = data.school ? data.school.split(",") : "";
            const school = schoolArray.length ? schoolArray.splice(1) : "";

            $("#edit_resume_school_id").empty();
            if (schoolArray.length) {
                school.forEach((item) => {
                    var newOption = new Option(item, item, false, true);
                    $('#edit_resume_school_id').append(newOption).trigger('change');
                });
            }

            const courseArray = data.course ? data.course.split(",") : "";
            const course = courseArray.length ? courseArray.splice(1) : "";

            $("#edit_resume_course_id").empty();
            if (courseArray.length) {
                course.forEach((item) => {
                    var newOption = new Option(item, item, false, true);
                    $('#edit_resume_course_id').append(newOption).trigger('change');
                });
            }

            const positionArray = data.position ? data.position.split(",") : "";
            const position = positionArray.length ? positionArray.splice(1) : "";

            $("#edit_resume_position_id").empty();
            if (positionArray.length) {
                position.forEach((item) => {
                    var newOption = new Option(item, item, false, true);
                    $('#edit_resume_position_id').append(newOption).trigger('change');
                });
            }

            const applied_dt = moment(new Date(data.applied_dt)).format('L');
            $('#frm-edit-resume-dialog [name="applied_dt"]').val(applied_dt);
            $('#frm-edit-resume-dialog [name="description"]').val(data.description);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        }
    });
    file_images(); 
});

$("#edit_resume_recruitment").select2({
    width: '100%',
});

$("#edit_resume_status").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
});

$("#edit_resume_status").on("change", function(){
    let status_val = $("#edit_resume_status").val();
    if(status_val == 'forinterview'){
        $(".interview_dt").removeClass('m--hide');
        $(".hired_dt").addClass('m--hide');
        $("#hired_dt").val(null);
    }
    else if (status_val == 'hired'){
      $(".hired_dt").removeClass('m--hide');
      $(".interview_dt").addClass('m--hide');
      $("#interview_dt").val(null);
    }
    else{
        $(".interview_dt").addClass('m--hide');
        $(".hired_dt").addClass('m--hide');
        $("#hired_dt").val(null);
        $("#interview_dt").val(null);
    }
});

$("#tag_id, #edit_resume_tag_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_tag"),
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$("#school_id, #edit_resume_school_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_school"),
        processResults: function (data) {
            return data;
        },
        delay: 500
    }
});

$("#course_id, #edit_resume_course_id").select2({
    placeholder: 'SELECT AN OPTION',
    width: '100%',
    ajax: {
        url: baseUrl("crs/get_course"),
        processResults: function (data) {
            return data;
        },
        delay: 500
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

$('#applied_dt, #edit_resume_applied_dt')
    .datepicker({
        todayHighlight: true,
        autoclose: true,
        pickerPosition: 'bottom left',
        todayBtn: 'linked',
        format: 'yyyy-mm-dd',
        forceParse: false
    });

    $('#hired_dt').datepicker({
      todayHighlight: true,
      autoclose: true,
      pickerPosition: 'bottom left',
      todayBtn: 'linked',
      format: 'yyyy-mm-dd',
      forceParse: false,
      endDate: new Date() 
    });


// $("input[name='interview_dt']").datetimepicker({
//     todayHighlight: true,
// 	autoclose: true,
// 	pickerPosition: 'top-left',
// 	todayBtn: true,
// 	format: 'yyyy/mm/dd hh:ii',
//     defaultTime:'05:00'
// });

$('#interview_dt').datetimepicker({
	todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
	todayBtn: true,
	format: 'yyyy-mm-dd hh:ii',
});

function formatTag(data) {
    if (data.charAt(0) === ",") {
        return data.substr(1);
    } else {
        return data;
    }
}

$("#editfilereset").on("click", function(){
    $.ajax({
        url: baseUrl("crs/delAllTempFile"),
        type: "get",
        dataType: "json",
        success: function(resp){
            if(resp){
                $("#edit_fileupload").val("");
                $("#edit_files div").remove();
                window.location.reload();
            }
        }
    });
});

function itemDatatableActions($id, $status, $filename) {
    if ($id) {
        var _actionButton = "";
        _actionButton += "<div class='dropdown'>";
        _actionButton += "<a href='#' class='btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' data-toggle='dropdown'>";
        _actionButton += "<i class='fa fa-ellipsis-v'></i>";
        _actionButton += "</a>";
        _actionButton += "<div class='dropdown-menu dropdown-menu-right'>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='edit_view_resume(" + $id + ")'><i class='la la-eye'></i>View</a>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' onclick='edit_delete_resume(" + $id + ")'><i class='la la-trash'></i>Delete</a>";
        _actionButton += "<a style='width: auto; cursor: pointer;' class='dropdown-item' href='"+baseUrl("uploads/files/hrd/resume_"+id+"/"+$filename+"")+"' download><i class='la la-cloud-download'></i>Download</a>";
        _actionButton += " </div>";
        _actionButton += "</div>";
  
        return _actionButton;
    } else {
        return "";
    }
}

// the same on view_resume page code for addnew 
$("#editNewFileForm").on("submit", function(e){
    e.preventDefault();
    $.ajax({
        url: baseUrl("crs/add_new_file_attach"),
        type: "post",
        data: {
        csrf_token: _csrf_hash,
        id: id
        },
        dataType: "json",
        success: function(reps){
            if(reps == true){
                $("#EditFileModal").modal("hide");
                tblContent.ajax.reload();
                $("#edit_files div").remove();
                editPreviewFile.rows = Object.assign({});
            }
        }
    });
});
$("#editfileClose").on("click", function(){
    $.ajax({
        url: baseUrl("crs/delAllTempFile"),
        type: "get",
        dataType: "json",
        success: function(resp){
            if(resp){
                $("#EditFileModal").modal("hide");
                $("#edit_fileupload").val("");
                $("#edit_files div").remove();
            }
        }
    });
});
function edit_view_resume($id){
    $("#edit_view_file_modal").modal("show");
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
         $("#edit_file_filename").html(data['filename']);
        if(status != 0){
            toastr.success("Success", "Resume Updated.", 10000);
            $("#edit_preview_file_resume").attr("src", baseUrl("crs/resume"));
        }else{
            $("#edit_preview_file_resume").attr("src", baseUrl("uploads/files/hrd/"+data['filename']));
        }
        
      }
    });
}
function edit_delete_resume($id){
    $.ajax({
      url: baseUrl("crs/file_delete"),
      type: "post",
      data: {
        csrf_token: _csrf_hash,
        id: $id,
      },
      success: function(reps){
        if(reps == true){
          tblContent.ajax.reload();
        }
      }
    });
}
function editResumeInfo() {
    let resumeEdit = document.getElementById("frm-edit-resume-dialog");
    let form = $("#frm-edit-resume-dialog");
    const formData = new FormData(resumeEdit);

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("crs/update_resume"),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            global: false,
            data: formData,
            success: function (response) {
                console.log(response);
                if(response != false){
                    toastr.success("Success", "Resume Updated.", 10000);
                    window.location.href = baseUrl("crs/resume");
                }
            }
        });
    }
}
var array_file_ext = ["doc", "pdf", "jpg"];
var file_images = function () {
    var url = baseUrl("crs/edit_new_temp_file");
    $("#edit_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {
              csrf_token: _csrf_hash,
              id: id
            },
            done: function (e, data) {
                var result = data.result;
                if(result == 0){
                    alert("File is not supported! File is not uploaded!");
                }
                editPreviewFile.count = result.length;
                editPreviewFile.rows = Object.assign({}, editPreviewFile.rows, result);
            },
            progressall: function (e, data) {
                $("#editprogress").show();
                var progress = parseInt((data.loaded / data.total) * 100, 10);
                var progressTotal = 0;
  
                var steps = setInterval(function () {
                    progressTotal += 10;
                    $("#editprogress .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#editprogress .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);
  
                if (progress == 100) {
                    setTimeout(function () {
                        $("#editprogress").hide();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
  };
  var editPreviewFile = new Vue({
    el: "#edit_preview",
    data: { 
        rows: [],
        className: "",
        count: 0
    },
    methods: {
        getExtension: function(file){
            var type = file.split('.').pop();
            if(array_file_ext.includes(type)){
                switch(type) {
                case "doc":
                    return baseUrl("assets/images/file_icons/doc.svg");
                      
                case "pdf":
                    return baseUrl("assets/images/file_icons/pdf.svg");
                    
                case "jpg":
                    return baseUrl("assets/images/file_icons/jpg.svg");
                    
                default:
                    return baseUrl("assets/images/file_icons/default.svg");
                }
            }
        },
        getClass: function(classes){
            var type = classes.split('.').pop();
            if(array_file_ext.includes(type)){
                switch(type) {
                case "doc":
                    return "m-widget4 m-widget2__item m-widget2__item--primary col-lg-12";
                        
                case "pdf":
                    return "m-widget4 m-widget2__item m-widget2__item--danger col-lg-12";
                    
                case "jpg":
                    return "m-widget4 m-widget2__item m-widget2__item--success col-lg-12";
                    
                default:
                    return "m-widget4 m-widget2__item m-widget2__item--secondary text-dark col-lg-12";
                }
            }
        },
        fileDelete: function(id){
            $.ajax({
                url: baseUrl("crs/temp_file_delete"),
                type: "post",
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                },
                dataType: "json",
                success: function(resp){
                    editPreviewFile.rows = Object.assign({});
                    editPreviewFile.rows = Object.assign({}, resp);
                }
            });
            $.ajax({
                url: baseUrl("crs/temp_file_delete"),
                type: "post",
                data: {
                    csrf_token: _csrf_hash,
                    id: id,
                },
                dataType: "json",
                success: function(resp){
                    editPreviewFile.rows = Object.assign({});
                    editPreviewFile.rows = Object.assign({}, resp);
                }
            });
        }
    }
});
$("#saveEdit").on("click", function(){
    editResumeInfo();
});