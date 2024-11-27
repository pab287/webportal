jQuery(document).ready(function () {
    var dtPickerBirthDate = $("#frmAddEmployeeData").find("#bday").datepicker({
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
            var self = $(e.target);
            self.validate();
        });
    fileUploadPhoto();
    validatePersonalEmployeeData();
});


var validatePersonalEmployeeData = function () {
    $.validate({
        form: "#frmAddEmployeeData",
        lang: "en",
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            $.ajax({
                url: formUrl,
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function () {
                    $(currentForm)
                        .find(".btn-submit")
                        .addClass("m-btn--custom m-loader m-loader--light m-loader--right")
                        .prop("disabled", true);
                },
                success: function (json) {
                    if (json.response) {
                        toastr.success(
                            json.toastr_msg,
                            "Employee data has been added.",
                            5000
                        );

                        window.location.href = baseUrl("hris/masterfile/edit_employee_masterfile/" + json.id);
                    } else {
                        toastr.error(
                            json.toastr_msg,
                            "Error adding employee data!",
                            5000
                        );
                    }

                    $(currentForm)
                        .find(".btn-submit")
                        .removeClass(
                            "m-btn--custom m-loader m-loader--light m-loader--right"
                        ).prop("disabled", true);
                }
            });
            return false;
        }
    });
}

var fileUploadPhoto = function () {
    var url = baseUrl("hris/masterfile/temp_upload_employee_avatar");
    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var avatarImage = result.added_image;
                    var tempImage = result.temp_image;

                    uploadVM.left_pane = Object.assign({}, {display_avatar: avatarImage});
                    vmTab1.vm_tab1 = Object.assign({}, {pic_filename: tempImage});

                    toastr.success(result.toastr_msg, "Upload Image", 5000);
                    $("#modalUpdatePhoto").modal("hide");
                } else {
                    toastr.error(result.toastr_msg, "Upload Image", 5000);
                }
            },
            progressall: function (e, data) {
                $("#progress").show();
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

var uploadVM = new Vue({
    el: "#left_pane-card",
    data: {left_pane: {display_avatar: baseUrl("assets/images/profile/no_image.jpg")}}
});

var vmTab1 = new Vue({
    el: "#frmAddEmployeeData",
    data: {
        vm_tab1: {
            pic_filename: "",
        }
    }
});