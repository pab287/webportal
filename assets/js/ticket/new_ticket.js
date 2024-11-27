jQuery(document).ready(function () {
    $("#progress").hide();
    fileUploadPhoto();
    $("#webportal").hide();
});

$("select#category").select2({
    width: "100%",
    placeholder: "Select an option",
    ajax: {
        url: baseUrl("ticket/ticket/get_category_collection/") + 'category',
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$("select#sub_category").select2({
    width: "100%",
    placeholder: "Select an option",
    ajax: {
        url: baseUrl("ticket/ticket/get_category_collection/") + 'sub-category',
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$("select#department").select2({
    width: "100%",
    placeholder: "Select an option",
    ajax: {
        url: baseUrl("ticket/ticket/get_department_collection"),
        dataType: "json",
        delay: 250,
        processResults: function (data) {
            return data;
        }
    }
});

$('#date_required').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    dateTimeFormat: 'yyyy/mm/dd hh:mm tt',
}).on("changeDate", function (e) {
    moment(e.date).format("yyyy/mm/dd hh:mm tt");
    var self = $(e.target);
    self.validate();
});


let images = [];
let fileUploadPhoto = function () {
    
    var url = baseUrl("ticket/ticket/upload_ticket_photo");

    const allowedFileTypes = [
        {
            _type: ["jpg", "jpeg", "png", "PNG", "JPEG", "JPG"],
            icon: "jpg.svg",
            color: "success"
        },
        {
            _type: ["docx", "DOCX"],
            icon: "doc.svg",
            color: "info"
        },
        {
            _type: ["pdf", "PDF"],
            icon: "pdf.svg",
            color: "danger"
        },
    ];

    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                console.log(result.response);
                if (result.response) {
                    var avatarImage = result.added_image;
                    var renderImage = result.render_image;
                    images.push(result.display_filename);
                    $("#picture").attr("src", renderImage);
                    $("#pic").val(images);
                    let ext = renderImage ? renderImage.split(".") : "";
                    let icon = '';
                    let color = '';
                    ext = ext[ext.length - 1];
                    allowedFileTypes.forEach((item, i) => {
                        if (item._type.includes(ext)) {
                            icon = item.icon;
                            color = item.color;
                        }
                    });
                    const icon_path = baseUrl('assets/images/file_icons/' + icon);
                    let previewButton = '';
                        if (result.file_extension == 'docx') {
                            previewButton = '';
                        } else{
                            previewButton = '' +
                                ' <button '+
                                    ' title="Preview" ' +
                                    ' onclick="previewDocument(\'' + avatarImage + '\', \'' + icon + '\', \'' + result.display_filename + '\')"' +
                                    ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnRemove" type="button" id="previewFile"> '+
                                    ' <i class="la la-eye"></i>' +
                                ' </button>';
                        }
                    const uploadlist = ' ' + 
                            '<div class="m-widget2">'+
                            '<div class="m-widget2__item m-widget2__item--'+ color +'">' +
                                '<div class="m-widget2__checkbox">'+
                                    '<div class="m-widget2__img m-widget2__img--icon">'+
                                    '<img src='+ icon_path +' width="45" alt>' +
                                    '</div>'+
                                '</div>'+
                                '<div class="m-widget2__desc">'+
                                    '<span class="m-widget2__user-text">'+
                                    ''+
                                    '</span><br>'+
                                    '<span class="m-widget2__user-name">'+
                                    result.display_filename +
                                    '</span>'+
                                    '<span class="m-widget2__user-name">'+
                                    '</span><br><br>'+
                                '</div>' +
                                '<div class="m-widget2__actions">' +
                                   ''  + previewButton +
                                    ' <button ' +
                                    ' type="button" ' +
                                    ' onclick="removeDocument(this)"'+
                                    ' class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView" data-name="'+result.display_filename+'">'+
                                    ' <i class="la la-times"></i>'+
                                    ' </button>'+
                                '</div>'+
                            '</div>'+
                            '</div>';
                    $("#uploaded_files").append(uploadlist);
                    $("#no_attachment").remove();
                    toastr.success(result.toastr_msg, "Upload Image", 5000);
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

$("#category").on("change", function (e) {
    let type = $("#category option:selected").text();
    if(type == 'webportal'){
        $("#webportal").show();
    }else{
        $("#webportal").hide();
    }
});


$.validate({
    form: '#frm_status_new',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("ticket/ticket/save_ticket"),
            type: "POST",
            dataType: "json",
            data: $("#frm_status_new").find("input,select,textarea,img").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {
                if (data) {
                    toastr.success("Ticket was successfully saved.", "New Ticket Saved.", 5000)
                    setTimeout(() => {
                        window.location.assign(baseUrl("ticket/tickets"));
                    }, 700);
                } else {
                    toastr.error(data.toastr_msg, "Notice: Error!", 5000);
                }
                $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});

function previewDocument(url, icon, filename) {
    let src = url;
    if (icon === "doc.svg") {
        src = "https://docs.google.com/gview?url=" + url + "&embeded=true";
    }

    const modal = $("#preview-document-dialog");
    const modal_title = modal.find(".modal-title");
    const modal_body = modal.find(".modal-body");
    const preview = "<embed src='" + src + "' />";
    modal_title.html(filename);
    modal_body.html(preview);
    modal.modal("show");
}

function removeDocument(el){
    var _name = $(el).attr("data-name");
    const parent = $(el).closest('.m-widget2__item');
    images = images.filter((n) => {return n != _name});
    $(parent).remove();
    $("#pic").val(images);
    toastr.success(_name,"Removed File", 5000);
}