let ticket_vue = null;

jQuery(document).ready(function () {
    $("#progress").hide();
    fileUploadPhoto();
    $("#webportal").hide();
    $("#dept-res").hide();
});

$("select#category").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _tempContentData.category,
    allowClear: true,
});

$("#severity").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _tempContentData.severity,
    allowClear: true,
}).val('low').trigger('change');

$("select#responsiblity").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _tempContentData.responsibility,
    allowClear: true,
});

$("select#sub_category").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _tempContentData.subcategory,
    allowClear: true,
});

$("select#department").select2({
    width: "100%",
    placeholder: "Select an option",
    data: _tempContentData.department,
    allowClear: true,
}).val(_tempContentData.department_id).trigger('change');

$('#date_required').datetimepicker({
    todayHighlight: true,
    autoclose: true,
    pickerPosition: 'bottom-left',
    todayBtn: true,
    format: 'MM dd, yyyy HH:ii P',
    showMeridian: true,
}).on("changeDate", function (e) {
    var formattedDate = moment(e.date).format('YYYY-MM-DD HH:mm');
    $(this).val(formattedDate);
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
        // {
        //     _type: ["docx", "DOCX"],
        //     icon: "doc.svg",
        //     color: "info"
        // },
        {
            _type: ["pdf", "PDF"],
            icon: "pdf.svg",
            color: "danger"
        },
    ];

    $('#fileupload').on('change', function(e) {
        let valid = true;
        let errorMessage = '';
        
        $.each(e.target.files, function(index, file) {
            // Get filename without extension
            const fileName = file.name.substring(0, file.name.lastIndexOf('.'));
            
            // Check for special characters
            if (!/^[a-zA-Z0-9\s._-]+$/g.test(fileName)) {
                valid = false;
                errorMessage = 'File "' + file.name + '" contains special characters. Please rename the file without special characters.';
                return false; // Break the loop
            }
        });

        if (!valid) {
            toastr.error(errorMessage, "Upload Image", 5000);
            $(this).val(''); // Clear input
            return false;
        }
    });

    $("#fileupload")
        .fileupload({
            url: url,
            dataType: "json",
            formData: {csrf_token: _csrf_hash},
            done: function (e, data) {
                var result = data.result;
                if (result.response) {
                    var avatarImage = result.added_image;
                    var renderImage = result.render_image;
                    images.push(result.display_filename);
                    const filename = result.display_filename;
                    const shortenedName = filename.length <= 20 ? filename : `${filename.slice(0, 20)}...`;
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
                        const uploadlist = `
                            <div class="m-widget2">
                                <div class="m-widget2__item m-widget2__item--${color}">
                                    <div class="m-widget2__checkbox">
                                        <div class="m-widget2__img m-widget2__img--icon">
                                            <img src="${icon_path}" width="45" alt>
                                        </div>
                                    </div>
                                    <div class="m-widget2__desc">
                                        <span class="m-widget2__user-text"></span><br>
                                        <span class="m-widget2__user-name">${shortenedName}</span>
                                        <span class="m-widget2__user-name"></span><br><br>
                                    </div>
                                    <div class="m-widget2__actions">
                                        ${previewButton}
                                        <button type="button"
                                                onclick="removeDocument(this, '${result.display_filename}')"
                                                class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnView"
                                                data-name="${result.display_filename}">
                                            <i class="la la-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
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
    if(type == 'software'){
        $("#dept-res").show();
    }else{
        $("#dept-res").hide();
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

function removeDocument(el, filename) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl("ticket/ticket/remove_file"),
                type: 'POST',
                dataType: "json",
                data: {
                    csrf_token : _csrf_hash,
                    filename : filename,
                },
                success: function (response) {
                    if (response.result) {
                        const _name = $(el).attr("data-name");
                        const parent = $(el).closest('.m-widget2__item');
                        images = images.filter((n) => {return n != _name});
                        $(parent).remove();
                        $("#pic").val(images);
                        toastr.success(_name,"Removed File", 5000);
                    } else {
                        toastr.error("File not found","Error", 5000);
                    }
                }
            });
        }
    });
}



function checkExistingTickets(){
    $.ajax({
        url: baseUrl("ticket/ticket/get_existing_ticket_per_user"),
        type: "POST",
        dataType: "json",
        data: {
            csrf_token: _csrf_hash,
        },
        success: function (data) {
            if (data.length > 0) {
                $("#ticket-preview-dialog").modal("show");
                ticket_vue.vm_tickets = data;
            }
            console.log(data);
        }
    });
}

ticket_vue = new Vue({
    el: "#fticket-preview-dialog",
    data: {vm_tickets: []},
    mounted: function () {
        checkExistingTickets();
        console.log(this.vm_tickets);
    }
});