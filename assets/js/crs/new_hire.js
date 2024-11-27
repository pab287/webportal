var get = window.location.search;
var data = get.replace("?id=", "");
let $id;
if(data != null){
  $id = data;
}else{
  $id = "";
}
$(document).ready(function(){
    if ($id) {
        $.ajax({
            url: siteUrl("crs/get_current_resume/" + $id),
            global: false,
            dataType: "json",
            success: function (json) {
                if (json.response) {
                    vmToHire.row = Object.assign({}, json.data);
                    $("#modal-form_hire").modal("show");
                }
            }
        });
    } else {
        return false;
    }
});

function hireResumeInfo(formElement) {
    const form = $(formElement);
    const formData = new FormData(formElement);

    if (form.isValid()) {
        $.ajax({
            url: baseUrl("crs/hire_resume"),
            dataType: "JSON",
            type: "POST",
            processData: false,
            contentType: false,
            global: false,
            data: formData,
            success: function (json) {
                if (json.response) {
                    tblResume.ajax.reload();
                    toastr.success(json.toastr_msg, "Resume Hired", { timeOut: 5000 });
                    $("#modal-form_hire").modal("hide");
                } else {
                    toastr.error(json.toastr_msg, "Resume Hired", { timeOut: 5000 });
                }
            }
        });
    }
}

var vmToHire = new Vue({
    el: "#hire-content",
    data: { row: {}, position: {} },
    methods: {
        select2Position: function () {
            var _this = this;
            var currentElement = _this.$el;
            var selectPos = $(currentElement).find("select#position");
            var dtPickerBday = $(currentElement).find("input#bday");
            if (typeof selectPos !== "undefined" && selectPos.length == 1) {
                selectPos.select2({
                    width: "100%",
                    placeholder: "Select an option",
                    ajax: {
                        url: siteUrl("crs/get_select2_request_position"),
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            return data;
                        },
                    }
                }).on("select2:select", function (e) {
                    var tempData = e.params.data;
                    console.log(tempData);
                    _this.position = Object.assign({}, tempData);
                });
            }

            if (typeof dtPickerBday !== "undefined" && dtPickerBday.length == 1) {
                var dtPickerBirthDate = dtPickerBday.datepicker({
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    },
                    format: "yyyy-mm-dd",
                    autoclose: true
                })
                    .on("changeDate", function (e) {
                        var currentDt = moment(e.date).format("YYYY-MM-DD");
                    });
            }
        }
    }, mounted: function () {
        var _this = this;
        _this.select2Position();
    }
});

function archiveResume(formElement) {
    const form = $(formElement);
    const url = form.attr('action');
    $.ajax({
        url,
        type: "GET",
        dataType: "JSON",
        global: false,
        success: function (data) {
            tblResume.ajax.reload();
            $("#modal-confirm-archive-resume").modal("hide");
            if (data) {
                toastr.success("Resume was successfully archived.", "Resume Archived.", 10000);
            } else {
                toastr.error("An error occurred.", "Error", 10000);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error adding / update data');
        }
    });
}

// $("#position").select2({
//     placeholder: 'SELECT AN OPTION',
//     width: '100%',
//     ajax: {
//         url: baseUrl("crs/get_position"),
//         processResults: function (data) {
//             return data;
//         },
//         delay: 500
//     }
//   });