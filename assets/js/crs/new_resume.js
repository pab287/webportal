$(document).ready(function(){
    temp_images();
});

$("#recruitment").select2({
    width: '100%',
    placeholder: 'SELECT SOURCE'
});

$("#status").select2({
    width: '100%',
    placeholder: 'SELECT STATUS'
});

$("#status").on("change", function(){
    let status_val = $("#status").val();
    if(status_val == 'forinterview'){
        $(".interview_dt").removeClass('m--hide');
    }else{
        $(".interview_dt").addClass('m--hide');
    }
});

$("#interview_dt").datetimepicker({
    todayHighlight: true,
	autoclose: true,
	pickerPosition: 'bottom-left',
    todayBtn: 'linked',
	format: 'yyyy/mm/dd hh:ii',
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
        format: 'mm/dd/yyyy',
        forceParse: false
    });

function formatTag(data) {
    if (data.charAt(0) === ",") {
        return data.substr(1);
    } else {
        return data;
    }
}
$("#addfilereset").on("click", function(){
    $.ajax({
        url: baseUrl("crs/delAllTempFile"),
        type: "get",
        dataType: "json",
        success: function(resp){
            if(resp){
                $("#temp_fileupload").val("");
                $("#temp_files div").remove();
                window.location.reload();
            }
        }
    });
});

function saveResumeInfo(formElement, e) {
    e.preventDefault();
    const form = $(formElement);
    const formData = new FormData(formElement);
    
    if (form.isValid()) {
        $.ajax({
            url: baseUrl("crs/new_resume"),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                if (response.success) {
                    search_val = response.data;
                    $("#generalSearch").val(search_val);
                    toastr.success(response.message, "New Resume Saved.", 10000);
                    $("#modal_form_document").modal("hide");
                    location.href = baseUrl("crs/resume");
                }else{
                    toastr.error(response.message, "New Resume Not Saved.", 10000);
                }
            }
        });
    }
}

var array_file_ext = ["doc", "pdf", "jpg"];
var temp_images = function () {
    var url = baseUrl("crs/add_temp_file");
    $("#temp_fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                $("#progress").removeClass("d-none");
                $("#progress").show();
                var result = data.result;
                if(result == 0){
                    alert("File is not supported! File is not uploaded!");
                }
                previewFile.count = result.length;
                previewFile.rows = Object.assign({}, previewFile.rows, result);
            },
            progressall: function (e, data) {
                var progress = parseInt((data.loaded / data.total) * 100, 10);
                var progressTotal = 0;

                var steps = setInterval(function () {
                    progressTotal += 10;
                    $("#progress .progress-bar").css("width", progressTotal + "%");
                    if (progressTotal == 100) {
                        clearInterval(steps);
                        progressTotal = 0;
                        setTimeout(function () {
                            $("#progress .progress-bar").css("width", progressTotal + "%");
                        }, 1500);
                    }
                }, 10);

                if (progress == 100) {
                    setTimeout(function () {
                        $("#progress").hide();
                    }, 1000);
                }
            }
        })
        .prop("disabled", !$.support.fileInput)
        .parent()
        .addClass($.support.fileInput ? undefined : "disabled");
};
var previewFile = new Vue({
    el: "#new_preview",
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
                    return "m-widget4 m-widget2__item m-widget2__item--primary col-lg-4";
                        
                case "pdf":
                    return "m-widget4 m-widget2__item m-widget2__item--danger col-lg-4";
                    
                case "jpg":
                    return "m-widget4 m-widget2__item m-widget2__item--success col-lg-4";
                    
                default:
                    return "m-widget4 m-widget2__item m-widget2__item--default col-lg-4";
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
                    previewFile.rows = Object.assign({});
                    previewFile.rows = Object.assign({}, resp);
                }
            });
        }
    }
});